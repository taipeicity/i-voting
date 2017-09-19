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
<div class="survey_notice">
    <form id="intro_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=intro&task=intro.check_intro_form&Itemid=' . $this->itemid, false); ?>" >

			<div class="notice">
				<?php if ($this->item->is_notice_email) { ?>
				<div class="email">
					<label for="email">投票開始與即將結束時請通知我</label>
					<input type="text" id="email" name="email" value="" placeholder="請填寫電子信箱" size="35" maxlength="100">
					<br /><br />
				</div>
				<?php } ?>
				<?php if ($this->item->is_notice_phone) { ?>
				<div class="phone">
					<label for="phone">投票開始與即將結束時請通知我</label>
					<input type="text" id="phone" name="phone" value="" placeholder="請填寫手機號碼" size="20" maxlength="10">
					<br /><br />
				</div>
				<?php } ?>
				<br /><br />
			</div>

		<div class="btns">
			<a id="submit_img" class="submit" href="javascript:void(0);" >
				確定送出
			</a>
			<noscript>
				您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
			</noscript>
			<a href="<?php echo JURI::root(); ?>" class="btn">
				回首頁
			</a>
		</div>

		<input type="hidden" name="task" value="intro.check_intro_form">
		<input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
		<?php echo JHTML::_( 'form.token' ); ?>
    </form>

</div>

<div id="message_area" style="display: none;">
	<div class="alert alert-message">
		<a class="close" data-dismiss="alert">×</a>
		<h4 class="alert-heading">訊息</h4>
		<div>
			<p id="message_content"></p>
		</div>
	</div>
</div>


<script>
	jQuery.fn.showMessage = function(msg) {
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
   }


	jQuery(document).ready(function(){

		jQuery("#submit_img").bind( "click", function() {

			if (jQuery("#email").val() || jQuery("#phone").val()) {
				reEmail=/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/;

				if (jQuery("#email").val()) {
					if (!reEmail.test(jQuery.trim(jQuery("#email").val()))) {
						jQuery("#message_area").showMessage('Email格式錯誤，請確認您是否輸入正確。');
						jQuery("#email").focus();
						return false;
					}
				}


				if (jQuery("#phone").val()) {
					rePhone=/^09[0-9]{8}$/;
					if (!rePhone.test(jQuery.trim(jQuery("#phone").val()))) {
						jQuery("#message_area").showMessage('手機號碼格式錯誤。');
						jQuery("#phone").focus();
						return false;
					}
				}



				jQuery("#intro_form").submit();

			} else {
				jQuery("#message_area").showMessage('請至少選擇一項。');
				jQuery("#email").focus();
				return false;
			}


		});
	});
</script>