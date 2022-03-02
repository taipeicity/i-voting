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
 * Surveys_count model.
 *
 */
class SurveyforceModelSurveys_count extends JModelList
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
		$query->select("*");
        $query->from($db->quoteName('#__survey_force_survs_release'));
		$query->where("stage >= 5");
		$query->order("vote_start");
		
        $db->setQuery($query);

        return $db->loadObjectList();
    }
	
	
	/**
     * 取得所有議題數量
     * 
	 * @param	int $filter_year  篩選年份
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  int
	 */
    public function getVoteTotalSurvey($filter_year, $filter_survey_ids) {
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
		$query->select("survey_id, COUNT(*)");
        $query->from($db->quoteName('#__survey_force_vote'));
		$query->group("survey_id");
		
		// 篩選年份
		$query->where('created >= ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year - 1)));
		$query->where('created < ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year)));
		
		// 要排除的議題編號
		if ($filter_survey_ids) {
			$query->where('survey_id NOT IN (' . implode(",", $filter_survey_ids) . ')');
		}

        $db->setQuery($query);

        return count($db->loadObjectList());
    }
	
	
	/**
     * 取得所有票號的統計 = 總投票人數
     * 
	 * @param	int $filter_year  篩選年份
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  int
	 */
    public function getVoteTotalTicket($filter_year, $filter_survey_ids) {
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
		$query->select("COUNT(*)");
        $query->from($db->quoteName('#__survey_force_vote'));
		
		// 篩選年份
		$query->where('created >= ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year - 1)));
		$query->where('created < ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year)));
		
		// 要排除的議題編號
		if ($filter_survey_ids) {
			$query->where('survey_id NOT IN (' . implode(",", $filter_survey_ids) . ')');
		}

        $db->setQuery($query);

        return $db->loadResult();
    }
	
	

	/**
     * 取得所有驗證方式
     * 
	 * @param	string $api_type  API類別
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  array 最新消息資料
	 */
	public function getAllVerifyTypes() {
        $db = JFactory::getDBO();
		
        $db->setQuery("SELECT * FROM `#__extensions` WHERE `type` = 'plugin' AND `access` = '1' AND `enabled` = '1' AND `folder` = 'verify' ORDER BY `ordering`");

        return $db->loadAssocList('element', 'name');
    }
	
	
	
	/**
     * 取得所有驗證方式的統計
     * 
	 * @param	string $api_type  API類別
	 * @param	int $filter_year  篩選年份
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  array
	 */
    public function getVerifyCount($api_type, $filter_year, $filter_survey_ids) {
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select('verify_type, SUM(count) as total');
		$query->group('verify_type');
		
		if ($api_type) {	// 取得API的資料
			$query->from('#__survey_force_vote_verify_count_api');
			$query->where('api = ' . $db->quote($api_type));
		} else {
			$query->from('#__survey_force_vote_verify_count');
		}

		// 篩選年份
		$query->where('created >= ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year - 1)));
		$query->where('created < ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year)));
		
		// 要排除的議題編號
		if ($filter_survey_ids) {
			$query->where('survey_id NOT IN (' . implode(",", $filter_survey_ids) . ')');
		}

        $db->setQuery($query);

        return $db->loadAssocList('verify_type', 'total');
    }

	
	/**
     * 取得指定性別的總計
     * 
	 * @param	int $sex  性別
	 * @param	string $api_type  API類別
	 * @param	int $filter_year  篩選年份
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  int 總計
	 */
    public function getSexCount($sex, $api_type, $filter_year, $filter_survey_ids) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		
		if ($api_type) {	// 取得API的資料
			$query->from('#__survey_force_analyze_sex_api');
			$query->where('api = ' . $db->quote($api_type));
		} else {
			$query->from('#__survey_force_analyze_sex');
		}
		
		$query->where('sex = ' . $db->quote($sex));
		
		// 篩選年份
		$query->where('created >= ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year - 1)));
		$query->where('created < ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year)));
		
		// 要排除的議題編號
		if ($filter_survey_ids) {
			$query->where('survey_id NOT IN (' . implode(",", $filter_survey_ids) . ')');
		}

		$db->setQuery($query);
		$db->execute();

		 return count($db->loadObjectList());
	}
   
	
	/**
     * 取得指定議題編號的年齡分析
     * 
	 * @param	string $api_type  API類別
	 * @param	int $filter_year  篩選年份
	 * @param	array $filter_survey_ids  要排除的議題編號
	 * 
	 * @return  int 總計
	 */
    public function getAgeCount($api_type, $filter_year, $filter_survey_ids) {

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('age, count(*) as total');
		$query->group('age');
		
		if ($api_type) {	// 取得API的資料
			$query->from('#__survey_force_analyze_age_api');
			$query->where('api = ' . $db->quote($api_type));
		} else {
			$query->from('#__survey_force_analyze_age');
		}
		
		// 篩選年份
		$query->where('created >= ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year - 1)));
		$query->where('created < ' . $db->quote(sprintf("%d-12-31 16:00:00", $filter_year)));
		
		// 要排除的議題編號
        if ($filter_survey_ids) {
			$query->where('survey_id NOT IN (' . implode(",", $filter_survey_ids) . ')');
		}

        $db->setQuery($query);

        return $db->loadAssocList('age', 'total');
    }
	
}
