<?php

/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewVerify2nd extends JViewLegacy {

	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$config          = JFactory::getConfig();
		$app             = JFactory::getApplication();
		$this->itemid    = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');

		$this->state     = $this->get('state');
		$this->params    = $this->state->get('params');
		$this->preview   = false;
		$this->back_link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid=" . $this->survey_id . "&Itemid=" . $this->itemid, false);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);
		$intro_link    = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);

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

		// 檢查投票模式是否正確
		$result = json_decode(SurveyforceVote::checkVotePattern($this->survey_id), true);
		if ($result['status']) {
			$app->redirect($category_link, $result['msg']);
		}

		// 檢查未公開議題是否有token碼
		if (SurveyforceVote::getSurveyItem($this->survey_id)->is_public == 0) {
			if (SurveyforceVote::checkSurveyStep($this->survey_id, "token") == false) {
				$msg = "該議題不存在，請重新選擇正確的議題。";
				$app->redirect($category_link, $msg);
			}
		}

		// 檢查是否有依序執行步驟
		if (SurveyforceVote::checkSurveyStep($this->survey_id, "verify") == false) {
			$msg = "該議題未從投票起始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}


		// 取出第2驗證頁的驗證方式
		$this->verify2nd_type = SurveyforceVote::getSurveyData($this->survey_id, "verify2nd_type");
		if ($this->verify2nd_type == "") {
			$msg = "該議題未從投票起始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}


		$this->category_link = $category_link;

		if (!$this->item) {
			$this->item = $this->get('Item');
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		// Display the view
		parent::display($tpl);

	}

}
