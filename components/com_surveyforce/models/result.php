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
 * Result Model.
 */
class SurveyforceModelResult extends JModelItem {

	public function __construct()
	{
		parent::__construct();
	}

	public function populateState()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params	= $app->getParams();

		$survey_id	= $jinput->getInt('sid', 0);

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
	
	// 議題
	public function getItem() {
		$app = JFactory::getApplication();

		$id = $this->state->get('survey.id');

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
	
	// 選項
	public function getFields($orderby = 0) {
		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');
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
		$sid = $this->state->get('survey.id');
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
		$sid = $this->state->get('survey.id');
		$qtype = array("select", "number", "table");
		$db  = $this->getDBO();
		
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('#__survey_force_vote_count as d');
		$query->where($db->quoteName('d.survey_id') . " = " . $db->quote($sid));
		$query->order("d.question_id , d.field_id");

		$db->setQuery($query);
		$items = $db->loadObjectList();

				
		$results = array(); $quest = "";
		foreach($items as $key => $item) {
			if($quest != $item->question_id) {
				$quest = $item->question_id;
				$results[$quest] = new stdClass();
				$results[$quest]->quest_title 	= $item->question_title;
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
		$sid = $this->state->get('survey.id');
		$db  = $this->getDBO();

		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('#__survey_force_vote_sub_count as d');
		$query->where($db->quoteName('d.survey_id') . " = " . $db->quote($sid));

		$db->setQuery($query);
		$items = $db->loadObjectList();


		$results = array(); $quest = "";
		foreach($items as $key => $item) {
			$index = $item->field_id . "_" . $item->sub_field_id;
			$results[$index] = new stdClass();
			$results[$index]->field_id 		= $item->field_id;
			$results[$index]->sub_field_id 	= $item->sub_field_id;
			$results[$index]->sub_field_title 	= $item->sub_field_title;
			$results[$index]->count 			= $item->count;

		}

		return $results;

	}

	// 紙本投票結果
	public function getPaperResults() {
		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');
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
		$sid = $this->state->get('survey.id');
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


	// 現地投票結果
	public function getPlaceResults() {
		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');
		$db = $this->getDBO();

		$query = $db->getQuery(true);

		$query->select("*");
		$query->from($db->quoteName('#__survey_force_vote_place'));
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

	// 子選項現地投票結果
	public function getPlaceSubResults() {
		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');
		$db = $this->getDBO();

		$query = $db->getQuery(true);

		$query->select("*");
		$query->from($db->quoteName('#__survey_force_vote_place'));
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


	public function getResultsTime() {
		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');
		$db  = $this->getDBO();

		$query = $db->getQuery(true);
		$query->select("created");
		$query->from('#__survey_force_vote_count as d');
		$query->where($db->quoteName('d.survey_id') . " = " . $db->quote($sid));

		$db->setQuery($query);
		$row = $db->loadResult();

		return $row;

	}

	public function getQuestField($option_answers){

		$app = JFactory::getApplication();
		$sid = $this->state->get('survey.id');

		$qtype = array ("select", "number", "table");
		//練習模式取得題目及選項
		require_once(JPATH_COMPONENT . '/models/question.php');
		$Questions = SurveyforceModelQuestion::getQuestions($sid);
		foreach ($Questions as $num => $qid) {

			$Question = SurveyforceModelQuestion::getQuestion($qid->id); //題目名稱：$Question->sf_qtext
			$Options  = SurveyforceModelQuestion::getOptions($qid->id); //選項名稱：$Options->ftext

			$options[$Question->id]              = new stdClass(); //仿照正式區的資料格式存入資料
			$options[$Question->id]->quest_title = $Question->sf_qtext;
			$options[$Question->id]->quest_type  = $Question->question_type;
			foreach ($Options as $Option) {
				$options[$Question->id]->field_title[$Option->id] = $Option->ftext;
			}
			//依據不同題目類型寫入題目及選項資料
			if (in_array($Question->question_type, $qtype)) {  //有子選項
				$SubOptions = SurveyforceModelQuestion::getSubOptions($qid->id); //子選項名稱：$SubOption->title
				foreach ($Options as $serial => $Option) {
					foreach ($SubOptions as $SubOption) {
						$join = $option_answers[$qid->id][$serial]["field_id"] . "_" . $option_answers[$qid->id][$serial]["sub_field_id"];
						$OSO  = $Option->id . "_" . $SubOption->id;
						if ($OSO == $join) {
							$sub_options[$join]                  = new stdClass();
							$sub_options[$join]->field_id        = $Option->id;
							$sub_options[$join]->sub_field_id    = $SubOption->id;
							$sub_options[$join]->sub_field_title = $SubOption->title;
							$sub_options[$join]->count           = "1";
						}
					}
				}
			} else { //無子選項
				foreach ($options[$Question->id]->field_title as $field_id => $field_title) {
					foreach ($option_answers[$Question->id] as $field) {
						if ($field["field_id"] == $field_id) {
							$options[$Question->id]->count[$field_id] = "1";
						}
					}
				}
			}
		}

		$items["options"] = $options;
		$items["sub_options"] = $sub_options;


		return $items;

	}

	public function getRank(){

		$sid = $this->state->get('survey.id');

		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_vote_total_count'));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($sid));
		$query->where($db->quoteName('sub_field_id') . ' = ' . $db->quote(0));

		$db->setQuery($query);

		$rows =  $db->loadObjectList();

		foreach ($rows as $row) {
			$rank[$row->field_id] = $row->rank;
		}

		return $rank;
	}

	public function getRankSub(){

		$sid = $this->state->get('survey.id');

		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_vote_total_count'));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($sid));
		$query->where($db->quoteName('sub_field_id') . ' > ' . $db->quote(0));

		$db->setQuery($query);
		$rows =  $db->loadObjectList();

		foreach ($rows as $row) {
			$sub_rank[$row->field_id][$row->sub_field_id] = $row->rank;
		}

		return $sub_rank;
	}
}
