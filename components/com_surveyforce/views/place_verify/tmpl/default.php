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
<div class="survey_verify">
	<div class="page-header">
		驗證身分資料
	</div>
	<div class="verify">
		<div class="img" align="center">
			<img class="sample_image" src="<?php echo JURI::root(). $this->item->place_image; ?>" alt="掃描標的物圖片">
		</div>
		<form id="verify_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=verify&task=place_verify.check_verify_form&Itemid='. $this->itemid, false); ?>" >
			<table class="formtable" width="100%">
				<tr>
					<th>
						<label for="idnum">身分證字號：</label>
					</th>
					<td>
						<input type="text" id="idnum" name="idnum" placeholder="例：A234567890" maxlength="10" autocomplete="off" >
						<span id="msg">&nbsp;</span>
					</td>
				</tr>
			</table>
			<div class="btns">
				<a id="submit_img" class="submit" href="javascript:void(0);" style="display: none;" >
					確定送出
				</a>
				<noscript>
					您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
				</noscript>
			</div>
			<input type="hidden" name="task" value="place_verify.check_verify_form">
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
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
		jQuery('html, body').scrollTop(0);
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
		jQuery("#system-message-container").show();
		
		MsgNone();
	}

	function MsgNone() {
		jQuery("#system-message-container").delay(3000).hide(0);
	}

	jQuery(document).ready(function(){
		jQuery("#idnum").focus();

		var msgtext = jQuery("#system-message .alert-message p").text(); 
		if(msgtext != "") {
			MsgNone();
		}
		
		// 使用條碼掃瞄機時
		jQuery("#idnum").bind( "keyup", function() {
			if (jQuery(this).val().length == 10) {
				jQuery( "#submit_img" ).trigger( "click" );
			}
		});

		jQuery("#submit_img").bind( "click", function() {
			jQuery("#msg").html("");

			if (!jQuery("#idnum").val()) {
				jQuery("#message_area").showMessage("請填身分證字號");
				jQuery("#idnum").focus();
				return false;
			} else {
				reID = /^[A-Za-z]{1}[1-2]{1}[0-9]{8}$/;
				if (!reID.test(jQuery.trim(jQuery("#idnum").val()))) {
					jQuery("#message_area").showMessage("身分證字號格式錯誤");
					jQuery("#idnum").val('');
					jQuery("#idnum").focus();
					return false;
				} else {
					jQuery.fancybox.showLoading();
					jQuery("#verify_form").submit();
				}

			}

		});
	});
</script>
<style>
	#idnum {
		text-transform:uppercase;
	}
	#msg {
		color: red;
	}

	.sample_image {
		max-width: 400px;
	}
</style>