<?php

/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewVerify_opt extends JViewLegacy {

	public function __construct() {
		parent::__construct();

	}

	public function display($tpl = null) {
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$this->itemid = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');

		$session = &JFactory::getSession();

		$this->state = $this->get('state');
		$this->params = $this->state->get('params');

		$this->item = $this->get('Item');

		// 網頁標題
		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);
		$intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);

		// 檢查是否閒置過久
		if (SurveyforceVote::isSurveyExpired($this->survey_id) == false) {
			$msg = "網頁已閒置過久，請重新點選議題進行投票。";
			$app->redirect($category_link, $msg);
		}

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";
			$app->redirect($category_link, $msg);
		}

		// 檢查是否有依序執行步驟
		if (SurveyforceVote::checkSurveyStep($this->survey_id, "verify") == false) {
			$msg = "該議題未從投票起始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}


		$this->verify_required = SurveyforceVote::getSurveyData($this->survey_id, "verify_required");
		$this->verify_type = SurveyforceVote::getSurveyData($this->survey_id, "verify_type");
		$this->verify_params = SurveyforceVote::getSurveyData($this->survey_id, "verify_params");

		$this->intro_link = $intro_link;
		$this->category_link = $category_link;


		if ($this->item->verify_type == '["none"]' && $this->item->is_lottery == 0) {
			$this->back_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);
		} else {
			$this->back_link = JRoute::_("index.php?option=com_surveyforce&view=statement&sid={$this->survey_id}&Itemid={$this->itemid}", false);
		}


		// Display the view
		parent::display($tpl);

	}

}
