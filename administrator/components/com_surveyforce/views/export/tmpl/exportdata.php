<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$filename = 'votelist';

$app = JFactory::getApplication();

$quest_index = 0;  // 第幾題用
$field_count = array(); // 票數
$total_count = array(); // 總票數
$result_num = $this->item->result_num; // 顯示數目
$qtype = array("select", "number", "table"); // 有子選項的題目類型

ob_start();

if ($this->results) {
	// 議題
	echo $this->item->title . "\r\n\r\n";
	
	// 投票人數
	echo "投票人數," . $this->total_num . "\r\n\r\n";
	
	foreach ($this->results as $key => $result) { $quest_index++;
		
		// 題目
		echo (count($this->results) > 1) ? "第" . $quest_index . "題：" : "題目";
		echo $result->quest_title . "\r\n";
		
		if (!in_array($result->quest_type, $qtype)) {  // 題目類型
			// 開放式欄位
			if($result->quest_type == 'open') {
				echo "開放式欄位";
				echo "\r\n";
				foreach($this->open as $open){
					echo $open->other;
					echo "\r\n";
				}
				echo "\r\n";
				continue;
			} 
			
			if(!$this->paper[$key]) {
				echo "投票類別,";
				echo "得票數";
				echo "\r\n";
			}else{
				echo "投票類別,";
				echo "網路,";
				echo "紙本,";
				echo "總得票數";
				echo "\r\n";	
			}

			unset($field_count);
			unset($total_count);
			foreach ($this->fields[$key] as $fkey => $field) {
				$field_count[$fkey] = ($result->count[$fkey]) ? ($result->count[$fkey]) : 0;
												
				// 是否有紙本投票
				if($this->paper[$key]) {
					$total_count[$fkey] = $field_count[$fkey] + $this->paper[$key][$fkey];
				}
			}
		
			// 依票數排序
			if ($this->item->result_orderby == 1) {
				// 是否有紙本投票
				if($this->paper[$key]) {
					arsort($total_count);
				}else{
					arsort($field_count);
				}
			}
		
			$num = 0;
			foreach (($this->paper[$key]) ? $total_count : $field_count as $ckey => $count) {
				$field_name = $this->fields[$key][$ckey];
				
				if(!$this->paper[$key]) {
					echo $field_name . ",";
					echo $count;
					echo "\r\n";
				}else{
					echo $field_name . ",";
					echo $field_count[$ckey] . ",";
					echo $this->paper[$key][$ckey] . ",";
					echo $count;
					echo "\r\n";	
				}			
				
				// 顯示數目
				$num++;
			}
			
		}else{
			
			// 表格式匯出為表格狀
			if($result->quest_type == 'table') {
				$i = 0;
				foreach($this->sub_fields[$key] as $sf) {
					$i++;
					if($i == 1) {
						echo ",";
					}
					echo $sf;
					if($i != count($this->sub_fields[$key])) {
						echo ",";
					}
				}
				echo "\r\n";
				
				foreach($result->field_title as $fkey => $field_title) {
					unset($field_count);
					unset($total_count);
					foreach($this->sub_fields[$key] as $sfkey => $sub_field) {
						$index = $fkey . "_" . $sfkey;
						$field_count[$sfkey] = ($this->sub_results[$index]->count) ? ($this->sub_results[$index]->count) : 0;
						if($this->sub_paper[$fkey]) {
							$total_count[$sfkey] = $field_count[$sfkey] + $this->sub_paper[$fkey][$sfkey];
						}
					}
					echo $field_title . ",";
					$j = 0;
					foreach(($this->sub_paper[$fkey]) ? $total_count : $field_count as $count) {
						$j++;
						echo $count;
						if($j != count($field_count)) {
							echo ",";
						}
					}
					echo "\r\n";
				}
				echo "\r\n";
				continue;	
			}


			foreach ($result->field_title as $fkey => $field_title) {
				
				echo $field_title . "\r\n";
				
				if(!$this->sub_paper[$fkey]) {
					echo "投票類別,";
					echo "得票數";
					echo "\r\n";
				}else{
					echo "投票類別,";
					echo "網路,";
					echo "紙本,";
					echo "總得票數";
					echo "\r\n";	
				}
				
				unset($field_count);
				unset($total_count);
				foreach ($this->sub_fields[$key] as $sfkey => $sub_field) {
					$index = $fkey . "_" . $sfkey;
					$field_count[$sfkey] = ($this->sub_results[$index]->count) ? ($this->sub_results[$index]->count) : 0;
												
					// 是否有紙本投票
					if($this->sub_paper[$fkey]) {
						$total_count[$sfkey] = $field_count[$sfkey] + $this->sub_paper[$fkey][$sfkey];
					}
				}
						
				// 依票數排序
				if ($this->item->result_orderby == 1) {
					// 是否有紙本投票
					if($this->sub_paper[$fkey]) {
						arsort($total_count);
					}else{
						arsort($field_count);
					}
				}
				
				$num = 0;
				foreach (($this->sub_paper[$fkey]) ? $total_count : $field_count as $ckey => $count) {
					$field_name = $this->sub_fields[$key][$ckey];
					
					if(!$this->sub_paper[$fkey]) {
						echo $field_name . ",";
						echo $count;
						echo "\r\n";
					}else{
						echo $field_name . ",";
						echo $field_count[$ckey] . ",";
						echo $this->sub_paper[$fkey][$ckey] . ",";
						echo $count;
						echo "\r\n";	
					}					
					
					// 顯示數目
					$num++;
				}
				
				echo "\r\n"; 
			}		
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

echo mb_convert_encoding($output, "Big5" , "UTF-8");
jexit();
?>
