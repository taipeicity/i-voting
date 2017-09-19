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


?>
<div class="survey_finish">

	<div class="page-header red">
		訊息寄送成功
	</div>
	<div class="finish">
		<br>
		<br>
		
		<div class="btns">
			<?php if ($this->display_result == 1) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->survey_id . '&Itemid=' . $this->itemid, false); ?>" class="submit">
				觀看投票結果
			</a>
			<?php } ?>
			<a class="btn" href="<?php echo JURI::root(); ?>" id="return_index">
				回首頁
			</a>
		</div>
	</div>

</div>