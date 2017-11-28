<?php
/**
*   @package         Sfmenu
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php';

$list	= modSfmenuHelper::getList($params);
$app	= JFactory::getApplication();
$menu	= $app->getMenu();
$active	= $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path	= isset($active) ? $active->tree : array();
$showAll	= $params->get('showAllChildren');
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

if(count($list)) {
	require JModuleHelper::getLayoutPath('mod_sfmenu', $params->get('layout', 'default'));
}
