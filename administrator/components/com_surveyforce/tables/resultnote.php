<?php

/**
*   @package         Surveyforce
*   @version           1.0-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class SurveyforceTableResultnote extends JTable {

	function __construct(&$db) {
		parent::__construct('#__survey_force_survs', 'id', $db);
	}

	public function store($updateNulls = false) {


		return true;

	}


}