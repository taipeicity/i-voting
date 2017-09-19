<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Autocheck model.
 *
 */
class SurveyforceModelAutocheck extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }

    public function getItem() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        
        $query->select('*');
        $query->from($db->quoteName('#__survey_force_survs'));
        $query->where('id = ' . (int) $sid);

        $db->setQuery($query);

        $surv = $db->loadObject();

        return $surv;
    }

    public function getBeforeVoteNum() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();        
        $query = $db->getQuery(true);
        
        $query->select('count(*) AS num');
        $query->from($db->quoteName('#__survey_force_vote', 'sfv'));
        $query->join('LEFT', $db->quoteName('#__survey_force_survs', 'sfs') . ' ON sfv.survey_id = sfs.id');
        $query->where($db->quoteName('survey_id') . ' = ' . (int) $sid);
        $query->where('sfs.vote_start > sfv.created');

        $db->setQuery($query);

        $num = $db->loadObject();

        return $num;
    }

    public function getBeforePeopleNum() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        
        $query->select('count(DISTINCT ticket_num) AS num');
        $query->from($db->quoteName('#__survey_force_vote_detail', 'sfvd'));
        $query->join('LEFT', $db->quoteName('#__survey_force_survs', 'sfs') . ' ON sfvd.survey_id = sfs.id');
        $query->where($db->quoteName('survey_id') . ' = ' . (int) $sid);
        $query->where('sfs.vote_start > sfvd.created');

        $db->setQuery($query);

        $num = $db->loadObject();

        return $num;
    }

    public function getAfterVoteNum() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        
        $query->select('count(*) AS num');
        $query->from($db->quoteName('#__survey_force_vote', 'sfv'));
        $query->join('LEFT', $db->quoteName('#__survey_force_survs', 'sfs') . ' ON sfv.survey_id = sfs.id');
        $query->where($db->quoteName('survey_id') . ' = ' . (int) $sid);
        $query->where('sfs.vote_start < sfv.created');
        $query->where('sfs.vote_end > sfv.created');

        $db->setQuery($query);

        $num = $db->loadObject();

        return $num;
    }

    public function getAfterPeopleNum() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        
        $query->select('count(DISTINCT ticket_num) AS num');
        $query->from($db->quoteName('#__survey_force_vote_detail', 'sfvd'));
        $query->join('LEFT', $db->quoteName('#__survey_force_survs', 'sfs') . ' ON sfvd.survey_id = sfs.id');
        $query->where($db->quoteName('survey_id') . ' = ' . (int) $sid);
        $query->where('sfs.vote_start < sfvd.created');
        $query->where('sfs.vote_end > sfvd.created');

        $db->setQuery($query);

        $num = $db->loadObject();

        return $num;
    }

    public function getBackStageRecordUser() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();        
        $query = $db->getQuery(true);
        
        $query->select('DISTINCT name, title');
        $query->from($db->quoteName('#__login_record'));
        $query->where($db->quoteName('cid') . ' = ' . $db->quote($sid));
        $query->where($db->quoteName('name') . ' != ' . $db->quote(''));
        $query->where($db->quoteName('title') . ' != ' . $db->quote(''));
		
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
    }
    
    public function getBackStageRecordIp() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();        
        $query = $db->getQuery(true);
        
        $query->select('DISTINCT user_ip, title');
        $query->from($db->quoteName('#__login_record'));
        $query->where($db->quoteName('cid') . ' = ' . $db->quote($sid));
        $query->where($db->quoteName('user_ip') . ' != ' . $db->quote(''));
        $query->where($db->quoteName('title') . ' != ' . $db->quote(''));
        
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
    }
    
    public function getVoteLogSum() {
        
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        
        $db = $this->getDBO();        
        $query = $db->getQuery(true);
        
        $query->select('*');
        $query->from($db->quoteName('#__vote_log_count'));
        $query->where($db->quoteName('cid') . ' = ' . $db->quote($sid));
        $query->order($db->quoteName('vote_date') . ' ASC');

        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
    }

}
