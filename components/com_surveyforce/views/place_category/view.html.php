<?php
/**
*   @package         Surveyforce
*   @version           1.3-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewPlace_category extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {
		$session 	= &JFactory::getSession();
		$app = JFactory::getApplication();
		
		$this->itemid = $app->input->getInt('Itemid');
        $this->state = $this->get('state');
        $this->params = $this->state->get('params');


		// 返回連結
		$login_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$this->itemid}", false);

		// 檢查是否有登入
		if (!$session->get('place_username')) {
			$msg = "您尚未登入，請重新登入。";
			$app->redirect($login_link, $msg);
		}

		// 清空 session 記錄
		$session->clear('place_survey_id');

		// 取得所有現地投票的議題清單
		$this->items = $this->get('Items');
		if (!$this->items) {
			$msg = "目前未有任何現地投票議題。";
			$app->redirect($login_link, $msg);
		}
       
		
		$document = JFactory::getDocument();
		$document->setTitle("進行中議題");


		 // Display the view
		$layout	= $app->input->getString('layout', 'default');
		$this->setLayout($layout);

        parent::display($tpl);
        
    }

}

