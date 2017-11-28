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

jimport('joomla.application.component.controller');

class ITPGoogleSearchController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param   boolean         $cachable If true, the view output will be cached
     * @param   array           $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array()) {
        
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite **/
        
        $cachable   = false; // Huh? Why not just put that in the constructor?

        // Set the default view name and format from the Request.
        // Note we are using catid to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $viewName  = $app->input->getCmd('view', 'search');
        $app->input->set('view', $viewName);

        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'INT',
            'limitstart'        => 'INT',
            'filter_order'      => 'CMD',
            'filter_order_Dir'  => 'CMD',
            'lang'              => 'CMD',
        );

        parent::display($cachable, $safeurlparams);
        return $this;
    }
    
} 