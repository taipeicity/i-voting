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
 * Verify2nd controller class.
 */
class SurveyforceControllerVerify2nd extends JControllerForm {

	public function getModel($name = 'verify', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;

	}

	// 檢查表單欄位
	public function check_verify_form() {
		$config = JFactory::getConfig();
		$session = &JFactory::getSession();
		$prac = $session->get('practice_pattern');
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$model = $this->getModel();


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
		if (SurveyforceVote::checkSurveyStep($survey_id, "verify") == false) {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);
			return;
		}


		// 取出第2驗證頁的驗證方式
		$verify2nd_type = SurveyforceVote::getSurveyData($survey_id, "verify2nd_type");
		$vote_num_params = SurveyforceVote::getSurveyData($survey_id, "vote_num_params");
		if ($verify2nd_type == "") {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}



		// 檢查欄位是否已填寫
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=verify2nd&sid={$survey_id}&Itemid={$itemid}", false);
		unset($msges);
		$msges = array ();


		// 失敗超過3次，則停止10秒
		if (SurveyforceVote::getSurveyData($survey_id, "verify_failure_num") >= 3) {
			if (SurveyforceVote::getSurveyData($survey_id, "verify_failure_time") == 0) {
				SurveyforceVote::setSurveyData($survey_id, "verify_failure_time", time());
			}

			if (SurveyforceVote::getSurveyData($survey_id, "verify_failure_time") > (time() - 10)) {
				$this->setRedirect($return_link, "驗證失敗次數過多，請稍候再試。");
				return;
			} else {
				SurveyforceVote::setSurveyData($survey_id, "verify_failure_time", 0);
			}
		}


		$post = $app->input->getArray($_POST);
		$verify2nd_types = json_decode($verify2nd_type, true);

		foreach ($verify2nd_types as $type) {
			JPluginHelper::importPlugin('verify', $type);
			$className = 'plgVerify' . ucfirst($type);

			$result = json_decode($className::onCheckField2nd($post));
			if ($result->status == 0) {
				$msges[] = $result->msg;
			}
		}



		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}




		// 驗證資料是否正確
		$agent_path = $config->get('agent_path');

		foreach ($verify2nd_types as $type) {
			$className = 'plgVerify' . ucfirst($type);
			$verify_name = $className::onGetVerifyName(); // 取得驗證名稱

			$result = json_decode($className::onVerifyData2nd($survey_id, $post));
			if ($result->status == 0) {  // 驗證失敗
				$msges[] = $result->msg;

				// 記錄失敗次數
				$verify_failure_num = SurveyforceVote::getSurveyData($survey_id, "verify_failure_num") + 1;
				SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", $verify_failure_num);
			} else {

				// 再判斷是否有無投過票
				if ($result->identify) {

					$result_check = json_decode($this->check_poll($agent_path, $survey_id, $result->identify, $type, $verify_name, $vote_num_params, $client_ip));

					if ($result_check->status == 1) {
						$verify_identify[$type] = $result->identify; // 確認無投過票，記錄識別碼
					} else {
						$msges[] = $result_check->msg;
					}
				}
			}

			// 記錄驗證結果至DB
			if (!$prac) {
				$model->recordVerifyStatus($survey_id, $type, $result->status, $client_ip);
			}
		}


		if ($verify_failure_num >= 3) {
			SurveyforceVote::setSurveyData($survey_id, "verify_failure_time", time());
			$this->setRedirect($return_link, "資料驗證失敗，由於驗證失敗次數過多，請稍候再試。");
			return;
		}





		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}




		// 記錄所有驗證方式的identify
		$verify_identify_1st = SurveyforceVote::getSurveyData($survey_id, "verify_identify");
		if (is_array(verify_identify_1st)) { // 將第一階段有通過驗證的都納入
			$verify_identify = array_merge($verify_identify_1st, $verify_identify);
		}
		SurveyforceVote::setSurveyData($survey_id, "verify_identify", $verify_identify);




		// 設定已通過verify步驟
		SurveyforceVote::setSurveyStep($survey_id, "verify");
		SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", 0);
		if (!$prac) {
			$session->set("survey_id", $survey_id);
		}

		$link = JRoute::_("index.php?option=com_surveyforce&view=question&sid={$survey_id}&Itemid={$itemid}", false);
		$this->setRedirect($link);

	}

	// 判斷是否有無投過票
	public function check_poll($_agent_path, $_survey_id, $_identify, $_verify_type, $_verify_type_name, $_vote_num_params, $_client_ip) {
		unset($result);

		$session = &JFactory::getSession();
		$prac = $session->get('practice_pattern');

		$api_request_url = $_agent_path . "/server_poll.php";
		unset($api_request_parameters);
		$api_request_parameters = array (
			'survey_id' => $_survey_id,
			'identify' => $_identify,
			'verify_type' => $_verify_type,
			'vote_num_params' => $_vote_num_params,
			'client_ip' => $_client_ip
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);

		if ($api_result == "") {
			$result = array ("status" => 0, "msg" => "無法檢查是否已投票，請重新操作。");
		} else {
			$decode_data = json_decode($api_result);

			if ($prac) {
				$result = array ("status" => 1, "msg" => "");
			} else {
				if ($decode_data->status == 1) {
					$result = array ("status" => 0, "msg" => sprintf("%s的資料已投過票，%s，請選擇其他驗證方式或選擇其他議題進行投票。", $_verify_type_name, $decode_data->msg));
				} else if ($decode_data->status == 2) {
					$result = array ("status" => 0, "msg" => sprintf("%s，請稍候再試或選擇其他議題進行投票。", $decode_data->msg));
				} else {
					$result = array ("status" => 1, "msg" => "");
				}
			}
		}

		return json_encode($result);

	}

}
