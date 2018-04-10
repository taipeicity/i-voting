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

class SurveyforceViewAnalyzes extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null) {
		$config = JFactory::getConfig();


		$submenu = 'analyzes';
		SurveyforceHelper::showTitle($submenu);
		SurveyforceHelper::addSubmenu($submenu);

		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->canDo      = JHelperContent::getActions('com_surveyforce');
		$this->fields     = $this->get('Fields');
		$model            = $this->getModel();
		$this->items      = $model->getFields($this->items);

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}


		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);

	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() {
		$canDo = JHelperContent::getActions('com_surveyforce');

		JToolBarHelper::addNew('analyze.add');
		JToolBarHelper::editList('analyze.edit');
		JToolBarHelper::deleteList('', 'analyzes.delete');


		if ($canDo->get('core.config')) {
			JToolbarHelper::preferences('com_surveyforce');
		}

		JHtmlSidebar::setAction('index.php?option=com_surveyforce&view=surveys');

		JHtmlSidebar::addFilter('- 選擇狀態 -', 'filter_state', JHtml::_('select.options', array (
			"1" => "發布", "0" => "停止發布"
		), 'value', 'text', $this->state->get('filter.state'), true));


	}

	protected function getSortFields() {
		return array (
			'state' => '狀態', 'title' => JText::_('COM_SURVEYFORCE_ANALYZES_QUESTS'),
			'id'    => JText::_('JGRID_HEADING_ID')
		);

	}

}
