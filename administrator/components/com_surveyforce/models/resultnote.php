<?php

/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');


class SurveyforceModelResultnote extends JModelAdmin {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {

        parent::__construct($config);
    }

    public function getTable($type = 'Resultnote', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null) {

        $result = parent::getItem($pk);
        return $result;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {

        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_surveyforce.edit.resultnote.data', array());

        if (empty($data)) {

            $data = $this->getItem();

        }

        return $data;
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.resultnote', 'resultnote', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }


	// 更新欄位
	public function updateField($_field_name, $_field_value, $_id) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->update('#__survey_force_survs');
		$query->set($db->quoteName($_field_name) . " = ". $db->quote($_field_value));
		$query->where( $db->quoteName('id') . " = ". $db->quote($_id) );


		$db->setQuery($query);

		if($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);
			return false;
		}
	}

}