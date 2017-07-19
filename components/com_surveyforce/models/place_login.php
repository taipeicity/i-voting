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
class SurveyforceModelPlace_login extends JModelItem {

	public function __construct()
	{
		parent::__construct();
	}

	public function populateState()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params	= $app->getParams();

		$survey_id	= $params->get('survey_id');

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



}
