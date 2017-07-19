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
<div class="survey_verify">
	<div class="page-header">
		請填寫驗證資料
	</div>
	<div class="verify">
		<form id="verify_form" name="verify_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=verify2nd&task=verify2nd.check_verify_form&Itemid=' . $this->itemid, false); ?>" >
			<table class="formtable" width="100%">
				<?php

					if ($this->verify2nd_type) {
						$verify_types = json_decode($this->verify2nd_type, true);

						unset($check_jscode);
						foreach ($verify_types as $type) {
							JPluginHelper::importPlugin('verify', $type);
							$className = 'plgVerify' . ucfirst($type);

							// 取得顯示欄位
							if (method_exists($className, 'onGetFormHtml2nd')) {
								$html = json_decode( $className::onGetFormHtml2nd() );
				?>
				<tr>
					<th>
						<?php echo $html->select;  ?>&nbsp;&nbsp;
						<?php echo $html->title;  ?>
					</th>
					<td>
						<?php echo $html->input; ?>
					</td>
				</tr>
				<?php
							}

							// 取得JS檢查
							if (method_exists($className, 'onGetCheckJsCode2nd')) {
								$check_jscode .= $className::onGetCheckJsCode2nd();
							}
							
							// Facebook API
							if(method_exists($className, 'onGetFacebookJsCode2nd')) {
								$facebook = $className::onGetFacebookJsCode2nd();
							}

						}
					}
				?>


			</table>
			<div class="btns">
				<a class="submit" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=verify&sid=". $this->survey_id. "&Itemid=". $this->itemid, false); ?>" >
					上一步
				</a>
				<a id="submit_img" class="submit" href="javascript:void(0);" >
					下一步
				</a>
				<noscript>
					您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
				</noscript>
				<div><a href="<?php echo $this->category_link; ?>" class="btn <?php // echo "small"; ?>">取消</a></div>
			</div>
			<input type="hidden" name="task" value="verify2nd.check_verify_form">
			<input type="hidden" id="sid" name="sid" value="<?php echo $this->survey_id; ?>">
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

<?php
if ((strpos($_SERVER['HTTP_HOST'], "ivoting.taipei") !== false) && SurveyforceVote::getSurveyData( $this->survey_id, "verify_failure_num") > 0) {
?>
<script>
	ga('create', 'UA-71563139-3', 'auto', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');

</script>
<?php } ?>

<script>
	jQuery.fn.showMessage = function(msg) {
		jQuery('html, body').scrollTop(0);
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
		jQuery("#system-message-container").show();
   }

	<?php echo $facebook; ?>

	jQuery(document).ready(function(){
		jQuery("#submit_img").show();

		jQuery('input:text')[1].focus();

		jQuery("#submit_img").bind( "click", function() {
			jQuery("#system-message-container").hide();
			
			// check filed is empty
			<?php
				// 印出所有js 檢查程式碼
				if ($this->verify2nd_type) {
					echo $check_jscode;
				}
			
			?>

			jQuery("#verify_form").submit();


		});
	});
</script>
