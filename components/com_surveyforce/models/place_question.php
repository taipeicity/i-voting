<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Place_question Model.
 */
class SurveyforceModelPlace_question extends JModelItem {

	public function __construct()
	{
		parent::__construct();
	}

	public function populateState()
	{
		$session 	= &JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params	= $app->getParams();

		$survey_id	= $session->get('place_survey_id');

		$this->setState('survey.id', $survey_id);
		$this->setState('params', $params);
	}

    public function getSurveyParams() {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        return $params;
    }

    public function getSurveyConfig() {

        $params = JComponentHelper::getParams('com_surveyforce');

        return $params;
    }

	public function getSurvey() {
		$session 	= &JFactory::getSession();
		$id = $session->get('place_survey_id');

		$db		= $this->getDbo();


		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.id = '. (int) $id);
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


	public function getQuestions($_survey_id) {
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('a.id');
		$query->from($db->quoteName('#__survey_force_quests') . ' AS a');
		$query->where('a.sf_survey = '. (int) $_survey_id);
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');


		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getQuestion($_qid) {
		$app = JFactory::getApplication();
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_quests') . ' AS a');
		$query->where('a.id = '. (int) $_qid);
		$query->where('a.published = 1');

		$db->setQuery($query);

		return $db->loadObject();
	}


	public function getOptions($_qid) {
		$app = JFactory::getApplication();
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_fields') . ' AS a');
		$query->where('a.quest_id = '. (int) $_qid);
		$query->order('a.ordering ASC');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getOption($_option_id) {
		$app = JFactory::getApplication();
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_fields') . ' AS a');
		$query->where('a.id = '. (int) $_option_id);

		$db->setQuery($query);

		return $db->loadObject();
	}



	// 取得子選項清單
	public function getSubOptions($_qid) {
		$app = JFactory::getApplication();
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_sub_fields') . ' AS a');
		$query->where('a.quest_id = '. (int) $_qid);
		$query->order('a.ordering ASC');

		$db->setQuery($query);

		return $db->loadObjectList();
	}



	// 新增投票
	public function insertVote($_ticket_num, $_survey_id, $_created) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$columns = array('ticket_num', 'survey_id', 'created');

		$values = array(
			$db->quote($_ticket_num),
			$db->quote($_survey_id),
			$db->quote($_created)
		);

		$query->insert($db->quoteName('#__survey_force_vote'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);

		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}


	// 新增投票選項
	public function insertVoteDetail ($_ticket_num, $_survey_id, $_question_id, $_option, $_created) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$columns = array('ticket_num', 'survey_id', 'question_id', 'field_id', 'other', 'sub_field_id', 'is_place', 'created');

		$values = array(
			$db->quote($_ticket_num),
			$db->quote($_survey_id),
			$db->quote($_question_id),
			$db->quote($_option['field_id']),
			$db->quote($_option['other']),
			$db->quote($_option['sub_field_id']),
			1,
			$db->quote($_created)
		);

		$query->insert($db->quoteName('#__survey_force_vote_detail'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);

		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}


	// 更新議題的總投票數
	public function updateTotalVote($_survey_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->update('#__survey_force_survs');
		$query->set($db->quoteName('total_vote') . " = ". $db->quoteName('total_vote'). " + 1");
		$query->where($db->quoteName('id') . " = '{$_survey_id}'");


		$db->setQuery($query);

		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}


	// 新增投票Lock
	public function insertVoteLock($_survey_id, $_identify, $_verify_type) {
		$date = JFactory::getDate();
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$identify = JHtml::_('utility.endcode', $_identify);		// 有個資需加密

		$columns = array('survey_id', 'identify', 'verify_type', 'created');

		$values = array(
			$db->quote($_survey_id),
			$db->quote($identify),
			$db->quote($_verify_type),
			$db->quote($date->toSql())
		);

		$query->insert($db->quoteName('#__survey_force_vote_lock'));
		$query->columns($columns);
		$query->values(implode(',', $values));

		$db->setQuery($query);

		try {
			if ($db->execute()) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $e) {
			return false;
		}
	}


	// 刪除投票Lock
	public function deleteVoteLock($_survey_id, $_identify, $_verify_type) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$identify = JHtml::_('utility.endcode', $_identify);		// 有個資需加密

		$conditions = array(
			$db->quoteName('survey_id') . ' = ' . $db->quote($_survey_id),
			$db->quoteName('identify') . ' = ' . $db->quote($identify),
			$db->quoteName('verify_type') . ' = ' . $db->quote($_verify_type)
		);

		$query->delete($db->quoteName('#__survey_force_vote_lock'));
		$query->where($conditions);

		$db->setQuery($query);

		try {
			if ($db->execute()) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $e) {
			return false;
		}

	}


	// 取得分類清單
	public function getQuestionCats($_question_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from($db->quoteName('#__survey_force_quests_cat') . ' AS a');
		$query->where('a.question_id = '. (int) $_question_id);
		$query->order('a.ordering ASC');


		$db->setQuery($query);

		return $db->loadObjectList();
	}

	

}
