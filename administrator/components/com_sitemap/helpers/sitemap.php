<?php
/**
*   @package         Sitemap
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die;

/**
 * Sitemap component helper.
 */
class SitemapHelper
{
	/**
	 * Configure the submenu linkbar.
	 * load this from controller.php	
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SITEMAP_SUBMENU_ITEMS'),
			'index.php?option=com_sitemap',
			$vName == 'items'
		);
		
		
		if ($vName == 'categories')
		{
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('網站地圖')),
				'sitemap-categories');
		}
	}
}
