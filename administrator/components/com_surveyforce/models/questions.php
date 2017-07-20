<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelQuestions extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id', 'sf_survey', 'sf_qtype', 'sf_qtext', 'sf_impscale', 'sf_rule', 'sf_fieldtype', 'ordering', 'sf_compulsory', 'sf_section_id', 'published', 'sf_qstyle', 'sf_num_options', 'is_final_question', 'sf_default_hided');
        }
         
        parent::__construct($config);
    }

	function getSurvey_names() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('id AS value, title AS text');
		$query->from('#__survey_force_survs');
		$query->order('id');

		// Setup the query
		$db->setQuery($query);

		// Return the result
		return $db->loadObjectList();
	}

    protected function populateState($ordering = null, $direction = null) {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);


        parent::populateState();
    }

    protected function getListQuery() {
        $db = $this->getDbo();
        $surv_id = JFactory::getApplication()->input->getCmd('surv_id');
        $query = $db->getQuery(true);
        $query->select('a.sf_section_id, a.id,  a.sf_qtext, a.ordering, a.sf_compulsory, a.published, a.question_type');
        $query->from('`#__survey_force_quests` AS a');
     
        
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->Quote('%' . $db->Escape($search, true) . '%');
            $query->where('`sf_qtext` LIKE ' . $search);
        }
        if((int)$surv_id)
		{
            $query->where('`sf_survey` = ' . $surv_id);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

        $orderCol = $this->state->get('list.ordering',  '`a`.`id`');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        return $query;
    }

	// 刪除題目
    function delete($cid) {
        $db = JFactory::getDbo();

		foreach($cid as $question_id) {
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__survey_force_fields');
			$query->where('quest_id = '. $db->quote($question_id));
			$db->setQuery($query);
			$option_rows = $db->loadObjectList();

			if ($option_rows) {
				foreach($option_rows as $option_row) {
					// 刪除檔案
					if ($option_row->image) {
						unlink(JPATH_SITE. "/". $option_row->image);
					}

					// 刪除檔案
					if ($option_row->file1) {
						unlink(JPATH_SITE. "/". $option_row->file1);
					}
				}
			}

			// 刪除選項
			$query = $db->getQuery(true);
			$query->delete('#__survey_force_fields');
			$query->where('quest_id = '. $db->quote($question_id));
			$db->setQuery($query);
			$db->execute();


			// 刪除子選項
			$query = $db->getQuery(true);
			$query->delete('#__survey_force_sub_fields');
			$query->where('quest_id = '. $db->quote($question_id));
			$db->setQuery($query);
			$db->execute();


		}

		
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_quests');
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $db->execute();
		
    }

    public function publish($cid, $value = 1) {
        $database = JFactory::getDBO();
        $task = JFactory::getApplication()->input->getCmd('task');
        $state = ($task == 'publish') ? 1 : 0;

        if (!is_array($cid) || count($cid) < 1) {
            $action = ($task == 'publish') ? 'publish' : 'unpublish';
            echo "<script> alert('" . JText::_('COM_SURVEYFORCE_SELECT_AN_ITEM_TO') . " $action'); window.history.go(-1);</script>\n";
            exit();
        }

        $cids = implode(',', $cid);

        $query = "UPDATE #__survey_force_quests"
                . "\n SET published = " . intval($state)
                . "\n WHERE id IN ( $cids )"
        ;
        $database->setQuery($query);
        if (!$database->execute()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        return true;
    }
    
    public function compulsory($cid){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $task = JFactory::getApplication()->input->getCmd('task');
        $state = ($task == 'compulsory') ? 1 : 0;
        
        $query->update('#__survey_force_quests');
        $query->set('sf_compulsory='.$state);
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        
        if (!$db->execute()) {
            echo "<script> alert('" . $db->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        return true;
    }

    public function saveorder($cid, $order, $ajax = false){
        $db = JFactory::getDbo();
       
        $section_ids = JFactory::getApplication()->input->get('section_ids', array(), 'array');
        $section_order = JFactory::getApplication()->input->get('section_order', array(), 'array');

        if(count($section_ids)){
            foreach ($section_ids as $ii => $section_id) {
                $db->setQuery("UPDATE `#__survey_force_qsections` SET `ordering` = '".$section_order[$ii]."' WHERE `id` = '".$section_id."'");
                $db->execute();
            }
        }

        $cid = JFactory::getApplication()->input->get('item_ids', array(), 'array');

        foreach($cid as $key=>$id){
			$query = $db->getQuery(true);
			$query ->update('#__survey_force_quests');
			$query ->set('ordering='.$order[$key]);
			$query ->where('id='.$id);
			$db->setQuery($query);
			$db->execute();
        }

		if ( !$ajax )
			JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->input->get('surv_id', 0));


    }

    public function getSections()
    {
        $database = JFactory::getDbo();

        $survid = JFactory::getApplication()->input->get('surv_id');
        if ($survid) {
            $query = " SELECT a.* "
                    ." FROM #__survey_force_qsections AS a "
                    ." WHERE 1=1 "
                    .( $survid ? "\n AND a.sf_survey_id = $survid" : '' )
                    ." ORDER BY a.ordering";
            $database->setQuery( $query );  
            
            $sections = $database->loadObjectList();
           
            return $sections;
        }

        return array();

    }
    

	// 取得題型
	public function getQuestionTypes(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__extensions');
		$query->where("`type` = 'plugin'");
		$query->where("`access` = '1'");
		$query->where("`enabled` = '1'");
		$query->where("`folder` = 'survey'");

		$db->setQuery($query);
		$rows = $db->loadAssocList('element');

		return $rows;
	 }

}
