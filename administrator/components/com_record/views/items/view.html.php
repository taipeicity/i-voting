<?php
/**
 * 紀錄管理 - 清單View
 * 
 * @version    CVS: 1.0.0
 * @package    com_record
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * View class for a list
 *
 * @package com_record
 */
class RecordViewItems extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		
		$this->model = $this->getModel();
		
		

		RecordHelper::addSubmenu('items');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar() {
		require_once JPATH_COMPONENT . '/helpers/record.php';

		$state = $this->get('State');
		$canDo = RecordHelper::getActions($state->get('filter.category_id'));
		$user = JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title('API紀錄');
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_record');
		}


		JHtmlSidebar::setAction('index.php?option=com_record&view=items');

		
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields() {
		
	}

}
