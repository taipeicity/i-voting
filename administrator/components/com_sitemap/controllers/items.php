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
 * Sitemap Controller
 */
class SitemapControllerItems extends JControllerAdmin
{
	public function __construct($config = array())
	{	
		parent::__construct($config);
		$this->text_prefix = "JGLOBAL";
	}
	public function getModel($name = 'item', $prefix = 'SitemapModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}
