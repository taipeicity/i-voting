<?php
/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');


/**
 * Place_question controller class.
 */
class SurveyforceControllerPlace_question extends JControllerForm {
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'place_question', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function check_question_form() {
		$model = $this->getModel();
		$session 	= &JFactory::getSession();
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$survey_id = $session->get('place_survey_id');
		$itemid	= $app->input->getInt('Itemid', 0);
		$question_id	= $app->input->getInt('qid', 0);
		$client_ip = JHtml::_('utility.getUserIP');

//		JRequest::checkToken() or die( 'Invalid Token' );

		$login_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$itemid}", false);
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=place_category&Itemid={$this->itemid}", false);
		$verify_link = JRoute::_("index.php?option=com_surveyforce&view=place_verif&sid={$survey_id}&Itemid={$itemid}", false);


		// 檢查是否有登入
		if (!$session->get('place_username')) {
			$msg = "您尚未登入，請重新登入。";
			$this->setRedirect($login_link, $msg);
			return;
		}


		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == false) {
			$msg = "該議題已結束投票時間。";
			$this->setRedirect($category_link, $msg);
			return;
		}

		// 檢查題目是否是議題其中之一
		$question_id = $app->input->getInt('qid', 0);
		$result = json_decode( $this->checkQuestionInSurvey($survey_id, $question_id) );
		if ($result->status == 0) {

			$this->setRedirect($category_link, $result->msg);
			return;
		} else {
			$question_item = $result->question_item;		// 回傳題目內容
		}


		// 載入plugin的function來檢查
		JPluginHelper::importPlugin('survey', $question_item->question_type);
		$className = 'plgSurvey' . ucfirst($question_item->question_type);

		// 檢查選項是否有填寫及是否是題目其中之一
		$post = $app->input->getArray($_POST);
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=place_question&sid={$survey_id}&qid={$question_id}&Itemid={$itemid}", false);
		unset($msges);
		$msges = array();

		if (method_exists($className, 'onCheckOptionField')) {
			$result = json_decode( $className::onCheckOptionField($question_item, $post) );	// 檢查欄位是否有填寫

			if ($result->status == 0) {
				$msges[] = $result->msg;
			}

		}


		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}

		// 記錄答案 (一樣依題目ID做記錄)
		unset($option_answers);
		$option_answers = SurveyforceVote::getSurveyData($survey_id, "option_answers");
		if ($option_answers == "") {	// 找無資料，表示尚未記錄過
			$option_answers = array();
		}
		$option_answers[$question_id] = $className::onSaveUserOption($question_item, $post);
		SurveyforceVote::setSurveyData($survey_id, "option_answers", $option_answers );


		// 檢查所有題目是否都已做過，若尚未，則轉入該題目。
		$questions = $model->getQuestions($survey_id);
		foreach ($questions as $question) {
			if ( !array_key_exists($question->id, $option_answers) ) {
				$next_question_link = JRoute::_("index.php?option=com_surveyforce&view=place_question&sid={$survey_id}&qid={$question->id}&Itemid={$itemid}", false);
				$this->setRedirect($next_question_link);
				return;
			}
		}

		
		// 送入票箱前的檢查
		// 檢查議題是否到期
		$result = json_decode($this->checkSurvey($survey_id));
		if (!$result->status) {
			$this->setRedirect($category_link, $result->msg);
			return;
		}

		// 先鎖住程式執行
		$ivoting_save_path = $config->get( 'ivoting_save_path' );
		if ($model->insertVoteLock($survey_id, $session->get('place_verify_idnum'), "idnum")) {
			
			// 檢查是否已驗證通過
			if ($session->get('place_verify') == false || $session->get('place_verify_idnum') == false) {
				$msg = "您尚未通過身分證驗證，請重新驗證。";
				$this->setRedirect($verify_link, $msg);
				return;
			}


			// 檢查是否已投過票
			$agent_path = $config->get( 'agent_path' );
			$result = json_decode($this->checkIsVote($agent_path, $survey_id, $session->get('place_verify_idnum'), "idnum"));
			if ($result->status == 1) {
				$this->setRedirect($verify_link, $result->msg);
				return;
			}

			// 送進票箱
			$ivoting_path = $config->get( 'ivoting_path' );
			$result = json_decode($this->markVote($agent_path, $survey_id, $session->get('place_verify_idnum'), "idnum", $client_ip));
			if ($result->status == 0) {
				$this->setRedirect($return_link, $result->msg);
				return;
			}


			// 取票號
			$result = json_decode($this->getTicket($agent_path, $survey_id));
			if ($result->status == 0) {
				$this->setRedirect($return_link, $result->msg);
				return;
			} else {
				$ticket_num = $result->ticket_num;
			}


			// 將選票的內容寫入DB
			$created = JFactory::getDate()->toSql();
			$result = json_decode( $this->insertVoteToDB ( $survey_id, $ticket_num, $option_answers, $created) );
			if ($result->status == 0) {
				$this->setRedirect($return_link, $result->msg);
				return;
			}


			// 將選票的內容寫入log (若為測試站台，則不需)
			if ( $config->get( 'is_testsite', false ) == false ) {
				$result = json_decode( $this->insertVoteToLog($survey_id, $ticket_num, $option_answers, $created, $client_ip, $ivoting_save_path, 1 ) );
				if ($result->status == 0) {
					$this->setRedirect($return_link, $result->msg);
					return;
				}


			}


			// 刪除投票Lock
			$model->deleteVoteLock($survey_id, $session->get('place_verify_idnum'), "idnum");


			// 清空session
			$session->clear("place_verify");
			$session->clear("place_verify_idnum");

			unset($option_answers);
			SurveyforceVote::setSurveyData($survey_id, "option_answers", $option_answers );


		} else {
			$msg = "相同驗證資料投票中，請稍後重試。";
			$this->setRedirect($return_link, $msg);
			return;
		}


		$link = JRoute::_("index.php?option=com_surveyforce&view=place_finish&sid={$survey_id}&Itemid={$itemid}", false);

		$this->setRedirect($link);
		return;
	}


	// 檢查議題是否在投票時間內
	public function checkSurvey($_survey_id) {
		unset($result);

		// 檢查投票時間
		if (!SurveyforceVote::isSurveyValid( $_survey_id )) {
			$result = array("status" => 0, "msg" => "該議題目前未在可投票時間內，請重新選擇。");
		} else {
			$result = array("status" => 1, "msg" => "");
		}

		return json_encode($result);

	}


	// 檢查題目ID是否為屬於該議題
	public function checkQuestionInSurvey($_survey_id, $_question_id) {
		$model = $this->getModel();
		unset($result);

		$row = $model->getQuestion($_question_id);
		if ($row->sf_survey == $_survey_id) {
			$result = array("status" => 1, "msg" => "", "question_item" => $row);
		} else {
			$result = array("status" => 0, "msg" => "該題目並非屬於該議題之一，請重新選擇。");
		}

		return json_encode($result);

	}


	// 檢查選項是否有填寫，及選項ID是否為屬於該題目
	public function checkOptionInQuestion($_question_id, $_option_id) {
		$model = $this->getModel();
		unset($result);

		$result = array("status" => 0, "msg" => "該選項並非屬於該題目之一，請重新選擇。");

		if ($_option_id) {
			$rows = $model->getOptions($_question_id);
			foreach ($rows as $row) {
				if ($row->id == $_option_id) {
					$result["status"] = 1;
					$result["msg"] = "";

					break;
				}
			}
		} else {
			$result["msg"] = "請選擇其中一項";
		}



		return json_encode($result);

	}



	// 檢查是否已投過票
	public function checkIsVote($_agent_path, $_survey_id, $_identify, $_verify_type) {
		unset($result);

		// Agent API - 檢查是否已投票
		$api_request_url = $_agent_path. "/server_poll.php";
		$api_request_parameters = array(
			'survey_id' => $_survey_id,
			'identify' => $_identify,
			'verify_type' => $_verify_type,
			'vote_num_params' => '{"vote_num_type":0,"vote_day":"0","vote_num":"0"}'
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
		if ( $api_result == "") {
			$result = array("status" => 0, "msg" => "無法執行步驟，請重新操作。");
		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 1) {
				$result = array("status" => 1, "msg" => "該資料已投過票，請重新輸入。");
			} else {
				$result = array("status" => 0, "msg" => "");
			}
		}

		return json_encode($result);
	}


	// Agent API - 寫入投票標記
	public function markVote($_agent_path, $_survey_id, $_identify, $_verify_type, $_client_ip) {
		$api_request_url = $_agent_path. "/server_poll.php";
		$api_request_parameters = array(
			'survey_id' => $_survey_id,
			'identify' => $_identify,
			'verify_type' => $_verify_type,
			'client_ip' => $_client_ip
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "PUT", $api_request_parameters);
		if ( $api_result == "") {
			$result = array("status" => 0, "msg" => "無法執行步驟，請重新操作。");

		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 0) {
				$result = array("status" => 0, "msg" => "無法新增資料，請稍後再試。");
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);

			} else {
				$result = array("status" => 1, "msg" => "");
			}

		}


		return json_encode($result);

	}


	// Agent API - 取票號
	public function getTicket($_agent_path, $_survey_id) {
		$api_request_url = $_agent_path. "/server_ticket.php";
		$api_request_parameters = array(
			'survey_id' => $_survey_id
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
		if ( $api_result == "") {
			$result = array("status" => 0, "msg" => "無法執行步驟，請重新操作。");

		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 0) {
				$result = array("status" => 0, "msg" => "無法新增資料，請稍後再試。");
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
			} else {
				$result = array("status" => 1, "msg" => "", "ticket_num" =>  $decode_data->ticket_num);
			}

		}


		return json_encode($result);
	}


	// 寫入投票內容至DB
	public function insertVoteToDB ( $_survey_id, $_ticket_num, $_option_answers, $_created) {
		$model = $this->getModel();

		// 寫入票號
		if ( !$model->insertVote($_ticket_num, $_survey_id, $_created) ) {
			$result = array("status" => 0, "msg" => "無法新增資料，請稍後再試。");

			return json_encode($result);
		}

		// 寫入每個選項  (暫不考慮開放式欄位)
		if (count($_option_answers) > 0) {
			foreach ($_option_answers as $question_id => $options) {
				foreach ($options as $option) {
					if ( !$model->insertVoteDetail($_ticket_num, $_survey_id, $question_id, $option, $_created) ) {
						$result = array("status" => 0, "msg" => "無法新增資料，請稍後再試。");

						return json_encode($result);
					}
				}
			}
		}


		// 更新該議題的總票數
		$model->updateTotalVote($_survey_id);


		$result = array("status" => 1, "msg" => "");
		return json_encode($result);

	}


	// 寫入投票內容至Log
	public function insertVoteToLog($_survey_id, $_ticket_num, $_option_answers, $_created, $_client_ip, $_ivoting_save_path, $_is_public) {
		$session 	= &JFactory::getSession();

		$result = array("status" => 1, "msg" => "");

		// log分日期存放
		$today = JHtml::_('date', $_created, "Ymd");
		$withIP_file = $_ivoting_save_path. "/log/withIP/". $_survey_id. "_". $today. ".log";
		$noIP_file = $_ivoting_save_path. "/log/noIP/". $_survey_id. "_". $today. ".log";


		// 寫入選項名稱 ( 不寫開放式欄位 )
		if (count($_option_answers) > 0) {
			unset($question_str);
			$qcount = 1;
			foreach ($_option_answers as $question_id => $options) {
				$question_str .= "Q". $qcount. ":";
				foreach ($options as $option) {
					$question_str .= $option["logstr"]. ",";
				}
				$question_str .= ";";
				$qcount++;
			}
		}


		// 寫入含IP log
		$fp = fopen ($withIP_file, "a+");
		if ($fp) {
			flock($fp, LOCK_EX);
			$log_str = sprintf("%s\t%s\t%s\t%s\r\n", JHtml::_('date', $_created, "Y-m-d H:i:s"), $_ticket_num, $question_str, $_client_ip);
			fputs($fp, $log_str);
			flock($fp, LOCK_UN);
			fclose ($fp);
		} else {
			$result = array("status" => 0, "msg" => "無法新增記錄檔。");
			JHtml::_('utility.recordLog', "vote_log.php", "Can not open file:". $withIP_file, JLog::ERROR);
		}

		// 寫入不含IP log
		$str = sprintf("%s\t%s\t%s\r\n", JHtml::_('date', $_created, "Y-m-d H:i:s"), $_ticket_num, $question_str);
		$fp = fopen ($noIP_file, "a+");
		if ($fp) {
			flock($fp, LOCK_EX);
			fputs($fp, $str);
			flock($fp, LOCK_UN);
			fclose ($fp);
		} else {
			$result = array("status" => 0, "msg" => "無法新增記錄檔。");
			JHtml::_('utility.recordLog', "vote_log.php", "Can not open file:". $noIP_file, JLog::ERROR);
		}

		return json_encode($result);
	}



}