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
class SurveyforceViewQuestion extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;
	protected $surveys;
	protected $ordering_list;

	public function display($tpl = null) {
		$model = $this->getModel();
		$app = JFactory::getApplication();
		SurveyforceHelper::showTitle('QUESTION_ADMIN');

		$this->option = 'com_surveyforce';
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$new_qtype_id = $app->getUserStateFromRequest("question.new_qtype_id", 'new_qtype_id', 0);
		$sf_survey = $app->getUserStateFromRequest("question.sf_survey", 'sf_survey', 0);

		if (!$sf_survey)
			$sf_survey = $app->getUserStateFromRequest("question.surv_id", 'surv_id', 0);

		if ($this->item->id) {
			$new_qtype_id = $this->item->question_type;
		} else {
			$this->item->question_type = $new_qtype_id;
			$this->item->sf_survey = $sf_survey;
		}

		$type = $new_qtype_id;

		$this->surveys = $this->get('SurveysList');
		$this->question_type_item = $model->getQuestionType($type);
		$this->ordering_list = $this->get('Ordering');
		$this->survey_item = SurveyforceHelper::getSuveryItem($sf_survey);


		JPluginHelper::importPlugin('survey', $type);
		$className = 'plgSurvey' . ucfirst($type);


		$data = array ();
		$data['id'] = $this->item->id;
		$data['quest_type'] = $type;
		$data['item'] = $this->item;


		$model = JModelLegacy::getInstance("Question", "SurveyforceModel");
		$lists = $model->getLists($this->item->id);

		// 載入選項清單
		if (method_exists($className, 'onGetAdminOptions')) {
			$this->options = $className::onGetAdminOptions($this->item->id);
		}

		$cat_type = array ("imgcat");
		if (in_array($this->item->question_type, $cat_type)) {
			$this->cats = $className::onGetAdminCats($this->item->id);
		}


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);

	}

    protected function addToolbar() {
		$canDo = JHelperContent::getActions('com_surveyforce');

		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$user_id = $user->get('id');
		$unit_id = $user->get('unit_id');

		// 取得議題作者的資料
		$created_user = JFactory::getUser($this->survey_item->created_by);
		$created_unit_id = $created_user->get('unit_id');

		// 審核設定取得
		$self_gps = JUserHelper::getUserGroups($user->get('id'));
		$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

		if ($canDo->get('core.own')) {
			JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
		} else {
			// 作者 或 同單位審核者 或 最高權限 才可儲存
			if ($this->survey_item->created_by == $user_id || ($this->survey_item->checked_by == $user_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) {

				// 未送審前 才可儲存
				if ($this->survey_item->is_complete == 0 && $this->survey_item->is_checked == 0) {
					JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
				}else if (( ($this->survey_item->checked_by == $user_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) && $this->survey_item->is_complete == 1 && $this->survey_item->is_checked == 0) {
                    // 送審後 只有 同單位審核者 或 最高權限 才可新增和刪除
					JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
				} else {
					JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CLOSE');
				}
			} else {
				JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CLOSE');
			}
		}

	}

}
