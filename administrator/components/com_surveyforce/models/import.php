<?php

/**
*   @package         Surveyforce
*   @version           1.0-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Import model.
 *
 */
class SurveyforceModelImport extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }


    protected function getListQuery() {

	}

	// 取得題目及選項
	public function getQuestions($_survey_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('f.id as option_id, f.ftext as option_title');
		$query->from('#__survey_force_fields AS f');

		$query->select('q.id as question_id, q.sf_qtext as question_title');
		$query->join('LEFT', '#__survey_force_quests AS q ON q.id = f.quest_id');
		$query->where('q.sf_survey = '. $db->quote($_survey_id));
		$query->where('q.published = 1');
		$query->order('q.ordering ASC, f.ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
 

	// 取得子選項
	public function getSubOptions($_question_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('sf.id as sub_option_id, sf.title as sub_option_title');
		$query->from('#__survey_force_sub_fields AS sf');
		$query->where('sf.quest_id = '. $db->quote($_question_id));
		$query->order('sf.ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}


	// 刪除紙本投票
	public function deletePaperVote($_surv_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$conditions = array(
			$db->quoteName('survey_id') . ' = ' . $db->quote($_surv_id)
		);

		$query->delete($db->quoteName('#__survey_force_vote_paper'));
		$query->where($conditions);

		$db->setQuery($query);


		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法刪除：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}



	// 儲存紙本投票
	public function savePaperVote($_surv_id, $_question_id, $_option_id, $_sub_option_id, $vote, $_created) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$columns = array('survey_id', 'question_id', 'field_id', 'sub_field_id', 'vote_num', 'created');

		$values = array(
			$db->quote($_surv_id),
			$db->quote($_question_id),
			$db->quote($_option_id),
			$db->quote($_sub_option_id),
			$db->quote($vote),
			$db->quote($_created)
		);

		$query->insert($db->quoteName('#__survey_force_vote_paper'));
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


	// 取得紙本投票
	public function getPaperVote($_surv_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$query->select('*');
		$query->from('#__survey_force_vote_paper AS a');
		$query->where($db->quoteName('a.survey_id') . ' = ' . $db->quote($_surv_id));
		$query->order('a.question_id ASC, a.field_id ASC, a.sub_field_id ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	// 取得紙本投票的選項票數
	public function getPaperVoteByOption($_surv_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$query->select('*');
		$query->from('#__survey_force_vote_paper AS a');
		$query->where($db->quoteName('a.survey_id') . ' = ' . $db->quote($_surv_id));
		$query->where('a.sub_field_id = 0');
		$query->order('a.question_id ASC, a.field_id ASC, a.sub_field_id ASC');

		$db->setQuery($query);
		$rows = $db->loadAssocList('field_id', 'vote_num');

		return $rows;
	}

	// 取得紙本投票的子選項票數
	public function getPaperVoteBySubOption($_surv_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$query->select('*');
		$query->select('CONCAT(field_id, sub_field_id) AS new_field');
		$query->from('#__survey_force_vote_paper AS a');
		$query->where($db->quoteName('a.survey_id') . ' = ' . $db->quote($_surv_id));
		$query->where('a.sub_field_id > 0');
		$query->order('a.question_id ASC, a.field_id ASC, a.sub_field_id ASC');

		$db->setQuery($query);
		$rows = $db->loadAssocList('new_field', 'vote_num');

		return $rows;
	}


	// 儲存紙本投票總覽
	public function savePaperVoteSummary($_surv_id, $_code, $_created, $created_by, $_created_username) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$columns = array('survey_id', 'code', 'created', 'created_by', 'created_username');

		$values = array(
			$db->quote($_surv_id),
			$db->quote($_code),
			$db->quote($_created),
			$db->quote($created_by),
			$db->quote($_created_username)
		);

		$query->insert($db->quoteName('#__survey_force_vote_paper_summary'));
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

	// 儲存紙本投票紀錄
	public function savePaperVoteRecord($_surv_id, $_code, $_question_id, $_option_id, $_sub_option_id, $vote, $_created) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$columns = array('survey_id', 'code', 'question_id', 'field_id', 'sub_field_id', 'vote_num', 'created');

		$values = array(
			$db->quote($_surv_id),
			$db->quote($_code),
			$db->quote($_question_id),
			$db->quote($_option_id),
			$db->quote($_sub_option_id),
			$db->quote($vote),
			$db->quote($_created)
		);

		$query->insert($db->quoteName('#__survey_force_vote_paper_record'));
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
	
	// 取得紙本投票總覽
	public function getPaperVoteSummary($_surv_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$query->select('*');
		$query->from('#__survey_force_vote_paper_summary AS a');
		$query->where($db->quoteName('a.survey_id') . ' = ' . $db->quote($_surv_id));
		$query->order('a.created ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
	
	// 取得紙本投票紀錄
	public function getPaperVoteRecord($_surv_id, $_code) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);


		$query->select('*');
		$query->from('#__survey_force_vote_paper_record AS a');
		$query->where($db->quoteName('a.survey_id') . ' = ' . $db->quote($_surv_id));
		$query->where($db->quoteName('a.code') . ' = ' . $db->quote($_code));
		$query->order('a.question_id ASC, a.field_id ASC, a.sub_field_id ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
	
	

	// 刪除紙本投票紀錄總覽
	public function updatePaperVote($_surv_id, $_question_id, $_option_id, $_sub_option_id, $_vote_num, $_created) {		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$fields = array(
			$db->quoteName('vote_num') . ' = `vote_num` - ' . $db->quote($_vote_num),
			$db->quoteName('created') . ' = ' . $db->quote($_created)
		);
	
		$conditions = array(
			$db->quoteName('survey_id') . ' = ' . $db->quote($_surv_id),
			$db->quoteName('question_id') . ' = ' . $db->quote($_question_id),
			$db->quoteName('field_id') . ' = ' . $db->quote($_option_id),
			$db->quoteName('sub_field_id') . ' = ' . $db->quote($_sub_option_id),
		);

		$query->update($db->quoteName('#__survey_force_vote_paper'))->set($fields)->where($conditions);

		$db->setQuery($query);
		

		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}
	

	
	// 刪除紙本投票紀錄總覽
	public function deletePaperVoteRecord($_surv_id, $_code) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$conditions = array(
			$db->quoteName('survey_id') . ' = ' . $db->quote($_surv_id),
			$db->quoteName('code') . ' = ' . $db->quote($_code)
		);

		$query->delete($db->quoteName('#__survey_force_vote_paper_record'));
		$query->where($conditions);

		$db->setQuery($query);


		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法刪除：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}
	

	
	// 刪除紙本投票紀錄總覽
	public function deletePaperVoteSummary($_surv_id, $_code) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$conditions = array(
			$db->quoteName('survey_id') . ' = ' . $db->quote($_surv_id),
			$db->quoteName('code') . ' = ' . $db->quote($_code)
		);

		$query->delete($db->quoteName('#__survey_force_vote_paper_summary'));
		$query->where($conditions);

		$db->setQuery($query);


		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法刪除：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}
	
	
}