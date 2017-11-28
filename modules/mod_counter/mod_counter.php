<?php
/**
*   @package         Counter
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$lang =  $params->get('lang', '');
$ga_profile_id= $params->get('ga_profile_id');
$is_show =  $params->get('is_show', '1');

require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'helper.php');


$list = modCounterHelper::getList($params);

require(JModuleHelper::getLayoutPath('mod_counter'));
