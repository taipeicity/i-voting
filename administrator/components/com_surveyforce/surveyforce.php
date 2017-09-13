<?php

/**
 *   @package         Surveyforce
 *   @version           1.1-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
//TODO: Plugins , "Hide this question if" option
//TODO: Answers:
/*
  Ranking DropDown
  Ranking Drag'AND'Drop

 */

defined('_JEXEC') or die('Restricted access');

JLoader::register('SurveyforceHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'surveyforce.php');

$GLOBALS['survey_version'] = '3.1.1.003';
global $survey_version;

if (!defined('_SURVEY_FORCE_COMP_NAME'))
	define('_SURVEY_FORCE_COMP_NAME', JText::_('COM_SURVEYFORCE_SURVEYFORCE_DELUXE_VER') . $survey_version);

if (!defined('_SEL_CATEGORY'))
	define('_SEL_CATEGORY', '- ' . JText::_('COM_SURVEYFORCE_SELECT_CATEGORY') . ' -');
if (!defined('_CMN_NEW_ITEM_FIRST'))
	define('_CMN_NEW_ITEM_FIRST', JText::_('COM_SURVEYFORCE_NEW_ITEMS_DEFAULT_TO_THE_FIRST_PLACE'));
if (!defined('_PDF_GENERATED'))
	define('_PDF_GENERATED', JText::_('COM_SURVEYFORCE_GENERATED'));
if (!defined('_CURRENT_SERVER_TIME_FORMAT'))
	define('_CURRENT_SERVER_TIME_FORMAT', '%Y %m %d %H %M %S');
if (!defined('_CURRENT_SERVER_TIME'))
	define('_CURRENT_SERVER_TIME', JHtml::_('date', time(), 'Y-m-d H:i'));
if (!defined('_PN_DISPLAY_NR'))
	define('_PN_DISPLAY_NR', JText::_('COM_SURVEYFORCE_DISPLAY'));



$controller = JControllerLegacy::getInstance('Surveyforce');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
