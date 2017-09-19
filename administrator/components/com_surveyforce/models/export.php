<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Export model.
 *
 */
class SurveyforceModelExport extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }


    public function getItem() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db  = $this->getDBO();
		
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs'));
		$query->where('id = '. (int) $sid);
		
		$db->setQuery($query);
		
		$surv = $db->loadObject();
		
		return $surv;
	}
	
	// 選項
	public function getFields($orderby = 0) {		
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db  = $this->getDBO();
		
		$query = $db->getQuery(true);
		$query->select("f.id, f.ftext, q.id AS qid");
		$query->from($db->quoteName('#__survey_force_fields', 'f'));
		$query->leftJoin($db->quoteName('#__survey_force_quests', 'q') . " ON q.id = f.quest_id");
		$query->where($db->quoteName('q.sf_survey') . " = " . $db->quote($sid));
		$query->where($db->quoteName('q.published') . " = '1'");
		
		// 依選項排序
		if($orderby == 0) {
			$query->order("qid, f.ordering");
		}
		
		$db->setQuery($query);
		$items = $db->loadObjectList("id");

		$fields = array();
		foreach($items as $key => $item) {
			$fields[$item->qid][$item->id] = $item->ftext;
		}
		
		return $fields;
	}
	
	// 子選項
	public function getSubFields($orderby = 0) {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(ture);
		$query->select("s.id, s.title, q.id AS qid");
		$query->from($db->quoteName('#__survey_force_sub_fields', 's'));
		$query->leftJoin($db->quoteName('#__survey_force_quests', 'q') . " ON q.id = s.quest_id");
		$query->where($db->quoteName('q.sf_survey') . " = " . $db->quote($sid));
		$query->where($db->quoteName('q.published') . " = '1'");
		
		// 依選項排序
		if($orderby == 0) {
			$query->order("qid, s.ordering");
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$sub_fields = array();
		foreach($items as $key => $item) {
			$sub_fields[$item->qid][$item->id] = $item->title;
		}
				
		return $sub_fields;
	}
	
	// 投票結果
	public function getResults() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$qtype = array("select", "number", "table");
		$db  = $this->getDBO();
		
		$query = $db->getQuery(true);
		$query->select("d.*, q.sf_qtext AS quest_title, q.question_type, f.ftext AS field_title, count(d.field_id) AS count");
		$query->from($db->quoteName('#__survey_force_vote_detail', 'd'));
		$query->leftJoin($db->quoteName('#__survey_force_quests', 'q') . " ON q.id = d.question_id");
		$query->leftJoin($db->quoteName('#__survey_force_fields', 'f') . " ON f.id = d.field_id");
		$query->where($db->quoteName('d.survey_id') . " = " . $db->quote($sid));
		$query->where($db->quoteName('q.published') . " = '1'");
		$query->group($db->quoteName('d.field_id'));
		$query->order("d.question_id , d.field_id");

		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$results = array(); $quest = "";
		foreach($items as $key => $item) {
			if($quest != $item->question_id) {
				$quest = $item->question_id;
				$results[$quest] = new stdClass();
				$results[$quest]->quest_title 	= $item->quest_title;
				$results[$quest]->quest_type 	= $item->question_type;
			}
			$results[$quest]->field_title[$item->field_id] 	= $item->field_title;
			if(!in_array($item->question_type,$qtype)) {
				$results[$quest]->count[$item->field_id] = $item->count;
			}
		}
	
		
		return $results;
	}
	
	// 子選項投票結果
	public function getSubResults() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select("d.field_id, d.sub_field_id, s.title AS sub_field_title, count(d.sub_field_id) AS count");
		$query->from($db->quoteName('#__survey_force_vote_detail', 'd'));
		$query->leftJoin($db->quoteName('#__survey_force_quests', 'q') . " ON q.id = d.question_id");
		$query->leftJoin($db->quoteName('#__survey_force_sub_fields', 's') . " ON s.id = d.sub_field_id");
		$query->where($db->quoteName('d.survey_id') . " = " . $db->quote($sid));
		$query->where($db->quoteName('q.question_type') . " IN ('select', 'number', 'table')");
		$query->where($db->quoteName('q.published') . " = '1'");
		$query->group("d.field_id, d.sub_field_id");
		$query->order("d.field_id , d.sub_field_id");
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		
		$subs = array();
		foreach($items as $key => $item) {
			$index = $item->field_id . "_" . $item->sub_field_id;
			$subs[$index] = new stdClass();
			$subs[$index]->field_id 		= $item->field_id;
			$subs[$index]->sub_field_id 	= $item->sub_field_id;
			$subs[$index]->sub_field_title 	= $item->sub_field_title;
			$subs[$index]->count 			= $item->count;
		}
		
		return $subs;
	}

	// 紙本投票結果
	public function getPaperResults() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select("*");
		$query->from($db->quoteName('#__survey_force_vote_paper'));
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
		$query->where($db->quoteName('sub_field_id') . " = '0'");
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$paper = array();
		foreach ($items as $key => $item) {
			$paper[$item->question_id][$item->field_id] = $item->vote_num;
		}
		
		return $paper;
	}
	
	// 子選項紙本投票結果
	public function getPaperSubResults() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select("*");
		$query->from($db->quoteName('#__survey_force_vote_paper'));
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
		$query->where($db->quoteName('sub_field_id') . " != '0'");

		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$sub_paper = array();
		foreach($items as $key => $item) {
			$sub_paper[$item->field_id][$item->sub_field_id] = $item->vote_num;
		}
		
		return $sub_paper;
	}
	
	// 總投票人數
	public function getTotalNum() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select("count(survey_id) as num");
		$query->from("#__survey_force_vote");
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
		$query->group("survey_id");
		
		$db->setQuery($query);
		$item = $db->loadObject();
		
		return $item->num;
	}

	public function getOpenResults() {
		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');
		$db = $this->getDBO();
		
		$query = $db->getQuery(true);
		
		$query->select("id, field_id, other");
		$query->from($db->quoteName('#__survey_force_vote_detail'));
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
		$query->where($db->quoteName('other') . " != ''");

		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		return $items;
	}
}