<?php

/**
 * @package         Surveyforce
 * @version           1.0-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Result model.
 *
 */
class SurveyforceModelResult extends JModelList
{
    protected $text_prefix = 'COM_SURVEYFORCE';

    public function getItem()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select('*, q.id as qid, quantity, state');
        $query->from($db->quoteName('#__survey_force_survs_release', 's'));
        $query->leftJoin($db->quoteName('#__survey_force_survs_vote_quantity', 'q') . 'on `q`.`survey_id` = `s`.`id`');
        $query->where('`s`.`id` = '.(int) $sid);

        $db->setQuery($query);

        return $db->loadObject();
    }

    // 選項
    public function getFields()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("f.id, f.ftext, q.id AS qid");
        $query->from($db->quoteName('#__survey_force_fields', 'f'));
        $query->leftJoin($db->quoteName('#__survey_force_quests', 'q')." ON q.id = f.quest_id");
        $query->where($db->quoteName('q.sf_survey')." = ".$db->quote($sid));
        $query->where($db->quoteName('q.published')." = '1'");
        $query->order("qid, f.ordering");

        $db->setQuery($query);
        $items = $db->loadObjectList("id");

        $fields = [];
        foreach ($items as $key => $item) {
            $fields[$item->qid][$item->id] = $item->ftext;
        }

        return $fields;
    }

    // 子選項
    public function getSubFields()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(ture);
        $query->select("s.id, s.title, q.id AS qid");
        $query->from($db->quoteName('#__survey_force_sub_fields', 's'));
        $query->leftJoin($db->quoteName('#__survey_force_quests', 'q')." ON q.id = s.quest_id");
        $query->where($db->quoteName('q.sf_survey')." = ".$db->quote($sid));
        $query->where($db->quoteName('q.published')." = '1'");
        $query->order("qid, s.ordering");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $sub_fields = [];
        foreach ($items as $key => $item) {
            $sub_fields[$item->qid][$item->id] = $item->title;
        }

        return $sub_fields;
    }

    // 投票結果
    public function getResults($dates = false)
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("question_id, field_id, count(*) AS count");
        $query->from($db->quoteName('#__survey_force_vote_detail'));
        $query->where(sprintf("%s = %s", $db->quoteName('survey_id'), $db->quote($sid)));

        $query->group($db->quoteName('field_id'));

        if (is_array($dates)) {
            $query->select("DATE_FORMAT(created, '%Y-%m-%d') as date");
            $query->where(sprintf("%s BETWEEN %s AND %s", $db->quoteName('created'), $db->quote(sprintf("%s 00:00:00", $dates['firstdate'])), $db->quote(sprintf("%s 23:59:59", $dates['lastdate']))));
            $query->group($db->quoteName("date"));
            $query->order("date");
        } else {
            $query->where(sprintf("%s = %s", $db->quoteName('is_place'), $db->quote(0)));
        }

        $query->order("question_id , field_id");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $results = [];
        $quest = "";

        if (is_array($dates)) {
            $date = "";
            foreach ($items as $item) {
                if ($date != $item->date) {
                    $date = $item->date;
                }
                if ($quest != $item->question_id) {
                    $quest = $item->question_id;
                    $results[$date][$quest] = new stdClass();
                }
                $results[$date][$quest]->count[$item->field_id] = $item->count;
            }
        } else {
            foreach ($items as $item) {
                if ($quest != $item->question_id) {
                    $quest = $item->question_id;
                    $results[$quest] = new stdClass();
                }
                $results[$quest]->count[$item->field_id] = $item->count;
            }
        }

        return $results;
    }

    // 子選項投票結果
    public function getSubResults($dates = false)
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("d.field_id, d.sub_field_id, count(d.sub_field_id) AS count");
        $query->from($db->quoteName('#__survey_force_vote_detail', 'd'));
        $query->where($db->quoteName('d.survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('d.sub_field_id')." != '0'");

        $query->group("field_id, d.sub_field_id");

        if (is_array($dates)) {
            $query->select("DATE_FORMAT(created, '%Y-%m-%d') as date");
            $query->where(sprintf("%s BETWEEN %s AND %s", $db->quoteName('created'), $db->quote(sprintf("%s 00:00:00", $dates['firstdate'])), $db->quote(sprintf("%s 23:59:59", $dates['lastdate']))));
            $query->group($db->quoteName("date"));
            $query->order("date");
        } else {
            $query->where($db->quoteName('d.is_place')." = '0'");
        }

        $query->order("d.field_id , d.sub_field_id");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $subs = [];

        if (is_array($dates)) {
            foreach ($items as $key => $item) {
                $index = $item->field_id."_".$item->sub_field_id;
                $subs[$item->date][$index] = new stdClass();
                $subs[$item->date][$index]->count = $item->count;
            }
        } else {
            foreach ($items as $key => $item) {
                $index = $item->field_id."_".$item->sub_field_id;
                $subs[$index] = new stdClass();
                $subs[$index]->count = $item->count;
            }
        }

        return $subs;
    }

    public function getQuestions()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("q.id, q.sf_qtext AS quest_title, q.question_type, f.id AS field_id, f.ftext AS field_title");
        $query->from($db->quoteName('#__survey_force_quests', 'q'));
        $query->leftJoin($db->quoteName('#__survey_force_fields', 'f')." ON f.quest_id = q.id");
        $query->where($db->quoteName('q.sf_survey')." = ".$db->quote($sid));
        $query->where($db->quoteName('q.published')." = '1'");
        $query->order("q.ordering , f.id");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $results = [];
        $quest = "";
        foreach ($items as $key => $item) {
            if ($quest != $item->id) {
                $quest = $item->id;
                $results[$quest] = new stdClass();
                $results[$quest]->quest_title = $item->quest_title;
                $results[$quest]->quest_type = $item->question_type;
            }
            $results[$quest]->field_title[$item->field_id] = $item->field_title;
        }

        return $results;
    }

    public function getSubQuestions()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("d.field_id, d.sub_field_id, s.title AS sub_field_title, count(d.sub_field_id) AS count");
        $query->from($db->quoteName('#__survey_force_vote_detail', 'd'));
        $query->leftJoin($db->quoteName('#__survey_force_quests', 'q')." ON q.id = d.question_id");
        $query->leftJoin($db->quoteName('#__survey_force_sub_fields', 's')." ON s.id = d.sub_field_id");
        $query->where($db->quoteName('d.survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('q.question_type')." IN ('select', 'number', 'table')");
        $query->where($db->quoteName('q.published')." = '1'");
        $query->group("d.field_id, d.sub_field_id");
        $query->order("d.field_id , d.sub_field_id");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $subs = [];
        foreach ($items as $key => $item) {
            $index = $item->field_id."_".$item->sub_field_id;
            $subs[$index] = new stdClass();
            $subs[$index]->field_id = $item->field_id;
            $subs[$index]->sub_field_id = $item->sub_field_id;
            $subs[$index]->sub_field_title = $item->sub_field_title;
            $subs[$index]->count = $item->count;
        }

        return $subs;
    }

    // 紙本投票結果
    public function getPaperResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_paper'));
        $query->where($db->quoteName('survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('sub_field_id')." = '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $paper = [];
        foreach ($items as $key => $item) {
            $paper[$item->question_id][$item->field_id] = $item->vote_num;
        }

        return $paper;
    }

    // 子選項紙本投票結果
    public function getPaperSubResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_paper'));
        $query->where($db->quoteName('survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('sub_field_id')." != '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $sub_paper = [];
        foreach ($items as $key => $item) {
            $sub_paper[$item->field_id][$item->sub_field_id] = $item->vote_num;
        }

        return $sub_paper;
    }

    public function getOpenResults($dates = false)
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("question_id, field_id, other");
        $query->from($db->quoteName('#__survey_force_vote_detail'));
        $query->where(sprintf("%s = %s", $db->quoteName('survey_id'), $db->quote($sid)));
        $query->where(sprintf("%s != ''", $db->quoteName('other')));

        if (is_array($dates)) {
            $query->select("DATE_FORMAT(created, '%Y-%m-%d') as date");
            $query->where(sprintf("%s BETWEEN %s AND %s", $db->quoteName('created'), $db->quote(sprintf("%s 00:00:00", $dates['firstdate'])), $db->quote(sprintf("%s 23:59:59", $dates['lastdate']))));
            $query->group($db->quoteName("ticket_num"));
            $query->order("date");
        }

        $db->setQuery($query);
        $items = $db->loadObjectList();

        if (is_array($dates)) {
            foreach ($items as $key => $item) {
                unset($items[$key]);
                $items[$item->date][$item->question_id][] = $item->other;
            }
        }

        return $items;
    }

    // 取得總投票人數
    public function getTotalVoters()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("COUNT(DISTINCT ticket_num)");
        $query->from($db->quoteName('#__survey_force_vote_detail'));
        $query->where($db->quoteName('survey_id') . ' = ' . $db->quote($sid));

        $db->setQuery($query);

        return $db->loadResult();
    }

    // 取得網路投票人數
    public function getInterVoters()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("COUNT(DISTINCT ticket_num)");
        $query->from($db->quoteName('#__survey_force_vote_detail'));
        $query->where($db->quoteName('survey_id') . ' = ' . $db->quote($sid));
        $query->where($db->quoteName('is_place') . ' = 0');

        $db->setQuery($query);

        return $db->loadResult();
    }

    // 現地投票結果
    public function getPlaceResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("question_id, field_id, count(*) AS vote_num");
        $query->from($db->quoteName('#__survey_force_vote_detail'));
        $query->where($db->quoteName('survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('is_place')." = 1");

        $query->group($db->quoteName('field_id'));

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $paper = [];
        foreach ($items as $key => $item) {
            $paper[$item->question_id][$item->field_id] = $item->vote_num;
        }

        return $paper;
    }

    // 子選項現地投票結果
    public function getPlaceSubResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("question_id, field_id, sub_field_id, count(sub_field_id) AS vote_num");
        $query->from('#__survey_force_vote_detail');
        $query->where($db->quoteName('survey_id')." = ".$db->quote($sid));
        $query->where($db->quoteName('sub_field_id')." > '0'");
        $query->where($db->quoteName('is_place')." = 1");

        $query->group($db->quoteName('field_id'));
        $query->group($db->quoteName('sub_field_id'));

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $sub_paper = [];
        foreach ($items as $key => $item) {
            $sub_paper[$item->field_id][$item->sub_field_id] = $item->vote_num;
        }

        return $sub_paper;
    }

    // 驗證方式投票結果
    public function getVerifyResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_vote_verify_count');
        $query->where($db->qn('survey_id').' = '.$db->q($sid));

        $db->setQuery($query);

        return $db->loadObjectList();
    }
	
	// 由API投票的驗證方式投票結果
    public function getVerifyApiResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_vote_verify_count_api');
        $query->where($db->qn('survey_id').' = '.$db->q($sid));

        $db->setQuery($query);

        return $db->loadAssocList("verify_type", "count");
    }


    // 取得前台投票結果
    public function getCountResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $qtype = array("select", "number", "table");
        $db    = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("c.*");
        $query->from($db->qn('#__survey_force_quests', 'q'));
        $query->leftJoin($db->qn('#__survey_force_vote_count', 'c') . ' ON c.question_id = q.id');
        $query->where($db->qn('q.sf_survey') . " = " . $db->q($sid));
        $query->where($db->qn('q.published') . " = " . $db->q(1));

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $results = array();
        $quest   = "";
        foreach ($items as $key => $item) {
            if (!is_null($item->id)) {
                if ($quest != $item->question_id) {
                    $quest                        = $item->question_id;
                    $results[$quest]              = new stdClass();
                    $results[$quest]->quest_title = $item->question_title;
                    $results[$quest]->quest_type  = $item->question_type;
                }
                $results[$quest]->field_title[$item->field_id] = $item->field_title;
                if (!in_array($item->question_type, $qtype)) {
                    $results[$quest]->count[$item->field_id] = $item->count;
                }
            }
        }

        return $results;
    }

    // 取得前台子選項投票結果
    public function getCountSubResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->getInt('id');
        $db  = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select("sc.*");
        $query->from($db->qn('#__survey_force_quests', 'q'));
        $query->leftJoin($db->qn('#__survey_force_vote_count', 'c') . ' ON c.question_id = q.id');
        $query->rightJoin($db->qn('#__survey_force_vote_sub_count', 'sc') . ' ON sc.field_id = c.field_id');
        $query->where($db->qn('q.sf_survey') . " = " . $db->q($sid));
        $query->where($db->qn('q.published') . " = " . $db->q(1));

        $db->setQuery($query);
        $items = $db->loadObjectList();


        $results = array();
        $quest   = "";
        foreach ($items as $key => $item) {
            $index                            = $item->field_id . "_" . $item->sub_field_id;
            $results[$index]                  = new stdClass();
            $results[$index]->field_id        = $item->field_id;
            $results[$index]->sub_field_id    = $item->sub_field_id;
            $results[$index]->sub_field_title = $item->sub_field_title;
            $results[$index]->count           = $item->count;
        }

        return $results;
    }

    // 取得前台紙本投票結果
    public function getCountPaperResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->get('id');
        $db  = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_paper'));
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
        $query->where($db->quoteName('sub_field_id') . " = '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $paper = array();
        foreach ($items as $key => $item) {
            $paper[$item->question_id][$item->field_id] = $item->vote_num;
        }

        return $paper;
    }

    // 取得前台子選項紙本投票結果
    public function getCountPaperSubResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->get('id');
        $db  = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_paper'));
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
        $query->where($db->quoteName('sub_field_id') . " != '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $sub_paper = array();
        foreach ($items as $key => $item) {
            $sub_paper[$item->field_id][$item->sub_field_id] = $item->vote_num;
        }

        return $sub_paper;
    }


    // 取得前台現地投票結果
    public function getCountPlaceResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->get('id');
        $db  = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_place'));
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
        $query->where($db->quoteName('sub_field_id') . " = '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $paper = array();
        foreach ($items as $key => $item) {
            $paper[$item->question_id][$item->field_id] = $item->vote_num;
        }

        return $paper;
    }

    // 取得前台子選項現地投票結果
    public function getCountPlaceSubResults()
    {
        $app = JFactory::getApplication();
        $sid = $app->input->get('id');
        $db  = $this->getDBO();

        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->quoteName('#__survey_force_vote_place'));
        $query->where($db->quoteName('survey_id') . " = " . $db->quote($sid));
        $query->where($db->quoteName('sub_field_id') . " != '0'");

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $sub_paper = array();
        foreach ($items as $key => $item) {
            $sub_paper[$item->field_id][$item->sub_field_id] = $item->vote_num;
        }

        return $sub_paper;
    }

    public function getQuantity()
    {
        $model = JModelLegacy::getInstance('survey', 'SurveyforceModel', array('ignore_request' => true));
        return $model->getQuantity();
    }
}
?>