<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewVoted extends JViewLegacy {

	protected $items;
	protected $state;
	protected $voted_html;
	protected $verify_type;

	public function display($tpl = null) {

		$this->item = $this->get('Item');


		JToolBarHelper::title("投票管理: 查詢投票紀錄");

		$app  = JFactory::getApplication();
		preg_match('/[a-z]+/', $this->item->verify_type, $selected);
		$this->type = ($app->input->getString('verify_type')) ? $app->input->getString('verify_type') : $selected[0];


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
	}

}
