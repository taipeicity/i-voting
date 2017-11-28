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

if(!defined("ITPGOOGLESEARCH_COMPONENT_ADMINISTRATOR")) {
    define("ITPGOOGLESEARCH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_itpgooglesearch");
}

// Register Component libraries
JLoader::register("ItpGoogleSearchVersion", ITPGOOGLESEARCH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "libraries" .DIRECTORY_SEPARATOR. "version.php");
