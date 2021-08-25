<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Addend Controller
 */
class SurveyforceControllerAddend extends JControllerForm {

    public function __construct($config = array()) {
		
        parent::__construct($config);
    }


	// 匯出範例檔案
	public function exportAssignSampleFile() {

		$app = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');
		$assign_columns = $app->input->getString('assign_column');	// 取得所有欄位名稱
		$fileName = sprintf("assign_sample_%d.csv", $surv_id);		// 檔案名稱
		
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Content-Type: text/csv;charset=utf-8');
		header("Content-Disposition: attachment; filename=" . $fileName . "; filename*=UTF-8''" . urlencode($fileName));
		header("Content-type: application/force-download");

		$fp = fopen("php://output","w");
		fwrite($fp, chr(255). chr(254));
		$row = implode("\t", $assign_columns). "\n";
		$str = mb_convert_encoding($row, 'UTF-16LE', 'UTF-8');
		fwrite($fp, $str);

		fclose($fp);
		exit;


	}
	
	
}
