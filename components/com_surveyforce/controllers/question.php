<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access.
defined('_JEXEC') or die;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use GuzzleHttp\Client;

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');

/**
 * Question controller class.
 */
class SurveyforceControllerQuestion extends JControllerForm
{
    /**
     * Proxy for getModel.
     *
     * @since    1.6
     */
    public function getModel($name = 'question', $prefix = '', $config = ['ignore_request' => true])
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function check_question_form()
    {
        $model = $this->getModel();
        $config = JFactory::getConfig();
        $session = &JFactory::getSession();
        $app = JFactory::getApplication();
        $params = $app->getParams();

        $survey_id = $app->input->getInt('sid', 0);
        $itemid = $app->input->getInt('Itemid', 0);
        $client_ip = JHtml::_('utility.getUserIP');

        $is_public = SurveyforceVote::getSurveyData($survey_id, "is_public");

        $category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);
        $intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);

        // 檢查是否閒置過久
        if (SurveyforceVote::isSurveyExpired($survey_id) == false) {
            $msg = "網頁已閒置過久，請重新點選議題進行投票。";
            $this->setRedirect($category_link, $msg);

            return;
        }

        // 檢查議題是否有效
        if (SurveyforceVote::isSurveyValid($survey_id) == false) {
            $msg = "該議題目前未在可投票時間內，請重新選擇。";
            $this->setRedirect($category_link, $msg);

            return;
        }

        // 檢查是否有中途更換議題
        if (SurveyforceVote::checkSurveyStep($survey_id, "statement") == false) {
            $msg = "未從該議題投票啟始頁進入，請重新執行。";
            $this->setRedirect($intro_link, $msg);

            return;
        }

        // 檢查未公開議題是否有依序執行步驟
        if (SurveyforceVote::getSurveyItem($survey_id)->is_public == 0) {
            if (SurveyforceVote::checkSurveyStep($survey_id, "token") == false) {
                $msg = "該議題為未公開投票，請重新點選議題連結進行投票。";
                $app->redirect($category_link, $msg);

                return;
            }
        }

        // 檢查題目是否是議題其中之一
        $question_id = $app->input->getInt('qid', 0);
        $result = json_decode($this->checkQuestionInSurvey($survey_id, $question_id));
        if ($result->status == 0) {
            $this->setRedirect($category_link, $result->msg);

            return;
        } else {
            $question_item = $result->question_item;  // 回傳題目內容
        }

        // 載入plugin的function來檢查
        JPluginHelper::importPlugin('survey', $question_item->question_type);
        $className = 'plgSurvey' . ucfirst($question_item->question_type);

        // 檢查選項是否有填寫及是否是題目其中之一
        $post = $app->input->getArray($_POST);
        $return_link = JRoute::_("index.php?option=com_surveyforce&view=question&sid={$survey_id}&qid={$question_id}&Itemid={$itemid}", false);
        $msges = [];

        if (method_exists($className, 'onCheckOptionField')) {
            $result = json_decode($className::onCheckOptionField($question_item, $post)); // 檢查欄位是否有填寫

            if ($result->status == 0) {
                $msges[] = $result->msg;
            }
        }

        if (count($msges) > 0) {
            $this->setRedirect($return_link, implode("<br>", $msges));

            return;
        }

        // 記錄答案 (一樣依題目ID做記錄)
		$option_answers = [];
        $option_answers = SurveyforceVote::getSurveyData($survey_id, "option_answers");
        $onto_answers = SurveyforceVote::getSurveyData($survey_id, "onto_answers");
        if ($option_answers == "") { // 找無資料，表示尚未記錄過
            $option_answers = [];
        }
        if ($onto_answers == "") { // 找無資料，表示尚未記錄過
            $onto_answers = [];
        }
        $option_answers[$question_id] = $className::onSaveUserOption($question_item, $post);
        $onto_answers[$question_id] = $option_answers[$question_id];
        $onto_answers[$question_id]['sf_qtext'] = $question_item->sf_qtext;
        SurveyforceVote::setSurveyData($survey_id, "option_answers", $option_answers);
        SurveyforceVote::setSurveyData($survey_id, "onto_answers", $onto_answers);

        // 檢查所有題目是否都已做過，若尚未，則轉入該題目。
        $questions = $model->getQuestions($survey_id);
        foreach ($questions as $question) {
            if (!array_key_exists($question->id, $option_answers)) {
                $next_question_link = JRoute::_("index.php?option=com_surveyforce&view=question&sid={$survey_id}&qid={$question->id}&Itemid={$itemid}", false);
                $this->setRedirect($next_question_link);

                return;
            }
        }

        // 代表題目都已完成，進行送入票箱的檢查
        $prac = $session->get('practice');
		$is_testsite = $config->get( 'is_testsite', false );

        if (!$prac) {

            // 檢查議題是否到期
            $result = json_decode($this->checkSurvey($survey_id));
            if (!$result->status) {
                $link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);

                $this->setRedirect($link, $result->msg);

                return;
            }

            // 先鎖住程式執行
            $ivoting_save_path = $config->get('ivoting_save_path');
            $agent_path = $config->get('agent_path');
            $ivoting_path = $config->get('ivoting_path');

            $verify_identify = SurveyforceVote::getSurveyData($survey_id, "verify_identify");  // 取出驗證方式的識別碼
            $vote_num_params = SurveyforceVote::getSurveyData($survey_id, "vote_num_params");
            if (count($verify_identify) > 0) {
                foreach ($verify_identify as $type => $identify) {
                    if (!$model->insertVoteLock($survey_id, $identify, $type)) {
                        $msg = "驗證資料重複傳送，請稍候重試。";
                        $this->setRedirect($return_link, $msg);

                        return;
                    }
                }
            }
			

            // 檢查是否已投過票
            if (count($verify_identify) > 0) {
                foreach ($verify_identify as $type => $identify) {
                    $result = json_decode($this->checkIsVote($agent_path, $survey_id, $identify, $type, $vote_num_params, $client_ip));
                    if ($result->status == 0) {  // 已投過票
                        $this->setRedirect($category_link, $result->msg);

                        return;
                    }
                }
            } else {
                $this->setRedirect($intro_link, "請確認已通過議題驗證。");

                return;
            }
			
			// 預設資料
			$created = JFactory::getDate()->toSql();	// 目前時間
						
			// 取得議題公開狀態
            $display_result = SurveyforceVote::getSurveyData($survey_id, "display_result");
            $is_test = SurveyforceVote::getSurveyData($survey_id, "is_test");
            if ($is_test) {
                $display_result = true;
                $is_public = true;
            }
	
			
			
			
			// 取票號
            $result = json_decode($this->getTicket($agent_path, $survey_id));
            if ($result->status == 0) {		// 無法取得票號                
				$this->setRedirect($return_link, $result->msg);
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("無法取得票號, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);
				
                return;
            } else {
                $ticket_num = $result->ticket_num;
            }
			
			
			
			// 將選票內容送至區塊鏈
            if ($display_result && $is_public && !$is_testsite) {
				$survey_title = SurveyforceVote::getSurveyData($survey_id, "title");
				$is_blockchain = SurveyforceVote::getSurveyData($survey_id, "is_blockchain");
				
				// 將需要上鏈的資料寫入資料表中
				$this->insertBlockchainToDB($survey_id, $survey_title, $ticket_num, json_encode($onto_answers), $is_blockchain, $created);
				
            }
			
			
			$verify_is_student = $session->get('verify_is_student_' . $survey_id, 0);
			$verify_is_local = $session->get('verify_is_local_' . $survey_id, 0);
			$verify_company_name = $session->get('verify_company_name_' . $survey_id, "");

            // 送進票箱 agent(poll)
            foreach ($verify_identify as $type => $identify) {
                $result = json_decode($this->markVote($agent_path, $survey_id, $identify, $type, $client_ip, $verify_is_student, $verify_is_local, $verify_company_name));
                if ($result->status == 0) {
                    $this->setRedirect($return_link, $result->msg);
					JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入Agent Poll, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);

                    return;
                }
            }

            
			
            // 將選票的內容寫入DB db(detail)
            $result = json_decode($this->insertVoteToDB($survey_id, $ticket_num, $option_answers, $created));
            if ($result->status == 0) {
//                $this->setRedirect($return_link, $result->msg);
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入DB Detail, sid:%d, Msg:%s, ticket:%s, answer:%s", $survey_id, $result->msg, $ticket_num, json_encode($option_answers)), JLog::ERROR);

                return;
            }

            // 寫入性別、年齡分析
			$verify_params = json_decode(SurveyforceVote::getSurveyData($survey_id, "verify_params"), true);  // 取出驗證方式參數
            foreach ($verify_identify as $type => $identify) {
                // 檢查投票人資料填寫是否有身分證
                $any_params = false;
                if (array_key_exists('any', $verify_params) && array_key_exists('fields', $verify_params['any'])) {
                    if (in_array('idnum', $verify_params['any']['fields']) && in_array('birth', $verify_params['any']['fields'])) {
                        $any_params = true;
                    }
                }

				// 可投票人名單是否有啟用身分證
                $assign_params = false;
                if ($verify_params['assign']['assign_is_idnum']) {
					$assign_params = true;
                }
			
				// 驗證方式為身分證、台北通、投票人資料填寫(有啟用身分證)、可投票人名單(有啟用身分證)
                if ($type == 'idnum' || $type == 'taipeicard' || ($type == 'any' && $any_params == true) || ($type == 'assign' && $assign_params == true)) {
                    $verify_reserve = $session->get('verify_post_' . $survey_id);
                    $result = json_decode($this->insertVoteAge($survey_id, $verify_reserve, $created, $type));
                    // 清除資料
                    $session->clear('verify_post_' . $survey_id);
                    if ($result->status == 0) {
//                        $this->setRedirect($return_link, $result->msg);
						JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入性別分析, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);

                        return;
                    }

                    if ($result->age) {
                        $result = json_decode($this->insertVoteSex($survey_id, $identify, $created));

                        if ($result->status == 0) {
//                            $this->setRedirect($return_link, $result->msg);
							JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入性別分析, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);

                            return;
                        }
                    }
                }
            }

            // 將選票的內容寫入log
            $result = json_decode($this->insertVoteToLog($survey_id, $ticket_num, $option_answers, $created, $client_ip, $ivoting_save_path, $is_public));
            if ($result->status == 0) {
//                $this->setRedirect($return_link, $result->msg);
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入log檔, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);

                return;
            }

           

            // 分析欄位內容寫入DB
            $analyze_answers = SurveyforceVote::getSurveyData($survey_id, "analyze_answers");
            $result = json_decode($this->insertAnalyzeToDB($survey_id, $analyze_answers, $created));
            if ($result->status == 0) {
//                $this->setRedirect($return_link, $result->msg);
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("寫入分析欄位, sid:%d, Msg:%s", $survey_id, $result->msg), JLog::ERROR);

                return;
            }
        }
			
        // 設定已通過question步驟
        SurveyforceVote::setSurveyStep($survey_id, "question", true);
        if (SurveyforceVote::getSurveyItem($survey_id)->is_public == 0) {
            SurveyforceVote::setSurveyStep($survey_id, "token");
        }

        if (!$prac) {
            // 寫入票號至Session
            SurveyforceVote::setSurveyData($survey_id, "ticket", $ticket_num);

            // 刪除投票Lock
            if (count($verify_identify) > 0) {
                foreach ($verify_identify as $type => $identify) {
                    $model->deleteVoteLock($survey_id, $identify, $type);
                }
            }
        }

        // 所有驗證方式的結束
        $select_verify_types = SurveyforceVote::getSurveyData($survey_id, "select_verify_types");
        if ($select_verify_types) {
            foreach ($select_verify_types as $type) {
                JPluginHelper::importPlugin('verify', $type);
                $className = 'plgVerify' . ucfirst($type);
                if (method_exists($className, 'onVerifyFinish')) {
                    $className::onVerifyFinish($survey_id);
                }
            }

            // 刪除驗證方式 (避免按上一頁返回)
            SurveyforceVote::setSurveyData($survey_id, "verify_identify", null);
        }

        $link = JRoute::_("index.php?option=com_surveyforce&view=finish&sid={$survey_id}&Itemid={$itemid}", false);

        $this->setRedirect($link);
    }

    // 檢查議題是否在投票時間內
    public function checkSurvey($_survey_id)
    {
        // 檢查投票時間
        if (!SurveyforceVote::isSurveyValid($_survey_id)) {
            $result = ["status" => 0, "msg" => "該議題目前未在可投票時間內，請重新選擇。"];
        } else {
            $result = ["status" => 1, "msg" => ""];
        }

        return json_encode($result);
    }

    // 檢查題目ID是否為屬於該議題
    public function checkQuestionInSurvey($_survey_id, $_question_id)
    {
        $model = $this->getModel();

        $row = $model->getQuestion($_question_id);
        if ($row->sf_survey == $_survey_id) {
            $result = ["status" => 1, "msg" => "", "question_item" => $row];
        } else {
            $result = ["status" => 0, "msg" => "該題目並非屬於該議題之一，請重新選擇。"];
        }

        return json_encode($result);
    }

    // 檢查是否已投過票
    public function checkIsVote($_agent_path, $_survey_id, $_identify, $_verify_type, $_vote_num_params, $_client_ip)
    {
        // Agent API - 檢查是否已投票
        $api_request_url = $_agent_path . "/server_poll.php";
        $api_request_parameters = [
            'survey_id' => $_survey_id,
            'identify' => $_identify,
            'verify_type' => $_verify_type,
            'vote_num_params' => $_vote_num_params,
            'client_ip' => $_client_ip,
        ];

        $api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
        if ($api_result == "") {
            $result = ["status" => 0, "msg" => "無法執行步驟，請重新操作。"];
        } else {
            $decode_data = json_decode($api_result);

            if ($decode_data->status == 1) {
                $result = ["status" => 0, "msg" => sprintf("您的資料已投過票，%s。", $decode_data->msg)];
                //$result = array ("status" => 0, "msg" => '投票人已在本區投過票，此資格投票僅限1次，若於本市其他行政區尚有「設籍、居住」或「就學、就業」資格，請選擇另一種驗證方式，並點選該行政區進行投票。');
            } else {
                if ($decode_data->status == 2) {
                    $result = ["status" => 0, "msg" => sprintf("%s，請稍候再試或選擇其他議題進行投票。", $decode_data->msg)];
                } else {
                    $result = ["status" => 1, "msg" => ""];
                }
            }
        }

        return json_encode($result);
    }

    // Agent API - 寫入投票標記
    public function markVote($_agent_path, $_survey_id, $_identify, $_verify_type, $_client_ip, $_verify_is_student, $_verify_ic_local, $_verify_company_name)
    {		
        $api_request_url = $_agent_path . "/server_poll.php";
        $api_request_parameters = [
            'survey_id' => $_survey_id,
            'identify' => $_identify,
            'verify_type' => $_verify_type,
            'client_ip' => $_client_ip,
            'verify_is_student' => $_verify_is_student,
            'verify_is_lcoal' => $_verify_ic_local,
            'verify_company_name' => $_verify_company_name,
        ];

        $api_result = SurveyforceVote::curlAPI($api_request_url, "PUT", $api_request_parameters);
        if ($api_result == "") {
            $result = ["status" => 0, "msg" => "無法執行步驟，請重新操作。"];
        } else {
            $decode_data = json_decode($api_result);

            if ($decode_data->status == 0) {
                $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
                JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
            } else {
                $result = ["status" => 1, "msg" => ""];
            }
        }

        return json_encode($result);
    }

    // Agent API - 取票號
    public function getTicket($_agent_path, $_survey_id)
    {
        $api_request_url = $_agent_path . "/server_ticket.php";
        $api_request_parameters = [
            'survey_id' => $_survey_id,
        ];

        $api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
        if ($api_result == "") {
            $result = ["status" => 0, "msg" => "無法執行步驟，請重新操作。"];
        } else {
            $decode_data = json_decode($api_result);

            if ($decode_data->status == 0) {
                $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
                JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
            } else {
                $result = ["status" => 1, "msg" => "", "ticket_num" => $decode_data->ticket_num];
            }
        }

        return json_encode($result);
    }

    // Agent API - 刪除票號
    public function deleteTicket($_agent_path, $_survey_id, $_ticket_num)
    {
        $api_request_url = $_agent_path . "/server_ticket.php";
        $api_request_parameters = [
            'survey_id' => $_survey_id,
            'ticket_num' => $_ticket_num
        ];

        $api_result = SurveyforceVote::curlAPI($api_request_url, "DELETE", $api_request_parameters);
        if ($api_result == "") {
            $result = ["status" => 0, "msg" => "無法執行步驟，請重新操作。"];
        } else {
            $decode_data = json_decode($api_result);

            if ($decode_data->status == 0) {
                $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
                JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
            } else {
                $result = ["status" => 1, "msg" => "", "ticket_num" => $decode_data->ticket_num];
            }
        }

        return json_encode($result);
    }

    // 寫入投票內容至DB
    public function insertVoteToDB($_survey_id, $_ticket_num, $_option_answers, $_created)
    {
        $model = $this->getModel();

        // 寫入票號
        if (!$model->insertVote($_ticket_num, $_survey_id, $_created)) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];

            return json_encode($result);
        }

        // 寫入每個選項  (暫不考慮開放式欄位)
        if (count($_option_answers) > 0) {
            foreach ($_option_answers as $question_id => $options) {
                foreach ($options as $option) {
                    if (!$model->insertVoteDetail($_ticket_num, $_survey_id, $question_id, $option, $_created)) {
                        $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];

                        return json_encode($result);
                    }
                }
            }
        }

        // 更新該議題的總票數
        $model->updateTotalVote($_survey_id);

        $result = ["status" => 1, "msg" => ""];

        return json_encode($result);
    }

    // 寫入投票內容至Log (記錄真正的時間)
    public function insertVoteToLog(
        $_survey_id,
        $_ticket_num,
        $_option_answers,
        $_created,
        $_client_ip,
        $_ivoting_save_path,
        $_is_public
    )
    {
        $session = &JFactory::getSession();

        $result = ["status" => 1, "msg" => ""];

        // log分日期存放
        $today = JHtml::_('date', $_created, "Ymd");
        $withIP_file = $_ivoting_save_path . "/log/withIP/" . $_survey_id . "_" . $today . ".log";
        $noIP_file = $_ivoting_save_path . "/log/noIP/" . $_survey_id . "_" . $today . ".log";

        $output = "%datetime% %message%\n";
        $formatter = new LineFormatter($output);

        // 寫入選項名稱 ( 不寫開放式欄位 )
        if (count($_option_answers) > 0) {
            $question_str = '';
            $qcount = 1;
            foreach ($_option_answers as $question_id => $options) {
                $question_str .= "Q" . $qcount . ":";
                foreach ($options as $option) {
                    $question_str .= $option["logstr"] . ",";
                }
                $question_str .= ";";
                $qcount++;
            }
        }

        // 寫入含IP log
        try {
            $log_str = sprintf("%s\t%s\t%s", $_ticket_num, $question_str, $_client_ip);
            $withIpHanlder = new StreamHandler($withIP_file, 'INFO');
            $withIpHanlder->setFormatter($formatter);

            $log = new Logger('Github.Log.withIp');
            $log->pushHandler($withIpHanlder);
            $log->info($log_str);
        } catch (Exception $e) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試"];
            JHtml::_('utility.recordLog', "vote_log.php", sprintf("無法新增記錄檔：%s", $e), JLog::ERROR);
        }

        // 寫入不含IP log
        try {
            $log_str = sprintf("%s\t%s", $_ticket_num, $question_str);
            $noIpHanlder = new StreamHandler($noIP_file, 'INFO');
            $noIpHanlder->setFormatter($formatter);

            $log = new Logger('Github.Log.NoIp');
            $log->pushHandler($noIpHanlder);
            $log->info($log_str);
        } catch (Exception $e) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試"];
            JHtml::_('utility.recordLog', "vote_log.php", sprintf("無法新增記錄檔：%s", $e), JLog::ERROR);
        }

        return json_encode($result);
    }

    public function insertAnalyzeToDB($survey_id, $analyze_answers, $created)
    {
        $model = $this->getModel();

        if (!$model->insertAnalyzeData($survey_id, $analyze_answers, $created)) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
        } else {
            $result = ["status" => 1, "msg" => ""];
        }

        return json_encode($result);
    }

    public function insertVoteSex($survey_id, $identify, $created)
    {
        $model = $this->getModel();

		// 取得性別
		// 臺灣地區無戶籍國民、大陸地區人民、港澳居民：男性使用A、女性使用B
		// 外國人：男性使用C、女性使用D
		// 新式居留證：男性使用8、女性使用9
        $sex = substr(strtoupper($identify), 1, 1);
		$sex = ($sex == "A" || $sex == "C" || $sex == 8) ? 1 : $sex;
		$sex = ($sex == "B" || $sex == "D" || $sex == 9) ? 2 : $sex;
		
        $params = [
            'survey_id' => $survey_id,
            'sex' => $sex,
            'created' => $created,
        ];
        if (!$model->insertAnalyzeSex($params)) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
        } else {
            $result = ["status" => 1, "msg" => ""];
        }

        return json_encode($result);
    }

    public function insertVoteAge($survey_id, $verify_reserve, $created, $type)
    {
        $model = $this->getModel();

        $age = date('Y') - ($verify_reserve[$type]["birth_year"] + 1911);

        if (date('n') == $verify_reserve[$type]["birth_month"]) {
            if (date('j') < $verify_reserve[$type]["birth_day"]) {
                $age -= 1;
            }
        } else {
            if (date('n') < $verify_reserve[$type]["birth_month"]) {
                $age -= 1;
            }
        }

        $range = substr($age, 0, 1);
        $params = [
            'survey_id' => $survey_id,
            'age' => $range,
            'created' => $created,
        ];
        if (!$model->insertAnalyzeAge($params)) {
            $result = ["status" => 0, "msg" => "無法新增資料，請稍候再試。"];
        } else {
            $result = ["status" => 1, "age" => true];
        }

        return json_encode($result);
    }

    public function blockChain($_survey_id, $_ticket_num, $_option_answers, $_config, $_created)
    {
        $params = JComponentHelper::getParams('com_surveyforce');
        $_url = trim($params->get('blockchain_onto'));

        $result = ["status" => 1, "msg" => "", "block_msg" => ""];

        $token = $_config->get('blockchain_secret');

        $questions = array_map(function ($answers) {
            $response = array_map(function ($field) {
                return ['value' => $field['logstr'], 'selected' => true];
            }, $answers);
            unset($response['sf_qtext']);

            return [
                'questionType' => 'multiple-choice',
                'question' => $answers['sf_qtext'],
                'response' => $response,
            ];
        }, $_option_answers);

        $param = [
            'title' => SurveyforceVote::getSurveyData($_survey_id, 'title'),
            'respondent' => $_ticket_num,
            'questions' => array_values($questions),
        ];

        try {
            $client = new Client([
                'defaults' => [
                    'headers' => [
                        'Authorization' => ' Bearer ' . $token,
                        'Conten-Type' => 'application/json',
                    ],
                ],
            ]);

            $today = JHtml::_('date', $_created, "Ymd");

            $log_params = [
                'stream' => $_config->get('log_path') . '/blockchain/onto/send_before/' . $_survey_id . '/'
                    . $today
                    . '.log',
                'level' => 'INFO',
                'name' => 'blockchain_onto_before',
                'message' => sprintf('%s %s', $_ticket_num, json_encode($param))
            ];
            JHtml::_('utility.Monolog', $log_params);

            $request = $client->createRequest('POST', $_url, [
                'config' => [
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                    ]
                ],
                'json' => $param]);

            $response = $client->send($request);
            $content = $response->getBody()->getContents();

            $log_params = [
                'stream' => $_config->get('log_path') . '/blockchain/onto/send_after/' . $_survey_id . '/access_'
                    . $today . '.log',
                'level' => 'INFO',
                'name' => 'blockchain_onto_after',
                'message' => sprintf('%s %s', $_ticket_num, $content)
            ];
            JHtml::_('utility.Monolog', $log_params);

            SurveyforceVote::setSurveyData($_survey_id, "blockchain_response", $content);
        } catch (Exception $e) {
            $today = JHtml::_('date', $_created, "Ymd");

            $log_params = [
                'stream' => $_config->get('log_path') . '/blockchain/onto/send_after/' . $_survey_id . '/error_'
                    . $today . '.log',
                'level' => 'ERROR',
                'name' => 'blockchain_onto_after',
                'message' => $e
            ];
            JHtml::_('utility.Monolog', $log_params);

            $result = ["status" => 0, "msg" => "目前無法連線區塊鏈服務，請稍候再試", "block_msg" => $e->getMessage()];
        }

        return json_encode($result);
    }
	
	// 檢查區塊鏈介接是否正常
    public function checkBlockChain()
    {
        $params = JComponentHelper::getParams('com_surveyforce');
        $_url = trim($params->get('blockchain_check'));
		
		$result = ["status" => 1, "msg" => "", "block_msg" => ""];
		
		$ch = curl_init($_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_CAINFO, JPATH_SITE. "/cacert.pem");
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		
		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$message = curl_error($ch);
		curl_close($ch);
		
		
		switch ($code) {
			case 200:
				break;
			default:
				$result = ["status" => 0, "msg" => "因暫時網路忙線，請您再次送出投票。如仍處忙線狀況，請稍候再試，造成您的不便，敬請見諒。", "block_msg" => sprintf("code:%s, CURL Msg: %s, Response Msg:%s", $code, $message, $response)];
				
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("checkBlockChain, code:%s Msg:%s", $code, $response) , JLog::ERROR);
				break;
		}

       
        return json_encode($result);
    }
	
	
	// 新增區塊鏈排程記錄
	function insertBlockchainToDB($survey_id, $survey_title, $ticket_num, $onto_answers, $is_blockchain, $created) {
		$db = JFactory::getDbo();

		$query   = $db->getQuery(true);
		$columns = array ('survey_id', 'survey_title', 'ticket_num', 'answer', 'is_blockchain', 'created');

		$values = array (
			$db->quote($survey_id), 
			$db->quote($survey_title), 
			$db->quote($ticket_num), 
			$db->quote($onto_answers), 
			$db->quote($is_blockchain), 
			$db->quote($created)
		);

		$query->insert($db->quoteName('#__survey_force_blockchain'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);
		$db->execute();

	}
	
	
}
