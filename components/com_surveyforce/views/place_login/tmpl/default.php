<?php
/**
*   @package         Surveyforce
*   @version           1.3-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$itemid = $app->input->getInt('Itemid');




?>
<div class="survey_place">
	<div class="page-header">
		實體投票登入頁
	</div>
	<div class="place">
		<form id="place_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&task=place_login.check_login_form&Itemid='. $this->itemid, false); ?>" >

			<table class="formtable entity_verify">
				<tr>
					<th>
						<label for="username">帳號：</label>
					</th>
					<td>
						<input type="text" id="username" name="username" >
					</td>
				</tr>

				<tr>
					<th>
						<label for="passwd">密碼：</label>
					</th>
					<td>
						<input type="password" id="passwd" name="passwd" >
					</td>
				</tr>

				<tr>
					<th rowspan="2">
						<label for="recaptcha_response_field">輸入驗證碼：</label>
					</th>
					<td>
						<input type="text" id="recaptcha_response_field2" name="recaptcha_response_field2" maxlength="50" placeholder="請輸入圖片中的數字" autocomplete="off" >
					</td>
				</tr>

				<tr>
					<td>
						<div id="captcha_field">
							<?php include_once("rd/securimage/voice_show.php");	?>
						</div>
					</td>
				</tr>
			</table>
			<div class="btns">
				<a id="submit_img" class="submit" href="javascript:void(0);" style="display: none;" >
					確定送出
				</a>
				<noscript>
					<input type="submit" value="確定送出">
				</noscript>
			</div>
			<input type="hidden" name="task" value="place_login.check_login_form">
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
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
		jQuery("#system-message-container").show();
   }


	jQuery(document).ready(function(){
		jQuery("#username").focus();
		jQuery("#recaptcha_response_field").show();
		jQuery("#submit_img").show();

		jQuery("#submit_img").bind( "click", function() {
			jQuery("#system-message-container").hide();

			if(jQuery('#username').val() == "") {
				jQuery("#message_area").showMessage('請輸入帳號。');
				jQuery("#username").focus();
				return false;
			}

			if(jQuery('#passwd').val() == "") {
				jQuery("#message_area").showMessage('請輸入密碼。');
				jQuery("#oasswd").focus();
				return false;
			}


			if(!jQuery("#recaptcha_response_field2").val()) {
				jQuery("#message_area").showMessage('請填寫驗證碼。');
				jQuery("#recaptcha_response_field2").focus();
				return false;
			}



			jQuery.ajax({
				type	: "POST",
				url		: "<?php echo JURI::base(); ?>rd/securimage/securimage_valid.php",
				dataType: "json",
				data: { 'recaptcha_response_field': jQuery("#recaptcha_response_field2").val(), 'sid': 0},
				async: false,
				
				success	: function( result ){
					if( result.status == false ){
						if(result.num >= 3) {
							if(result.num == 3) {
								jQuery("#message_area").showMessage('驗證碼輸入錯誤，由於驗證碼失敗次數過多，請稍後再試。');
							}else{
								jQuery("#message_area").showMessage('驗證碼失敗次數過多，請稍後再試。');
							}	
						}else {
							jQuery("#message_area").showMessage('驗證碼輸入錯誤，請重新填寫。');
						}
						jQuery("#recaptcha_response_field2").val("");
						jQuery("#recaptcha_response_field2").focus();
						jQuery(".refresh_captcha").trigger( "click" );

						return false;
					} else {
						jQuery("#place_form").submit();
					}
				}
			});
		});
	});
	

</script>
<style>
	#username, #passwd, #recaptcha_response_field2 {
		margin: 0;
		padding: 0;
		width: 300px;
		height: 30px;
	}
</style>