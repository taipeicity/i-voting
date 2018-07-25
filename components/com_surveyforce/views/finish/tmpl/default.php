<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$session     = &JFactory::getSession();
$prac        = $session->get('practice_pattern');
$tmp_session = json_decode($session->get("tmp_session"), true);
$document    = JFactory::getDocument();
$params      = JComponentHelper::getParams('com_surveyforce');

if ($this->lottery_remind == true && $this->join_lottery == false) {
	$style = '.page-header {
	            color: red;
	            text-align: left;
                border-radius: 1ex;
                padding: 0.5em 1em 0.5em 1em;
	         }
	         .lottery_remind{
	         text-align: center;
	         ';
	$document->addStyleDeclaration($style);
}

?>

<script>
    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    };


    jQuery(document).ready(function () {

		<?php if($this->preview == false){ ?>

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

		<?php if ($this->task == 'check_finish_form') { ?>

		<?php if($this->is_lottery){ ?>

        jQuery("#previous_step").bind("click", function () {

            jQuery("#task").val("finish.resetLotteryStep");
            jQuery("#finish_form").submit();

        });

		<?php } ?>


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


            if (is_submit == false) {
                jQuery("#message_area").showMessage('請填寫表單。');
                return false;
            } else {
                jQuery("#finish_form").submit();
            }
        });

		<?php } ?>

		<?php if ($this->task == 'setLotteryStep') { ?>

        jQuery("#next_step").bind("click", function () {

            jQuery("#is_join_lottery").val("0");

            var lottery_name = jQuery("#lottery_name");
            var lottery_phone = jQuery("#lottery_phone");

            lottery_name.val(jQuery.trim(lottery_name.val()));
            lottery_phone.val(jQuery.trim(lottery_phone.val()));

            if (lottery_name.val() === "" && lottery_phone.val() === "") {
                jQuery("#finish_form").submit();
            } else if (lottery_name.val() && lottery_phone.val()) {

                if(!lottery_phone.val().match(/^09\d{8}/g)){
                    jQuery("#message_area").showMessage('手機號碼格式錯誤。');
                    return false;
                }

                jQuery("#is_join_lottery").val("1");
                jQuery("#finish_form").submit();
            } else {
                jQuery("#message_area").showMessage('請填寫參加抽獎活動資料。');
                return false;
            }
        });

		<?php } ?>

		<?php } ?>
    });
</script>


