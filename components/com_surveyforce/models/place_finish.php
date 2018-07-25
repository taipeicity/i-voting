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
 * Place_finish Model.
 */
class SurveyforceModelPlace_finish extends JModelItem {

	public function __construct() {
		parent::__construct();
	}

	public function populateState() {
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();

		$survey_id = $params->get('survey_id');

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

	public function getItem() {
		$session = &JFactory::getSession();
		$id = $session->get('place_survey_id');

		$db = $this->getDbo();


		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs_release') . ' AS a');
		$query->where('a.id = ' . (int) $id);
		$query->where('a.published = 1');
		$query->where('a.is_complete = 1');
		$query->where('a.is_checked = 1');


		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();
	}

}
