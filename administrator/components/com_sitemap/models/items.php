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
 * Sitemap Model
 */
class SitemapModelItems extends JModelList
{
	private $fieldName = array('id', 'catid', 'title', 'exclude', 'editor');
  /**
	 * Method to build an SQL query to load the list data.
	 * Read http://docs.joomla.org/Developing_a_Model-View-Controller_%28MVC%29_Component_for_Joomla!1.6_-_Part_07#Create_the_model
	 * @return	string	An SQL query
	 */
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('id,title,exclude');

		// From the table
		$query->from('#__sitemap');
		return $query;
	}
	
	public function getFieldsCount(){
    return count($this->fieldName);
  }
  public function getFieldNames(){
    return $this->fieldName;
  }
}