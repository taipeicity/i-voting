<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

$session = JFactory::getSession();
$this->form->setValue('stage', null, $session->get('stage', $this->item->stage));

$vote_num_params = json_decode($this->form->getValue('vote_num_params'));

if ($this->item->stage == 5 or $this->item->stage == 6) {
	$this->form->setFieldAttribute('vote_pattern', 'required', 'true');
}

$script = '';

if ($vote_num_params->vote_num_type == 1) {
	$script .= 'jQuery("#jform_vote_day").addClass("required");';
	$script .= 'jQuery("#jform_vote_num").addClass("required");';
}

if ($vote_num_params->vote_num_protect == 1) {
	$script .= 'jQuery("#jform_vote_num_protect_time").addClass("required");';
} else if ($vote_num_params->vote_num_protect == 2) {
	$script .= 'jQuery("#jform_vote_num_protect_vote").addClass("required");';
}

if ($this->form->getValue('is_notice_email') == 1) {
	$script .= 'jQuery(".is_notice_email").addClass("required");';
}

if ($this->form->getValue('is_notice_phone') == 1) {
	$script .= 'jQuery(".is_notice_phone").addClass("required");';
}


if ($this->form->getValue("place_image")) {
	$element = SurveyforceHelper::getOldArea("place_image", $this->form->getValue('place_image'));
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_place_image_area\")", $element);
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');

?>
<script type="text/javascript">

    jQuery(document).ready(function () {

        jQuery.fn.checkSettings_launchedJs = function () {

            var check = true;
            //啟用現地投票
            var is_old_verify = jQuery("#is_old_verify");
            if (jQuery("#jform_is_place0").is(":checked")) {
                if (!jQuery("#is_old_verify_type").val().match(/idnum/g) || is_old_verify.val() === "0" || is_old_verify.val() === "") {
                    if ((jQuery("#verify_method_1").is(":checked") || jQuery("#verify_method_0").is(":checked")) && jQuery("#verify_mix_idnum").checkVerify("#verify_method_1", ":checked") === false) {
                        check = false;
                    } else if (jQuery("#verify_method_2").is(":checked") && jQuery("#idnum").checkVerify("#verify_method_2", ":selected") === false) {
                        check = false;
                    }
                }
            }

            return check;
        };

        jQuery.fn.checkVerify = function (verify_method, status) {
            var check = true;
            var id = jQuery('#' + this.attr("id"));

            if (id.is(status) === false) {
                var verify_type = {'idnum': '身分證字號驗證', 'assign': '可投票人名單驗證'};
                var verify_func = {'idnum': '現地投票', 'assign': '分析功能'};
                var verify_selector = {
                    ':checked': id.next(),
                    ':selected': id
                };

                if (jQuery("#jform_id").val()) {

                    var reset_verify = jQuery("#reset_verify");
                    if (reset_verify.is(":hidden")) {
                        reset_verify.trigger('click');
                    }

                    if (verify_method !== '#verify_method_2') {
                        jQuery(verify_method).trigger('click');
                    }

                }

                jQuery('#message_area').showMessage('啟用' + verify_func[this.attr('value')] + '需選擇' + verify_type[this.attr('value')] + '方式。', verify_selector[status]);
                if (verify_method === '#verify_method_2') {
                    jQuery('#src_select').animate({
                        scrollTop: id.offset().top
                    }, 100);
                }
                check = false;
            }

            return check;
        };

        // 投票數設定
        jQuery("#jform_vote_num_param").on("click", function () {
            var vote_num_param = jQuery(this);
            vote_num_param.find(":input.small").removeClass("required");
            if (parseInt(vote_num_param.find(":checked").val()) === 1) {
                vote_num_param.find(":checked ~ .small").addClass("required");
            }
        });

        // 防止灌票機制
        jQuery("#jform_vote_rule_param").on("click", function () {
            var vote_rule_param = jQuery(this);
            vote_rule_param.find(":input.small").removeClass("required");
            vote_rule_param.find(":checked ~ .small").addClass("required");
        });

        // 郵件訊息通知
        jQuery("#jform_is_notice_email").on("click", function () {
            var is_notice_email = jQuery(this);
            if (parseInt(is_notice_email.find(":checked").val()) === 1) {
                jQuery(".is_notice_email").addClass("required");
            } else {
                jQuery(".is_notice_email").removeClass("required");
            }
        });

        // 手機訊息通知
        jQuery("#jform_is_notice_phone").on("click", function () {
            var is_notice_phone = jQuery(this);
            if (parseInt(is_notice_phone.find(":checked").val()) === 1) {
                jQuery(".is_notice_phone").addClass("required");
            } else {
                jQuery(".is_notice_phone").removeClass("required");
            }
        });

        // 啟用分析功能
        jQuery("#jform_is_analyze").on("click", function () {
            var is_analyze = jQuery(this);
            if (parseInt(is_analyze.find(":checked").val()) === 1) {
                jQuery("#analyze_column").show(500);
            } else {
                jQuery("#analyze_column").hide(500);
            }
        });

        // 現地投票圖片
        jQuery("#del_place_image_btn").on("click", function () {
            jQuery(this).deleteImage("place_image");
        });

        // 分析功能
        var id = jQuery("#jform_id").val();
        jQuery("#analyze_column_" + id).on("show", function () {
            var analyzeBodyHeight = jQuery(window).height() - 200;
            jQuery(".analyze-body").css("max-height", analyzeBodyHeight);
            jQuery("body").addClass("modal-open");
            var modalBody = jQuery(this).find('.analyze-body');
            modalBody.find('iframe').remove();
            modalBody.prepend("<iframe class=\"iframe\" src=\"index.php?option=com_surveyforce&amp;view=survey&amp;layout=column&amp;id=" + id + "&amp;tmpl=component\" name=\"選擇分析欄位\" width=\"99%\"></iframe>");
            jQuery(".iframe").css("height", analyzeBodyHeight);
        }).on("hide", function () {
            jQuery("body").removeClass("modal-open");
        });
    });
