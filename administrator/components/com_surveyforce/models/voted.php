<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Voted model.
 *
 */
class SurveyforceModelVoted extends JModelList {

	protected $text_prefix = 'COM_SURVEYFORCE';

	public function __construct($config = array ()) {

		parent::__construct($config);
	}

	public function getItem() {

		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs_release'));
		$query->where('id = ' . (int) $sid);

		$db->setQuery($query);

		$surv = $db->loadObject();

		return $surv;
	}

	public function getColumn($suffix) {

		$app = JFactory::getApplication();
		$sid = $app->input->getInt('surv_id');

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__assign_summary'));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($sid));
		$query->where($db->quoteName('table_suffix') . ' = ' . $db->quote($suffix));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}


}
