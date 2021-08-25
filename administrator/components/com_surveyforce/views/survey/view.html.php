<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewSurvey extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    protected $user_id;

    protected $core_review;

    protected $self_gps;

    protected $canDo;

    protected $HasDataAnalyze;

    protected $verify_types;

    protected $check_analyze;

    protected $nowDate;

    protected $otherDataName;

    protected $edit_stage;

    protected $canDo_created_by;

    protected $stage;

    protected $releaseItem;

    public function display($tpl = null)
    {
        SurveyforceHelper::showTitle('SURVEY_ADMIN');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->quantity = $this->get('Quantity');

        $session = JFactory::getSession();

        $this->verify_types = $this->get('AllVerifyType');

        $layout = $this->getLayout();

        if ($this->item->id) {
            $this->edit_stage = $session->get("edit_stage.".$this->item->id, $this->item->stage);
            $this->form->setValue('edit_stage', null, $this->edit_stage);
            $this->stage = $this->item->stage;

            if ($layout == "column") {
                $this->AnalyzeColumns = $this->get('AnalyzeColumns'); // 取得參數設定
                $this->survey_id = $this->item->id;

                $date = JFactory::getDate();
                $nowDate = $date->toSql();
                if (strtotime($this->item->vote_end) < strtotime($nowDate)) {
                    $this->end = true;
                } else {
                    $this->end = false;
                }
            } else {
                $this->HasDataAnalyze = SurveyforceHelper::getAnalyzeQuestion() == null ? false : true;
                $this->check_analyze = SurveyforceHelper::checkAnalyze($this->item->id) == null ? false : true;
            }

            // 假如投票開始就將議題全部記錄到session
            $releaseItem = $this->get('ReleaseItem');
            $releaseItem->state = $this->quantity->state;
            $releaseItem->quantity = $this->quantity->quantity;
            if ($releaseItem->stage == 6 and $releaseItem->is_complete == 1 and $releaseItem->is_checked == 1 and $releaseItem->published == 1) {

                $vote_start = JHtml::_('date', $releaseItem->vote_start, 'Y-m-d H:i:s');
                $vote_end = JHtml::_('date', $releaseItem->vote_end, 'Y-m-d H:i:s');

                if (time() >= strtotime($vote_start) and time() <= strtotime($vote_end)) {
                    $session->set('voting.'.$releaseItem->id, true, 'survey');
                    $session->set('origin.'.$releaseItem->id, json_encode($releaseItem), 'survey');
                }
            }

        } else {
            $this->form->setValue('edit_stage', null, $session->get("edit_stage.new"));
            $this->edit_stage = $session->get("edit_stage.new", 1);
            $this->stage = 1;

            $this->HasDataAnalyze = true;
            $this->check_analyze = false;
        }

        $user = JFactory::getUser();
        $this->user_id = $user->get('id');

        // 審核設定取得
        $this->self_gps = JUserHelper::getUserGroups($this->user_id);

        // 議題編輯權限
        $this->canDo = JHelperContent::getActions('com_surveyforce');

        // 承辦人欄位設定
        $this->canDo_created_by = JHelperContent::getActions('com_users')->get('core.manage');

        $date = JFactory::getDate();
        $this->nowDate = $date->toSql();

        $this->stage_name = [
            1 => JText::_("COM_SURVEYFORCE_CHECK"),
            2 => JText::_("COM_SURVEYFORCE_REVIEW"),
            3 => JText::_("COM_SURVEYFORCE_DISCUSS"),
            4 => JText::_("COM_SURVEYFORCE_OPTIONS"),
            5 => JText::_("COM_SURVEYFORCE_LAUNCHED"),
            6 => JText::_("COM_SURVEYFORCE_RESULT"),
        ];
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $bar = JToolBar::getInstance('toolbar');

        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $nowDate = $this->nowDate;

        if ($isNew) {
            JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CANCEL');

            $this->can_save = true;
        } else {

            // 作者 或 專審人員 或 最高權限 才可儲存和刪除
            if ($this->item->created_by == $this->user_id || $this->canDo->get('core.review') || $this->canDo->get('core.own')) {
                // 最高權限
                if ($this->canDo->get('core.own')) {
                    JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');
                    JToolBarHelper::save2copy('survey.save2copy', 'COM_SURVEYFORCE_SAVE_AS_COPY'); // 另存新議題

                    // 送審按鈕
                    if ($this->item->is_complete == 0 || ($this->item->is_complete == 1 && $this->item->is_checked == 1)) {
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.send_check\')"><i class="icon-folder icon-white"></i>送審</button></div>');
                    }
                    $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small" onclick="Joomla.submitbutton(\'survey.delete\')"><i class="icon-delete icon-white"></i>刪除</button></div>');

                    // 審核按鈕
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 0) {
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-inverse" onclick="Joomla.submitbutton(\'survey.pass_success\')"><i class="icon-folder icon-white"></i>審核通過</button></div>');
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-warning" href="#divForm" id="btnForm"><i class="icon-folder icon-white"></i>審核不通過</button></div>');
                    }

                    $re_check = false;

                    // 重新送審
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 1) {
                        if ($this->item->stage < 6) {
                            $re_check = true;
                        } else {
                            if (strtotime($this->item->vote_start) > strtotime($nowDate)) {
                                $re_check = true;
                            }
                        }
                    }

                    if ($re_check) {
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.recheck\')"><i class="icon-folder icon-white"></i>重新審核</button></div>');
                    }

                    $this->can_save = true;
                } else {
                    if (($this->item->is_complete == 0 || ($this->item->is_complete == 1 && $this->item->is_checked == 1)) && ! $this->canDo->get('core.review')) {

                        /*
                         * 議題第6階段且投票時間截止後不能編輯
                         */
                        if ($this->item->stage == 6 and strtotime($this->item->vote_end) < strtotime($nowDate)) {
                            $voting = false;
                        } else {
                            $voting = true;
                        }

                        if ($voting) {
                            JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
                            JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');

                            $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.send_check\')"><i class="icon-folder icon-white"></i>送審</button></div>');
                            $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small" onclick="Joomla.submitbutton(\'survey.delete\')"><i class="icon-delete icon-white"></i>刪除</button></div>');
                        }

                        $this->can_save = true;
                    }

                    // 審核人員-審核按鈕
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 0 && $this->canDo->get('core.review')) {

                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-inverse" onclick="Joomla.submitbutton(\'survey.pass_success\')"><i class="icon-folder icon-white"></i>審核通過</button></div>');
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-warning" href="#divForm" id="btnForm"><i class="icon-folder icon-white"></i>審核不通過</button></div>');

                        $this->can_save = true;
                    }

                    // 送審成功後，議題尚未開始投票前，可以再重新審核
                    if ($this->item->is_complete == 1 && $this->item->is_checked == 1 && (strtotime($this->item->vote_start) > strtotime($nowDate)) && $this->canDo->get('core.review')) {
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.recheck\')"><i class="icon-folder icon-white"></i>重新審核</button></div>');
                    }

                    $re_check = false;

                    // 重新送審
                    if ($this->canDo->get('core.review') && $this->item->is_complete == 1 && $this->item->is_checked == 1) {
                        if ($this->item->stage < 6) {
                            $re_check = true;
                        } else {
                            if (strtotime($this->item->vote_start) > strtotime($nowDate)) {
                                $re_check = true;
                            }
                        }
                    }

                    if ($re_check) {
                        $bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'survey.recheck\')"><i class="icon-folder icon-white"></i>重新審核</button></div>');
                    }

                    if ($this->canDo->get('core.copy')) {
                        JToolBarHelper::save2copy('survey.save2copy', 'COM_SURVEYFORCE_SAVE_AS_COPY'); // 另存新議題
                    }
                }
                JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
            } else {
                // 只有承辦才能使用複製議題
                if ($this->canDo->get('core.copy')) {
                    JToolBarHelper::save2copy('survey.save2copy', 'COM_SURVEYFORCE_SAVE_AS_COPY'); // 另存新議題
                }
                JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
            }
        }

        JHtmlSidebar::addFilter('- 選擇是否公開 -', 'filter_public', JHtml::_('select.options', [
            "1" => "公開",
            "0" => "不公開",
        ], 'value', 'text', $this->state->get('filter.public'), true));

        JHtmlSidebar::addFilter('- 選擇議題所屬 -', 'filter_own', JHtml::_('select.options', [
            "1" => "自己",
            "2" => "同單位",
        ], 'value', 'text', $this->state->get('filter.own'), true));
    }

    protected function getPublishFields()
    {
        return [
            "1" => "發布",
            "0" => "不發布",
        ];
    }

    protected function getRequiredFields()
    {
        return [
            "1" => "必填",
            "0" => "非必填",
        ];
    }
}
