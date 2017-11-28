<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "libraries" .DIRECTORY_SEPARATOR. "init.php";

jimport('joomla.application.component.controller');

$app = JFactory::getApplication();
/** @var $app JApplicationSite **/

$controller = JControllerLegacy::getInstance('ITPGoogleSearch');
$controller->execute($app->input->getCmd('task'));
$controller->redirect();