<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy {

    public function display($cachable = false, $urlparams = array()) {
        $view = JFactory::getApplication()->input->get('view');
        $task = JFactory::getApplication()->input->get('task');

        

        parent::display($cachable);
    }


}
