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
 * Sitemap Model Sitemap
 */
class SitemapModelSitemap extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() 
	{
		$app = JFactory::getApplication();
		// Get the message id
		$id = JRequest::getInt('id');
		$this->setState('sitemap.id', $id);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'sitemap', $prefix = 'sitemapTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @return The message to be displayed to the user
	 */
	 
	public function getExclude() 
	{	
		
		$app	= JFactory::getApplication();
		$params = $app->getParams();
		$id		=$params->get('id', 0);
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__sitemap');
		$query->where("id=$id");
		$db->setQuery($query);
		$ex = $db->loadObject();
		return $ex;
	}
}
