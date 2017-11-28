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
 * Sitemap View Items
 */
class SitemapViewItems extends JViewLegacy {

	/**
	 * Items view display method
	 * @return void
	 */
	function display($tpl = null) {
		// Get data from the model and Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		SitemapHelper::addSubmenu('items');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		//get row count
		$this->fieldCount = $this->get('FieldsCount');
		//get fields name array
		$this->fieldNames = $this->get('FieldNames');

		// Set the sidebar		
		

//		 Set the toolbar, sidebar
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_SITEMAP_MANAGER_ITEMS'), 'generic.png');
		JToolBarHelper::addNew('item.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('item.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'items.delete', 'JTOOLBAR_DELETE');
		JToolBarHelper::divider();
//		JToolBarHelper::preferences('com_sitemap');
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SITEMAP_ADMINISTRATION'));
	}

}
