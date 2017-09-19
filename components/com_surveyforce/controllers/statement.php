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
 * Statement controller class.
 */
class SurveyforceControllerStatement extends JControllerForm {
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'statement', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function check_statement_form() {
		$config = JFactory::getConfig();
		$session 	= &JFactory::getSession();
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		$model = $this->getModel();


		$survey_id	= $app->input->getInt('sid', 0);
		$itemid	= $app->input->getInt('Itemid', 0);


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


		// 檢查欄位是否已填寫
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=statement&sid={$survey_id}&Itemid={$itemid}", false);
		unset($msges);
		$msges = array();

		$agree_statement = $app->input->getInt('agree_statement');
		if ($agree_statement == 0) {
			$msges[] = "請勾選同意書選項。";
		}

		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}


		// 設定已通過statement步驟
		SurveyforceVote::setSurveyStep($survey_id, "statement");


		// 若為擇一且有多個驗證方式，則轉向多步驟頁面
		$verify_required = SurveyforceVote::getSurveyData($survey_id, "verify_required");
		$verify_type = SurveyforceVote::getSurveyData($survey_id, "verify_type");
		if ($verify_required == 0 && count(json_decode($verify_type)) > 1) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=verify_opt&sid={$survey_id}&Itemid={$itemid}", false);
		} else {
			$link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid={$survey_id}&Itemid={$itemid}", false);
		}

		$this->setRedirect($link);


	}

}