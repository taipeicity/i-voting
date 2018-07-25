<?php

/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewIntro extends JViewLegacy {

	public function __construct() {
		parent::__construct();

	}

	public function display($tpl = null) {
		$session         = &JFactory::getSession();
		$model           = $this->getModel();
		$app             = JFactory::getApplication();
		$this->itemid    = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');


		$this->state            = $this->get('state');
		$this->params           = $this->state->get('params');
		$this->voting_menuid    = $this->params->get('voting_mymuid');
		$this->completed_menuid = $this->params->get('completed_mymuid');

		$this->preview      = false;
		$this->questions    = $this->get('Questions');
		$this->options      = $this->get('Options');
		$this->finish_votes = $this->get('FinishVotes');
		$this->qrcode       = JHtml::_('utility.getQRcode', JUri::getInstance()->toString(array (
			'scheme', 'host', 'port', 'path', 'query'
		)));

		$Item = $this->get('Item');

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);

		// 檢查議題是否有效
		if ($Item->id != 0) {
			if ($Item->published == 1 && $Item->is_complete == 1 && $Item->is_checked == 1) { // 議題通過審核
				// 檢查投票模式是否正確
				$result = json_decode(SurveyforceVote::checkVotePattern($Item->id), true);
				if ($result['status']) {
					$app->redirect($category_link, $result['msg']);
				}

				if ($Item->is_public == 0) { // 未公開
					if ($app->input->getString('token') == JHtml::_('utility.endcode', $Item->title)) { // 檢查token
						$this->item = $Item;
					} else {
						$msg = "該議題不存在，請重新選擇正確的議題。";
						$app->redirect($category_link, $msg);
					}
				} else { // 公開
					$this->item = $Item;
				}
			} else {
				$msg = "該議題不存在，請重新選擇正確的議題。";
				$app->redirect($category_link, $msg);
			}
		} else {
			$msg = "該議題不存在，請重新選擇正確的議題。";
			$app->redirect($category_link, $msg);
		}

		$this->print = $app->input->getBool('print');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}


		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));


		// Display the view
		parent::display($tpl);

	}

}
