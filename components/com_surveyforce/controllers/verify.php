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
 * Verify controller class.
 */
class SurveyforceControllerVerify extends JControllerForm {

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
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





		// 檢查欄位是否已填寫
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid={$survey_id}&Itemid={$itemid}", false);
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
				SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", 0);
				SurveyforceVote::setSurveyData($survey_id, "verify_failure_time", 0);
			}
		}

		// 取出該議題的驗證方式
		$verify_required = SurveyforceVote::getSurveyData($survey_id, "verify_required");
		$survey_verify_types = SurveyforceVote::getSurveyData($survey_id, "verify_type");
		$verify_params = SurveyforceVote::getSurveyData($survey_id, "verify_params");
		$vote_num_params = SurveyforceVote::getSurveyData($survey_id, "vote_num_params");
		$post = $app->input->getArray($_POST);

		// 檢查所選的驗證方式是否有被修改
		if ($survey_verify_types) {
			$verify_types = json_decode($survey_verify_types, true);  // 取得該議題所有驗證方式

			unset($select_verify_types);
			if ($verify_required) { // 同時驗證
				$select_verify_types = $verify_types;
			} else { // 擇一驗證
				$type = $app->input->getString('verify_type');
				if (in_array($type, $verify_types)) {
					$select_verify_types = array ($type);
				} else {
					$msges[] = "該驗證方式並不存在，請重新選擇。";
				}
			}

			// 將所有選擇的驗證方式進行欄位檢查
			if ($select_verify_types) {
				foreach ($select_verify_types as $type) {
					JPluginHelper::importPlugin('verify', $type);
					$className = 'plgVerify' . ucfirst($type);

					$result = json_decode($className::onCheckField($post, $verify_params, $survey_id)); // 檢查欄位是否有填寫
					if ($result->status == 0) {
						$msges[] = $result->msg;
					}
				}

				// 記錄所有驗證選擇的方式
				SurveyforceVote::setSurveyData($survey_id, "select_verify_types", $select_verify_types);
			}
		} else {
			$this->setRedirect($intro_link, "驗證方式失效，請重新操作。");
			return;
		}


		// 檢查驗證碼
		$captcha = $app->input->getString('recaptcha_response_field2');
		if ($captcha) {
			// 與session中的值做比對
			if ($session->get('captcha_' . $survey_id) == md5($captcha)) {
				// 比對正確則清空session
				$session->Set('captcha_' . $survey_id, "");
			} else {
				$msges[] = "驗證碼比對錯誤，請重新填寫驗證。";
			}
		} else {
			$msges[] = "請填寫驗證碼。";
		}


		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}




		// 將所有選擇的驗證方式進行資料驗證 及 是否有投過票
		$agent_path = $config->get('agent_path');
		unset($verify_identify);
		$verify_identify = array ();

		if ($select_verify_types) {
			unset($verify2nd);
			$verify2nd = array ();

			foreach ($select_verify_types as $type) {
				$className = 'plgVerify' . ucfirst($type);
				$verify_name = $className::onGetVerifyName(); // 取得驗證名稱
				// 先取得該驗證方式是 先判斷是否投票再資料驗證?  或是 先資料驗證再判斷是否投票?
				unset($identify);
				$identify = $className::onGetVerifyIdentify($survey_id, $post, $verify_params);

				if ($identify) { //  若有回傳識別碼，則為先判斷是否投票再驗證資料
					// 先判斷是否有無投過票
					$result_check_poll = json_decode($this->check_poll($agent_path, $survey_id, $identify, $type, $verify_name, $vote_num_params, $client_ip));

					if ($result_check_poll->status == 1) { // 未投過票
						// 再驗證資料是否正確
						$result_check_data = json_decode($className::onVerifyData($survey_id, $post, $verify_params));
						if ($result_check_data->status == 0) {  // 驗證失敗
							$msges[] = $result_check_data->msg;

							// 記錄驗證結果至DB
							if (!$prac) { //練習區不用紀錄驗證結果至DB
								$model->recordVerifyStatus($survey_id, $type, $result_check_data->status, $client_ip);
							}
							// 記錄失敗次數
							$verify_failure_num = SurveyforceVote::getSurveyData($survey_id, "verify_failure_num") + 1;
							SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", $verify_failure_num);
						} else if ($result_check_data->status == 1) {  // 驗證成功
							$verify_identify[$type] = $identify; // 確認無投過票，記錄識別碼
							// 記錄驗證結果至DB
							if (!$prac) { //練習區不用紀錄驗證結果至DB
								$model->recordVerifyStatus($survey_id, $type, $result_check_data->status, $client_ip);
							}
						} else if ($result_check_data->status == 2) {  // 需進入第2驗證頁
							$verify2nd[] = $type;
						}
					} else { // 已投過票
						$msges[] = $result_check_poll->msg;
					}
				} else {  // 若沒有回傳，則為先資料驗證再判斷是否投票
					// 驗證資料是否正確
					$result_check_data = json_decode($className::onVerifyData($survey_id, $post, $verify_params));
					if ($result_check_data->status == 0) {  // 驗證失敗
						$msges[] = $result_check_data->msg;

						// 記錄驗證結果至DB
						if (!$prac) { //練習區不用紀錄結果至DB
							$model->recordVerifyStatus($survey_id, $type, $result_check_data->status, $client_ip);
						}
						// 記錄失敗次數
						$verify_failure_num = SurveyforceVote::getSurveyData($survey_id, "verify_failure_num") + 1;
						SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", $verify_failure_num);
					} else if ($result_check_data->status == 1) {  // 驗證成功
						// 判斷是否有無投過票
						$verify_name = $className::onGetVerifyName(); // 取得驗證名稱

						$result_check_poll = json_decode($this->check_poll($agent_path, $survey_id, $result_check_data->identify, $type, $verify_name, $vote_num_params, $client_ip));

						if ($result_check_poll->status == 1) {
							$verify_identify[$type] = $result_check_data->identify; // 確認無投過票，記錄識別碼
						} else {
							$msges[] = $result_check_poll->msg;
						}
						// 記錄驗證結果至DB
						if (!$prac) { //練習區不用紀錄結果至DB
							$model->recordVerifyStatus($survey_id, $type, $result_check_data->status, $client_ip);
						}
					} else if ($result_check_data->status == 2) {  // 需進入第2驗證頁
						$verify2nd[] = $type;
					}
				}
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

		// 記錄第1頁所有驗證方式的identify
		SurveyforceVote::setSurveyData($survey_id, "verify_identify", $verify_identify);



		// 進入第2驗證頁
		if (count($verify2nd) > 0) {
			// 設定第2頁驗證方式
			SurveyforceVote::setSurveyData($survey_id, "verify2nd_type", json_encode($verify2nd));

			$link = JRoute::_("index.php?option=com_surveyforce&view=verify2nd&sid={$survey_id}&Itemid={$itemid}", false);
			$this->setRedirect($link);
			return;
		} else { // 單一頁驗證，通過後轉至個資
			// 設定已通過verify步驟
			SurveyforceVote::setSurveyStep($survey_id, "verify");
			SurveyforceVote::setSurveyData($survey_id, "verify_failure_num", 0);
			if (!$prac) {
				$session->set("survey_id", $survey_id);
			}

			$link = JRoute::_("index.php?option=com_surveyforce&view=question&sid={$survey_id}&Itemid={$itemid}", false);
			$this->setRedirect($link);
			return;
		}

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
