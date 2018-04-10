<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Addend model.
 *
 */
class SurveyforceModelAddend extends JModelList {

	protected $text_prefix = 'COM_SURVEYFORCE';

	public function __construct($config = array ()) {


		parent::__construct($config);
	}


	protected function getListQuery() {

	}

	public function getAssignColumn($suffix, $surv_id) {

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__assign_summary'));
		$query->where($db->quoteName('table_suffix') . ' = ' . $db->quote($suffix));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($surv_id));
		$query->order('column_num ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}


}