<div class="survey_finish">
    <form id="finish_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=finish&task=finish.' . $this->task . '&Itemid=' . $this->itemid, false); ?>">
        <div class="page-header">
			<?php if ($prac) { ?>
                您的投票練習已完成
				<?php
			} else {
				if ($this->lottery_remind) {
					?>
                    <div class="lottery_remind">抽獎提醒</div>
					<?php
					if ($this->join_lottery) {

						echo "<hr>";
						echo $params->get("lottery_join");
					} else {
						echo "<hr>";
						echo $params->get("lottery_unjoin");
					}
				} else {
					?>
                    您的投票已完成
					<?php
				}
			} ?>
        </div>
        <hr>
		<?php if (!$prac) { ?>

            <div class="finish">

				<?php if ($this->task == 'check_finish_form') { ?>

                    <div class="short_link">
						<?php if ($this->short_url) {
							?>
                            您的投票記錄可於【
                            <a href="<?php echo $this->preview == false ? $this->short_url : "javascript:void(0)"; ?>" target="_blank" title="投票記錄查詢"><?php echo $this->short_url; ?></a>】查看
						<?php } ?>
                    </div>
                    <div class="warning">
                        (投票記錄不含個人資料，請妥善保存，不再補發)
                    </div>
                    <br />

				<?php } ?>

                <div class="item-list">
                    <table class="formtable">

						<?php if ($this->task == 'check_finish_form') { //如果無抽獎或已經檢查過抽獎步驟 ?>

                            <tr>
                                <th>
                                    <input type="hidden" id="is_save_email" name="is_save_email" value="0">
                                    <label for="is_save_email">投票紀錄留存：</label>
                                </th>
                                <td>
                                    <input type="text" id="save_email" name="save_email" value="<?php echo empty($tmp_session['save_email']) ? '' : $tmp_session['save_email']; ?>" placeholder="請填寫電子信箱" autocomplete="off" maxlength="100">
                                </td>
                            </tr>

							<?php if ($this->is_notice_email) { ?>
                                <tr>
                                    <th>
                                        <input type="hidden" id="is_notice_email" name="is_notice_email" value="0">
                                        <label for="is_notice_email">Email通知開票結果：</label>
                                    </th>
                                    <td>
                                        <input type="text" id="notice_email" name="notice_email" value="<?php echo empty($tmp_session['notice_email']) ? '' : $tmp_session['notice_email']; ?>" placeholder="請填寫電子信箱" autocomplete="off" maxlength="100">
                                        <div class="rwd_copy_email"></div>&nbsp;
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
                                        <input type="text" id="notice_phone" name="notice_phone" value="<?php echo empty($tmp_session['notice_phone']) ? '' : $tmp_session['notice_phone']; ?>" placeholder="請填寫手機號碼" autocomplete="off" maxlength="10">
                                    </td>
                                </tr>

							<?php } ?>

						<?php } ?>

						<?php if ($this->is_lottery && $this->task == 'setLotteryStep') { ?>
                            <tr>
                                <th>
                                    <input type="hidden" id="is_join_lottery" name="is_join_lottery" value="0">
                                    <label for="is_join_lottery">參加抽獎活動</label>
                                </th>
                                <td>
                                    <input type="text" id="lottery_name" name="lottery_name" value="<?php echo empty($tmp_session['lottery_name']) ? '' : $tmp_session['lottery_name']; ?>" placeholder="請填寫姓名" autocomplete="off" maxlength="50">
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="text" id="lottery_phone" name="lottery_phone" value="<?php echo empty($tmp_session['lottery_phone']) ? '' : $tmp_session['lottery_phone']; ?>" placeholder="請填寫手機號碼" autocomplete="off" maxlength="20">
                                </td>
                            </tr>
						<?php } ?>

                    </table>
                </div>
            </div>
            <br>
			<?php if ($this->task == 'setLotteryStep') { ?>
                <div class="btns">
                    <div class="warning">
                        (若不參加抽獎活動則無需填寫，請直接按下一步)
                    </div>
					<?php if ($this->preview == false) { ?>
                        <a id="next_step" class="submit" href="javascript:void(0);"> 下一步 </a>
					<?php } ?>
                </div>
			<?php } ?>

		<?php } ?>

		<?php if ($this->preview == false) { ?>

			<?php if ($this->task == 'check_finish_form' || $prac) { ?>

                <div class="btns">

					<?php
					if (!$prac) {
						if ($this->preview == false && $this->is_lottery && !SurveyforceVote::getSurveyData($this->survey_id, "join_lottery")) { ?>
                            <a id="previous_step" class="submit" href="javascript:void(0);"> 上一步 </a>
						<?php } ?>
                        <a id="submit_img" class="submit" href="javascript:void(0);"> 確定送出 </a>
                        <noscript>
                            您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
                        </noscript>
					<?php }
					?>

					<?php if ($this->display_result == 1) { ?>
                        <a href="<?php echo $this->preview == false ? JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->survey_id . '&Itemid=' . $this->itemid, false) : "javascript:void(0)"; ?>" class="submit"> 觀看投票結果 </a>
					<?php } ?>

                    <a href="<?php echo $this->preview == false ? JURI::root() : "javascript:void(0)"; ?>" class="btn" id="return_index"> 回首頁 </a>

                </div>

			<?php } ?>
		<?php } ?>

		<?php if ($this->preview == true) { ?>
            <div class="btns">
                <a href="<?php echo $this->back_link; ?>" class="submit">上一頁</a>
                <a href="<?php echo $this->next_link; ?>" class="submit">下一頁</a>
            </div>
		<?php } ?>


        <input type="hidden" id="task" name="task" value="finish.<?php echo $this->task; ?>">
        <input type="hidden" name="ticket_num" value="<?php echo $this->ticket_num; ?>">
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


