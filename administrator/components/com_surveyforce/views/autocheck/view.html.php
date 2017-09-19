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
class SurveyforceViewAutocheck extends JViewLegacy {

    protected $items;
    protected $state;

    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $layout = $app->input->getString("layout");

        $this->item = $this->get('Item');

        /* 投票紀錄 */
        $this->before_votenum = $this->get('BeforeVoteNum');
        $this->before_peopleNum = $this->get('BeforePeopleNum');
        $this->after_votenum = $this->get('AfterVoteNum');
        $this->after_peopleNum = $this->get('AfterPeopleNum');

        /* 後台登入紀錄 */
        $this->BackStageRecord_User = $this->get('BackStageRecordUser');
        $this->BackStageRecord_Ip = $this->get('BackStageRecordIp');

        /* 投票日誌檔統計 */
        $this->VoteLogSum = $this->get('VoteLogSum');

        JToolBarHelper::title("投票管理: 自動檢核投票紀錄");

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
