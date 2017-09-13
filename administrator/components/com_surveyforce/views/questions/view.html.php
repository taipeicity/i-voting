<?php

/**
 *   @package         Surveyforce
 *   @version           1.1-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewQuestions extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null) {
		$this->state = $this->get('State');
		$this->surv_id = JFactory::getApplication()->input->get('surv_id', 0);
		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);

		$document = JFactory::getDocument();
		$document->addScript('components/com_surveyforce/assets/js/js.js');
		$submenu = 'questions';
//        SurveyforceHelper::showTitle($submenu);
		JToolBarHelper::title("投票管理:" . $this->survey_item->title . " - 題目", $submenu);
//        SurveyforceHelper::getCSSJS();

		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$sections = $this->get('Sections');


		$rows = array ();

		foreach ($items as $item) {
			if (!$item->sf_section_id) {
				$rows[0][] = $item;
			}
		}


		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->items = $rows;
		$this->sections = $sections;
		$this->pagination = $pagination;

		$this->question_types = $this->get('QuestionTypes');


		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);

	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() {
		$bar = JToolBar::getInstance('toolbar');
		$canDo = JHelperContent::getActions('com_surveyforce');

		$surv_id = JFactory::getApplication()->input->get('surv_id');



		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$user_id = $user->get('id');
		$unit_id = $user->get('unit_id');

		// 取得議題作者的資料
		$created_user = JFactory::getUser($this->survey_item->created_by);
		$created_unit_id = $created_user->get('unit_id');

	
		$self_gps = JUserHelper::getUserGroups($user->get('id'));
		$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');


		// 作者 或 專審人員 或 最高權限 才可新增和刪除
		if ($this->survey_item->created_by == $user_id || ($this->survey_item->checked_by == $user_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) {

			// 未送審前 可新增和刪除
			if ($this->survey_item->is_complete == 0 && $this->survey_item->is_checked == 0) {
				$bar->appendButton('Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_surveyforce&amp;tmpl=component&amp;task=question.new_question_type&amp;KeepThis=true&amp;surv_id=' . JFactory::getApplication()->input->get('surv_id', 0) . '&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-new icon-white"></i>' . JText::_('COM_SURVEYFORCE_NEW') . '</a></div>');
				JToolBarHelper::editList('question.edit');
				JToolBarHelper::divider();
				JToolBarHelper::custom('questions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('questions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				JToolBarHelper::divider();
				JToolBarHelper::deleteList('', 'questions.delete');
				JToolBarHelper::divider();
			}


			// 送審後 只有 同單位審核者 或 最高權限 才可新增和刪除
			if (( ($this->survey_item->checked_by == $user_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) && $this->survey_item->is_complete == 1 && $this->survey_item->is_checked == 0) {
				$bar->appendButton('Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_surveyforce&amp;tmpl=component&amp;task=question.new_question_type&amp;KeepThis=true&amp;surv_id=' . JFactory::getApplication()->input->get('surv_id', 0) . '&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-new icon-white"></i>' . JText::_('COM_SURVEYFORCE_NEW') . '</a></div>');
				JToolBarHelper::editList('question.edit');
				JToolBarHelper::divider();
				JToolBarHelper::custom('questions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('questions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				JToolBarHelper::divider();
				JToolBarHelper::deleteList('', 'questions.delete');
				JToolBarHelper::divider();
			}

			if ($canDo->get('core.own') && $this->survey_item->is_complete == 1 && $this->survey_item->is_checked == 1) {
				$bar->appendButton('Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_surveyforce&amp;tmpl=component&amp;task=question.new_question_type&amp;KeepThis=true&amp;surv_id=' . JFactory::getApplication()->input->get('surv_id', 0) . '&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-new icon-white"></i>' . JText::_('COM_SURVEYFORCE_NEW') . '</a></div>');
				JToolBarHelper::editList('question.edit');
				JToolBarHelper::divider();
				JToolBarHelper::custom('questions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('questions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				JToolBarHelper::divider();
				JToolBarHelper::deleteList('', 'questions.delete');
				JToolBarHelper::divider();
			}
		}

		JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');


		JHtmlSidebar::setAction('index.php?option=com_surveyforce&view=questions');

		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', array ("1" => "發佈的", "0" => "停止發佈的"), 'value', 'text', $this->state->get('filter.state'), true)
		);

	}

	protected function getSortFields() {
		return array (
			'sf_qtext' => JText::_('COM_SURVEYFORCE_TEXT'),
			'ordering' => JText::_('COM_SURVEYFORCE_ORDER'),
			'sf_qtype' => '題型',
			'published' => '發佈狀態',
			'id' => JText::_('COM_SURVEYFORCE_ID')
		);

	}

}
