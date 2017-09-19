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

class SurveyforceViewSurveys extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null) {
		$config = JFactory::getConfig();
		$this->is_testsite = $config->get('is_testsite', false);
		$this->testsite_link = $config->get('testsite_link', '');


		$submenu = 'surveys';
		SurveyforceHelper::showTitle($submenu);



		$this->items = $this->get('Items');
		$this->units = $this->get('Units');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->canDo = JHelperContent::getActions('com_surveyforce');


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
		$user = JFactory::getUser();

		if (!(in_array(11, $user->groups) || in_array(12, $user->groups) || in_array(13, $user->groups))) {
			JToolBarHelper::addNew('survey.add');
			JToolBarHelper::editList('survey.edit');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.config')) {
			JToolbarHelper::preferences('com_surveyforce');
		}




		JHtmlSidebar::setAction('index.php?option=com_surveyforce&view=surveys');

		JHtmlSidebar::addFilter(
		'- 選擇是否公開 -', 'filter_public', JHtml::_('select.options', array ("1" => "公開", "0" => "不公開"), 'value', 'text', $this->state->get('filter.public'), true)
		);

		JHtmlSidebar::addFilter(
		'- 選擇議題所屬 -', 'filter_own', JHtml::_('select.options', array ("1" => "自己", "2" => "同單位"), 'value', 'text', $this->state->get('filter.own'), true)
		);

	}

	protected function getSortFields() {
		return array (
			's.title' => '名稱',
			's.publish_up' => '上架時間',
			's.vote_start' => '開始投票時間',
			's.vote_end' => '投票結束時間',
			'ut.title' => '單位',
			'u.name' => '承辦人員',
			's.is_public' => '是否公開',
			's.id' => JText::_('JGRID_HEADING_ID')
		);

	}

}
