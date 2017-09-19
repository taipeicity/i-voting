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
 * Intro controller class.
 */
class SurveyforceControllerIntro extends JControllerForm {

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'intro', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;

	}

	public function start_vote() {
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$survey_id = $app->input->getInt('sid', 0);
		$itemid = $app->input->getInt('Itemid', 0);

		$expire_minute = $config->get('expire_minute', 30);

		$session = &JFactory::getSession();

		$return_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";

			$this->setRedirect($return_link, $msg);
			return;
		}




		// 設定已通過verify步驟
		SurveyforceVote::setSurveyStep($survey_id, "intro", true);

		// 寫入議題資料-標題、驗證方式
		$survey_item = SurveyforceVote::getSurveyItem($survey_id);
		SurveyforceVote::setSurveyData($survey_id, "title", $survey_item->title, true);
		SurveyforceVote::setSurveyData($survey_id, "verify_required", $survey_item->verify_required);
		SurveyforceVote::setSurveyData($survey_id, "verify_type", $survey_item->verify_type);
		SurveyforceVote::setSurveyData($survey_id, "verify_params", $survey_item->verify_params);
		SurveyforceVote::setSurveyData($survey_id, "is_public", $survey_item->is_public);
		SurveyforceVote::setSurveyData($survey_id, "is_notice_email", $survey_item->is_notice_email);
		SurveyforceVote::setSurveyData($survey_id, "is_notice_phone", $survey_item->is_notice_phone);
		SurveyforceVote::setSurveyData($survey_id, "display_result", $survey_item->display_result);
		SurveyforceVote::setSurveyData($survey_id, "is_lottery", $survey_item->is_lottery);
		SurveyforceVote::setSurveyData($survey_id, "vote_num_params", $survey_item->vote_num_params);
		SurveyforceVote::setSurveyData($survey_id, "expire_time", time() + ($expire_minute * 60));

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




		// 清空所有驗證的保留欄位資料
		$session->clear('verify_reserve_' . $survey_id);
		$session->clear('verify_google_' . $survey_id);


		$this->setRedirect($link);

	}

	public function check_intro_form() {
		$model = $this->getModel();
		$session = &JFactory::getSession();
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();


		$survey_id = $app->input->getInt('sid', 0);
		$itemid = $app->input->getInt('Itemid', 0);




		$return_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == true) {
			$msg = "該議題已開始進行投票，請直接進行投票。";

			$this->setRedirect($return_link, $msg);
			return;
		}

		$survey_item = $model->getSurvey($survey_id);


		unset($msges);
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
						$msges[] = "手機號碼格式錯誤。";
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

}
