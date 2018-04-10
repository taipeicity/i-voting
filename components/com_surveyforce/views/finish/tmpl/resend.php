<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$session     = &JFactory::getSession();
$tmp_session = json_decode($session->get("tmp_session"), true);

?>

<script>
    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    };


    jQuery(document).ready(function () {

        jQuery("#submit_img").bind("click", function () {

            jQuery("#is_join_lottery").val("0");

            var lottery_name = jQuery("#lottery_name");
            var lottery_phone = jQuery("#lottery_phone");

            lottery_name.val(jQuery.trim(lottery_name.val()));
            lottery_phone.val(jQuery.trim(lottery_phone.val()));

            if (lottery_name.val() && lottery_phone.val()) {
                jQuery("#is_join_lottery").val("1");
                jQuery("#resend_form").submit();
            } else {
                jQuery("#message_area").showMessage('請填寫參加抽獎活動資料。');
                return false;
            }
        });

    });
</script>


<div class="survey_finish">
    <form id="resend_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=finish&task=finish.' . $this->task . '&Itemid=' . $this->itemid, false); ?>">
        <div class="page-header">
            補送抽獎資料
        </div>

        <div class="finish">

            <div class="item-list">
                <table border="0" class="formtable">

                    <tr>
                        <th>
                            <input type="hidden" id="is_join_lottery" name="is_join_lottery" value="0">
                            <label for="is_join_lottery">參加抽獎活動：</label>
                        </th>
                        <td>
                            <input type="text" id="lottery_name" name="lottery_name" value="<?php echo empty($tmp_session['lottery_name']) ? '' : $tmp_session['lottery_name']; ?>" placeholder="請填寫姓名" autocomplete="off" maxlength="50">
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="text" id="lottery_phone" name="lottery_phone" value="<?php echo empty($tmp_session['lottery_phone']) ? '' : $tmp_session['lottery_phone']; ?>" placeholder="請填寫電話" autocomplete="off" maxlength="20">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br>


        <div class="btns">

            <a id="submit_img" class="submit" href="javascript:void(0);"> 確定送出 </a>
            <noscript>
                您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
            </noscript>

        </div>


        <input type="hidden" id="task" name="task" value="finish.<?php echo $this->task; ?>">
        <input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
        <input type="hidden" name="ticket_num" value="<?php echo $this->ticket_num; ?>">
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


