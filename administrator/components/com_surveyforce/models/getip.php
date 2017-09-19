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
 * Getip model.
 *
 */
class SurveyforceModelGetip extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }

    public function getItem() {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('surv_id');
        $vote_source = $app->input->getInt('source_type');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__survey_force_survs'));
        $query->where('id = ' . (int) $sid);

        $db->setQuery($query);

        $surv = $db->loadObject();

        return $surv;
    }

}
