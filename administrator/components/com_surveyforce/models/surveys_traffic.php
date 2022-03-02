<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Surveys_traffic model.
 *
 */
class SurveyforceModelSurveys_traffic extends JModelList
{
    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_surveyforce.survey', 'survey', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }
	
	/**
     * 取得所有議題
	 * 
	 * @return  array
	 */
    public function getSurveys() {
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
		$query->select("id AS value, CONCAT(id, ' - ', title) AS text");
        $query->from($db->quoteName('#__survey_force_survs_release'));
		$query->order("id");
		
        $db->setQuery($query);

        return $db->loadObjectList();
    }
	
	
	/**
     * 取得指定日期的投票票號
     * 
	 * @param	int $filter_survey_id  議題編號
	 * @param	int $filter_date  篩選日期
	 * 
	 * @return  int
	 */
    public function getVoteTickets($filter_survey_id, $filter_date) {
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
		$query->select("*");
        $query->from($db->quoteName('#__survey_force_vote'));
		
		// 篩選議題編號
		$query->where('survey_id = ' . $db->quote($filter_survey_id));
		
		// 篩選日期
		$query->where('created >= ' . $db->quote(sprintf("%s 16:00:00", (date("Y-m-d", strtotime($filter_date) - 86400)))));
		$query->where('created < ' . $db->quote(sprintf("%s 16:00:00", $filter_date)));
		
		

        $db->setQuery($query);

        return $db->loadObjectList();
    }
	
	

}
