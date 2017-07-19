<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Survey Model.
 */
class SurveyforceModelVerify_opt extends JModelItem {

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

	public function getItem() {
		$app = JFactory::getApplication();

//		$id	= $app->input->getInt('id', 0);
		$id = $this->state->get('survey.id');

		$db		= $this->getDbo();


		$query	= $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.id = '. (int) $id);
		$query->where('a.published = 1');
		$query->where('a.is_complete = 1');
		$query->where('a.is_checked = 1');

//		$query->select('c.title AS category_title')
//			->join('LEFT', $db->quoteName('#__categories'). ' AS c ON c.id = a.catid');


		// Filter by publish
		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();
	}


}
