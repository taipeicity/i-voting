<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewImport extends JViewLegacy {

	protected $items;
    protected $state;
    

    public function display($tpl = null) {
		$model = $this->getModel();
		$config = JFactory::getConfig();
        $app = JFactory::getApplication();
		$this->surv_id = JFactory::getApplication()->input->get('surv_id', 0);

		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);
		JToolBarHelper::title("投票管理:". $this->survey_item->title. " - 匯入紙本投票資料");

        $this->state = $this->get('State');
        $this->paper_vote = $model->getPaperVote($this->surv_id);
       

		// 產生議題CSV檔
		$web_ivoting_path = $config->get( 'ivoting_path' );
		$this->csv_file = $web_ivoting_path. "/survey/surveys/sample_". $this->surv_id. ".csv";
		$real_csv_file = JPATH_SITE. "/". $this->csv_file;

		
		$this->questions = $model->getQuestions($this->surv_id);

		$file = fopen($real_csv_file, "w");
		fwrite($file, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($file, array("題目", "選項", "子選項", "票數") );

		if ($this->questions) {
			foreach ($this->questions as $question) {
				// 檢查是否有子選項
				$sub_options = $model->getSubOptions($question->question_id);

				if ($sub_options) {
					foreach ($sub_options as $sub_option) {		// 寫入子選項
						fputcsv($file, array($question->question_title, $question->option_title, $sub_option->sub_option_title, 0) );
					}
				} else {
					fputcsv($file, array($question->question_title, $question->option_title, "", 0) );
				}
			}
		}

		fputcsv($file, array("") );
		fputcsv($file, array("**請填上票數資料，其餘欄位請勿變更，否則可能造成匯入失敗或匯入錯誤的問題。") );
		fclose($file);
		
		
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
