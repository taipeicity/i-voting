<?php

/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelSurvey extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Survey', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.survey', 'survey', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }

    public function getAllVerifyType() {
        $db = JFactory::getDBO();

        $db->setQuery("SELECT * FROM `#__extensions` WHERE `type` = 'plugin' and `access` = '1' and `enabled` = '1' and `folder` = 'verify' Order by `ordering`");
        $items = $db->loadObjectList();

        return $items;
    }

    // 更新欄位
    public function updateField($_field_name, $_field_value, $_id) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->update('#__survey_force_survs');
        $query->set($db->quoteName($_field_name) . " = " . $db->quote($_field_value));
        $query->where($db->quoteName('id') . " = " . $db->quote($_id));


        $db->setQuery($query);

        if ($db->execute()) {
            return true;
        } else {
            JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
            return false;
        }
    }

    // 取得議題
    public function getSurvey($_survey_id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_survs AS a');
        $query->where('a.id = ' . $db->quote($_survey_id));

        $db->setQuery($query);
        $row = $db->loadObject();

        return $row;
    }

    // 刪除議題
    public function delete($_survey_id) {
        $db = JFactory::getDbo();


        // 依序先刪選項、子選項 -> 題目 -> 議題
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__survey_force_quests');
        $query->where('sf_survey = ' . $db->quote($_survey_id));
        $db->setQuery($query);
        $question_rows = $db->loadObjectList();

        if ($question_rows) {
            foreach ($question_rows as $question_row) {
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__survey_force_fields');
                $query->where('quest_id = ' . $db->quote($question_row->id));
                $db->setQuery($query);
                $option_rows = $db->loadObjectList();

                if ($option_rows) {
                    foreach ($option_rows as $option_row) {
                        // 刪除檔案
                        if ($option_row->image) {
                            unlink(JPATH_SITE . "/" . $option_row->image);
                        }

                        // 刪除檔案
                        if ($option_row->file1) {
                            unlink(JPATH_SITE . "/" . $option_row->file1);
                        }
                    }
                }

                // 刪除選項
                $query = $db->getQuery(true);
                $query->delete('#__survey_force_fields');
                $query->where('quest_id = ' . $db->quote($question_row->id));
                $db->setQuery($query);
                $db->execute();


                // 刪除子選項
                $query = $db->getQuery(true);
                $query->delete('#__survey_force_sub_fields');
                $query->where('quest_id = ' . $db->quote($question_row->id));
                $db->setQuery($query);
                $db->execute();
            }

            // 刪除題目資料
            $query = $db->getQuery(true);
            $query->delete('#__survey_force_quests');
            $query->where('sf_survey = ' . $db->quote($_survey_id));
            $db->setQuery($query);
            $db->execute();
        }




        // 刪除議題的banner
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__survey_force_survs');
        $query->where('id = ' . $db->quote($_survey_id));
        $db->setQuery($query);
        $survey_row = $db->loadObject();

        if ($survey_row->image) {
            unlink(JPATH_SITE . "/" . $survey_row->image);
        }

        // 刪除議題資料
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_survs');
        $query->where('id = ' . $db->quote($_survey_id));
        $db->setQuery($query);
        $db->execute();
    }

    // 取得題目
    public function getQuestions($_survey_id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_quests AS a');
        $query->where('a.sf_survey = ' . $db->quote($_survey_id));
        $query->where('a.published = 1');

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    // 取得指定題目的選項數目
    public function getOptionsCount($_question_id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('COUNT(*)');
        $query->from('#__survey_force_fields AS a');
        $query->where('a.quest_id = ' . $db->quote($_question_id));

        $db->setQuery($query);
        $count = $db->loadResult();

        return $count;
    }

    // 取得單位內的所有使用者
    public function getUsersByUnit($_unit_id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__users AS a');
        $query->where('a.unit_id = ' . $db->quote($_unit_id));
        $query->where('a.block = 0');

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    // 取得特定使用者
    public function getUser($_user_id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__users AS a');
        $query->where('a.id = ' . $db->quote($_user_id));

        $db->setQuery($query);
        $row = $db->loadObject();

        return $row;
    }

    // 更新Email通知的狀態
    public function updateEmailNotice($_survey_id, $_type) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->update('#__survey_force_email_notice');
        $query->set($db->quoteName('is_send') . " = '0'");
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($_survey_id));


        $db->setQuery($query);

        if ($db->execute()) {
            return true;
        } else {
            JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
            return false;
        }
    }

    // 更新Phone通知的狀態
    public function updatePhoneNotice($_survey_id, $_type) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->update('#__survey_force_phone_notice');
        $query->set($db->quoteName('is_send') . " = '0'");
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($_survey_id));


        $db->setQuery($query);

        if ($db->execute()) {
            return true;
        } else {
            JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
            return false;
        }
    }

}
