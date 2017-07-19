<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>
<div class="survey_statement">
	<div class="page-header">
		個資聲明頁
	</div>
	<div class="statement">
		<form id="statement_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&task=statement.check_statement_form&Itemid=' . $this->itemid, false); ?>" >

			<div class="agree_statement">
				<input type="checkbox" id="agree_statement1" class="agree_statement" name="agree_statement" value="1">
				<label for="agree_statement1">我已閱讀並且接受同意書內容</label>
			</div>
			
			<div class="statement_area">
				<?php
				echo $this->statement_text;
				?>
			</div>

			<div class="agree_statement">
				<input type="checkbox" id="agree_statement2" class="agree_statement" name="agree_statement" value="1">
				<label for="agree_statement2">我已閱讀並且接受同意書內容</label>
			</div>

			<div class="btns">
				<a class="submit" href="javascript:history.back();" >
					上一步
				</a>

				<a id="submit_img" class="submit" href="javascript:void(0);" title="下一步" >
					下一步
				</a>
				<noscript>
					您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
				</noscript>
				<div><a href="<?php echo $this->intro_link; ?>" class="btn <?php // echo "small"; ?>">取消</a></div>
			</div>


			<input type="hidden" name="task" value="statement.check_statement_form">
			<input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
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
   }

	jQuery(document).ready(function(){
		jQuery("#submit_img").show();

		jQuery("#agree_statement1, #agree_statement2").bind("change", function() {
			if ( jQuery(this).prop( "checked" ) == true ) {
				jQuery(".agree_statement").attr("checked", true);
			} else {
				jQuery(".agree_statement").attr("checked", false);
			}
		});

		jQuery("#submit_img").bind( "click", function() {
			jQuery("#system-message-container").hide();

			// check filed is empty

			if ( !jQuery("#agree_statement1").prop('checked') ||  !jQuery("#agree_statement2").prop('checked')) {
				jQuery("#message_area").showMessage('請勾選同意書選項。');
				return false;
			}

			jQuery("#statement_form").submit();
		});
	});
</script>
<style>
	.return {
		display: none;
	}
</style>