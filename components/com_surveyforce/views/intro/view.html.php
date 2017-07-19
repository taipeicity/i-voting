<?php

/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Joomla.Component
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewIntro extends JViewLegacy {

	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$session 	= &JFactory::getSession();
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$this->itemid = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');


		$this->state = $this->get('state');
		$this->params = $this->state->get('params');
		$this->voting_menuid = $this->params->get('voting_mymuid');
		$this->completed_menuid = $this->params->get('completed_mymuid');

		$this->questions = $this->get('Questions');
		$this->options = $this->get('Options');
		$this->finish_votes = $this->get('FinishVotes');
                
		$this->item = $this->get('Item');

		$this->print	= $app->input->getBool('print');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);

		// 檢查議題是否有效
		if ($this->item->id == 0) {
			$msg = "該議題不存在，請重新選擇正確的議題。";
			$app->redirect($category_link, $msg);
		}

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));


		// Display the view
		parent::display($tpl);
	}

}
