<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Knowus
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Knowus', JPATH_COMPONENT);
JLoader::register('KnowusController', JPATH_COMPONENT . '/controller.php');
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
JLoader::register('IsDetail', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .
    'isDetail.php');
JLoader::register('KnowusHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .
    'knowus.php');
// Execute the task.
$controller = BaseController::getInstance('Knowus');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
