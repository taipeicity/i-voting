<?php
/**
*   @package         Return
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die;
?>
<div class="mod_return return">
	<div class="return_inner home">
		<a href="index.php"><span><?php echo JText::_('JGLOBAL_BACK_HOME'); ?></span></a>
	</div>
	<div class="return_inner">
		<a href="javascript:history.go(-1)"><span><?php echo JText::_('JGLOBAL_RETURN'); ?></span></a>
	</div>
	<noscript>您的瀏覽器不支援script程式碼。請使用"Backspace"按鍵回到上一頁。</noscript>
</div>