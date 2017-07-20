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
class SurveyforceViewResultnote extends JViewLegacy {

    protected $state;
	protected $form;
    

    public function display($tpl = null) {
		$model = $this->getModel();
		$config = JFactory::getConfig();
        $app = JFactory::getApplication();
		$this->surv_id = JFactory::getApplication()->input->get('id', 0);


		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);
		JToolBarHelper::title("投票管理:". $this->survey_item->title. " - 編輯投票結果說明");


        $this->state = $this->get('State');
		$this->form = $this->get('Form');
       

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }


		// 判斷權限
		$canDo	= JHelperContent::getActions('com_surveyforce');
		$user = JFactory::getUser();
		$user_id = $user->get('id');
		$unit_id = $user->get('unit_id');

		// rene
		$self_gps = JUserHelper::getUserGroups($user->get('id'));
		$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

		// 取得議題作者的資料
		$created_user = JFactory::getUser($this->survey_item->created_by);
		$created_unit_id = $created_user->get('unit_id');
		if ( $this->survey_item->created_by == $user_id || ($unit_id == $created_unit_id && in_array($core_review, $self_gps)) || $canDo->get('core.own') ) {
			if ( $this->survey_item->is_complete == 1 && $this->survey_item->is_checked == 1) {
				$this->is_save = true;
			}
		}

		if ($this->is_save) {
			$this->addToolbar();
			parent::display($tpl);
		}
    }

    protected function addToolbar() {
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::apply('resultnote.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('resultnote.save', 'JTOOLBAR_SAVE');

		JToolBarHelper::cancel('resultnote.cancel', 'JTOOLBAR_CLOSE');

	}





}
