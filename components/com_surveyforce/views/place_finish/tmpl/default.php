<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// 自動跳轉頁面
$doc = & JFactory::getDocument();
$link = JRoute::_("index.php?option=com_surveyforce&view=place_verify&sid={$this->survey_id}&Itemid={$this->itemid}", false);
$doc->setMetaData("refresh", "5; url='" . $link . "'", true);

?>
<div class="survey_finish">

	<div class="page-header" style="margin-top:200px; font-weight: bold; color: #BD2800;">
		您的投票已完成
	</div>

</div>
