<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Category Model.
 *
 */
class SurveyforceModelCategory extends JModelList {

	public function __construct() {
		$this->database = JFactory::getDbo();
		parent::__construct();

	}

	public function populateState() {
		$params = JFactory::getApplication()->getParams();
		$jinput = JFactory::getApplication()->input;

		$id = $jinput->get('id', 0, 'INT');

		$this->setState('list.limit', 6);

		$value = JRequest::getInt('limitstart', 0);
		$this->setState('list.start', $value);

		$this->setState('category.id', $id);
		$this->setState('params', $params);

	}

	public function getListQuery() {

	}

	public function getSurveyItems() {

		$app    = JFactory::getApplication();
		$params = $app->getParams();

		$vote_cat = $params->get('vote_cat');
		$orderby  = $params->get('orderby', 'vote_end ASC');

		$session = &JFactory::getSession();


		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');

		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$query->order('a.' . $orderby);

		if ($session->get('practice_pattern')) {
			$vote_cat = 1;
		}

		switch ($vote_cat) {
			case 0:   // 提案資料內容
				$condition = $session->get('soon.radio', 1);
				if ($condition == 1 || $condition == 2) {
					$query->where($db->quoteName('a.is_define') . ' = ' . $db->quote(0));
					$query->where($db->quoteName('a.proposal_process') . ' = ' . $db->quote($condition));
				} else {
					$query->where($db->quoteName('a.is_define') . ' = ' . $db->quote(1));
				}
				$query->where($db->quoteName('a.vote_start') . ' > ' . $nowDate);
				break;

			case 1:   // 進行中
				$query->where('a.is_define = 1');
				$query->where('a.vote_start <= ' . $nowDate);
				$query->where('a.vote_end >= ' . $nowDate);
				break;

			case 2:   // 歷史區投票                
				if ($session->get('completed.search') == null && $session->get('completed.radio') == null) {
					$query->setLimit(3);
				} else {
					if ($session->get('completed.search')) {
						$query->where('a.title LIKE ' . $db->quote('%' . $session->get('completed.search') . '%'));
					} else {
						if (preg_match("/define|undefine/", $session->get('completed.radio'))) {
							$is_define = array ("define" => "1", "undefine" => "0");
							$query->where('a.is_define = ' . $db->quote($is_define[$session->get('completed.radio')]));
						} else {
							$query->where('YEAR(a.vote_end) - ' . $db->quote($session->get('completed.radio')) . ' >= ' . $db->quote(0));
							$query->where($db->quote($session->get('completed.radio')) . ' - YEAR(a.vote_start) >= ' . $db->quote(0));
						}
					}
				}
				$query->where('CASE WHEN a.is_define = 1 THEN a.vote_end < ' . $nowDate . ' ELSE a.vote_start < ' . $nowDate . ' END');

				break;
			default:
				$query->where('a.vote_end < ' . $nowDate);
				break;
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;

	}

	// 取得最先的投票開始
	public function getVoteStart() {
		$app      = JFactory::getApplication();
		$params   = $app->getParams();
		$vote_cat = $params->get('vote_cat');
		$orderby  = $params->get('orderby', 'publish_up ASC');


		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.vote_start');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');


		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$query->where('a.vote_end < ' . $nowDate);

		$query->order('a.vote_start ASC');


		$db->setQuery($query);

		return $db->loadResult();

	}

	// 取得最後的投票結束
	public function getVoteEnd() {
		$app      = JFactory::getApplication();
		$params   = $app->getParams();
		$vote_cat = $params->get('vote_cat');
		$orderby  = $params->get('orderby', 'publish_up ASC');


		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.vote_end');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');


		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$query->where('a.vote_end < ' . $nowDate);

		$query->order('a.vote_end DESC');


		$db->setQuery($query);

		return $db->loadResult();

	}

	// 取得進行中的議題數
	public function getVotingCounts() {
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');
		$query->where('a.is_define = 1');


		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// Filter by vote date
		$query->where('a.vote_start <= ' . $nowDate);
		$query->where('a.vote_end >= ' . $nowDate);

		$db->setQuery($query);
		$count = $db->loadObjectList();

		return $count;

	}

	// 取得即將開始的議題數
	public function getSoonCounts() {
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');


		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// Filter by vote date
		$query->where('a.vote_start > ' . $nowDate);


		$db->setQuery($query);
		$count = $db->loadObjectList();

		return $count;

	}

	// 取得已完成的議題數
	public function getCompletedCounts() {
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_public = 1');


		// Join Unit
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');
		$query->select('ut.title as unit_title');


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		$query->where('CASE WHEN a.is_define = 1 THEN a.vote_end < ' . $nowDate . ' ELSE a.vote_start < ' . $nowDate . ' END');

		$db->setQuery($query);
		$count = $db->loadObjectList();

		return $count;

	}

	function getTimeDiff($begin_time, $end_time) {
		$starttime = $begin_time;
		$endtime   = $end_time;

		$timediff = $endtime - $starttime;
		$days     = intval($timediff / 86400);
		$remain   = $timediff % 86400;
		$hours    = intval($remain / 3600);
		$remain   = $remain % 3600;
		$mins     = intval($remain / 60);
		$secs     = $remain % 60;
		$res      = array ("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);

		return $res;

	}

}
