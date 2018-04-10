<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceModelSurveys extends JModelList {

	public function __construct($config = array ()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array (
				'id', 's.id', 'title', 's.title', 'published', 's.published', 'ordering', 's.ordering', 'publish_up',
				's.publish_up', 'publish_down', 's.publish_down', 'vote_start', 's.vote_start', 'vote_end',
				's.vote_end', 'u.name', 'ut.title', 'is_public', 's.is_public',
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$is_public = $this->getUserStateFromRequest($this->context . '.filter.public', 'filter_public', '', 'string');
		$this->setState('filter.public', $is_public);

		$own = $this->getUserStateFromRequest($this->context . '.filter.own', 'filter_own', '', 'string');
		$this->setState('filter.own', $own);

		// List state information.
		parent::populateState('s.id', 'desc');
	}

	protected function getListQuery() {
		$user          = JFactory::getUser();
		$user_id       = $user->get('id');
		$unit_id       = $user->get('unit_id');
		$cross_unit    = $user->get('cross_unit'); // 跨單位
		$params        = JComponentHelper::getParams('com_surveyforce');
		$print         = $params->get('print'); // 取得議題列印群組ID
		$show_result   = $params->get('show_result'); // 取得觀看結果群組ID
		$export_result = $params->get('export_result'); // 取得匯出結果群組ID
		$isSuperAdmin  = JFactory::getUser()->authorise('core.admin');
		$cross_branch  = false;
		if (in_array($print, $user->groups) || in_array($show_result, $user->groups) || in_array($export_result, $user->groups)) {
			$cross_branch = true;
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('s.`id`, s.`title`, s.`is_public`, s.`published`, s.is_complete, s.is_checked, s.vote_pattern, s.publish_up, s.publish_down, s.vote_start, s.vote_end, s.created_by, s.is_lottery, s.is_place, s.verify_type, s.verify_params, s.display_result, s.is_analyze');
		$query->from('`#__survey_force_survs` as s');

		$query->select('u.unit_id, u.name as create_name');
		$query->join('LEFT', '#__users AS u ON u.id = s.created_by');
		$query->select('ut.title as unit_title');
		$query->join('LEFT', '#__unit AS ut ON ut.id = u.unit_id');

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%' . $db->Escape($search, true) . '%');
			$query->where('s.`title` LIKE ' . $search);
		}


		// Filter by published state
		$is_public = $this->getState('filter.public');
		if (is_numeric($is_public)) {
			$query->where('s.is_public = ' . (int) $is_public);
		} elseif ($is_public === '') {
			$query->where('(s.is_public IN (0, 1))');
		}

		// Filter
		$own = $this->getState('filter.own');
		if ($own == 1) {
			$query->where('u.id = ' . (int) $user_id);
		} else if ($own == 2) {
			$query->where('u.unit_id = ' . (int) $unit_id);
		} else {
			if (!$isSuperAdmin) {
				$isManager = 0;
				$groups    = self::getGroupLevel();
				$self_gps  = JUserHelper::getUserGroups($user->get('id'));
				foreach ($self_gps as $sgp) {
					if ($groups[$sgp]->title == "系統管理者") {
						$isManager = 1;
						break;
					}
				}
			}

			if (!($isSuperAdmin or $isManager) && !$cross_branch && $cross_unit == 0) {
				$query->where('u.unit_id = ' . (int) $unit_id);
			}
		}

		$orderCol  = $this->state->get('list.ordering', 's.`id`');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function getUnits() {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__unit AS a');
		$query->where('a.state = 1');
		$query->order('a.ordering ASC, a.id ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	function delete($cid) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__survey_force_survs');
		$query->where('id IN (' . implode(',', $cid) . ')');
		$db->setQuery($query);
	}

	public function publish($cid, $value = 1) {
		$database = JFactory::getDBO();
		$task     = JFactory::getApplication()->input->getCmd('task');
		$state    = ($task == 'publish') ? 1 : 0;

		if (!is_array($cid) || count($cid) < 1) {
			$action = ($task == 'publish') ? 'publish' : 'unpublish';
			echo "<script> alert('" . JText::_('COM_SURVEYFORCE_SELECT_AN_ITEM_TO') . " $action'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode(',', $cid);

		$query = "UPDATE #__survey_force_survs" . "\n SET published = " . intval($state) . "\n WHERE id IN ( $cids )";
		$database->setQuery($query);
		if (!$database->execute()) {
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		return true;
	}

	public function getGroupLevel() {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)->select('a.*, COUNT(DISTINCT b.id) AS level')->from($db->quoteName('#__usergroups') . ' AS a')->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')->group('a.id, a.title, a.lft, a.rgt, a.parent_id')->order('a.lft ASC');
		$db->setQuery($query);
		$groups = $db->loadObjectList("id");

		return $groups;
	}

	// 取得題目清單
	public function getQuestions() {
		$app        = JFactory::getApplication();
		$_survey_id = $app->input->getInt('id');

		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.sf_qtext, b.ftext');
		$query->from($db->quoteName('#__survey_force_quests') . ' AS a');
		$query->leftJoin($db->quoteName('#__survey_force_fields') . 'AS b ON b.quest_id = a.id');
		$query->where('a.sf_survey = ' . (int) $_survey_id);
		$query->where('a.published = 1');
		$query->order('a.ordering ASC, b.ordering ASC');


		$db->setQuery($query);

		return $db->loadObjectList();
	}

	// 取得選項清單
	public function getOptions() {
		$app      = JFactory::getApplication();
		$quest_id = $app->input->getInt('id');

		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_fields') . ' AS a');
		$query->where('a.quest_id = ' . (int) $quest_id);
		$query->order('a.ordering ASC');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getPreviewQuestions($items = null) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.sf_survey');
		$query->from($db->quoteName('#__survey_force_quests') . ' AS a');
		$query->where('a.published = 1');
		$query->order('a.sf_survey DESC');

		$db->setQuery($query);
		$rows = $db->loadObjectList('sf_survey');

		foreach ($items as $key => $item) {
			if ($rows[$item->id]) {
				$items[$key]->questions = 1;
			}else{
				$items[$key]->questions = 0;
			}
		}

		return $items;
	}

}
