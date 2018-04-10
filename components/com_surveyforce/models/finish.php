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
 * Finish Model.
 */
class SurveyforceModelFinish extends JModelItem {

	public function __construct() {
		parent::__construct();
	}

	public function populateState() {
		$app    = JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();

		$survey_id = $jinput->getInt('sid', 0);

		$this->setState('survey.id', $survey_id);
		$this->setState('params', $params);
	}

	public function getSurveyParams() {
		$app    = JFactory::getApplication();
		$params = $app->getParams();

		return $params;
	}

	public function getSurveyConfig() {

		$params = JComponentHelper::getParams('com_surveyforce');

		return $params;
	}


	// 新增投票通知-郵件
	public function insertNoticeEmail($_survey_id, $_email, $_type) {
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// 檢查是否已有記錄，若無才記錄
		$query->select('id');
		$query->from('#__survey_force_email_notice');
		$query->where('survey_id = ' . $db->quote($_survey_id));
		$query->where('email = ' . $db->quote($_email));
		$query->where('type = ' . $db->quote($_type));
		$db->setQuery($query);
		if ($db->loadResult()) {
			return false;
		}

		$query   = $db->getQuery(true);
		$created = JFactory::getDate()->toSql();

		$columns = array ('survey_id', 'email', 'type', 'created');

		$values = array (
			$db->quote($_survey_id), $db->quote($_email), $db->quote($_type), $db->quote($created)
		);

		$query->insert($db->quoteName('#__survey_force_email_notice'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);

		if ($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);

			return false;
		}
	}


	// 新增投票通知-手機
	public function insertNoticePhone($_survey_id, $_phone, $_type) {
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// 檢查是否已有記錄，若無才記錄
		$query->select('id');
		$query->from('#__survey_force_phone_notice');
		$query->where('survey_id = ' . $db->quote($_survey_id));
		$query->where('phone = ' . $db->quote($_phone));
		$query->where('type = ' . $db->quote($_type));
		$db->setQuery($query);
		if ($db->loadResult()) {
			return false;
		}


		$query   = $db->getQuery(true);
		$created = JFactory::getDate()->toSql();

		$columns = array ('survey_id', 'phone', 'type', 'created');

		$values = array (
			$db->quote($_survey_id), $db->quote($_phone), $db->quote($_type), $db->quote($created)
		);

		$query->insert($db->quoteName('#__survey_force_phone_notice'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);

		if ($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);

			return false;
		}
	}

	public function getItem() {
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
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();
	}

	function getVoteDetail($ticket = '', $surv_id = '') {

		if ($ticket == '' || $surv_id == '') {
			return false;
		}

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("*");
		$query->from($db->quoteName('#__survey_force_vote_detail'));
		$query->where($db->quoteName('ticket_num') . " = " . $db->quote($ticket));
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($surv_id));

		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (!$items) {
			return false;
		}

		$survry    = $this->getSurvey($items[0]->survey_id);
		$questions = $this->getQuestions($items[0]->survey_id);
		$sub_field = $this->getSubField();

		foreach ($items as $key => $item) {
			$items[$key]->surv_title      = $survry->surv_title;
			$items[$key]->quest_title     = $questions[$item->field_id]->quest_title;
			$items[$key]->question_type   = $questions[$item->field_id]->question_type;
			$items[$key]->field_title     = $questions[$item->field_id]->field_title;
			$items[$key]->sub_field_title = $sub_field[$item->sub_field_id]->sub_field_title;
		}

		return $items;
	}

	function getSurvey($survey_id) {

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("id, title AS surv_title");
		$query->from($db->quoteName('#__survey_force_survs'));
		$query->where($db->quoteName('id') . " = " . $db->quote($survey_id));

		$db->setQuery($query);

		return $db->loadObject();
	}

	function getQuestions($survey_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("q.id AS qid, q.sf_qtext AS quest_title, q.question_type,f.id AS fid, f.ftext AS field_title");
		$query->from($db->quoteName('#__survey_force_quests', 'q'));
		$query->leftJoin($db->quoteName('#__survey_force_fields', 'f') . " ON f.quest_id = q.id");
		$query->where($db->quoteName('sf_survey') . " = " . $db->quote($survey_id));

		$db->setQuery($query);

		return $db->loadObjectList("fid");
	}

	function getSubField() {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("id AS sfid, title AS sub_field_title");
		$query->from($db->quoteName('#__survey_force_sub_fields'));

		$db->setQuery($query);

		return $db->loadObjectList("sfid");
	}

	function is_lottery($_ticket_num, $_survey_id) {

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		try {

			$db->transactionStart();

			// Fields to update.
			$fields = array (
				$db->quoteName('is_lottery') . ' = ' . $db->quote(1),
			);

			// Conditions for which records should be updated.
			$conditions = array (
				$db->quoteName('survey_id') . ' = ' . $db->quote($_survey_id),
				$db->quoteName('ticket_num') . ' = ' . $db->quote($_ticket_num),
			);

			$query->update($db->quoteName('#__survey_force_vote'));
			$query->set($fields);
			$query->where($conditions);

			$db->setQuery($query);
			$db->execute();

			$db->transactionCommit();

			return true;

		} catch (Exception $e) {

			// catch any database errors.
			$db->transactionRollback();
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);

			return false;
		}

	}

}
