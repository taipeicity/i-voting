<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access
defined('_JEXEC') or die;

/**
 * Method to build Route
 * 
 * @param array $query
 *
 * @return string
 */
function ITPGoogleSearchBuildRoute(&$query){
	
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    /* @var $menu JMenu */
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }

    if(isset($query['view'])){
        if(empty($query['Itemid'])){
            $attritubes = array("component");
            $values = array("com_itpgooglesearch");
            $menuItems = $menu->getItems($attritubes, $values );
            
            if(!empty($menuItems)) {
                
                foreach ($menuItems as $menuItem) {
                    if(
                    (strcmp("com_itpgooglesearch",$menuItem->query['option']) == 0 ) 
                    AND 
                    (strcmp("search",$menuItem->query['view']) == 0)){
                        break;
                    } else {
                        $menuItem = null;
                    }
                }
                if(!is_null($menuItem)) {
                    $query['Itemid'] = $menuItem->id;
                }

            } else {
                $segments[] = $query['view'];
            }
        }else{
			$segments[] = $query['view'];
			$segments[] = $query['gsquery'];
        }
        
        unset($query['view']);
		unset($query['gsquery']);
        
    }
    
    return $segments;
}

/**
 * Method to parse Route.
 *
 * @param array $segments
 *
 * @return array
 */
function ITPGoogleSearchParseRoute($segments){
    // echo "***"; exit();
    $vars = array();
    
    //Get the active menu item.
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu();
    $menuItem   = $menu->getActive();
    
    if(!isset($menuItem)) {
        $vars['view']   = $segments[0];
        return $vars;
    } 
    
    return $vars;
}