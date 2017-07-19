<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Survey Model.
 */
class SurveyforceModelIntro extends JModelItem {

    public function __construct()
    {
        parent::__construct();
    }

    public function populateState()
    {
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $params = $app->getParams();

        $survey_id = $jinput->getInt('sid', 0);

        $this->setState('survey.id', $survey_id);
        $this->setState('params', $params);
    }

    public function getSurveyParams()
    {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        return $params;
    }

    public function getSurveyConfig()
    {

        $params = JComponentHelper::getParams('com_surveyforce');

        return $params;
    }

    public function getItem()
    {
        $app = JFactory::getApplication();

        $id = $this->state->get('survey.id');

        $db = $this->getDbo();


        $query = $db->getQuery(true);
        $query->select('a.*');
        $query->from($db->quoteName('#__survey_force_survs') . ' AS a');
        $query->where('a.id = ' . (int) $id);
        $query->where('a.published = 1');
        $query->where('a.is_complete = 1');
        $query->where('a.is_checked = 1');


        // Filter by publish
        $nullDate = $db->Quote($db->getNullDate());
        $date = JFactory::getDate();
        $nowDate = $db->Quote($date->toSql());
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
        $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

        $db->setQuery($query);

        return $db->loadObject();
    }


    public function getSurvey($_survey_id)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.*');
        $query->from($db->quoteName('#__survey_force_survs') . ' AS a');
        $query->where('a.id = ' . (int) $_survey_id);

        $db->setQuery($query);

        return $db->loadObject();
    }

    // 新增投票通知-郵件
    public function insertNoticeEmail($_survey_id, $_email, $_type)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // 檢查是否已有記錄，若無才記錄
        $query->select('id');
        $query->from('#__survey_force_email_notice');
        $query->where('survey_id = ' . $db->quote($_survey_id));
        $query->where('email = ' . $db->quote($_email));
        $query->where('type = ' . $db->quote($_type));
        $db->setQuery($query);
        if ($db->loadResult())
        {
            return false;
        }

        $query = $db->getQuery(true);
        $created = JFactory::getDate()->toSql();

        $columns = array('survey_id', 'email', 'type', 'created');

        $values = array(
            $db->quote($_survey_id),
            $db->quote($_email),
            $db->quote($_type),
            $db->quote($created)
        );

        $query->insert($db->quoteName('#__survey_force_email_notice'));
        $query->columns($columns);
        $query->values(implode(',', $values));

        $db->setQuery($query);

        if ($db->execute())
        {
            return true;
        }
        else
        {
            JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);
            return false;
        }
    }

    // 新增投票通知-手機
    public function insertNoticePhone($_survey_id, $_phone, $_type)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // 檢查是否已有記錄，若無才記錄
        $query->select('id');
        $query->from('#__survey_force_phone_notice');
        $query->where('survey_id = ' . $db->quote($_survey_id));
        $query->where('phone = ' . $db->quote($_phone));
        $query->where('type = ' . $db->quote($_type));
        $db->setQuery($query);
        if ($db->loadResult())
        {
            return false;
        }


        $query = $db->getQuery(true);
        $created = JFactory::getDate()->toSql();

        $columns = array('survey_id', 'phone', 'type', 'created');

        $values = array(
            $db->quote($_survey_id),
            $db->quote($_phone),
            $db->quote($_type),
            $db->quote($created)
        );

        $query->insert($db->quoteName('#__survey_force_phone_notice'));
        $query->columns($columns);
        $query->values(implode(',', $values));

        $db->setQuery($query);

        if ($db->execute())
        {
            return true;
        }
        else
        {
            JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);
            return false;
        }
    }

    // 取得題目清單
    public function getQuestions()
    {
        $app = JFactory::getApplication();
        $_survey_id = $app->input->getInt('sid');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id, a.sf_qtext, b.ftext');
        $query->from($db->quoteName('#__survey_force_quests') . ' AS a');
        $query->leftJoin($db->quoteName('#__survey_force_fields') . 'AS b ON b.quest_id = a.id');
        $query->where('a.sf_survey = ' . (int) $_survey_id);
        $query->where('a.published = 1');
        $query->order('a.ordering ASC');


        $db->setQuery($query);

        return $db->loadObjectList();
    }

    // 取得選項清單
    public function getOptions()
    {
        $app = JFactory::getApplication();
        $quest_id = $app->input->getInt('sid');
        
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.*');
        $query->from($db->quoteName('#__survey_force_fields') . ' AS a');
        $query->where('a.quest_id = ' . (int) $quest_id);
        $query->order('a.ordering ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }


	// 取得已完成投票人數
	public function getFinishVotes() {
		$app = JFactory::getApplication();

		$id = $this->state->get('survey.id');

		$db		= $this->getDbo();


		$query	= $db->getQuery(true);
		$query->select('count(*) as total');
		$query->from($db->quoteName('#__survey_force_vote'));
		$query->where('survey_id = '. (int) $id);

		$db->setQuery($query);

		return $db->loadResult();
	}



}
