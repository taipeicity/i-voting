<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

define('survey_version','3.1.1.003');
global $survey_version;

define('COMPONENT_ITEM_ID', JFactory::getApplication()->input->get('Itemid', ''));
define('COMPONENT_OPTION', 'com_surveyforce');

$tag = JFactory::getLanguage()->getTag();
$lang = JFactory::getLanguage();
$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

JLoader::register('SurveyforceHelper', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'surveyforce.php');
JLoader::register('SurveyforceTemplates', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'templates.php');
JLoader::register('survey_force_front_html', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'surveyforce.html.php');
JLoader::register('survey_force_front_html', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'edit.surveyforce.html.php');
JLoader::register('SurveyforceVote', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vote.php');

$controller = JControllerLegacy::getInstance('Surveyforce');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();