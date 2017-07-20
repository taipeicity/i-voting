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

class SurveyforceViewSurvey extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {
        SurveyforceHelper::showTitle('SURVEY_ADMIN');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        $this->verify_types = $this->get('AllVerifyType');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        $bar = JToolBar::getInstance('toolbar');

        $canDo = JHelperContent::getActions('com_surveyforce');

        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user = JFactory::getUser();
        $user_id = $user->get('id');
        $unit_id = $user->get('unit_id');

        // 取得議題作者的資料
        $created_user = JFactory::getUser($this->item->created_by);
        $created_unit_id = $created_user->get('unit_id');

        $isNew = ($this->item->id == 0);
        $date = JFactory::getDate();
        $nowDate = $date->toSql();

        // rene 審核設定取得
        $self_gps = JUserHelper::getUserGroups($user->get('id'));
        $core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');


        if ($isNew) {
            JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CANCEL');

            $this->can_save = true;
        } else {
            // 作者 或 專審人員 或 最高權限 才可儲存和刪除
            if ($this->item->created_by == $user_id || (in_array($core_review, $self_gps)) || $canDo->get('core.own')) {
                // 最高權限
                if ($canDo->get('core.own')) {
                    JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');
                    $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small" onclick="Joomla.submitbutton(\'survey.delete\')"><i class="icon-delete icon-white"></i>刪除</a></div>');
                    if ($this->item->is_complete == 0) {
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.send_check\')"><i class="icon-folder icon-white"></i>送審</a></div>');
                    }

                    if ($this->item->is_complete == 1 && $this->item->is_checked == 0) {
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-inverse" onclick="Joomla.submitbutton(\'survey.pass_success\')"><i class="icon-folder icon-white"></i>審核通過</a></div>');
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-warning" href="#divForm" id="btnForm"><i class="icon-folder icon-white"></i>審核不通過</a></div>');
                    }

                    if ($this->item->is_complete == 1 && $this->item->is_checked == 1 && (strtotime($this->item->vote_start) > strtotime($nowDate))) {
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.recheck\')"><i class="icon-folder icon-white"></i>重新審核</a></div>');
                    }

                    $this->can_save = true;
                } else {
                    // 承辦人員-送審按鈕
                    if ($this->item->is_complete == 0) {
                        JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');

                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.send_check\')"><i class="icon-folder icon-white"></i>送審</a></div>');

                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small" onclick="Joomla.submitbutton(\'survey.delete\')"><i class="icon-delete icon-white"></i>刪除</a></div>');

                        $this->can_save = true;
                    }


                    // 審核人員-審核按鈕
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 0 && (in_array($core_review, $self_gps))) {
                        JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');

                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-inverse" onclick="Joomla.submitbutton(\'survey.pass_success\')"><i class="icon-folder icon-white"></i>審核通過</a></div>');
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-warning" href="#divForm" id="btnForm"><i class="icon-folder icon-white"></i>審核不通過</a></div>');

                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small" onclick="Joomla.submitbutton(\'survey.delete\')"><i class="icon-delete icon-white"></i>刪除</a></div>');

                        $this->can_save = true;
                    }

                    // 送審成功後，議題尚未開始投票前，可以再重新審核
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 1 && (strtotime($this->item->vote_start) > strtotime($nowDate)) && ( in_array($core_review, $self_gps) )) {
                        $bar->appendButton('Custom', '<div class="btn-group"><a class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.recheck\')"><i class="icon-folder icon-white"></i>重新審核</a></div>');
                    }
                }

                JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
            } else {
                JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
            }
        }
    }

}
