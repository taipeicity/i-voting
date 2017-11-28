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

/**
 * @package		Joomla.Site
 * @subpackage	mod_sfmenu
 * @since		1.5
 */
class modSfmenuHelper
{
	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @since	1.5
	 */
	static function getList(&$params)
	{	
		// Initialise variables.
		$list		= array();
		$db			= JFactory::getDbo();
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$path		= $active->tree;
		$start		= 0;
//		$end		= 3;
		$end = ((int) $params->get('endLevel') > 3)?3:(int) $params->get('endLevel') ;
		$showAll	= 1;
		$maxdepth	= $params->get('maxdepth');
                
		$items 		= $menu->getItems('menutype', $params->get('menutype'));
		
		$fix = ($items[0]->level > 1);
		
		$lastitem	= 0;
		
		if ($items) {
			foreach($items as $i => $item)
			{	
				if($fix){
					$item->level -=1; 
				}
				
				if (($start && $start > $item->level)
					|| ($end && $item->level > $end)
					|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
					|| ($maxdepth && $item->level > $maxdepth)
					|| ($start > 1 && !in_array($item->tree[0], $path))
				) {
					unset($items[$i]);
					continue;
				}
				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;
				$item->next_level = 0;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper		= ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower	= ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff	= ($items[$lastitem]->level - $item->level);
					$items[$lastitem]->next_level	= $item->level;
				}

				$lastitem			= $i;
				$item->active		= false;
				$item->flink = $item->link;

				switch ($item->type)
				{
					case 'separator':
						// No further action needed.
						continue;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link.'&Itemid='.$item->id;
						}
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
						break;

					default:
						$router = JSite::getRouter();
						if ($router->getMode() == JROUTER_MODE_SEF) {
							$item->flink = 'index.php?Itemid='.$item->id;
						}
						else {
							$item->flink .= '&Itemid='.$item->id;
						}
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				}
				else {
					$item->flink = JRoute::_($item->flink);
				}
				
				$item->title = htmlspecialchars($item->title);
				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''));
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', '')) : '';
			}

			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff	= ($items[$lastitem]->level - ($start?$start:1));
				$items[$lastitem]->next_level	= ($start?$start:1);
			}
		}
		
		return $items;
	}	

}
