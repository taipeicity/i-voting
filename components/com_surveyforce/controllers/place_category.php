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
 * Place_category controller class.
 */
class SurveyforceControllerPlace_category extends JControllerForm {
	/**recaptcha
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'place', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function start_vote() {
		$session = &JFactory::getSession();
		$app = JFactory::getApplication();

		$survey_id	= $app->input->getInt('sid', 0);
		$itemid	= $app->input->getInt('Itemid', 0);


		$return_link = JRoute::_("index.php?option=com_surveyforce&view=place_category&Itemid={$itemid}", false);

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
		SurveyforceVote::setSurveyData($survey_id, "title", $survey_item->title, true );
		SurveyforceVote::setSurveyData($survey_id, "verify_required", $survey_item->verify_required );
		SurveyforceVote::setSurveyData($survey_id, "verify_type", $survey_item->verify_type );
		SurveyforceVote::setSurveyData($survey_id, "verify_params", $survey_item->verify_params );
		SurveyforceVote::setSurveyData($survey_id, "is_public", $survey_item->is_public );
		SurveyforceVote::setSurveyData($survey_id, "is_notice_email", $survey_item->is_notice_email );
		SurveyforceVote::setSurveyData($survey_id, "is_notice_phone", $survey_item->is_notice_phone );
		SurveyforceVote::setSurveyData($survey_id, "display_result", $survey_item->display_result );
		SurveyforceVote::setSurveyData($survey_id, "is_lottery", $survey_item->is_lottery );
		SurveyforceVote::setSurveyData($survey_id, "is_place", $survey_item->is_place );
		SurveyforceVote::setSurveyData($survey_id, "vote_num_params", $survey_item->vote_num_params );
		SurveyforceVote::setSurveyData($survey_id, "expire_time", time() + 86400 );	// 現地投票無限制投票時間

		$link = JRoute::_("index.php?option=com_surveyforce&view=place_verify&sid={$survey_id}&Itemid={$itemid}", false);
		$this->setRedirect($link);
		return;
	}

}