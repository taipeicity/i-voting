<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$filename = 'analyzelist';

$app = JFactory::getApplication();

ob_start();

// 若驗證方式有台北通 + 身分證認證 + 投票人資料填寫
if ( strpos($this->survey_item->verify_type, "taipeicard") !== false && strpos($this->survey_item->verify_type, "idnum") !== false && strpos($this->survey_item->verify_type, "any") !== false) {
	echo "性別分析\r\n";
	echo "分析選項,網站,台北通,總計\r\n";
	echo sprintf("男,%d,%d,%d\r\n", ($this->male - $this->api_male), $this->api_male, $this->male);
	echo sprintf("女,%d,%d,%d\r\n", ($this->female - $this->api_female), $this->api_female, $this->female);
	echo sprintf("總共,%d,%d,%d\r\n", ($this->totalsex - $this->api_totalsex), $this->api_totalsex, $this->totalsex);

	echo "\r\n\r\n";

	
	echo "年齡分析\r\n";
	echo "分析選項,網站,台北通,總計\r\n";
	foreach ($this->age['age'] as $index => $item) {
		echo $item['title'].",";
		echo ($item['count'] - $this->api_age['age'][$index]['count']).",";
		echo $this->api_age['age'][$index]['count'].",";
		echo $item['count']."\r\n";
	}

	echo sprintf("總共,%d,%d,%d\r\n", ($this->age['total'] - $this->api_age[$index]['total']), $this->api_age[$index]['total'], $this->age['total']);
	
} else {
	echo "性別分析\r\n";
	echo "分析選項,人數\r\n";
	echo "男,{$this->male}\r\n";
	echo "女,{$this->female}\r\n";
	echo "總共,{$this->totalsex}";

	echo "\r\n\r\n";

	echo "年齡分析\r\n";
	echo "分析選項,人數\r\n";

	foreach ($this->age['age'] as $item) {
		echo $item['title'].",";
		echo $item['count']."\r\n";
	}

	echo "總共,{$this->age['total']}\r\n";
}
echo "\r\n\r\n";

if ($this->result) {

    foreach ($this->result as $key => $item) {
        // 分析題目
        echo "題目：".$item["quest_title"]."\r\n";

        foreach ($item["detail"] as $field_title => $count) {
            echo $field_title.",";
            echo $count."\r\n";
        }

        echo "\r\n";
    }
}

$output = ob_get_contents();
ob_end_clean();

header("Content-type: text/x-csv");

header("Content-Disposition: inline; filename=\"".$filename.".csv\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo mb_convert_encoding($output, "Big5", "UTF-8");
jexit();
?>
