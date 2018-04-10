<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$filename = 'analyzelist';

$app = JFactory::getApplication();


ob_start();


if ($this->result) {

	foreach ($this->result as $key => $item) {


		// 分析題目
		echo "題目：" . $item["quest_title"] . "\r\n";

		foreach ($item["detail"] as $field_title => $count) {
			echo $field_title . ",";
			echo $count . "\r\n";
		}

		echo "\r\n";
	}
}


$output = ob_get_contents();
ob_end_clean();

header("Content-type: text/x-csv");

header("Content-Disposition: inline; filename=\"" . $filename . ".csv\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo mb_convert_encoding($output, "Big5", "UTF-8");
jexit();
?>
