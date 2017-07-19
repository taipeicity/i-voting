<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewPlace_login extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {
		$session 	= &JFactory::getSession();
		
        $app = JFactory::getApplication();
		$this->itemid	= $app->input->getInt('Itemid');

		$this->state = $this->get('state');
        $this->params = $this->state->get('params');

		
		// 限制IP連線
		if ($this->params->get('is_limit_connection')) {
			$user_ip = JHtml::_('utility.getUserIP');
			$is_allow = false;

			// 單一IP
			if ( $this->params->get('single_ips') ) {
				$single_ips = explode(",", $this->params->get('single_ips'));

				if (in_array($user_ip, $single_ips)) {
					$is_allow = true;
				}
			}

			// IP範圍
			if ( $this->params->get('range_ips') ) {
				$range_ips = explode(",", $this->params->get('range_ips'));
				foreach ($range_ips as $ips) {
					$ipdata = explode("-", $ips);
					if (ip2long($user_ip) <= ip2long($ipdata[1]) && ip2long($ipdata[0]) <= ip2long($user_ip)) {
						$is_allow = true;
					}
				}

			}

			if ($is_allow == false) {
				JError::raiseError(404);
				jexit;
			}
			
		}

		// 清空 session 記錄
		$session->clear('place_survey_id');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

		$document = JFactory::getDocument();
		$document->setTitle("實體投票登入頁");

		// Display the view
        parent::display($tpl);

    }

}
