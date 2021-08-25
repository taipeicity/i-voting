<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewSurveys extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $cantDo;

    protected $undertaker;

    protected $print;

    protected $show_result;

    protected $export_result;

    public function display($tpl = null)
    {
        $params = JComponentHelper::getParams('com_surveyforce');

        if ($this->getLayout() == "default") {
            $config = JFactory::getConfig();
            $this->is_testsite = $config->get('is_testsite', false);
            $this->testsite_link = $config->get('testsite_link', '');

            $submenu = 'surveys';
            SurveyforceHelper::showTitle($submenu);

            $user = JFactory::getUser();
            $this->undertaker = $params->get('undertaker');
            $this->print = $params->get('print');
            $this->show_result = $params->get('show_result');
            $this->export_result = $params->get('export_result');
            $this->voting_itemid = $params->get('voting');
            $this->complete_itemid = $params->get('complete');
            $this->practice_itemid = $params->get('practice');
            $cross_unit = [$this->print, $this->show_result, $this->export_result];
            $action = true;

            foreach ($cross_unit as $item) {
                if (in_array($item, $user->groups)) {
                    $action = false;
                    break;
                }
            }

            // 匯出結果、議題列印、觀看結果三個權限看不到分析欄位
            if ($action) {
                SurveyforceHelper::addSubmenu($submenu);
            }

            $this->state = $this->get('State');
            $this->units = $this->get('Units');
            $this->pagination = $this->get('Pagination');
            $this->canDo = JHelperContent::getActions('com_surveyforce');
            $this->items = $this->get('Items');

            $model = $this->getModel();
            $this->totalnum = $model->getTotalnum($this->items);
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        } else {
            $app = JFactory::getApplication();
            $view = $app->input->getString("next", "intro");
            $id = $app->input->getInt("id");
            $this->item = SurveyforceHelper::getSuveryItem($id, true);
            $this->verify_type = $this->item->verify_type;
            $this->survey_id = $this->item->id;
            $this->preview = true;
            $layout = "default";
            $this->params = $params;

            if ($view != "intro") {
                if (!SurveyforceHelper::isSurveyExpired($this->item->id)) {
                    $view = "intro";
                }
            }

            switch ($view) {
                case "intro":
                    $this->view = $view;
                    // 處理議題預覽的檔案路徑
                    preg_match_all('/href\=\"(.+?)\"/', $this->item->desc, $match_href);
                    foreach ($match_href[1] as $key => $path) {
                        if (!preg_match("/https?/", $path)) {
                            $this->item->desc = str_replace($path, JURI::root() . $path, $this->item->desc);
                        }
                    }

                    // 處理議題預覽的檔案路徑
                    preg_match_all('/src\=\"(.+?)\"/', $this->item->desc, $match_img);
                    foreach ($match_img[1] as $key => $path) {
                        if (!preg_match("/https?/", $path)) {
                            $this->item->desc = str_replace($path, JURI::root() . $path, $this->item->desc);
                        }
                    }

                    $this->questions = $this->get('Questions');
                    $this->options = $this->get('Options');
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_intro&id={$this->item->id}&layout=preview", false);

                    require_once(JPATH_SITE . "/components/com_surveyforce/helpers/vote.php");

                    break;

                case "verify_opt":

                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify_opt&id={$this->item->id}&layout=preview&action=next_step", false);
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify_opt&id={$this->item->id}&layout=preview&action=previously_step", false);

                    break;

                case "verify":
                    require_once(JPATH_SITE . "/components/com_surveyforce/helpers/vote.php");
                    $this->verify_required = $this->item->verify_required;
                    $this->verify_params = $this->item->verify_params;
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify&id={$this->item->id}&layout=preview&action=previously_step", false);
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify&id={$this->item->id}&layout=preview&action=next_step", false);

                    if ($this->verify_required) {
                        $verify_type = json_decode($this->verify_type);
                        $this->url_param = $verify_type[0];
                    } else {
                        if (SurveyforceHelper::getPreviewData($this->item->id, "preview_type")) {
                            $verify_type = SurveyforceHelper::getPreviewData($this->item->id, "preview_type");
                            $this->url_param = $verify_type;
                            $this->verify_type = json_encode([$verify_type]);
                        } else {
                            $verify_type = json_decode($this->verify_type);
                            $this->url_param = $verify_type[0];
                        }
                    }

                    break;

                case "verify2nd":
                    $type = SurveyforceHelper::getPreviewData($this->item->id, "preview_type");
                    $this->verify2nd_type = json_encode([$type]);
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify2nd&id={$this->item->id}&layout=preview&action=previously_step", false);
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_verify2nd&id={$this->item->id}&layout=preview&action=next_step", false);

                    break;

                case "statement":
                    $this->statement_text = $params->get('statement_text');
                    // 上一頁
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_statement&id={$this->item->id}&layout=preview&action=previously_step", false);
                    // 下一頁
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_statement&id={$this->item->id}&layout=preview&action=next_step", false);

                    break;

                case "question":
                    require_once(JPATH_SITE . "/components/com_surveyforce/helpers/vote.php");
                    require_once(JPATH_SITE . "/components/com_surveyforce/models/question.php");
                    $question_id = $app->input->getInt('qid', 0);

                    // 取得所有題目
                    $this->questions = SurveyforceModelQuestion::getQuestions($this->item->id);
                    $this->questions_num = count($this->questions);

                    // 若題目ID為0 ，則給第1個題目ID
                    $this->question_id = ($question_id == 0) ? $this->questions[0]->id : $question_id;

                    // 計算題目編號
                    $this->count = 1;
                    foreach ($this->questions as $question) {
                        if ($question->id == $this->question_id) {
                            break;
                        }
                        $this->count++;
                    }

                    // 取得目前的題目
                    $this->question = SurveyforceModelQuestion::getQuestion($this->question_id);

                    // 取得選項
                    $this->options = SurveyforceModelQuestion::getOptions($this->question_id);

                    // 取得子選項
                    $this->sub_options = SurveyforceModelQuestion::getSubOptions($this->question_id);

                    // 有分類的題目
                    if ($this->question->question_type == "imgcat") {
                        $this->cats = SurveyforceModelQuestion::getQuestionCats($this->question_id);
                        $layout = "imgcat";
                    }

                    // 取得分析題目的下拉選單
                    if ($this->item->is_analyze == 1) {
                        $this->analyze_params = SurveyforceVote::getAnalyzeColumn($this->item->id);

                        // 確認是否填過分析欄位
                        if ($this->count == 1) {
                            $this->analyze_check = true;
                        } else {
                            $this->analyze_check = false;
                        }
                    }

                    // 上一頁
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_question&id={$this->item->id}&layout=preview&action=previously_step", false);

                    // 下一頁
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_question&id={$this->item->id}&layout=preview&action=next_step", false);

                    // 下一題
                    $this->next_question = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_question&id={$this->item->id}&layout=preview&action=next_question&qid={$this->question_id}", false);

                    // 上一題
                    $this->previously_question = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_question&id={$this->item->id}&layout=preview&action=previously_question&qid={$this->question_id}", false);

                    break;

                case "finish":

                    if ($this->item->is_lottery) {
                        if (SurveyforceHelper::getPreviewData($this->item->id, "lottery")) {
                            $this->task = "check_finish_form";
                            $this->lottery_remind = true;
                            $this->join_lottery = true;
                            $this->display_result = $this->item->display_result;
                        } else {
                            $this->is_lottery = $this->item->is_lottery;
                            $this->task = "setLotteryStep";
                        }
                    } else {
                        $this->task = "check_finish_form";
                        $this->lottery_remind = false;
                    }
                    $this->short_url = JURI::root() . "vote_detail.php?ticket={票號}";
                    $this->back_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_finish&id={$this->item->id}&layout=preview&action=previously_step", false);
                    $this->next_link = JRoute::_("index.php?option=com_surveyforce&task=surveys.preview_finish&id={$this->item->id}&layout=preview&action=next_step", false);

                    if (SurveyforceHelper::getPreviewData($this->item->id, "success") == true) {
                        $layout = "success";
                    }

                    break;
            }

            ob_start();
            include_once(JPATH_SITE . "/components/com_surveyforce/views/{$view}/tmpl/{$layout}.php");
            $content = ob_get_contents();
            ob_end_clean();
            $this->content = $content;
        }

        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return false;
        }

        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $user = JFactory::getUser();
		$bar = JToolBar::getInstance('toolbar');

        if (!(in_array($this->print, $user->groups) || in_array($this->show_result, $user->groups) || in_array($this->export_result, $user->groups))) {
            JToolBarHelper::addNew('survey.add');
            JToolBarHelper::editList('survey.edit');
            JToolBarHelper::divider();
			$bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-info" onclick="Joomla.submitbutton(\'surveys.go_count\')">議題數據分析</button></div>');
			$bar->appendButton('Custom', '<div class="btn-group"><button class="btn btn-small btn-primary" onclick="Joomla.submitbutton(\'surveys.go_graffic\')">議題流量分析</button></div>');
        }

        if ($this->canDo->get('core.config')) {
            JToolbarHelper::preferences('com_surveyforce');
        }

        JHtmlSidebar::setAction('index.php?option=com_surveyforce&view=surveys');

        JHtmlSidebar::addFilter('- 選擇是否公開 -', 'filter_public', JHtml::_('select.options', [
            "1" => "公開",
            "0" => "不公開",
        ], 'value', 'text', $this->state->get('filter.public'), true));

        JHtmlSidebar::addFilter('- 選擇議題所屬 -', 'filter_own', JHtml::_('select.options', [
            "1" => "自己",
            "2" => "同單位",
        ], 'value', 'text', $this->state->get('filter.own'), true));

        JHtmlSidebar::addFilter('- 選擇年度 -', 'filter_year', JHtml::_('select.options', $this->getYear(), 'value', 'text',
            $this->state->get('filter.year'), true));

        if (in_array(8, $user->groups)) {
            JHtmlSidebar::addFilter('- 選擇流程狀態 -', 'filter_status', JHtml::_('select.options', [
                "1" => "草稿",
                "2" => "待審核",
                "3" => "進行中",
                "4" => "已結束",
                "5" => "待上架",
                "6" => "已下架"
            ], 'value', 'text', $this->state->get('filter.status'), true));

            JHtmlSidebar::addFilter('- 選擇議題階段 ( 後台 ) -', 'filter_stage_back', JHtml::_('select.options', [
                "1" => "提案檢核",
                "2" => "提案初審",
                "3" => "提案討論",
                "4" => "形成選項",
                "5" => "宣傳準備與上架",
                "6" => "投票、結果公布及執行",
            ], 'value', 'text', $this->state->get('filter.stage_back'), true));

            JHtmlSidebar::addFilter('- 選擇議題階段 ( 前台 ) -', 'filter_stage_front', JHtml::_('select.options', [
                "1" => "提案檢核",
                "2" => "提案初審",
                "3" => "提案討論",
                "4" => "形成選項",
                "5" => "宣傳準備與上架",
                "6" => "投票、結果公布及執行",
            ], 'value', 'text', $this->state->get('filter.stage_front'), true));
        }
    }

    protected function getSortFields()
    {
        return [
            's.title' => '名稱',
            's.publish_up' => '上架時間',
            's.vote_start' => '開始投票時間',
            's.vote_end' => '投票結束時間',
            'ut.title' => '單位',
            'u.name' => '承辦人員',
            's.is_public' => '是否公開',
            's.id' => JText::_('JGRID_HEADING_ID'),
        ];
    }

    public function getYear()
    {
        $option = [];
        $rows = $this->get('Year');

        foreach ($rows as $row) {
            $option[$row->year] = $row->year;
        }

        return $option;
    }
}
