<?php

/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Joomla.Component
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
	function display($tpl = null)  {
		$app = JFactory::getApplication();
		$this->itemid	= $app->input->getInt('Itemid');

		$config = JFactory::getConfig();
		$config->set('list_limit', 0);

		// Assign data to the view
		$this->state 	= $this->get('state');
		$this->params 	= $this->state->get('params');
		$this->items		= $this->get('SurveyItems');

		$this->voting_counts = $this->get('VotingCounts');
		$this->soon_counts = $this->get('SoonCounts');
		$this->completed_counts = $this->get('CompletedCounts');


		$params = $app->getParams();
		$this->voting_mymuid = $params->get('voting_mymuid');
		$this->soon_mymuid = $params->get('soon_mymuid');
		$this->completed_mymuid = $params->get('completed_mymuid');


		$this->first_vote_start = $this->get('VoteStart');
		$this->last_vote_end = $this->get('VoteEnd');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$layout = $this->getLayout();
		if ($layout == "default") {
			$this->setLayout("voting");
		}

		// Display the view
		parent::display($tpl);


	}

}
