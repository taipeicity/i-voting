<?php

/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewResult extends JViewLegacy
{
    protected $surv_id;

    protected $item;

    protected $dateRange;

    protected $state;

    protected $total_voters;

    protected $fields;

    protected $sub_fields;

    protected $results;

    protected $sub_results;

    protected $paper;

    protected $sub_paper;

    protected $place;

    protected $sub_place;

    protected $DayResults;

    protected $DaySub_results;

    protected $DayPaper;

    protected $DaySub_paper;

    protected $DayPlace;

    protected $Daysub_place;

    protected $pagination;

    protected $totalPage;

    protected $li;

    protected $verifyResults;

    protected $verifyTypes;

    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');

        $app = JFactory::getApplication();
        $this->surv_id = $app->input->getInt('id');
		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);

        $this->total_voters = $this->get('TotalVoters'); // 取得總投票人數
        $this->pagination = $app->input->getInt('pagination', '1');

        if ($this->total_voters > 0) {
            $this->model = $this->getModel();
            $this->totalResults();
            $this->verifyResults();
            $this->dayResults();
			
			// 取得透過API投票的票數
			$this->verifyApiResults = $this->get('VerifyApiResults');
        }

        $this->quantity = $this->get('Quantity');

        // 註記當下頁籤
        $session = JFactory::getSession();
        $this->mark = $session->get('mark', 'total', 'result');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return false;
        }

        $this->addToolbar();
        $this->addScript();
        parent::display($tpl);
    }

    protected function addToolbar()
    {

        JFactory::getApplication()->input->set('hidemainmenu', true);
        JToolBarHelper::title("投票管理:".$this->item->title." - 觀看結果");

        JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
    }

    protected function addScript()
    {
        JHtml::_('jquery.framework');
        $document = JFactory::getDocument();
        $document->addScript("components/com_surveyforce/views/result/assets/result.js?".time());
    }

    protected function pagination()
    {
        $li = '';
        if ($this->pagination == 1) {
            $li .= '<li class="disabled"><a><i class="icon-first"></i></a></li>';
            $li .= '<li class="disabled"><a><i class="icon-previous"></i></a></li>';
        } else {
            $li = '<li><a class="hasTooltip" title="最先" href="javascript:void(0)" onclick="document.adminForm.pagination.value=1; Joomla.submitform();return false;">';
            $li .= '<i class="icon-first"></i></a></li>';
            $li .= '<li><a class="hasTooltip" title="上一頁" href="javascript:void(0)" onclick="document.adminForm.pagination.value='.($this->pagination - 1).'; Joomla.submitform();return false;">';
            $li .= '<i class="icon-previous"></i></a></li>';
        }

        // 最多五頁
        if ($this->totalPage > 5) {
            if ($this->pagination > 2) {
                if (($this->totalPage - $this->pagination) < 2) {
                    $startpage = $this->totalPage - 4;
                    $endpage = $this->totalPage;
                } else {
                    $startpage = $this->pagination - 2;
                    $endpage = $this->pagination + 2;
                }
            } else {
                $startpage = 1;
                $endpage = 5;
            }
        } else {
            $startpage = 1;
            $endpage = $this->totalPage;
        }

        for ($i = $startpage; $i <= $endpage; $i++) {
            $li .= sprintf('<li class="hidden-phone%s">', $i == $this->pagination ? ' active' : '');
            if ($i == $this->pagination) {
                $li .= '<a>'.$i.'</a>';
            } else {
                $li .= '<a href="javascript:void(0)" onclick="document.adminForm.pagination.value='.$i.'; Joomla.submitform();return false;">'.$i.'</a>';
            }
            $li .= '</li>';
        }

        if ($this->totalPage == $this->pagination) {
            $li .= '<li class="disabled"><a><i class="icon-next"></i></a></li>';
            $li .= '<li class="disabled"><a><i class="icon-last"></i></a></li>';
        } else {
            $li .= '<li><a class="hasTooltip" title="下一頁" href="javascript:void(0)"
               onclick="document.adminForm.pagination.value='.($this->pagination + 1).'; Joomla.submitform();return false;"><i class="icon-next"></i></a></li>';
            $li .= '<li><a class="hasTooltip" title="最後" href="javascript:void(0)"
               onclick="document.adminForm.pagination.value='.$this->totalPage.'; Joomla.submitform();return false;"><i
                        class="icon-last"></i></a></li>';
        }

        $this->li = $li;
    }

    public function totalResults()
    {
        // 類型、題目、選項題目資料
        $this->questions = $this->get('Questions');
        $this->sub_questions = $this->get('SubQuestions');

        $this->fields = $this->get('Fields');
        $this->sub_fields = $this->get('SubFields');

        // 總投票結果
        $this->results = $this->get('CountResults');
        $this->sub_results = $this->get('CountSubResults');
        $this->paper = $this->get('CountPaperResults');
        $this->sub_paper = $this->get('CountPaperSubResults');
        $this->place = $this->get('CountPlaceResults');
        $this->sub_place = $this->get('CountPlaceSubResults');

        $this->resultNum = $this->get('InterVoters');
        $this->placeResultNum = $this->get('PlaceVoters');

        // 取得開放式欄位資料
        $this->open = $this->get('OpenResults');
    }

    public function verifyResults()
    {
        $this->verifyResults = $this->get('VerifyResults');

		/* 
        if($this->surv_id > 113){
            $this->total_voters = 0;
            foreach ($this->verifyResults as $item) {
                $this->total_voters += $item->count;
            }
        }
		
		*/

        $surveyModel = JModelLegacy::getInstance('Survey', 'SurveyforceModel');
        $verifyAllTypes = $surveyModel->getAllVerifyType();
        if (! empty($this->verifyResults)) {
            $verifyAll = [];
            foreach ($verifyAllTypes as $type) {
                $verifyAll[$type->element] = $type->name;
            }
            $this->verifyAllTypes = $verifyAll;
        }
    }

    public function dayResults()
    {
        // 每日投票結果
        // 分頁數
        $start = strtotime(JHtml::date($this->item->vote_start, 'Y-m-d'));
        $end = strtotime(JHtml::date($this->item->vote_end, 'Y-m-d'));

        // 投票總天數
        $current = strtotime(JHtml::date(time(), 'Y-m-d'));
        if ($current < $end) {
            $end = $current;
        }

        // 取得全部日期
        while ($start <= $end) {
            $week[] = JHtml::date($start, 'Y-m-d');

            if (count($week) == 7) {
                $date['firstdate'] = current($week);
                $date['lastdate'] = end($week);
                $dates[] = $date;
                unset($week);
            } else {
                if ($start == $end) {
                    $date['firstdate'] = current($week);
                    $date['lastdate'] = end($week);
                    $dates[] = $date;
                }
            }

            $start = strtotime('+1 day', $start);
        }

        // 分頁總頁數
        $this->totalPage = count($dates);

        $weekStart = strtotime($dates[$this->pagination - 1]['firstdate']);
        $weekLast = strtotime($dates[$this->pagination - 1]['lastdate']);

        while ($weekStart <= $weekLast) {
            $range[] = JHtml::date($weekStart, 'Y-m-d');
            $weekStart = strtotime('+1 day', $weekStart);
        }

        $this->range = $range;

        if ($this->totalPage > 1) {
            $this->pagination();
        }

        $this->weekResults = $this->model->getResults($dates[$this->pagination - 1]);
        $this->weekSubResults = $this->model->getSubResults($dates[$this->pagination - 1]);
        $this->weekOpenResults = $this->model->getOpenResults($dates[$this->pagination - 1]);
    }
}
