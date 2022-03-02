<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Result Controller
 */
class SurveyforceControllerResult extends JControllerForm {

    public function __construct($config = array()) {
		
        parent::__construct($config);
    }

    public function mark() {
        $jinput = JFactory::getApplication()->input;
        $mark  = $jinput->getString("mark");

        $session = JFactory::getSession();
        $session->set("mark", $mark, 'result');

        echo true;
        exit;
    }
}
