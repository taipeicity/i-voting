<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewPlace_finish extends JViewLegacy {

	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$this->itemid = $app->input->getInt('Itemid');
		$this->survey_id = $session->get('place_survey_id');

		$this->state = $this->get('state');
		$this->params = $this->state->get('params');

		$this->item = $this->get('Item');


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}


		// 檢查
		$login_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$this->itemid}", false);
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=place_category&Itemid={$this->itemid}", false);
		$verify_link = JRoute::_("index.php?option=com_surveyforce&view=place_verify&sid={$this->survey_id}&Itemid={$this->itemid}", false);

		// 檢查是否有登入
		if (!$session->get('place_username')) {
			$msg = "您尚未登入，請重新登入。";
			$app->redirect($login_link, $msg);
		}

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
			$msg = "該議題已結束投票時間。";
			$app->redirect($category_link, $msg);
		}



		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		// Display the view

		parent::display($tpl);
	}

}
