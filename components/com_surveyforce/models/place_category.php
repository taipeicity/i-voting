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
class SurveyforceModelPlace_category extends JModelItem {

	public function __construct() {
		parent::__construct();
	}

	public function populateState() {
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$this->setState('params', $params);
	}

	public function getSurveyParams() {
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $params;
	}

	public function getItems() {
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_complete = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.is_place = 1');
		
		// Join Unit
		$query->select('ut.title as unit_title');
		$query->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');


		// Filter by publish
		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// 進行中
		$query->where('a.vote_start <= ' . $nowDate);
		$query->where('a.vote_end >= ' . $nowDate);

		// order by
		$query->order('a.vote_start desc');

		$db->setQuery($query);

		return $db->loadObjectList();
	}


}
