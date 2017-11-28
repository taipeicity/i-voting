<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Control Panel Controller
 *
 * @package     ITPGoogleSearch
 * @subpackage  Components
  */
class ITPGoogleSearchController extends JControllerLegacy {
    
    public function display($cachable = false, $urlparams = array()) {

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */
        
        $viewName      = $app->input->getCmd('view', 'cpanel');
        $app->input->set("view", $viewName);

        parent::display();
        return $this;
    }

}
