<?php
/**
*   @package         Scroll
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die;
?>
<a href="#" class="scrollup" title="<?php echo JText::_('JGLOBAL_SCROLL_TOP'); ?>">
	<div class="arrow arrow_top"></div>
	<?php echo JText::_('JGLOBAL_SCROLL_TOP'); ?>
</a>
<a href="#" class="scrollbottom" title="<?php echo JText::_('JGLOBAL_SCROLL_BOTTOM'); ?>">
	<?php echo JText::_('JGLOBAL_SCROLL_BOTTOM'); ?>
	<div class="arrow arrow_bottom"></div>
</a>

<script>

	jQuery(function($){

		$('.scrollup').click(function () {
			$("html, body").animate({
				scrollTop: 0
			}, 600);
			return false;
		});

		$('.scrollbottom').click(function () {
			$('html, body').animate({
				scrollTop:$(document).height()
			}, 600);
			return false;
		});
	});
</script>