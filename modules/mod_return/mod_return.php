<?php
/**
*   @package         Return
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
 
// no direct access
defined( '_JEXEC' ) or die;

$app = JFactory::getApplication();

// Include the syndicate functions only once
//require_once( dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php' );
 
//$list = modReturnHelper::getList( $params );

require( JModuleHelper::getLayoutPath( 'mod_return', $params->get('layout', 'default') ));
?>
