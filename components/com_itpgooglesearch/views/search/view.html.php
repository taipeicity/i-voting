<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ITPGoogleSearchViewSearch extends JViewLegacy {

    /**
     * @var JRegistry
     */
    protected $state;

    /**
     * @var JRegistry
     */
    protected $params;

    protected $phrase = "";

    public function display($tpl = null) {
        
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite **/
        
        $state          = $this->get('State');
        $this->params   = $state->get("params");

        $this->phrase   = $app->getUserStateFromRequest("com_itpgooglesearch.query", "gsquery");

        parent::display($tpl);
        
    }
}
