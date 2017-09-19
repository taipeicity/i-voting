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
jimport('joomla.filesystem.file');

/**
 * Question controller class.
 */
class SurveyforceControllerQuestion extends JControllerForm {

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'question', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;

	}

	public function check_question_form() {
		$model = $this->getModel();
		$config = JFactory::getConfig();
		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$survey_id = $app->input->getInt('sid', 0);
		$itemid = $app->input->getInt('Itemid', 0);
		$client_ip = JHtml::_('utility.getUserIP');



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
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);
			return;
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
		unset($msges);
		$msges = array ();

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
		unset($option_answers);
		$option_answers = SurveyforceVote::getSurveyData($survey_id, "option_answers");
		if ($option_answers == "") { // 找無資料，表示尚未記錄過
			$option_answers = array ();
		}
		$option_answers[$question_id] = $className::onSaveUserOption($question_item, $post);
		SurveyforceVote::setSurveyData($survey_id, "option_answers", $option_answers);



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
		$prac = $session->get('practice_pattern');

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
						$msg = "相同驗證資料投票中，請稍後重試。";
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



			// 送進票箱
			foreach ($verify_identify as $type => $identify) {
				$result = json_decode($this->markVote($agent_path, $survey_id, $identify, $type, $client_ip));
				if ($result->status == 0) {
					$this->setRedirect($return_link, $result->msg);
					return;
				}
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
			$result = json_decode($this->insertVoteToDB($survey_id, $ticket_num, $option_answers, $created));
			if ($result->status == 0) {
				$this->setRedirect($return_link, $result->msg);
				return;
			}


			// 將選票的內容寫入log
			$result = json_decode($this->insertVoteToLog($survey_id, $ticket_num, $option_answers, $created, $client_ip, $ivoting_save_path, SurveyforceVote::getSurveyData($survey_id, "is_public")));
			if ($result->status == 0) {
				$this->setRedirect($intro_link, $result->msg);
				return;
			}
		}

		// 設定已通過question步驟
		SurveyforceVote::setSurveyStep($survey_id, "question", true);

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
	public function checkSurvey($_survey_id) {
		unset($result);

		// 檢查投票時間
		if (!SurveyforceVote::isSurveyValid($_survey_id)) {
			$result = array ("status" => 0, "msg" => "該議題目前未在可投票時間內，請重新選擇。");
		} else {
			$result = array ("status" => 1, "msg" => "");
		}

		return json_encode($result);

	}

	// 檢查題目ID是否為屬於該議題
	public function checkQuestionInSurvey($_survey_id, $_question_id) {
		$model = $this->getModel();
		unset($result);

		$row = $model->getQuestion($_question_id);
		if ($row->sf_survey == $_survey_id) {
			$result = array ("status" => 1, "msg" => "", "question_item" => $row);
		} else {
			$result = array ("status" => 0, "msg" => "該題目並非屬於該議題之一，請重新選擇。");
		}

		return json_encode($result);

	}

	// 檢查是否已投過票
	public function checkIsVote($_agent_path, $_survey_id, $_identify, $_verify_type, $_vote_num_params, $_client_ip) {
		unset($result);

		// Agent API - 檢查是否已投票
		$api_request_url = $_agent_path . "/server_poll.php";
		$api_request_parameters = array (
			'survey_id' => $_survey_id,
			'identify' => $_identify,
			'verify_type' => $_verify_type,
			'vote_num_params' => $_vote_num_params,
			'client_ip' => $_client_ip
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
		if ($api_result == "") {
			$result = array ("status" => 0, "msg" => "無法執行步驟，請重新操作。");
		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 1) {
				$result = array ("status" => 0, "msg" => sprintf("您的資料已投過票，%s。", $decode_data->msg));
			} else if ($decode_data->status == 2) {
				$result = array ("status" => 0, "msg" => sprintf("%s，請稍候再試或選擇其他議題進行投票。", $decode_data->msg));
			} else {
				$result = array ("status" => 1, "msg" => "");
			}
		}

		return json_encode($result);

	}

	// Agent API - 寫入投票標記
	public function markVote($_agent_path, $_survey_id, $_identify, $_verify_type, $_client_ip) {
		unset($result);

		$api_request_url = $_agent_path . "/server_poll.php";
		$api_request_parameters = array (
			'survey_id' => $_survey_id,
			'identify' => $_identify,
			'verify_type' => $_verify_type,
			'client_ip' => $_client_ip
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "PUT", $api_request_parameters);
		if ($api_result == "") {
			$result = array ("status" => 0, "msg" => "無法執行步驟，請重新操作。");
		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 0) {
				$result = array ("status" => 0, "msg" => "無法新增資料，請稍後再試。");
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
			} else {
				$result = array ("status" => 1, "msg" => "");
			}
		}


		return json_encode($result);

	}

	// Agent API - 取票號
	public function getTicket($_agent_path, $_survey_id) {
		$api_request_url = $_agent_path . "/server_ticket.php";
		$api_request_parameters = array (
			'survey_id' => $_survey_id
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
		if ($api_result == "") {
			$result = array ("status" => 0, "msg" => "無法執行步驟，請重新操作。");
		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 0) {
				$result = array ("status" => 0, "msg" => "無法新增資料，請稍後再試。");
				JHtml::_('utility.recordLog', "vote_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
			} else {
				$result = array ("status" => 1, "msg" => "", "ticket_num" => $decode_data->ticket_num);
			}
		}


		return json_encode($result);

	}

	// 寫入投票內容至DB
	public function insertVoteToDB($_survey_id, $_ticket_num, $_option_answers, $_created) {
		$model = $this->getModel();

		// 寫入票號
		if (!$model->insertVote($_ticket_num, $_survey_id, $_created)) {
			$result = array ("status" => 0, "msg" => "無法新增資料，請稍後再試。");

			return json_encode($result);
		}

		// 寫入每個選項  (暫不考慮開放式欄位)
		if (count($_option_answers) > 0) {
			foreach ($_option_answers as $question_id => $options) {
				foreach ($options as $option) {
					if (!$model->insertVoteDetail($_ticket_num, $_survey_id, $question_id, $option, $_created)) {
						$result = array ("status" => 0, "msg" => "無法新增資料，請稍後再試。");

						return json_encode($result);
					}
				}
			}
		}


		// 更新該議題的總票數
		$model->updateTotalVote($_survey_id);


		$result = array ("status" => 1, "msg" => "");
		return json_encode($result);

	}

	// 寫入投票內容至Log (記錄真正的時間)
	public function insertVoteToLog($_survey_id, $_ticket_num, $_option_answers, $_created, $_client_ip, $_ivoting_save_path, $_is_public) {
		$session = &JFactory::getSession();

		$result = array ("status" => 1, "msg" => "");

		// log分日期存放
		$today = JHtml::_('date', $_created, "Ymd");
		$withIP_file = $_ivoting_save_path . "/log/withIP/" . $_survey_id . "_" . $today . ".log";
		$noIP_file = $_ivoting_save_path . "/log/noIP/" . $_survey_id . "_" . $today . ".log";


		// 寫入選項名稱 ( 不寫開放式欄位 )
		if (count($_option_answers) > 0) {
			unset($question_str);
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
		$fp = fopen($withIP_file, "a+");
		if ($fp) {
			flock($fp, LOCK_EX);
			$log_str = sprintf("%s\t%s\t%s\t%s\r\n", JHtml::_('date', $_created, "Y-m-d H:i:s"), $_ticket_num, $question_str, $_client_ip);
			fputs($fp, $log_str);
			flock($fp, LOCK_UN);
			fclose($fp);
		} else {
			$result = array ("status" => 0, "msg" => "無法新增記錄檔。");
			JHtml::_('utility.recordLog', "vote_log.php", "Can not open file:" . $withIP_file, JLog::ERROR);
		}

		// 寫入不含IP log
		$str = sprintf("%s\t%s\t%s\r\n", JHtml::_('date', $_created, "Y-m-d H:i:s"), $_ticket_num, $question_str);
		$fp = fopen($noIP_file, "a+");
		if ($fp) {
			flock($fp, LOCK_EX);
			fputs($fp, $str);
			flock($fp, LOCK_UN);
			fclose($fp);
		} else {
			$result = array ("status" => 0, "msg" => "無法新增記錄檔。");
			JHtml::_('utility.recordLog', "vote_log.php", "Can not open file:" . $noIP_file, JLog::ERROR);
		}

		return json_encode($result);

	}

}
