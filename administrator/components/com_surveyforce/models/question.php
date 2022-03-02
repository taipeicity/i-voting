<?php

/**
*   @package         Surveyforce
*   @version           1.0-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Question model.
 *
 */
class SurveyforceModelQuestion extends JModelAdmin {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {

		if ( JFactory::getApplication()->input->get('surv_id', 0) )
			JFactory::getApplication()->setUserState('question.sf_survey', JFactory::getApplication()->input->get('surv_id', 0));

        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id', 'sf_qtype');
        }
        parent::__construct($config);
    }

    public function getTable($type = 'Question', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null) {

        $result = parent::getItem($pk);
        return $result;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_surveyforce.edit.question.data', array());

        if (empty($data)) {
            $data = $this->getItem();

			if ( $this->getState('question.new_qtype_id') )
			{
				$data->sf_qtype = $this->getState('question.new_qtype_id');

				if ( $this->getState('question.sf_survey') )
					$data->sf_survey = $this->getState('question.sf_survey');
			}

            $ordering = $this->getOrdering();
            // Prime some default values.
            if ($this->getState('question.id') == 0) {
                $app = JFactory::getApplication();
                $id = $app->getUserState('com_surveyforce.edit.question.id');
                if ($id)
                    $data->set('id', JFactory::getApplication()->input->getInt('id', $id));
            }
        }

        return $data;
    }

    public function getForm($data = array(), $loadData = true) {

        $new_qtype_id = JFactory::getApplication()->input->get('new_qtype_id');
        $sf_survey = JFactory::getApplication()->input->get('surv_id');

        $this->setState('question.new_qtype_id', $new_qtype_id);
        $this->setState('question.sf_survey', $sf_survey);
        
        $ordering = $this->getOrdering();

        $form = $this->loadForm('com_surveyforce.question', 'question', array('control' => 'jform', 'load_data' => $loadData));

		if ( empty($sf_survey) )
			$this->setState('question.sf_survey', $form->getValue('sf_survey'));

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getOrdering() {

        $db = JFactory::getDBO();
        $query = "SELECT id, sf_survey, ordering as value, CONCAT(ordering, '. ', sf_qtext) as text FROM `#__survey_force_quests`";
        $db->setQuery($query);
        $ordering_list = $db->loadObjectlist();
        return $ordering_list;

    }

    public function getSurveysList( $catId = false ) {

        $db = JFactory::getDbo();
        $query = "SELECT DISTINCT(`id`) AS `value`, `title` AS `text` FROM `#__survey_force_survs_release`"
			. ( $catId ? ' WHERE `sf_cat` = '.(int)$catId : '') ;

        $db->setQuery($query);
        $surveys = $db->loadObjectList();
        return $surveys;
    }

    public function getLists($id){

        if (!defined('_CMN_NEW_ITEM_FIRST')) define( '_CMN_NEW_ITEM_FIRST', JText::_('COM_SURVEYFORCE_NEW_ITEMS_DEFAULT_TO_THE_FIRST_PLACE'));
        $database = JFactory::getDBO();
        $sessions = JFactory::getSession();
        $my = JFactory::getUser();

        $is_return = $sessions->get('is_return_sf') > 0 ? true : false;
        $sessions->set('is_return_sf', -1);

        $row = JTable::getInstance('Question', 'SurveyforceTable', array());
        // load the row from the db table
        $row->load( $id );

        if ($id) {
            // do stuff for existing records
            $row->checkout($my->id);
        } else {
            // do stuff for new records
            $row->ordering      = 0;
            $row->sf_survey     = intval(  JFactory::getApplication()->getUserStateFromRequest( "question.sf_survey", 'sf_survey', 0 ) );
        }

        $lists = array();
        $lists['survid'] = ($row->sf_survey ? $row->sf_survey :0);

		if ( $lists['survid'] )
			JFactory::getApplication()->setUserState('question.sf_survey', $lists['survid']);

        $row->sf_qtext = $is_return ? $sessions->get('sf_qtext_sf') : $row->sf_qtext;
        // build the html select list for ordering
        if ($id) {
            $query = "SELECT a.ordering AS value, a.sf_qtext AS text"
            . "\n FROM #__survey_force_quests AS a"
            . ($row->sf_survey ? "\n WHERE a.sf_survey = '".$row->sf_survey."' " :'')
            . " AND sf_section_id = '".$row->sf_section_id."' "
            . "\n ORDER BY a.ordering, a.id ";
        }
        else {
            $query = "SELECT a.ordering AS value, a.sf_qtext AS text"
            . "\n FROM #__survey_force_quests AS a"
            . ($row->sf_survey ? "\n WHERE a.sf_survey = '".$row->sf_survey."' " :'')
            . "\n ORDER BY a.ordering, a.id ";
        }

        $text_new_order = _CMN_NEW_ITEM_FIRST;
        
        if ( $id ) {
            $order = SurveyforceHelper::sfGetOrderingList( $query );       
            $order = array_slice ($order, 1, -1);  
            $sel_value = $is_return ? $sessions->get('ordering_sf') : $row->ordering;
            $ordering = JHtmlSelect::genericlist( $order, 'ordering', 'class="text_area" size="1"', 'value', 'text', intval( $sel_value ) );
        } else {
            $ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $text_new_order;
        }
        $lists['ordering'] = $ordering; 
        
        //build list of surveys
        $query = "SELECT id AS value, title AS text"
        . "\n FROM #__survey_force_survs_release"
        . ( ! JFactory::getApplication()->isAdmin()? " WHERE sf_author = '{$my->id}' ": " ")
        . "\n ORDER BY id"
        ;
        $database->setQuery( $query );
        $surveys = ($database->LoadObjectList() == null? array(): $database->LoadObjectList());
        $disable = '';
        $sel_value = $is_return ? $sessions->get('sf_survey_sf') : $row->sf_survey;
        $survey = JHtmlSelect::genericlist( $surveys,'sf_survey', $disable.' class="text_area" size="1" ', 'value', 'text', intval( $sel_value ) ); 
        $lists['survey'] = $survey; 
        
        //build list of imp.scales
        $query = "SELECT id AS value, iscale_name AS text"
        . "\n FROM #__survey_force_iscales"
        . "\n ORDER BY iscale_name"
        ;
        $database->setQuery( $query );
        $impscales[] = JHTML::_('select.option', '0', '- '.JText::_('COM_SURVEYFORCE_SELECT_IMP_SCALE').' -' );
        
        $impscales = @array_merge( $impscales, ($database->LoadObjectList() == null? array(): $database->LoadObjectList()) );
        $sel_value = $is_return ? $sessions->get('sf_impscale_sf') : $row->sf_impscale;
        if ($is_return) {
            $query = "SELECT id FROM `#__survey_force_iscales` ORDER BY `id` DESC";
            $database->setQuery( $query );
            $sel_value = $database->loadResult();
        }

        $impscale = JHtmlSelect::genericlist( $impscales, 'sf_impscale', 'class="text_area" size="1" ', 'value', 'text', intval( $sel_value ) ); 
        $lists['impscale'] = $impscale; 
        
        $yes_no[] = JHTML::_('select.option', '1', JText::_('COM_SURVEYFORCE_YES'));
        $yes_no[] = JHTML::_('select.option', '0', JText::_('COM_SURVEYFORCE_NO'));
        
        $sel_value = $is_return ? $sessions->get('sf_compulsory_sf') : $row->sf_compulsory;
        $lists['compulsory'] = JHtmlSelect::genericlist($yes_no, 'sf_compulsory', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));
        $sel_value = $is_return ? $sessions->get('insert_pb_sf') : 1;
        $lists['insert_pb'] = JHtmlSelect::genericlist($yes_no, 'insert_pb', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));

        $lists['use_drop_down'] = JHtmlSelect::genericlist($yes_no, 'jform[sf_qstyle]', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_qstyle));

        $sel_value = $is_return ? $sessions->get('published') : 1;
        $lists['published'] = JHtmlSelect::genericlist($yes_no, 'published', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));

        $lists['sf_default_hided'] = JHtmlSelect::genericlist($yes_no, 'sf_default_hided', 'class="text_area" size="1" ', 'value', 'text', intval($row->sf_default_hided));

        //build list of sections
        $query = "SELECT id AS value, sf_name AS text"
                . "\n FROM #__survey_force_qsections"
                . "\n WHERE sf_survey_id =" . $row->sf_survey
                . "\n ORDER BY sf_name "
        ;
        $database->setQuery($query);

        $sf_sections[] = JHtmlSelect::option('0', JText::_('COM_SURVEYFORCE_SELECT_SECTION'));

        $sf_sections = @array_merge($sf_sections, ($database->LoadObjectList() == null ? array() : $database->LoadObjectList()));
        $sel_value = $is_return ? $sessions->get('sf_section_id_sf') : $row->sf_section_id;
        if (count($sf_sections) > 2) {
            $sf_sections = JHtmlSelect::genericlist($sf_sections, 'jform[sf_section_id]', 'class="text_area" size="1" ', 'value', 'text', intval($sel_value));
            $lists['sf_section_id'] = $sf_sections;
        } else {
            $lists['sf_section_id'] = null;
        }

        $query = "SELECT id AS value, sf_qtext AS text"
                . "\n FROM #__survey_force_quests WHERE id <> '" . $id . "' AND sf_qtype <> 8 "
                . ($row->sf_survey ? "\n and sf_survey = '" . $row->sf_survey . "'" : '')
                . "\n ORDER BY ordering, id "
        ;
        $database->setQuery($query);
        $quests = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        $i = 0;
        while ($i < count($quests)) {
            $quests[$i]->text = strip_tags($quests[$i]->text);
            if (strlen($quests[$i]->text) > 55)
                $quests[$i]->text = substr($quests[$i]->text, 0, 55) . '...';
            $quests[$i]->text = $quests[$i]->value . ' - ' . $quests[$i]->text;
            $i++;
        }
        $quest = JHtmlSelect::genericlist($quests, 'sf_quest_list', 'class="text_area" id="sf_quest_list" size="1" ', 'value', 'text', 0);
        $lists['quests'] = $quest;


        $query = "SELECT id AS value, sf_qtext AS text"
                . "\n FROM #__survey_force_quests WHERE id <> '" . $id . "' AND sf_qtype NOT IN (4, 7, 8) "
                . ($row->sf_survey ? "\n and sf_survey = '" . $row->sf_survey . "'" : '')
                . "\n ORDER BY ordering, id "
        ;
        $database->setQuery($query);
        $quests3 = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        $i = 0;
        while ($i < count($quests3)) {
            $quests3[$i]->text = strip_tags($quests3[$i]->text);
            if (strlen($quests3[$i]->text) > 55)
                $quests3[$i]->text = substr($quests3[$i]->text, 0, 55) . '...';
            $quests3[$i]->text = $quests3[$i]->value . ' - ' . $quests3[$i]->text;
            $i++;
        }

        $quest = JHtmlSelect::genericlist($quests3, 'sf_quest_list3', 'class="text_area" id="sf_quest_list3" size="1" onchange="javascript: showOptions(this.value);" ', 'value', 'text', 0);
        $lists['quests3'] = $quest;

        $query = "SELECT a.*, c.`sf_qtext`, c.`sf_qtype`, c.`id` AS `qid`,  d.`ftext` AS `aftext`, e.`stext` AS `astext`, b.`ftext` AS `qoption`, b.`id` AS `bid`, d.`id` AS `fdid`, e.`id` AS `sdid` FROM  `#__survey_force_fields` AS b, `#__survey_force_quests` AS c, `#__survey_force_quest_show` AS a LEFT JOIN `#__survey_force_fields` AS d ON a.`ans_field` = d.`id` LEFT JOIN `#__survey_force_scales` AS e ON a.`ans_field` = e.`id` WHERE a.`quest_id` = '" . $id . "' AND a.`answer` = b.`id` AND a.`quest_id_a` = c.`id` ";
        $database->setQuery($query);

        $lists['quest_show'] = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
 
        $i = 0;
        while ($i < count($lists['quest_show'])) {
            $lists['quest_show'][$i]->sf_qtext = strip_tags($lists['quest_show'][$i]->sf_qtext);
            if (strlen($lists['quest_show'][$i]->sf_qtext) > 55)
                $lists['quest_show'][$i]->sf_qtext = substr($lists['quest_show'][$i]->sf_qtext, 0, 55) . '...';
            $lists['quest_show'][$i]->sf_qtext = $lists['quest_show'][$i]->qid . ' - ' . $lists['quest_show'][$i]->sf_qtext;
            $i++;
        }

        $query = "SELECT next_quest_id "
                . "\n FROM #__survey_force_rules WHERE quest_id = '" . $row->id . "' and answer_id = 9999997 ";
        $database->setQuery($query);
        $squest = (int) $database->LoadResult();
        $quest = JHtmlSelect::genericlist($quests, 'sf_quest_list2', 'class="text_area" id="sf_quest_list2" size="1" ', 'value', 'text', $squest);
        $lists['quests2'] = $quest;
        $lists['checked'] = '';
        if ($squest)
            $lists['checked'] = ' checked = "checked" ';

        $lists['sf_fields_rule'] = array();
        $query = "SELECT b.ftext, c.sf_qtext, c.id as next_quest_id, a.priority, d.ftext as alt_ftext "
                . "\n FROM  #__survey_force_fields as b, #__survey_force_quests as c, #__survey_force_rules as a LEFT JOIN #__survey_force_fields as d ON a.alt_field_id = d.id "
                . "\n WHERE a.quest_id = '" . $row->id . "' and a.answer_id <> 9999997 and a.answer_id = b.id and a.next_quest_id = c.id ";
        $database->SetQuery($query);
        $lists['sf_fields_rule'] = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        if ($is_return) {
            $lists['sf_fields_rule'] = array();
            $sf_hid_rule = $sessions->get('sf_hid_rule_sf');
            $sf_hid_rule_quest = $sessions->get('sf_hid_rule_quest_sf');
            $sf_hid_rule_alt = $sessions->get('sf_hid_rule_alt_sf');
            $priority = $sessions->get('priority_sf');
            for ($i = 0, $n = count($sf_hid_rule); $i < $n; $i++) {
                $tmp = new stdClass();
                $tmp->next_quest_id = $sf_hid_rule_quest[$i];
                $tmp->ftext = $sf_hid_rule[$i];
                $tmp->alt_ftext = $sf_hid_rule_alt[$i];
                $tmp->priority = $priority[$i];
                $query = "SELECT c.sf_qtext FROM `#__survey_force_quests` as c WHERE c.id = " . $sf_hid_rule_quest[$i];
                $database->SetQuery($query);
                $tmp->sf_qtext = $database->LoadResult();
                $lists['sf_fields_rule'][] = $tmp;
            }
        }

        if (!is_array($lists['sf_fields_rule']) || count($lists['sf_fields_rule']) < 1)
            $lists['sf_fields_rule'] = array();

        return $lists;
    }


	public function getQuestionType( $_type ) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__extensions AS a');
		$query->where("a.type = 'plugin'");
		$query->where("a.folder = 'survey'");
		$query->where("a.element = ". $db->quote($_type) );

		$db->setQuery($query);
		$row = $db->loadObject();


		return $row;
	}

	public function getItems($sid) {
		$app = JFactory::getApplication();
		$db  = $this->getDBO();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs'));
		$query->where('id = ' . (int) $sid);

		$db->setQuery($query);

		$surv = $db->loadObject();

		return $surv;
	}

}