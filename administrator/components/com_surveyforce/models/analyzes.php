<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceModelAnalyzes extends JModelList {

	public function __construct($config = array ()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array (
				'id', 'title', 'state'
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$is_public = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
		$this->setState('filter.state', $is_public);


		// List state information.
		parent::populateState('id', 'desc');
	}

	protected function getStoreId($id = '') {
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery() {

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__survey_force_analyze_quests`');

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->Quote('%' . $db->Escape($search, true) . '%');
			$query->where('`title` LIKE ' . $search);
		}


		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('state = ' . (int) $state);
		} else if ($state === '') {
			$query->where('(state IN (0, 1))');
		}

		$orderCol  = $this->state->get('list.ordering', '`id`');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	function delete($cid) {

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->delete('#__survey_force_analyze_quests');
		$query->where('id IN (' . implode(',', $cid) . ')');

		$db->setQuery($query);
		$db->execute();

		return true;
	}

	public function publish($cid) {
		$database = JFactory::getDBO();
		$app      = JFactory::getApplication();
		$task     = $app->input->getCmd('task');
		$state    = ($task == 'publish') ? 1 : 0;

		if (!is_array($cid) || count($cid) < 1) {
			$action = ($task == 'publish') ? 'publish' : 'unpublish';
			echo "<script> alert('" . JText::_('COM_SURVEYFORCE_SELECT_AN_ITEM_TO') . " $action'); window.history.go(-1);</script>\n";
			exit();
		}

		$cids = implode(',', $cid);

		$query = "UPDATE #__survey_force_analyze_quests" . "\n SET state = " . intval($state) . "\n WHERE id IN ( $cids )";
		$database->setQuery($query);
		if (!$database->execute()) {
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		return true;
	}

	public function getFields($items) {

		$db = JFactory::getDBO();

		foreach ($items as $key => $item) {

			$query = $db->getQuery(true);

			$query->select('*');
			$query->from($db->quoteName('#__survey_force_analyze_fields'));
			$query->where($db->quoteName('quest_id') . ' = ' . $db->quote($item->id));

			$db->setQuery($query);

			if ($db->loadObject()) {
				$items[$key]->field = 1;
			} else {
				$items[$key]->field = 0;
			}
		}

		return $items;

	}


}
