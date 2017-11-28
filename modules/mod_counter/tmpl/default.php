<?php
/**
*   @package         Counter
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
/*
  if (getenv('HTTP_CLIENT_IP'))
  $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
  $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
  $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
  $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
  $ipaddress = getenv('REMOTE_ADDR');
  else
  $ipaddress = 'UNKNOWN';

  //$ip = ( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? ( $_SERVER['HTTP_X_FORWARDED_FOR'] ) : ( $_SERVER['REMOTE_ADDR'] );
  $ip = $ipaddress;
 *
 */

if ($is_show) {
	switch ($lang) {
		case 1:
			echo "<span class='foot_counter'>更新日期：" . date("Y-m-d") . "</span>";
			//echo "<span class='foot_counter'>今日瀏覽人數：$list->t_count</span>";
			echo "　<span class='foot_counter'>累積拜訪人數：$list->total</span>  ";
//			echo "　<span class='foot_counter'>使用者連線IP：$ip</span>";
			break;
		case 2:
			echo "<span class='foot_counter'>Last update：" . date("M d,Y") . "</span>";
			//echo "<span class='foot_counter'>Today Visitors：$list->t_count</span>";
			echo "　<span class='foot_counter'>Total Visitors：$list->total</span>";
//			echo "　<span class='foot_counter'>Users connect IP：$ip</span>";
			break;
		case 3:
			echo "<span class='foot_counter'>更新日期：" . date("Y-m-d") . "</span>";
			//echo "<span class='foot_counter'>今日瀏覽人數：$list->t_count</span>";
			echo "　<span class='foot_counter'>累積拜訪人數：$list->total</span>";
//			echo "　<span class='foot_counter'>使用者連線IP：$ip</span>";
			break;
	}
}
?>
