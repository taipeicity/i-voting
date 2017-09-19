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


// 載入plugin
JPluginHelper::importPlugin('survey', $this->question->question_type);
$className = 'plgSurvey' . ucfirst($this->question->question_type);

// 可投票數
$submit_str = ($this->questions_num == 1 || $this->questions_num == $this->count) ? "確定送出" : "下一題";
if ($this->question->is_multi == 0) {
    $submit_msg = "您可投1票，您確定都投完了嗎？<br/>如果是，就請再按一次" . $submit_str;
} else {
    $max_vote_num = (($this->question->multi_limit > 0)) ? $this->question->multi_limit : $this->question->multi_max;
    $submit_msg = sprintf("您總共有%d票，這%d票可以投同一類，也可投不同類，您確定都投完了嗎？<br/>如果是，就請再按一次%s；如果還沒投完，就請繼續投票。", $max_vote_num, $max_vote_num, $submit_str);
}
?>
<div class="survey_question">

    <div class="question">
        <form id="question_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=question&task=question.check_question_form&Itemid=' . $this->itemid, false); ?>" >
            <div class="header">
                <?php
                echo ($this->questions_num == 1) ? sprintf("%s", $this->question->sf_qtext) : sprintf("第%d題 - %s", $this->count, $this->question->sf_qtext);
                ?>
            </div>



            <?php
            // 載入html和JS
            if (method_exists($className, 'onGetOptionsHtml')) {
                $html = $className::onGetOptionsHtml($this->question, $this->options, $this->cats, $this->sub_options);

                echo $html;
            }
            ?>



            <div class="btns">
                <a id="submit_img" class="submit red" href="javascript:void(0);" >
                    <?php
                    echo ($this->questions_num == 1 || $this->questions_num == $this->count) ? "確定送出" : "下一題";
                    ?>

                </a>
                <noscript>
                您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
                </noscript>
                <div>
                    <input id="btn_reset" type="button" value="清除" class="small" />
                    <a href="<?php echo $this->category_link; ?>" class="btn small">取消</a>
                </div>
            </div>

            <input type="hidden" name="task" value="question.check_question_form">
            <input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
            <input type="hidden" name="qid" value="<?php echo $this->question_id; ?>">
            <input type="hidden" name="img_data" id="img_data" value="" />
            <?php echo JHTML::_('form.token'); ?>
        </form>
    </div>

</div>


<div class="already_fancybox">
    本案投票應投 
    <?php
    if ($this->question->is_multi > 0) {
        echo $this->question->multi_limit > 0 ? $this->question->multi_limit : $this->question->multi_min;
        echo $this->question->multi_max > 0 ? " 至 " . $this->question->multi_max : "";
    } else {
        echo '1 ';
    }
    ?> 票。<br>
    <span class="not_check">您尚未投選項</span>
    <span class="already_check">您已投：<span class="option_active"></span>，共 <span class="already">1</span> 票，還可投 <span class="yet"><?php echo $this->question->multi_limit; ?></span> 票。</span><br>
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

<?php if ((strpos($_SERVER['HTTP_HOST'], "ivoting.taipei") !== false) && $this->count == 1) { ?>
    <script>
        ga('create', 'UA-71563139-2', 'auto', {'name': 'newTracker'});
        ga('newTracker.send', 'pageview');
    </script>
<?php } ?>


<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/html2canvas.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>media/jui/js/jquery.plugin.html2canvas.js"></script>

<script>
    var _select_nums = 0;
    var _submit_check = false;

    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    }

    jQuery(document).ready(function () {
        jQuery("footer").css("visibility", "hidden");
        jQuery("#system-message-container").hide();
        jQuery("#submit_img").show();

        jQuery("#btn_reset").bind("click", function () {
            jQuery("#question_form")[0].reset();
            jQuery(".option .stamp").removeClass("active");
            _select_nums = 0;
            jQuery(".option_active")[0].innerHTML = "";
            jQuery(".already")[0].innerHTML = "0";
            jQuery(".yet")[0].innerHTML = <?php echo $this->question->multi_limit > 0 ? $this->question->multi_limit : $this->question->multi_max; ?>;
            jQuery(".already_check").hide();
            jQuery(".not_check").show();
        });

        jQuery("#submit_img").bind("click", function () {
            jQuery("#system-message-container").hide();

            // 選項的檢查
            if (jQuery("#question_form").checkField() == false) {
                _submit_check = false;
                return false;
            }

            // 送出前提示
            if (_submit_check == false) {
                jQuery("#message_area").showMessage('<?php echo $submit_msg; ?>');
                _submit_check = true;
                return false;
            }

            jQuery.fancybox.showLoading();
<?php
if ($this->other_snapshot) {
    ?>
                jQuery("#question_form").submit();
    <?php
} else {
    ?>
                jQuery('.survey_question').html2canvas({
                    onrendered: function (canvas) {
                        jQuery('#img_data').val(canvas.toDataURL("image/jpg"));

                        jQuery("#question_form").submit();
                    }
                });
    <?php
}
?>

        });
    });
</script>