</script>


<div class="control-group form-inline">
	<?php $this->form->setFieldAttribute('vote_pattern', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('vote_pattern'); ?>
    <div class="controls">
		<?php $vote_pattern = $this->item->vote_pattern; ?>
        <fieldset id="jform_vote_pattern" class="checkboxes required">
            <ul>
                <li>
                    <input type="checkbox" id="jform_vote_pattern0" name="jform[vote_pattern][]" value="1" aria-required="true"
						<?php if ($vote_pattern == 1 || $vote_pattern == 3) { ?>
                            checked
							<?php
						} ?>/>
                    <label for="jform_vote_pattern0"><?php echo JText::_("COM_SURVEYFORCE_SETTING_FORMAT_PATTERN"); ?></label>
                </li>
                <li>
                    <input type="checkbox" id="jform_vote_pattern1" name="jform[vote_pattern][]" value="2" aria-required="true"
						<?php if ($vote_pattern == 2 || $vote_pattern == 3) { ?>
                            checked
							<?php
						} ?>/>
                    <label for="jform_vote_pattern1"><?php echo JText::_("COM_SURVEYFORCE_SETTING_PRACTICE_PATTERN"); ?></label>
                </li>
            </ul>
        </fieldset>

    </div>
</div>


<div class="control-group form-inline">
	<?php $this->form->setFieldAttribute('vote_num_param', 'required', 'true'); ?>
	<?php echo $this->form->getLabel("vote_num_param"); ?>
    <div class="controls">
        <fieldset id="jform_vote_num_param" class="checkboxes">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="jform_vote_only" name="vote_num_type" value="0" <?php echo ($vote_num_params->vote_num_type == 0) ? "checked" : ""; ?>> 投票期間僅限一票
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="jform_vote_muti" name="vote_num_type" value="1" <?php echo ($vote_num_params->vote_num_type == 1) ? "checked" : ""; ?>> 驗證條件每
                        <input type="number" id="jform_vote_day" name="vote_num_type_vote_day" value="<?php echo $vote_num_params->vote_day; ?>" size="5" class="small">天
                        <input type="number" id="jform_vote_num" name="vote_num_type_vote_num" value="<?php echo $vote_num_params->vote_num; ?>" size="5" class="small"> 票
                    </label>
                </li>
            </ul>
    </div>
</div>


<div class="control-group form-inline">
	<?php $this->form->setFieldAttribute('vote_rule_param', 'required', 'true'); ?>
	<?php echo $this->form->getLabel("vote_rule_param"); ?>
    <div class="controls">
        <fieldset id="jform_vote_rule_param" class="checkboxes">
            <ul>
                <li>
                    <label>
                        <input type="radio" id="nolimit" name="vote_num_protect" value="0" <?php echo ($vote_num_params->vote_num_protect == 0) ? "checked" : ""; ?>> 同IP 不限制
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="ticket_a_sec" name="vote_num_protect" value="1" <?php echo ($vote_num_params->vote_num_protect == 1) ? "checked" : ""; ?>> 同IP 每<input type="number" id="jform_vote_num_protect_time" name="vote_num_protect_time" value="<?php echo $vote_num_params->vote_num_protect_time; ?>" size="5" class="small">秒內只能投1票
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" id="ticket_a_day" name="vote_num_protect" value="2" <?php echo ($vote_num_params->vote_num_protect == 2) ? "checked" : ""; ?>> 同IP 每天只能投<input type="number" id="jform_vote_num_protect_vote" name="vote_num_protect_vote" value="<?php echo $vote_num_params->vote_num_protect_vote; ?>" size="5" class="small">票
                    </label>
                </li>
            </ul>
        </fieldset>
    </div>

</div>

<div class="control-group form-inline">
	<?php echo $this->form->getLabel('is_notice_email'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('is_notice_email'); ?>
    </div>
</div>

<?php

$remind_text = '親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

現在已經開始投票了，請您至以下網址進行投票：【%url%】，投票時間至【%endtime%】止。

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。';
?>
<?php echo $this->form->renderField('remind_text', null, $remind_text); ?>

<?php
$drumup_text = '親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

投票即將於【%endtime%】結束，如您還沒投票，請您儘快至以下網址進行投票：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。';
?>
<?php echo $this->form->renderField('drumup_text', null, $drumup_text); ?>


<?php
$end_text = '親愛的民眾您好：

感謝您參與【%title%】i-Voting投票。

投票結果已公布於i-Voting網站，歡迎您至以下網址觀看結果：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。';
?>
<?php echo $this->form->renderField('end_text', null, $end_text); ?>

<div class="control-group form-inline">
	<?php echo $this->form->getLabel('is_notice_phone'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('is_notice_phone'); ?>
        (簡訊費用將由承辦單位負擔)
    </div>
</div>

<?php
$phone_remind_text = '【%title%】i-Voting已經開始投票了，請立即至i-Voting投票系統進行投票。';
?>
<?php echo $this->form->renderField('phone_remind_text', null, $phone_remind_text); ?>

<?php
$phone_drumup_text = '【%title%】i-Voting投票即將於【%endtime%】結束，請立即至i-Voting投票系統進行投票。';
?>
<?php echo $this->form->renderField('phone_drumup_text', null, $phone_drumup_text); ?>

<?php
$phone_end_text = '【%title%】i-Voting投票結果已公布，請立即至i-Voting投票系統進行查看。';
?>
<?php echo $this->form->renderField('phone_end_text', null, $phone_end_text); ?>

<?php echo $this->form->renderField('sms_user', null, JHtml::_('utility.decode', $this->form->getValue('sms_user'))); ?>

<?php echo $this->form->renderField('sms_passwd', null, JHtml::_('utility.decode', $this->form->getValue('sms_passwd'))); ?>


<div class="control-group form-inline">
	<?php echo $this->form->getLabel('is_place'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('is_place'); ?>
        (驗證方式需選擇身分證字號驗證)
    </div>
</div>

<div id="new_place_image_area">
	<?php echo $this->form->renderField('place_image'); ?>
</div>


<div class="control-group form-inline">
	<?php echo $this->form->getLabel('is_analyze'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('is_analyze'); ?>
    </div>
</div>


<div id="analyze_column" style="display: <?php echo ($this->form->getValue('is_analyze') == 1) ? "inline-block" : "none"; ?>;">
    <div class="control-group form-inline">
        <label id="jform_analyze_column-lbl" for="jform_analyze_column" class="control-label">分析欄位</label>
        <div class="controls" <?php echo ($this->check_analyze) ? '' : 'style="padding-top: 5px;"' ?>>
			<?php if ($this->HasDataAnalyze) { ?>
				<?php if ($this->check_analyze) { ?>
                    <a href="#analyze_column_<?php echo (int) $this->item->id; ?>" class="btn hasTooltip" role="button" data-toggle="modal" title="選擇分析欄位"><span class="icon-file"></span> 選擇</a>
				<?php } else { ?>
                    請先儲存再選擇分析欄位
				<?php } ?>
			<?php } else { ?>
                請先新增分析欄位資料再選擇分析欄位
			<?php } ?>
        </div>
    </div>
</div>

<div style="display: none;" id="analyze_column_<?php echo (int) $this->item->id; ?>" tabindex="-1" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>選擇分析欄位</h3>
    </div>
    <div class="analyze-body">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal" aria-hidden="true">關閉</button>
    </div>

</div>

<?php echo $this->form->renderField('is_quantity', null, $this->quantity->state); ?>
<?php echo $this->form->renderField('quantity', null, $this->quantity->quantity); ?>

<script>
    let init = false;
    const radio = document.getElementsByName("jform[is_quantity]");
    const list = [...radio];
    const quantityElement = document.querySelector("#jform_quantity");

    requireCheck();

    radio.forEach(item => {
      item.addEventListener("click", () => {
        requireCheck();
      });
    });

    function requireCheck() {
      const checked = list.filter(item => item.checked === true).pop().value;
      toggle(checked);
    }

    function toggle(checked) {
      if(parseInt(checked) === 1) {
          quantityElement.classList.add("required");
          quantityElement.setAttribute("aria-required", true);
          quantityElement.setAttribute("required", "required");
        } else {
          quantityElement.classList.remove('required');
          quantityElement.removeAttribute('aria-required');
          quantityElement.removeAttribute('required');
        }
    }
</script>
