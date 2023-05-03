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
 * Intro controller class.
 */
class SurveyforceControllerIntro extends JControllerForm {

	/**
	 * Proxy for getModel.
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'intro', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);

		return $model;

	}

	public function start_vote() {
		$config  = JFactory::getConfig();
		$app     = JFactory::getApplication();
		$session = &JFactory::getSession();
		$params = $app->getParams();
		$cat       = $params->get('cat');

		$survey_id = $app->input->getInt('sid', 0);
		$itemid    = $app->input->getInt('Itemid', 0);


		$expire_minute = $config->get('expire_minute', 30);

		$return_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";

			$this->setRedirect($return_link, $msg);

			return;
		}


		// 設定已通過verify步驟
		SurveyforceVote::setSurveyStep($survey_id, "intro", true);
		if (SurveyforceVote::getSurveyItem($survey_id)->is_public == 0) {
			SurveyforceVote::setSurveyStep($survey_id, "token");
		}
		
		// 寫入議題資料-標題、驗證方式
		$survey_item = SurveyforceVote::getSurveyItem($survey_id);
		
		// 議題使用台北通，且有開啟"使用身分證驗證"，則不顯示台北通的驗證方式
		$verify_types = json_decode($survey_item->verify_type, true);
		if (in_array("taipeicard", $verify_types) && $survey_item->is_verify_idnum == 1) {
			$verify_types = array_diff($verify_types, array("taipeicard"));
			$verify_types = array_values($verify_types);
		}
		
		
		SurveyforceVote::setSurveyData($survey_id, "title", $survey_item->title, true);
		SurveyforceVote::setSurveyData($survey_id, "verify_required", $survey_item->verify_required);
		SurveyforceVote::setSurveyData($survey_id, "verify_type", json_encode($verify_types));
		SurveyforceVote::setSurveyData($survey_id, "verify_params", $survey_item->verify_params);
		SurveyforceVote::setSurveyData($survey_id, "is_public", $survey_item->is_public);
		SurveyforceVote::setSurveyData($survey_id, "is_notice_email", $survey_item->is_notice_email);
		SurveyforceVote::setSurveyData($survey_id, "is_notice_phone", $survey_item->is_notice_phone);
		SurveyforceVote::setSurveyData($survey_id, "display_result", $survey_item->display_result);
		SurveyforceVote::setSurveyData($survey_id, "is_lottery", $survey_item->is_lottery);
		SurveyforceVote::setSurveyData($survey_id, "vote_num_params", $survey_item->vote_num_params);
		SurveyforceVote::setSurveyData($survey_id, "expire_time", time() + ($expire_minute * 60));
		SurveyforceVote::setSurveyData($survey_id, "vote_pattern", $survey_item->vote_pattern);
		SurveyforceVote::setSurveyData($survey_id, "is_analyze", $survey_item->is_analyze);
		SurveyforceVote::setSurveyData($survey_id, "cross_validation", $survey_item->cross_validation);
		SurveyforceVote::setSurveyData($survey_id, "is_test", $survey_item->isTest);
		SurveyforceVote::setSurveyData($survey_id, "is_blockchain", $survey_item->is_blockchain);
		SurveyforceVote::setSurveyData($survey_id, "option_answers", null);
		SurveyforceVote::setSurveyData($survey_id, "onto_answers", null);
		
		// 附加驗證
		SurveyforceVote::setSurveyData($survey_id, "is_additional_verify", $survey_item->is_additional_verify);
		SurveyforceVote::setSurveyData($survey_id, "is_idnum", $survey_item->is_idnum);
		SurveyforceVote::setSurveyData($survey_id, "is_birthday", $survey_item->is_birthday);
		SurveyforceVote::setSurveyData($survey_id, "is_student", $survey_item->is_student);
		SurveyforceVote::setSurveyData($survey_id, "is_local", $survey_item->is_local);
		SurveyforceVote::setSurveyData($survey_id, "is_company", $survey_item->is_company);
		SurveyforceVote::setSurveyData($survey_id, "local_table_suffix", $survey_item->local_table_suffix);
		SurveyforceVote::setSurveyData($survey_id, "student_table_suffix", $survey_item->student_table_suffix);

		if ($survey_item->is_analyze == 1) {
			SurveyforceVote::setSurveyData($survey_id, "analyze_column", json_encode(SurveyforceVote::getAnalyzeColumn($survey_id)));
		}

		$verify_type = json_decode($survey_item->verify_type, true);
		if (in_array("taipeicard", $verify_type)) {
			$session->clear("sid", "callback");
			$session->set("sid", $survey_id, "callback");
		}

		// 若為不驗證(圖形驗證)，且沒有提供抽獎，則略過個資頁
		if ($survey_item->verify_type == '["none"]' && $survey_item->is_lottery == 0) {
			SurveyforceVote::setSurveyStep($survey_id, "statement");

			// 若為擇一且有多個驗證方式，則轉向多步驟頁面
			if ($survey_item->verify_required == 0 && count(json_decode($survey_item->verify_type)) > 1) {
				$link = JRoute::_("index.php?option=com_surveyforce&view=verify_opt&sid={$survey_id}&Itemid={$itemid}", false);
			} else {
				$link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid={$survey_id}&Itemid={$itemid}", false);
			}
		} else {
			$link = JRoute::_("index.php?option=com_surveyforce&view=statement&sid={$survey_id}&Itemid={$itemid}", false);
		}
		
		// 練習區
		if ($cat == "practice") {
			$session->set('practice', 1);
        } else {
			$session->set('practice', 0);
        }

		// 清空所有驗證的保留欄位資料
		$session->clear('verify_reserve_' . $survey_id);
		$session->clear('verify_google_' . $survey_id);


		$this->setRedirect($link);

	}

	public function check_intro_form() {
		$model   = $this->getModel();
		$session = &JFactory::getSession();
		$config  = JFactory::getConfig();
		$app     = JFactory::getApplication();


		$survey_id = $app->input->getInt('sid', 0);
		$itemid    = $app->input->getInt('Itemid', 0);


		$return_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == true) {
			$msg = "該議題已開始進行投票，請直接進行投票。";

			$this->setRedirect($return_link, $msg);

			return;
		}

		$survey_item = $model->getSurvey($survey_id);


		$msges = array ();

		$email = trim($app->input->getString('email'));
		$phone = trim($app->input->getString('phone'));

		if ($email || $phone) {
			if ($email) {
				if ($survey_item->is_notice_email == 0) {
					$msges[] = "該議題並不提供電子郵件通知服務。請重新操作。";
				} else {
					// 檢查Email格式
					if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $email)) {
						$msges[] = "Email格式錯誤，請確認您是否輸入正確。";
					}
				}
			}


			if ($phone) {
				if ($survey_item->is_notice_phone == 0) {
					$msges[] = "該議題並不提供手機簡訊通知服務。請重新操作。";
				} else {
					// 檢查手機號碼格式
					if (!preg_match('/^09[0-9]{8}$/', $phone)) {
						$msges[] = "手機號碼格式錯誤(共10碼數字，請勿填其他符號)。";
					}
				}
			}
		} else {
			$msges[] = "請至少選擇其中一項。請重新操作。";
		}

		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));

			return;
		}


		// 記錄開票結束通知-郵件
		if ($email && $survey_item->is_notice_email) {
			$encode_email = JHtml::_('utility.endcode', $email);

			$model->insertNoticeEmail($survey_id, $encode_email, 1);
			$model->insertNoticeEmail($survey_id, $encode_email, 2);

			$msges[] = "電子郵件通知記錄成功。";
		}


		// 記錄開票結束通知-手機
		if ($phone && $survey_item->is_notice_phone) {
			$encode_phone = JHtml::_('utility.endcode', $phone);

			$model->insertNoticePhone($survey_id, $encode_phone, 1);
			$model->insertNoticePhone($survey_id, $encode_phone, 2);

			$msges[] = "手機通知記錄成功。";
		}


		$this->setRedirect($return_link, implode("<br>", $msges));

		return;

	}

	public function getPdf() {

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();

		$original_name = $app->input->getString('original_name');
		$survey_id     = $app->input->getInt('survey_id');
		$file_name     = $app->input->getString('file_name');
		$path          = $config->get('ivoting_path') . '/survey/pdf/' . $survey_id . '/' . $file_name . '.pdf';

		header('Cache-Control: public, must-revalidate');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . (string) (filesize($path)));
		header('Content-Disposition: attachment; filename="' . $original_name . '"');
		readfile($path);

		exit;
	}

}
