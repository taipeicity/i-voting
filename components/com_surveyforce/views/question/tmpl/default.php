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

$question_type  = array ("number", "table", "select", "open");
$analyze_params = $this->analyze_params;

// 載入plugin
JPluginHelper::importPlugin('survey', $this->question->question_type);
$className = 'plgSurvey' . ucfirst($this->question->question_type);
?>
<div class="survey_question">

    <div class="question">
        <form id="question_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=question&task=question.check_question_form&Itemid=' . $this->itemid, false); ?>">

            <div class="header">
				<?php
				echo ($this->questions_num == 1) ? sprintf("%s", $this->question->sf_qtext) : sprintf("第%d題 - %s", $this->count, $this->question->sf_qtext);
				?>
            </div>


			<?php
			// 載入html和JS
			if (method_exists($className, 'onGetOptionsHtml')) {
				$html = $className::onGetOptionsHtml($this->question, $this->options, $this->sub_options);

				echo $html;
			}
			?>


            <div class="btns">
				<?php if ($this->count != 1 && $this->preview == true) { ?>
                    <a class="button submit" href="<?php echo $this->previously_question; ?>">
						<?php
						echo "上一題";
						?>
                    </a>
				<?php } ?>
				<?php if (($this->questions_num != 1 && $this->questions_num != $this->count) || $this->preview == false) { ?>
                    <a id="submit_img" class="button submit" href="<?php echo $this->preview == false ? "javascript:void(0)" : $this->next_question; ?>">
						<?php
						echo ($this->questions_num == 1 || $this->questions_num == $this->count) ? "確定送出" : "下一題";
						?>
                    </a>
				<?php } ?>

				<?php if ($this->preview == false) { ?>
                    <noscript>
                        您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
                    </noscript>
                    <div>
                        <input id="btn_reset" type="button" value="清除" class="button reset" />
                        <a href="<?php echo $this->category_link; ?>" class="button cancel">取消</a>
                    </div>
				<?php } else { ?>
                    <a href="<?php echo $this->back_link; ?>" class="button submit">上一頁</a>
                    <a href="<?php echo $this->next_link; ?>" class="button submit">下一頁</a>
				<?php } ?>
            </div>
            <input type="hidden" id="lastQuestion" value="<?php echo JFilterOutput::cleanText($this->lastQuestion); ?>">
            <input type="hidden" id="is_public" value="<?php echo JFilterOutput::cleanText($this->is_public); ?>">
            <input type="hidden" id="display_result" value="<?php echo JFilterOutput::cleanText($this->display_result); ?>">
            <input type="hidden" id="is_test" value="<?php echo JFilterOutput::cleanText($this->is_test); ?>">
            <input type="hidden" name="task" value="question.check_question_form">
            <input type="hidden" name="sid" value="<?php echo JFilterOutput::cleanText($this->survey_id); ?>">
            <input type="hidden" name="qid" value="<?php echo JFilterOutput::cleanText($this->question_id); ?>">
            <input type="hidden" name="img_data" id="img_data" value="" />
			<?php echo JHTML::_('form.token'); ?>
        </form>

    </div>


</div>

<?php if (!in_array($this->question->question_type, $question_type)) { ?>
    <div class="already_fancybox">
        本案投票應投
		<?php
		if ($this->question->is_multi > 0) {
			echo $this->question->multi_limit > 0 ? $this->question->multi_limit : $this->question->multi_min;
			echo $this->question->multi_max > 0 ? " 至 " . $this->question->multi_max : "";
		} else {
			echo '1 ';
		}
		?> 票。<br> <span class="not_check">您尚未投選項</span>
        <span class="already_check">您已投選項：<span class="option_active"></span>，共 <span class="already">1</span> 票，還可投 <span class="yet"><?php echo $this->question->multi_limit; ?></span> 票。</span><br>
    </div>
<?php } ?>


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


<script src="<?php echo JURI::root(); ?>media/jui/js/html2canvas.js"></script>
<script src="<?php echo JURI::root(); ?>media/jui/js/jquery.plugin.html2canvas.js"></script>

<script>
    var _select_nums = 0;

    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    }

    jQuery(document).ready(function () {
		<?php if (!in_array($this->question->question_type, $question_type)) { ?>
        jQuery("footer").css("visibility", "hidden");
		<?php } ?>
        jQuery("#submit_img").show();

        jQuery("#btn_reset").bind("click", function () {
            jQuery("#question_form")[0].reset();
            jQuery(".option .stamp").removeClass("active");
            _select_nums = 0;
        });

        jQuery("#btn_reset").bind("click", function () {
            jQuery("#question_form")[0].reset();
            jQuery(".option .stamp").removeClass("active");
            _select_nums = 0;
			<?php if (!in_array($this->question->question_type, $question_type)) { ?>
            jQuery(".option_active")[0].innerHTML = "";
            jQuery(".already")[0].innerHTML = "0";
            jQuery(".yet")[0].innerHTML = <?php echo $this->question->multi_limit > 0 ? $this->question->multi_limit : $this->question->multi_max; ?>;
            jQuery(".already_check").hide();
            jQuery(".not_check").show();
			<?php } ?>
        });


		<?php if($this->preview == false){ ?>

        jQuery("#submit_img").bind("click", function () {
            jQuery("#system-message-container").hide();

            // 選項的檢查
            if (jQuery("#question_form").checkField() == false) {
                return false;
            }

            var lastQuestion = document.querySelector('#lastQuestion'),
                is_public = document.querySelector('#is_public'),
                display_result = document.querySelector('#display_result'),
                is_test = document.querySelector('#is_test');

          if (parseInt(is_test.value)) {
            is_public.value = 1;
            display_result.value = 1;
          }

          if (parseInt(is_public.value) && parseInt(display_result.value) && parseInt(lastQuestion.value)) {
            jQuery(".question").hide();
            var mod_return = document.querySelector(".mod_return");
            mod_return.hidden = true;

            var div = document.createElement("div"),
              div_span = document.createElement("div"),
              span = document.createElement("span"),
              div_dot = document.createElement("div");

            div.classList.add("blockchain");
            div_dot.classList.add("loading_dots");
            span.classList.add("loading_text");
            span.innerText = "處理中...";
            div_span.appendChild(span);

            for (var i = 0; i <= 4; i++) {
              var span_dot = document.createElement("span");

              div_dot.appendChild(span_dot);
            }

            div.appendChild(div_span);
            div.appendChild(div_dot);
            jQuery(".survey_question").append(div);
          } else {
            jQuery.fancybox.showLoading();
          }


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


		<?php } ?>
    });
</script>
