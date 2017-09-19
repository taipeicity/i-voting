<?php

/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewCategory extends JViewLegacy {

	protected $item;
	protected $pagination;

// Overwriting JView display method
	function display($tpl = null) {

		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$this->itemid = $app->input->getInt('Itemid');

		$params = $app->getParams();
		$vote_cat = $params->get('vote_cat');

		if ($vote_cat == 2) {
			$completed_form = json_decode($session->get('completed_form'));
			if (preg_match("/\d{4}/", $completed_form->condition)) {
				$this->year = $completed_form->condition;
			} else if (preg_match("/[1-2]/", $completed_form->condition)) {
				$this->is_define = $completed_form->condition;
			} else {
				$this->survey_search = $completed_form->search;
			}
		}

		$this->voting_mymuid = $params->get('voting_mymuid');
		$this->soon_mymuid = $params->get('soon_mymuid');
		$this->completed_mymuid = $params->get('completed_mymuid');

		$config = JFactory::getConfig();
		$config->set('list_limit', 0);

// Assign data to the view
		$this->state = $this->get('state');
		$this->params = $this->state->get('params');
		$this->items = $this->get('SurveyItems');

		$votings = $this->get('VotingCounts');
		$soons = $this->get('SoonCounts');
		$completeds = $this->get('CompletedCounts');

		if ($session->get('practice_pattern')) { //練習區

			$status = $session->get('practice_pattern');
			$this->practice_pattern = $status;

			//預設次數為0
			$voting_count = 0;
			foreach ($votings as $voting) {
				if ($voting->vote_pattern == 2 || $voting->vote_pattern == 3) {
					$voting_count += 1;
				}
			}
			$this->voting_counts = $voting_count;
		} else {  //正式區
			//預設次數為0
			$voting_count = 0;
			$soon_count = 0;
			$completed_count = 0;

			// 計算「進行中的投票」正式投票有幾個議題
			foreach ($votings as $voting) {
				if ($voting->vote_pattern == 1 || $voting->vote_pattern == 3) {
					$voting_count += 1;
				}
			}

			// 計算「提案資料內容」正式投票有幾個議題
			foreach ($soons as $soon) {
				if ($soon->vote_pattern == 1 || $soon->vote_pattern == 3) {
					$soon_count += 1;
				}
			}

			// 計算「已完成的投票」正式投票有幾個議題
			foreach ($completeds as $completed) {
				if ($completed->vote_pattern) {
					$completed_count += 1;
				}
			}
			
			//提案資料內容、進行中的投票、歷史投票區的議題數
			$this->voting_counts = $voting_count;
			$this->soon_counts = $soon_count;
			$this->completed_counts = $completed_count;
		}

		$this->first_vote_start = $this->get('VoteStart');
		$this->last_vote_end = $this->get('VoteEnd');

// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}


		$layout = $this->getLayout();

		if ($layout == "default" || $status == true) {
			$this->setLayout("voting");
		}


// Display the view
		parent::display($tpl);

	}

}
