<?php

/**
 * SurveyForce Delux Component for Joomla 3
 * @package SurveyForce
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
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