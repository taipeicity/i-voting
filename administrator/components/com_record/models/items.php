<?php
/**
 * 紀錄管理 - 清單DB Model
 * 
 * @version    CVS: 1.0.0
 * @package    com_record
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Methods supporting a list of item records.
 *
 * @package com_record
 */
class RecordModelItems extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'type_id', 'a.type_id',
				'state', 'a.state',
				'survey_id', 'a.survey_id',
				'request_time', 'a.request_time',
				'response_time', 'a.response_time',
				'execute_second', 'a.execute_second',
				'created', 'a.created',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$type_id = $this->getUserStateFromRequest($this->context . '.filter.type_id', 'filter_type_id', '');
		$this->setState('filter.type_id', $type_id);
		
		$survey_id = $this->getUserStateFromRequest($this->context . '.filter.survey_id', 'filter_survey_id', '');
		$this->setState('filter.survey_id', $survey_id);

		$start_time = $this->getUserStateFromRequest($this->context . '.filter.start_time', 'filter_start_time', '');
		$this->setState('filter.start_time', $start_time);

		$end_time = $this->getUserStateFromRequest($this->context . '.filter.end_time', 'filter_end_time', '');
		$this->setState('filter.end_time', $end_time);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_record');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.request_time', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->select('CASE a.state WHEN "1" THEN "成功" ELSE "失敗" END AS state_title');
		$query->select('CASE a.survey_id WHEN "0" THEN "" ELSE a.survey_id END AS survey_id');
		$query->from($db->quoteName('#__api_record') . ' AS a');

		$query->select('art.title AS type_title')
			->join('LEFT', $db->quoteName('#__api_record_type'). ' AS art ON art.id = a.type_id');


		// 篩選類型
		$type_id = $this->getState('filter.type_id');
		if (is_numeric($type_id)) {
			$query->where('a.type_id = ' . (int) $type_id);
		}

		// 篩選狀態
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		
		// 篩選議題
		$survey_id = $this->getState('filter.survey_id');
		if (is_numeric($survey_id)) {
			$query->where('a.survey_id = ' . (int) $survey_id);
		}
		
		// 篩選開始時間
		$start_time = $this->getState('filter.start_time');
		if ($start_time) {
			$query->where('a.request_time >= ' . $db->quote(sprintf("%s 16:00:00", strtotime($start_time) - 86400)));
		}
		
		// 篩選結束時間
		$end_time = $this->getState('filter.end_time');
		if ($end_time) {
			$query->where('a.request_time < ' . $db->quote(sprintf("%s 16:00:00", $end_time)));
		}

		// 搜尋關鍵字
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.msg LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
        return $query;
		
	}
	
}
