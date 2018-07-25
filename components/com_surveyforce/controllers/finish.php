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

jimport('joomla.application.component.controllerform');


/**
 * Finish controller class.
 */
class SurveyforceControllerFinish extends JControllerForm {
	/**
	 * Proxy for getModel.
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'finish', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	public function check_finish_form() {
		$model   = $this->getModel();
		$session = &JFactory::getSession();
		$app     = JFactory::getApplication();

		$survey_id = $app->input->getInt('sid', 0);
		$itemid    = $app->input->getInt('Itemid', 0);

		$survey_is_notice_email = SurveyforceVote::getSurveyData($survey_id, "is_notice_email");
		$survey_is_notice_phone = SurveyforceVote::getSurveyData($survey_id, "is_notice_phone");


		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);
		$intro_link    = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);


		// 檢查是否有中途更換議題
		if (SurveyforceVote::checkSurveyStep($survey_id, "finish") == false) {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);

			return;
		}


		$return_link = JRoute::_("index.php?option=com_surveyforce&view=finish&sid={$survey_id}&Itemid={$itemid}", false);
		unset($msges);
		$msges = array ();


		// 檢查欄位是否有填寫 及 是否重覆操作

		$tmp_session = [];
		// 檢查投票紀錄留存
		$save_email = "";
		if ($app->input->getInt('is_save_email', 0) == 1) {
			if (SurveyforceVote::getSurveyData($survey_id, "save_email")) {
				$msges[] = "該議題已寄送投票紀錄留存，請勿重覆選擇。";
			} else {
				$save_email                = trim($app->input->getString('save_email'));
				$tmp_session['save_email'] = $save_email;
				if ($save_email) {
					if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $save_email)) {
						$msges[] = "投票紀錄留存的電子信箱格式錯誤。";
					}
				} else {
					$msges[] = "請填寫投票紀錄留存的電子信箱。";
				}
			}

		}


		// 檢查Email通知開票結果
		$notice_email = "";
		if ($app->input->getInt('is_notice_email', 0) == 1 && $survey_is_notice_email) {
			if (SurveyforceVote::getSurveyData($survey_id, "notice_email")) {
				$msges[] = "已新增Email通知開票結果，請勿重覆選擇。";
			} else {
				if ($app->input->getInt('is_copy_email', 0) == 1) {
					$notice_email = trim($app->input->getString('save_email'));
				} else {
					$notice_email = trim($app->input->getString('notice_email'));
				}
				$tmp_session['notice_email'] = $notice_email;


				if ($notice_email) {
					if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $notice_email)) {
						$msges[] = "Email通知開票結果的電子信箱格式錯誤。";
					}
				} else {
					$msges[] = "請填寫Email通知開票結果的電子信箱。";
				}
			}
		}


		// 檢查簡訊通知開票結果
		$notice_phone = "";
		if ($app->input->getInt('is_notice_phone', 0) == 1 && $survey_is_notice_phone) {
			if (SurveyforceVote::getSurveyData($survey_id, "notice_phone")) {
				$msges[] = "已新增簡訊通知開票結果，請勿重覆選擇。";
			} else {
				$notice_phone                = trim($app->input->getString('notice_phone'));
				$tmp_session['notice_phone'] = $notice_phone;

				if ($notice_phone) {
					if (!preg_match('/^09[0-9]{8}$/', $notice_phone)) {
						$msges[] = "簡訊通知開票結果的號碼錯誤。";
					}
				} else {
					$msges[] = "請填寫簡訊通知開票結果的手機號碼。";
				}
			}

		}

		if (count($msges) > 0) {
			SurveyforceVote::setSurveyStep($survey_id, "check_column");
			$session->set("tmp_session", json_encode($tmp_session));
			$this->setRedirect($return_link, implode("<br>", $msges));

			return;
		}


		// 取得議題基本資料
		$survey_item_title = SurveyforceVote::getSurveyData($survey_id, "title");

		// 投票紀錄留存
		if ($app->input->getInt('is_save_email', 0) == 1) {
			// 取得短網址
			$short_url = SurveyforceVote::getSurveyData($survey_id, "short_url");

			$encode_email = JHtml::_('utility.endcode', $save_email);

			// 寄送短網址和票號
			$ticket_num = SurveyforceVote::getSurveyData($survey_id, "ticket");
			$result     = json_decode($this->sendLinkEmail($short_url, $ticket_num, $save_email, $encode_email, $survey_item_title));
			if ($result->status == 0) {
				$this->setRedirect($return_link, $result->msg);

				return;
			} else {
				// 註記已寄送
				SurveyforceVote::setSurveyData($survey_id, "save_email", $save_email);
			}

		}


		// Email通知開票結果
		if ($app->input->getInt('is_notice_email', 0) == 1 && $survey_is_notice_email) {
			$encode_email = JHtml::_('utility.endcode', $notice_email);
			$model->insertNoticeEmail($survey_id, $encode_email, 3);

			// 註記已寄送
			SurveyforceVote::setSurveyData($survey_id, "notice_email", $notice_email);
		}

		// 簡訊通知開票結果
		if ($app->input->getInt('is_notice_phone', 0) == 1 && $survey_is_notice_phone) {
			$encode_phone = JHtml::_('utility.endcode', $notice_phone);
			$model->insertNoticePhone($survey_id, $encode_phone, 3);

			// 註記已寄送
			SurveyforceVote::setSurveyData($survey_id, "notice_phone", $encode_phone);
		}

		// 通過留存頁面
		SurveyforceVote::setSurveyStep($survey_id, "success");

		$link = JRoute::_("index.php?option=com_surveyforce&view=finish&layout=success&sid={$survey_id}&Itemid={$itemid}", false);

		$this->setRedirect($link);

	}


	public function sendLinkEmail($_short_url, $_ticket_num, $_email, $_encode_email, $_survey_title) {
		unset($result);

		$app       = JFactory::getApplication();
		$survey_id = $app->input->getInt('sid', 0);

		$component_params   = JComponentHelper::getParams('com_surveyforce');
		$save_email_content = $component_params->get('save_email_content');


		$config     = JFactory::getConfig();
		$sitename   = $config->get('sitename');
		$from_email = $config->get('mailfrom');
		$from_name  = $config->get('fromname');

		$subject            = "i-Voting完成投票通知";
		$save_email_content = str_replace("%title%", $_survey_title, $save_email_content);        // 取代議題名稱
		$save_email_content = str_replace("%ticket%", $_ticket_num, $save_email_content);        // 取代票號
		$save_email_content = str_replace("%url%", '<a href="' . $_short_url . '" target="_blank">' . $_short_url . '</a>', $save_email_content);  // 取代結果頁連結

		// 依照是否有抽獎及是否送過抽獎資料判斷替換的文字
		if (SurveyforceVote::getSurveyData($survey_id, "is_lottery")) {
			if (SurveyforceVote::checkSurveyStep($survey_id, "join_lottery") == true) {
				$save_email_content = str_replace("%lottery%", $component_params->get("participant"), $save_email_content);  // 取代已參加提示
			} else {
				$resend_short_url   = SurveyforceVote::getSurveyData($survey_id, "resend_short_url");
				$save_email_content = str_replace("%lottery%", $component_params->get("non_participant"), $save_email_content);  // 取代未參加提示
				$save_email_content = str_replace("%resend%", $resend_short_url, $save_email_content);  // 取代未參加提示
			}
		} else {
			$save_email_content = str_replace("%lottery%", "", $save_email_content);  // 無抽獎活動即無提示
		}

		$alert_msg = nl2br($save_email_content);


		$send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $_email, $subject, $alert_msg, 1);


		if (is_object($send_email_status)) {
			$send_email_status = 0;
			JHtml::_('utility.recordLog', "debug_log.php", "投票紀錄無法發送", JLog::ERROR);

			$result = array ("status" => 0, "msg" => "目前目前寄送投票紀錄，請稍後將由系統重新寄發。");
		} else {
			$result = array ("status" => 1, "msg" => "");
		}

		JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $_encode_email, $subject, $alert_msg, 1);


		return json_encode($result);
	}


	// 儲存抽獎資料
	function insert_lottery($_agent_path, $_survey_id, $_lottery_name, $_lottery_phone, $_ticket_num) {
		unset($result);

		$api_request_url        = $_agent_path . "/server_lottery.php";
		$api_request_parameters = array (
			'survey_id'     => $_survey_id, 'lottery_name' => JHtml::_('utility.endcode', $_lottery_name),
			'lottery_phone' => JHtml::_('utility.endcode', $_lottery_phone)
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "PUT", $api_request_parameters);
		if ($api_result == "") {
			$result = array ("status" => 0, "msg" => "無法執行新增抽獎資料步驟，請重新操作。");

		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 0) {
				$result = array ("status" => 0, "msg" => "無法新增資料，請稍後再試。");
				JHtml::_('utility.recordLog', "lottery_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $decode_data->msg), JLog::ERROR);
			} else {
				$result = array ("status" => 1, "msg" => "");

				$model = $this->getModel();
				if (!$model->is_lottery($_ticket_num, $_survey_id)) {
					$lottery_log["msg"] = "無法新增抽獎紀錄";
					JHtml::_('utility.recordLog', "lottery_log.php", sprintf("sid:%d, Msg:%s", $_survey_id, $lottery_log["msg"]), JLog::ERROR);
				}
			}

		}


		return json_encode($result);
	}

	function setLotteryStep() {

		$app = JFactory::getApplication();

		$survey_id  = $app->input->getInt('sid', 0);
		$itemid     = $app->input->getInt('Itemid', 0);
		$ticket_num = $app->input->getString('ticket_num');

		$survey_is_lottery = SurveyforceVote::getSurveyData($survey_id, "is_lottery");

		$intro_link  = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=finish&sid={$survey_id}&Itemid={$itemid}", false);

		// 檢查是否有中途更換議題
		if (SurveyforceVote::checkSurveyStep($survey_id, "finish") == false) {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);

			return;
		}


		// 檢查參加抽獎活動
		if ($app->input->getInt('is_join_lottery', 0) == 1 && $survey_is_lottery) {

			if (SurveyforceVote::getSurveyData($survey_id, "join_lottery")) {
				$msg = "已參加抽獎活動，請勿重覆選擇。";
				$this->setRedirect($return_link, $msg);

				return;
			} else {
				$lottery_name  = trim($app->input->getString('lottery_name'));
				$lottery_phone = trim($app->input->getString('lottery_phone'));

				if ($lottery_name == "" || $lottery_phone == "") {

					$tmp_session['lottery_name']  = $lottery_name;
					$tmp_session['lottery_phone'] = $lottery_phone;

					$session = &JFactory::getSession();
					$session->set('tmp_session', json_encode($tmp_session));

					$msg = "請填寫參加抽獎活動資料。";
					$this->setRedirect($return_link, $msg);

					return;
				} else {
					$agent_path = JFactory::getConfig()->get('agent_path');

					if(!preg_match("/^09\d{8}/", $lottery_phone)){
						$msg = "手機號碼格式錯誤。";
						$this->setRedirect($return_link, $msg);
						return;
					}


					$result = json_decode($this->insert_lottery($agent_path, $survey_id, $lottery_name, $lottery_phone, $ticket_num));
					if ($result->status == 0) {
						$this->setRedirect($return_link, $result->msg);

						return;
					} else {
						// 註記已參加
						SurveyforceVote::setSurveyData($survey_id, "join_lottery", $lottery_name);
						// 設定已參加抽獎
						SurveyforceVote::setSurveyStep($survey_id, "join_lottery");
					}
				}

			}

		}

		SurveyforceVote::setSurveyStep($survey_id, "lottery");

		$this->setRedirect($return_link);

		return;
	}

	function resetLotteryStep() {

		$app = JFactory::getApplication();

		$survey_id = $app->input->getInt('sid', 0);
		$itemid    = $app->input->getInt('Itemid', 0);

		$intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);

		// 檢查是否有中途更換議題
		if (SurveyforceVote::checkSurveyStep($survey_id, "finish") == false) {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);

			return;
		}

		// 設定已通過question步驟
		SurveyforceVote::setSurveyStep($survey_id, "question", true);
		if (SurveyforceVote::getSurveyItem($survey_id)->is_public == 0) {
			SurveyforceVote::setSurveyStep($survey_id, "token");
		}

		$link = JRoute::_("index.php?option=com_surveyforce&view=finish&sid={$survey_id}&Itemid={$itemid}", false);
		$this->setRedirect($link);

		return;
	}

	public function check_resend_form() { // 補送抽獎資料

		$app        = JFactory::getApplication();
		$agent_path = JFactory::getConfig()->get('agent_path');

		$survey_id  = $app->input->getInt('sid', 0);
		$itemid     = $app->input->getInt('Itemid', 0);
		$ticket_num = $app->input->getString('ticket_num');
		$survs      = SurveyforceVote::getSurveyItem($survey_id);

		$return_link   = JRoute::_("index.php?option=com_surveyforce&view=finish&layout=resend&sid={$survey_id}&Itemid={$itemid}", false);
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);

		if (SurveyforceVote::isSurveyExpired($survey_id) == false) {
			$msg = "網頁已閒置過久，請重新點選議題進行投票。";
			$app->redirect($category_link, $msg);
		}

		// 檢查參加抽獎活動
		if ($app->input->getInt('is_join_lottery', 0) == 1 && $survs->is_lottery == 1) {

			if (SurveyforceVote::checkJoinLottery($ticket_num, $survey_id)) {
				$msg = "已補送抽獎活動資料，請勿重覆選擇。";
				$this->setRedirect($return_link, $msg);

				return;
			} else {
				$lottery_name  = trim($app->input->getString('lottery_name'));
				$lottery_phone = trim($app->input->getString('lottery_phone'));

				if ($lottery_name == "" || $lottery_phone == "") {

					$tmp_session['lottery_name']  = $lottery_name;
					$tmp_session['lottery_phone'] = $lottery_phone;

					$session = &JFactory::getSession();
					$session->set('tmp_session', json_encode($tmp_session));

					$msg = "請填寫參加抽獎活動的姓名及電話。";
					$this->setRedirect($return_link, $msg);

					return;
				} else {
					$result = json_decode($this->insert_lottery($agent_path, $survey_id, $lottery_name, $lottery_phone, $ticket_num));
					if ($result->status == 0) {
						$this->setRedirect($return_link, $result->msg);

						return;
					} else {
						// 註記已補送資料
						SurveyforceVote::setSurveyData($survey_id, "resend_lottery", $ticket_num, true);
					}
				}

			}

		}

		// 通過補送頁面
		SurveyforceVote::setSurveyStep($survey_id, "success");
		if (SurveyforceVote::getSurveyItem($survey_id)->is_public == 0) {
			SurveyforceVote::setSurveyStep($survey_id, "token");
		}

		$link = JRoute::_("index.php?option=com_surveyforce&view=finish&layout=success&sid={$survey_id}&Itemid={$itemid}", false);

		$this->setRedirect($link);

	}

}