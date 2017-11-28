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
 * General Controller of Sitemap component
 */
class SitemapController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			$cachable	If true, the view output will be cached
	 * @param   array  $urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	function display($cachable = false, $urlparams = false)
	{

		// require helper file
		require_once JPATH_COMPONENT.'/helpers/sitemap.php';

		$view   = $this->input->get('view', 'items');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'item' && $layout == 'edit' && !$this->checkEditId('com_sitemap.edit.item', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_sitemap&view=items', false));

			return false;
		}

		// set default view if not set
		JRequest::setVar('view', $view);

		// call parent behavior
		parent::display();
		return $this;
		
	}
}