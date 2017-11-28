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

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, "utf-8");

$app = JFactory::getApplication();
/** @var $app JApplicationSite */

$phrase = htmlentities($app->getUserStateFromRequest("com_itpgooglesearch.query", "gsquery"), ENT_QUOTES, "UTF-8");

require JModuleHelper::getLayoutPath('mod_itpgooglesearch', $params->get('layout', 'default'));