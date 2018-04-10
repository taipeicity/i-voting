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
 * Print model.
 *
 */
class SurveyforceModelPrint extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }


    protected function getListQuery() {

	}


	// 取得全部的題目
	public function getQuestions($_survey_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_quests');
		$query->where('sf_survey = '. $db->quote($_survey_id));
		$query->where('published = 1');
		$query->order('ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	public function getQuestionTypeName( $_type ) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('name');
		$query->from('#__extensions AS a');
		$query->where("a.type = 'plugin'");
		$query->where("a.folder = 'survey'");
		$query->where("a.element = ". $db->quote($_type) );

		$db->setQuery($query);
		
		return $db->loadResult();

	}


	// 取得選項
	public function getOptions($_question_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_fields');
		$query->where('quest_id = '. $db->quote($_question_id));
		$query->order('ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}


}