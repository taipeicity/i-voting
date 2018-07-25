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
class SurveyforceViewExport extends JViewLegacy {

	protected $items;
	protected $state;


	public function display($tpl = null) {

		$app     = JFactory::getApplication();
		$layout  = $this->getLayout();
		$surv_id = $app->input->getInt("surv_id");

		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		$orderby = $this->item->result_orderby;

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


		$level_type = ["低", "中", "高"];
		$survs      = [];
		if ($this->item) {

			$questions = $this->get('Questions');

			$survs[] = $this->form->getLabel('title') . "：" . $this->form->getValue('title');

			$survs[] = $this->form->getLabel('desc') . "：" . (($layout == "default") ? nl2br($this->form->getValue('desc')) : $this->form->getValue('desc'));

			// 提案檢核階段
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_CHECK') . "</b>：<span>&nbsp;</span>";

			$survs[] = $this->form->getLabel('proposer') . "：" . $this->form->getValue('proposer');

			$survs[] = $this->form->getLabel('plan_quest') . "：" . (($layout == "default") ? nl2br($this->form->getValue('plan_quest')) : $this->form->getValue('plan_quest'));

			$survs[] = $this->form->getLabel('plan_options') . "：" . (($layout == "default") ? nl2br($this->form->getValue('plan_options')) : $this->form->getValue('plan_options'));

			if ($this->form->getValue('proposal_download')) {
				$field_name = 'proposal_download';
			} else {
				$field_name = 'proposal_url';
			}
			$survs[] = $this->form->getLabel('proposal') . "：" . $this->form->getValue($field_name);

			$survs[] = $this->form->getLabel('precautions') . "：" . (($layout == "default") ? nl2br($this->form->getValue('precautions')) : $this->form->getValue('precautions'));

			$survs[] = $this->form->getLabel('second_the_motion') . "：" . $this->form->getValue('second_the_motion');

			$survs[] = $this->form->getLabel('deadline') . "：" . JHtml::_('date', $this->form->getValue('deadline'), "Y年m月d日 H:i");

			// 提案初審階段
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_REVIEW') . "</b>：<span>&nbsp;</span>";

			$survs[] = $this->form->getLabel('review_result') . "：" . (($layout == "default") ? nl2br($this->form->getValue('review_result')) : $this->form->getValue('review_result'));

			$survs[] = $this->form->getLabel('review_download') . "：" . $this->form->getValue('review_download');

			if ($this->form->getValue('review_download_ii')) {
				$survs[] = $this->form->getLabel('review_download_ii') . "：" . $this->form->getValue('review_download_ii');
			}

			// 提案討論階段
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_DISCUSS') . "</b>：<span>&nbsp;</span>";

			if ($layout == "default") {
				$discuss = nl2br($this->item->discuss_source);
			} else {
				if (preg_match_all("/https?\:\/\/.+\"/", $this->form->getValue('discuss_source'))) {
					$discuss = SurveyforceHelper::replaceUrl($this->form->getValue('discuss_source'));
				} else {
					$discuss = $this->form->getValue('discuss_source');
				}
			}
			$survs[] = $this->form->getLabel('discuss_source') . "：" . $discuss;

			$survs[] = $this->form->getLabel('discuss_plan_options') . "：" . (($layout == "default") ? nl2br($this->form->getValue('discuss_plan_options')) : $this->form->getValue('discuss_plan_options'));

			$survs[] = $this->form->getLabel('discuss_qualifications') . "：" . (($layout == "default") ? nl2br($this->form->getValue('discuss_qualifications')) : $this->form->getValue('discuss_qualifications'));

			$survs[] = $this->form->getLabel('discuss_verify') . "：" . SurveyforceHelper::getVerifyName($this->form->getValue('discuss_verify'));

			$survs[] = $this->form->getLabel('discuss_vote_time') . "：" . $this->form->getValue('discuss_vote_time');

			$survs[] = $this->form->getLabel('discuss_threshold') . "：" . (($layout == "default") ? nl2br($this->form->getValue('discuss_threshold')) : $this->form->getValue('discuss_threshold'));

			$survs[] = $this->form->getLabel('discuss_download') . "：" . $this->form->getValue('discuss_download');

			// 形成選項階段
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_OPTIONS') . "</b>：<span>&nbsp;</span>";

			$survs[] = $this->form->getLabel('options_cohesion') . "：" . (($layout == "default") ? nl2br($this->form->getValue('options_cohesion')) : $this->form->getValue('options_cohesion'));

			if ($layout == "default") {
				$default = JText::_('COM_SURVEYFORCE_OPTIONS_AGREE') . "：" . $this->form->getValue('options_agree');
				$default .= "<br>" . JText::_('COM_SURVEYFORCE_OPTIONS_OPPOSE') . "：" . $this->form->getValue('options_oppose');
			} else {
				$export = JText::_('COM_SURVEYFORCE_OPTIONS_AGREE') . "：" . $this->form->getValue('options_agree');
				$export .= "," . JText::_('COM_SURVEYFORCE_OPTIONS_OPPOSE') . "：" . $this->form->getValue('options_oppose');
			}
			$survs[] = $this->form->getLabel('options_agree') . "：" . (($layout == "default") ? $default : $export);

			$survs[] = $this->form->getLabel('options_caption') . "：" . (($layout == "default") ? nl2br($this->form->getValue('options_caption')) : $this->form->getValue('options_caption'));

			// 宣傳準備與上架階段
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_LAUNCHED') . "</b>：<span>&nbsp;</span>";

			foreach ($questions as $i => $question):
				$array_ques[$question->id][$question->sf_qtext][] = $question->ftext;
			endforeach;
			$y        = 1;
			$question = '';
			foreach ($array_ques as $id => $array_que) {
				foreach ($array_que as $title_name => $item) {
					if (count($array_ques) > 1) {
						$question .= "第" . $y . "題、";
					}
					$question .= "議題：{$title_name}";
					$question .= "<br>";
					$question .= "選項方案：";
					$question .= "<br>";
					for ($i = 0; $i < count($item); $i++) {
						$j        = $i + 1;
						$question .= $j . "." . $item[$i];
					}
					$question .= "<br>";
					$y++;
				}
			}

			$survs[] = "議題與選項方案" . "：" . (($layout == "default") ? nl2br($question) : $question);

			$survs[] = $this->form->getLabel('voters_eligibility') . "：" . (($layout == "default") ? nl2br($this->form->getValue('voters_eligibility')) : $this->form->getValue('voters_eligibility'));

			$survs[] = $this->form->getLabel('voters_authentication') . "：" . $this->form->getValue('voters_authentication');

			$survs[] = "投票時間：" . $this->form->getValue('during_vote');

			$survs[] = $this->form->getLabel('vote_way') . "：" . (($layout == "default") ? nl2br($this->form->getValue('vote_way')) : $this->form->getValue('vote_way'));

			$survs[] = $this->form->getLabel('launched_condition') . "：" . (($layout == "default") ? nl2br($this->form->getValue('launched_condition')) : $this->form->getValue('launched_condition'));

			switch ($this->form->getValue('launched_date')) {
				case 1:
					$announcement_date = "不公布";
					break;
				case 2:
					$announcement_date = JHtml::_('date', $this->form->getValue('announcement_date'), "Y年m月d日");
					break;
				case 3:
					$announcement_date = JHtml::_('date', $this->form->getValue('vote_end'), "Y年m月d日");
					break;
			}

			$survs[] = JText::_('COM_SURVEYFORCE_LAUNCHED_DATETIME') . "：" . $announcement_date;

			$results_proportion = '';
			$rp = ["whole" => "完全參採", "part" => "部分參採", "committee" => "送請專業委員會決策考量", "other" => "其他"];
			$results_proportion .= $rp[$this->form->getValue('results_proportion')];
			if ($this->form->getValue('results_proportion') == "part") {
				$results_proportion .= "：<br>";
				$results_proportion .= $this->form->getValue('part');
			}

			if ($this->form->getValue('results_proportion') == "other") {
				$results_proportion .= "：<br>";
				$results_proportion .= $this->form->getValue('other');
			}
			$survs[] = $this->form->getLabel('results_proportion') . "：" . (($layout == "default") ? nl2br($results_proportion) : $results_proportion);

			$survs[] = $this->form->getLabel('discuss_download') . "：" . $this->form->getValue('discuss_download');

			// 投票、結果公布及執行
			$survs[] = "<b>" . JText::_('COM_SURVEYFORCE_RESULT') . "</b>：<span>&nbsp;</span>";

			$survs[] = $this->form->getLabel('result_instructions') . "：" . (($layout == "default") ? nl2br($this->form->getValue('result_instructions')) : $this->form->getValue('result_instructions'));

			$survs[] = $this->form->getLabel('how_to_use') . "：" . (($layout == "default") ? nl2br($this->form->getValue('how_to_use')) : $this->form->getValue('how_to_use'));


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
