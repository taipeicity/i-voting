<?php

/**
 * SurveyForce Delux Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
