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

$session = &JFactory::getSession();
$prac = $session->get('practice_pattern');
?>
<div class="survey_finish">
    <form id="finish_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=finish&task=finish.check_finish_form&Itemid=' . $this->itemid, false); ?>" >
        <div class="page-header">
			<?php if ($prac) { ?>
				您的投票練習已完成
			<?php } else { ?>
				您的投票已完成
			<?php } ?>
        </div>
		<?php if (!$prac) { ?>
			<div class = "finish">
				<div class = "short_link">
					<?php if ($this->short_url) {
						?>
						您的投票記錄可於【<a href="<?php echo $this->short_url; ?>" target="_blank" title="投票記錄查詢"><?php echo $this->short_url; ?></a>】查看
					<?php } ?>
				</div>
				<div class="warning">
					(投票記錄不含個人資料，請妥善保存，不再補發)
				</div>
				<br />
				<div class="item-list">
					<table border="0" class="formtable">
						<tr>
							<th>
								<input type="hidden" id="is_save_email" name="is_save_email" value="0">
								<label for="is_save_email">投票紀錄留存：</label>
							</th>
							<td>
								<input type="text" id="save_email" name="save_email" value="" placeholder="請填寫電子信箱" maxlength="100" >
							</td>
						</tr>

						<?php if ($this->is_notice_email) { ?>
							<tr>
								<th>
									<input type="hidden" id="is_notice_email" name="is_notice_email" value="0">
									<label for="is_notice_email">Email通知開票結果：</label>
								</th>
								<td>
									<input type="text" id="notice_email" name="notice_email" value="" placeholder="請填寫電子信箱" maxlength="100" ><div class="rwd_copy_email"></div>&nbsp;
									<input type="checkbox" id="is_copy_email" name="is_copy_email" value="1"><label for="is_copy_email">同上列的電子信箱</label>
								</td>
							</tr>
						<?php } ?>

						<?php if ($this->is_notice_phone) { ?>
							<tr>
								<th>
									<input type="hidden" id="is_notice_phone" name="is_notice_phone" value="0">
									<label for="is_notice_phone">簡訊通知開票結果：</label>
								</th>
								<td>
									<input type="text" id="notice_phone" name="notice_phone" value="" placeholder="請填寫手機號碼"  maxlength="10">
								</td>
							</tr>
						<?php } ?>

						<?php if ($this->is_lottery) { ?>
							<tr>
								<th>
									<input type="hidden" id="is_join_lottery" name="is_join_lottery" value="0">
									<label for="is_join_lottery">參加抽獎活動：</label>
								</th>
								<td>
									<input type="text" id="lottery_name" name="lottery_name" value="" placeholder="請填寫姓名" maxlength="50">
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="text" id="lottery_phone" name="lottery_phone" value="" placeholder="請填寫電話" maxlength="20">
								</td>
							</tr>
						<?php } ?>

					</table>
				</div>
			</div>
			<br>
		<?php } ?>
        <div class="btns">
			<?php if (!$prac) { ?>
				<a id="submit_img" class="submit" href="javascript:void(0);" >
					確定送出
				</a>
				<noscript>
				您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
				</noscript>
			<?php } ?>
			<?php if ($this->display_result == 1) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->survey_id . '&Itemid=' . $this->itemid, false); ?>" class="submit">
					觀看投票結果
				</a>
			<?php } ?>
				<a href="<?php echo JURI::root(); ?>" class="btn" id="return_index">
				回首頁
			</a>


        </div>
        <input type="hidden" name="task" value="finish.check_finish_form">
        <input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
		<?php echo JHTML::_('form.token'); ?>
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


<?php if (strpos($_SERVER['HTTP_HOST'], "ivoting.taipei") !== false) { ?>
	<script>
		ga('create', 'UA-71563139-4', 'auto', {'name': 'newTracker'});
		ga('newTracker.send', 'pageview');

	</script>
<?php } ?>

<script>
	jQuery.fn.showMessage = function (msg) {
		jQuery('html, body').scrollTop(0);
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
		jQuery("#system-message-container").show();
	}


	jQuery(document).ready(function () {

		jQuery("#is_copy_email").bind("change", function () {
			if (jQuery("#is_copy_email").prop("checked") == true) {
				if (jQuery("#save_email").val() == "") {
					jQuery("#message_area").showMessage('請先填寫投票紀錄留存的電子信箱。');
					jQuery("#save_email").focus();
					jQuery("#is_copy_email").attr("checked", false);
					return false;
				} else {
					jQuery("#notice_email").val(jQuery("#save_email").val());
					jQuery("#notice_email").attr("disabled", "disabled");
				}
			} else {
				jQuery("#notice_email").removeAttr("disabled");
			}
		});


		jQuery("#submit_img").bind("click", function () {
			jQuery("#system-message-container").hide();

			is_submit = false;

			jQuery("#is_save_email").val("0");
			jQuery("#save_email").val(jQuery.trim(jQuery("#save_email").val()));
			if (jQuery("#save_email").val()) {
				reEmail = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/;

				if (jQuery("#save_email").val()) {
					if (!reEmail.test(jQuery("#save_email").val())) {
						jQuery("#message_area").showMessage('投票紀錄留存的電子信箱格式錯誤。');
						jQuery("#save_email").focus();
						return false;
					}
				}
				jQuery("#is_save_email").val("1");
				is_submit = true;
			}



<?php if ($this->is_notice_email) { ?>

				jQuery("#is_notice_email").val("0");
				jQuery("#notice_email").val(jQuery.trim(jQuery("#notice_email").val()));
				if (jQuery("#notice_email").val()) {
					reEmail = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/;

					if (jQuery("#notice_email").val()) {
						if (!reEmail.test(jQuery("#notice_email").val())) {
							jQuery("#message_area").showMessage('Email通知開票結果的電子信箱格式錯誤。');
							jQuery("#notice_email").focus();
							return false;
						}
					}

					jQuery("#is_notice_email").val("1");
					is_submit = true;
				}


<?php } ?>

<?php if ($this->is_notice_phone) { ?>

				jQuery("#is_notice_phone").val("0");
				jQuery("#notice_phone").val(jQuery.trim(jQuery("#notice_phone").val()));
				if (jQuery("#notice_phone").val()) {
					rePhone = /^09[0-9]{8}$/;

					if (jQuery("#notice_").val()) {
						if (!rePhone.test(jQuery("#notice_phone").val())) {
							jQuery("#message_area").showMessage('簡訊通知開票結果的號碼錯誤。');
							jQuery("#notice_phone").focus();
							return false;
						}
					}

					jQuery("#is_notice_phone").val("1");
					is_submit = true;
				}
<?php } ?>


<?php if ($this->is_lottery) { ?>

				jQuery("#is_join_lottery").val("0");
				jQuery("#lottery_name").val(jQuery.trim(jQuery("#lottery_name").val()));
				jQuery("#lottery_phone").val(jQuery.trim(jQuery("#lottery_phone").val()));

				if (jQuery("#lottery_name").val() == "" || jQuery("#lottery_phone").val() == "") {

				} else {
					jQuery("#is_join_lottery").val("1");
					is_submit = true;
				}


<?php } ?>


			if (is_submit == false) {
				jQuery("#message_area").showMessage('請填寫表單。');
				return false;
			} else {
				jQuery("#finish_form").submit();
			}
		});
	});
</script>
<style>

</style>