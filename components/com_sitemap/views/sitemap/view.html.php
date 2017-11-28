<?php
/**
*   @package         SITEMAP
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HTML View class for the sitemap Component
 */
class SitemapViewSitemap extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{	
		$this->state = $this->get('State');
		
		// Assign data to the view
		$this->item = $this->get('Item');		
		$this->exclude = $this->get('Exclude');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Display the view
		parent::display($tpl);
	}
}
