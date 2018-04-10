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

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewExport extends JViewLegacy {

	protected $items;
	protected $state;


	public function display($tpl = null) {

		$app     = JFactory::getApplication();
		$layout  = $this->getLayout();
		$surv_id = $app->input->getInt("surv_id");

		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$orderby     = $this->item->result_orderby;

		$model             = $this->getModel();
		$this->fields      = $model->getFields($orderby);
		$this->sub_fields  = $model->getSubFields($orderby);
		$this->results     = $this->get('Results');
		$this->sub_results = $this->get('SubResults');
		$this->paper       = $this->get('PaperResults');
		$this->sub_paper   = $this->get('PaperSubResults');
		$this->open        = $this->get('OpenResults');

		$this->total_num = $this->get('TotalNum');

		$this->verify_type = json_decode($this->item->verify_type);
		if (count($this->verify_type) == 1) {
			$plugin = JPluginHelper::getPlugin('verify', $this->verify_type[0]);
			if ($plugin) {
				// Get plugin params
				$pluginParams = new JRegistry($plugin->params);
				$level        = $pluginParams->get('level') - 1;
				if ($pluginParams->get('level') == 0) {
					$level = 0;
				}
			}
		}

//		$this->otherDataName = $this->get('IntroOtherData');

		$level_type = ["低", "中", "高"];
		$survs      = [];
		if ($this->item) {

			$survs[] = "名稱：" . $this->item->title;

			$survs[] = "議題介紹：" . (($layout == "default") ? nl2br($this->item->desc) : strip_tags($this->item->desc));

			if ($this->item->vote_way) {
				$survs[] = "投票方式：" . (($layout == "default") ? nl2br($this->item->vote_way) : strip_tags($this->item->vote_way));
			}
			$survs[] = "投票人資格：" . (($layout == "default") ? nl2br($this->item->voters_eligibility) : strip_tags($this->item->voters_eligibility));
			if ($this->item->is_define) {
				$survs[] = "投票人驗證方式：" . $this->item->voters_authentication;
				if ($this->item->verify_precautions) {
					$survs[] = "驗證方式注意事項說明：" . (($layout == "default") ? nl2br($this->item->verify_precautions) : strip_tags($this->item->verify_precautions));
				}
				if (count($this->verify_type) == 1) {
					$survs[] = "驗證強度：" . $level_type[$level];
				}
				$survs[] = "投票期間：" . $this->item->during_vote;
			}

			$survs[] = "宣傳推廣方式：" . (($layout == "default") ? nl2br($this->item->promotion) : strip_tags($this->item->promotion));
			$survs[] = "公布方式：" . (($layout == "default") ? nl2br($this->item->announcement_method) : strip_tags($this->item->announcement_method));

			if ($this->item->is_define) {
				if (!preg_match("/(0000\-00\-00)/", $this->item->announcement_date)) {
					$survs[] = "公布日期：" . JHtml::_('date', $this->item->announcement_date, "Y年n月j日G點i分");
				} else {
					$survs[] = "公布日期：不公布";
				}
			}

			$survs[] = "目前進度：" . (($layout == "default") ? nl2br($this->item->at_present) : strip_tags($this->item->at_present));


			if ($layout == "default") {
				$survs[] = "討論管道：" . nl2br($this->item->discuss_source);
			} else {
				if (preg_match_all("/https?\:\/\/.+\"/", $this->item->discuss_source)) {
					$survs[] = "討論管道：" . SurveyforceHelper::replaceUrl($this->item->discuss_source);
				} else {
					$survs[] = "討論管道：" . strip_tags($this->item->discuss_source);
				}
			}


			$results_proportion = '';
			switch ($this->item->results_proportion) {
				case "whole":
					$results_proportion = "完全參採";
					break;
				case "part":
					$results_proportion = "部分參採" . $this->item->part . "%";
					break;
				case "committee":
					$results_proportion = "送請專業委員會決策考量";
					break;
				case "other":
					$results_proportion = "其他(" . $this->item->other . ")";
					break;
			}

			$survs[] = "投票結果運用方式：" . $results_proportion;

			if($this->item->other_data) $other_data[] = $this->item->other_data;
			if($this->item->other_data2) $other_data[] = $this->item->other_data2;
			if($this->item->other_data3) $other_data[] = $this->item->other_data3;
			if (count($other_data) > 0) {
				$survs[] = "其他參考資料：" . implode("，", $other_data);
			}

			if ($this->item->other_url) {
				if ($layout == "default") {
					$other_url = "其他參考網址：" . '<a href="' . $this->item->other_url . '" target="_blank">' . $this->item->other_url . '</a>';
				} else {
					$other_url = "其他參考網址：" . $this->item->other_url;
				}
				$survs[] = $other_url;
			}

			if ($this->item->followup_caption) {
				if ($layout == "default") {
					$survs[] = "後續辦理情形：" . nl2br($this->item->followup_caption);
				} else {
					if (preg_match_all("/https?\:\/\/.+\"/", $this->item->followup_caption)) {
						$survs[] = "後續辦理情形：" . SurveyforceHelper::replaceUrl($this->item->followup_caption);
					} else {
						$survs[] = "後續辦理情形：" . strip_tags($this->item->followup_caption);
					}
				}


			}
			if ($layout == "default") {
				$survs[] = "注意事項：" . nl2br($this->item->precautions);
			} else {
				$survs[] = "注意事項：" . $this->item->precautions;
			}


			$this->intro = $survs;
		}

		JToolBarHelper::title("投票管理: 匯出結果");


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');

	}

}
