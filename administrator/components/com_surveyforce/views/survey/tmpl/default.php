<?php
/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
//JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

jimport('joomla.filesystem.file');
$user         = JFactory::getUser();
$config       = JFactory::getConfig();
$ivoting_path = $config->get('ivoting_path');
$id           = $this->item->id;
//SurveyforceHelper::addFileUploadFull('index.php?option=com_surveyforce&task=images.addImage&id=' . (int) $this->item->id, 'survey-form', 0);
// 載入所有plugin
$verify_all_array = array ();
$verify_mix_array = array ();
if ($this->verify_types) {
	// 依強度選擇驗證方式用的陣列
	$verify_mix_array[1] = array ();
	$verify_mix_array[2] = array ();
	$verify_mix_array[3] = array ();

	$mix_js       = '';
	$custom_js    = '';
	$setting_html = '';
	foreach ($this->verify_types as $type) {
		// 整理plugin資料 - 依強度選擇驗證方式
		$params = json_decode($type->params);
		if ($params->level > 0) {
			$verify_mix_array[$params->level][$type->element] = $type->name;
			$verify_all_array[$type->element]                 = $type->name;
		}

		// 載入所有plugin
		JPluginHelper::importPlugin('verify', $type->element);
		$className = 'plgVerify' . ucfirst($type->element);

		// 依強度選擇驗證方式 - 其他設定JS
		if (method_exists($className, 'onGetAdminMixSettingJS')) {
			$mix_js .= $className::onGetAdminMixSettingJS();
		}

		// 自訂驗證 - 其他設定JS
		if (method_exists($className, 'onGetAdminCustomSettingJS')) {
			$custom_js .= $className::onGetAdminCustomSettingJS();
		}

		// 其他設定HTML
		if (method_exists($className, 'onGetAdminSettingHTML')) {
			$setting_html .= $className::onGetAdminSettingHTML();
		}

		// 檢查設定JS
		if (method_exists($className, 'onAdminSettingCheckJS')) {
			$check_js .= $className::onAdminSettingCheckJS();
		}
	}
}
?>

<?php // echo $this->loadTemplate('menu');  ?>

<script type="text/javascript">

    Joomla.submitbutton = function (task) {

        jQuery('.btn').prop('disabled', true);

        if (task == 'survey.delete') {
            if (confirm("請確認是否要刪除該議題?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                jQuery('.btn').prop('disabled', false);
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.send_check') {
            if (confirm("請確認所有資料已儲存完成，是否確定要送出審核?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                jQuery('.btn').prop('disabled', false);
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.pass_success') {
            if (confirm("請確認要將該議題審核為已通過?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                jQuery('.btn').prop('disabled', false);
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.pass_fail') {
            if (jQuery("#fail_reason").val()) {
                if (confirm("請確認要將該議題審核為不通過?")) {
                    jQuery("#jform_fail_reason").val(jQuery("#fail_reason").val());
                    Joomla.submitform(task, document.getElementById('survey-form'));
                } else {
                    jQuery('.btn').prop('disabled', false);
                    return false;
                }
            } else {
                alert('請填寫不通過的原因。');
                jQuery('.btn').prop('disabled', false);
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.recheck') {
            if (confirm("請確認要將重新審核此議題?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                jQuery('.btn').prop('disabled', false);
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('survey-form'))) {
            jQuery('#dest_select option').attr('selected', 'selected');
			<?php echo $this->form->getField('desc')->save(); ?>

            // 送出前的檢查
            if (task == 'survey.apply' || task == 'survey.save') {

                //檢查討論管道欄位
                var discuss_source = jQuery("#jform_discuss_source").val();
                if (discuss_source.match(/href\=\"(.+?)\"/g)) {

                    var uri = discuss_source.match(/href\=\"(.+?)\"/g);
                    var discuss_source_tag_a = discuss_source.match(/\<\/a\>/g);

                    if (discuss_source_tag_a.length !== uri.length) {
                        jQuery("#message_area").showMessage('討論管道的超連結網址必須符合超連結範例', jQuery('#jform_discuss_source'));
                        return false;
                    }

                    for (var i = 0; i < uri.length; i++) {
                        if (!uri[i].match(/https?/g)) {
                            jQuery("#message_area").showMessage('討論管道的超連結網址必須為http://example.com或https://example.com。', jQuery('#jform_discuss_source'));
                            return false;
                        }
                    }

                }

                //檢查其他參考網址欄位
                if (jQuery("#jform_other_url").val()) {
                    var url = jQuery("#jform_other_url").val();
                    var rule = /^(https?:\/\/+[\w\-]+\.[\w\-]+)/i;
                    if (!rule.test(url)) {
                        jQuery("#message_area").showMessage('其他參考網址必須為http://example.com或https://example.com。', jQuery('#jform_other_url'));
                        return false;
                    }
                }

                //檢查後續辦理情形欄位
                if (jQuery("#jform_followup_caption").val()) {
                    var followup_caption = jQuery("#jform_followup_caption").val();

                    if (followup_caption.match(/href\=\".+?\"/g)) {

                        var followup_caption_uri = followup_caption.match(/href\=\".+?\"/g);
                        var followup_caption_tag_a = followup_caption.match(/\<\/a\>/g);

                        if (followup_caption_tag_a.length !== followup_caption_uri.length) {
                            jQuery("#message_area").showMessage('後續辦理情形的超連結網址必須符合超連結範例', jQuery('#jform_followup_caption'));
                            return false;
                        }

                        for (var j = 0; j < followup_caption_uri.length; j++) {
                            if (!followup_caption_uri[j].match(/https?/g)) {
                                jQuery("#message_area").showMessage('後續辦理情形的超連結網址必須為http://example.com或https://example.com。', jQuery('#jform_followup_caption'));
                                return false;
                            }
                        }

                    }
                }


                if (jQuery("#jform_desc").val() == "") {
                    jQuery("#message_area").showMessage('請填寫議題介紹。');
                    return false;
                }

                // 檢查發佈日期
                if (!jQuery("#jform_publish_up").checkDatePattern()) {
                    jQuery("#message_area").showMessage('日期格式不符。', jQuery('#jform_publish_up'));
                    return false;
                }

                if (!jQuery("#jform_vote_start").checkDatePattern()) {
                    jQuery("#message_area").showMessage('日期格式不符。', jQuery('#jform_vote_start'));
                    return false;
                }

                if (!jQuery("#jform_vote_end").checkDatePattern()) {
                    jQuery("#message_area").showMessage('日期格式不符。', jQuery('#jform_vote_end'));
                    return false;
                }

                if (Date.parse(jQuery("#jform_publish_down").val()).valueOf() < Date.parse("<?php echo date("Y-m-d H:i:s"); ?>").valueOf()) {
                    jQuery("#message_area").showMessage('議題下架時間必須晚於目前時間。', jQuery('#jform_publish_down'));
                    return false;
                }

                if (Date.parse(jQuery("#jform_publish_up").val()).valueOf() > Date.parse(jQuery("#jform_publish_down").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題上架時間必須早於下架時間。', jQuery('#jform_publish_up'));
                    return false;
                }

                if (Date.parse(jQuery("#jform_vote_end").val()).valueOf() < Date.parse(jQuery("#jform_vote_start").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題開始投票時間必須早於結束投票時間。', jQuery('#jform_vote_end'));
                    return false;
                }

                if (Date.parse(jQuery("#jform_publish_up").val()).valueOf() > Date.parse(jQuery("#jform_vote_start").val()).valueOf() || Date.parse(jQuery("#jform_publish_down").val()).valueOf() < Date.parse(jQuery("#jform_vote_end").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題投票時間不得晚於議題上架期間。', jQuery('#jform_vote_start'));
                    return false;
                }

                if (jQuery("input:checkbox:checked[name='jform[vote_pattern][]']").length === 0) {
                    jQuery("#message_area").showMessage('請勾選投票模式。', jQuery("label[for^='jform_vote_pattern']"));
                    return false;
                }


                // 投票數設定 - 驗證每天幾票
                if (jQuery('input:radio:checked[name="vote_num_type"]').val() == 1) {
                    if (isNaN(jQuery("#jform_vote_day").val()) || isNaN(jQuery("#jform_vote_num").val())) {
                        jQuery("#message_area").showMessage('投票數設定的驗證條件需為數字。', (isNaN(jQuery("#jform_vote_day").val())) ? jQuery('#jform_vote_day') : jQuery('#jform_vote_num'));
                        return false;
                    } else {
                        if (jQuery("#jform_vote_day").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫投票數設定的驗證條件。', jQuery('#jform_vote_day'));
                            return false;
                        }
                        if (jQuery("#jform_vote_num").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫投票數設定的驗證條件。', jQuery('#jform_vote_num'));
                            return false;
                        }
                    }

                }

                if (jQuery('input:radio:checked[name="vote_announcement_date"]').val() === "1") {
                    if (jQuery("#jform_announcement_date").val() == "") {
                        jQuery("#message_area").showMessage('請填寫公布日期。', jQuery('#jform_announcement_date'));
                        return false;
                    }

                    if (!jQuery("#jform_announcement_date").checkDatePattern()) {
                        jQuery("#message_area").showMessage('日期格式不符。', jQuery('#jform_announcement_date'));
                        return false;
                    }

                    if (Date.parse(jQuery("#jform_announcement_date").val()).valueOf() < Date.parse(jQuery("#jform_vote_end").val()).valueOf()) {
                        jQuery("#message_area").showMessage('公布日期不可早於投票結束時間。', jQuery('#jform_announcement_date'));
                        return false;
                    }

                }

                // 防止灌票機制
                if (jQuery('input:radio:checked[name="vote_num_protect"]').val() == 1) {
                    if (isNaN(jQuery("#jform_vote_num_protect_time").val())) {
                        jQuery("#message_area").showMessage('防止灌票機制的秒數需為數字。', jQuery('#jform_vote_num_protect_time'));
                        return false;
                    } else if (jQuery("#jform_vote_num_protect_time").val() == 0) {
                        jQuery("#message_area").showMessage('請填寫防止灌票機制的秒數條件。', jQuery('#jform_vote_num_protect_time'));
                        return false;
                    }

                }

                if (jQuery('input:radio:checked[name="vote_num_protect"]').val() == 2) {
                    if (isNaN(jQuery("#jform_vote_num_protect_vote").val())) {
                        jQuery("#message_area").showMessage('防止灌票機制的票數需為數字。', jQuery('#jform_vote_num_protect_vote'));
                        return false;
                    } else if (jQuery("#jform_vote_num_protect_vote").val() == 0) {
                        jQuery("#message_area").showMessage('請填寫防止灌票機制的票數條件。', jQuery('#jform_vote_num_protect_vote'));
                        return false;
                    }

                }


                // 郵件訊息設定
                if (jQuery('input:radio:checked[name="jform[is_notice_email]"]').val() == 1) {
                    if (jQuery("#jform_remind_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫電子郵件訊息通知-投票前提醒。', jQuery('#jform_remind_text'));
                        return false;
                    }
                    if (jQuery("#jform_drumup_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫電子郵件訊息通知-催票提醒。', jQuery('#jform_drumup_text'));
                        return false;
                    }
                    if (jQuery("#jform_end_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫電子郵件訊息通知-投票結束通知提醒。', jQuery('#jform_end_text'));
                        return false;
                    }
                }


                // 手機訊息設定
                if (jQuery('input:radio:checked[name="jform[is_notice_phone]"]').val() == 1) {
                    if (jQuery("#jform_phone_remind_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫手機訊息通知-投票前提醒。', jQuery('#jform_phone_remind_text'));
                        return false;
                    }
                    if (jQuery("#jform_phone_drumup_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫手機訊息通知-催票提醒。', jQuery('#jform_phone_drumup_text'));
                        return false;
                    }
                    if (jQuery("#jform_phone_end_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫手機訊息通知-投票結束通知提醒。', jQuery('#jform_phone_end_text'));
                        return false;
                    }

                    if (jQuery("#jform_sms_user").val() == "") {
                        jQuery("#message_area").showMessage('請填寫簡訊平台帳號。', jQuery('#jform_sms_user'));
                        return false;
                    }
                    if (jQuery("#jform_sms_passwd").val() == "") {
                        jQuery("#message_area").showMessage('請填寫簡訊平台密碼。', jQuery('#jform_sms_passwd'));
                        return false;
                    }
                }

                //啟用現地投票
                if (jQuery("#jform_is_place0").is(":checked")) {
                    if (!jQuery("#is_old_verify_type").val().match(/idnum/g) || jQuery("#is_old_verify").val() === "0" || jQuery("#is_old_verify").val() === "") {
                        if ((jQuery("#verify_method_1").is(":checked") || jQuery("#verify_method_0").is(":checked")) && jQuery("#verify_mix_idnum").checkVerify("#verify_method_1", ":checked") === false) {
                            return false;
                        } else if (jQuery("#verify_method_2").is(":checked") && jQuery("#idnum").checkVerify("#verify_method_2", ":selected") === false) {
                            return false;
                        }
                    }
                }


                // 驗證方式
                if (jQuery("#is_old_verify").val() == 0) {
                    if (jQuery('input:radio:checked[name="verify_method"]').val() == 1) {		// 依強度選擇驗證方式
                        if (jQuery('input:radio:checked[name="verify_mix"]').val() == undefined) {
                            jQuery("#message_area").showMessage('依強度選擇驗證方式 - 請選擇其中一種驗證方式。', jQuery('input:radio:checked[name="verify_method"]').parent().next().children());
                            return false;
                        }

                        // 載入JS檢查
                        check_verify_method = jQuery('input:radio:checked[name="verify_mix"]').val();
						<?php
						echo $check_js;
						?>

                    } else if (jQuery('input:radio:checked[name="verify_method"]').val() == 2) {	// 自訂驗證

                        if (jQuery('#dest_select').val() == null) {
                            jQuery("#message_area").showMessage('自訂驗證 - 請選擇其中一種驗證方式。', jQuery('input:radio:checked[name="verify_method"]').parent().next().children());
                            return false;
                        }

                        if (jQuery('#verify_required').val() == 1) {
                            if (jQuery("#dest_select").get(0).options.length < 2) {
                                jQuery("#message_area").showMessage('自訂驗證 - 驗證組合方式為同時，請至少選擇兩種驗證方式。', jQuery('input:radio:checked[name="verify_method"]').parent().next().children());
                                return false;
                            }
                        }


                        // 載入JS檢查
                        is_check_suceess = true;
                        jQuery("#dest_select").find(":selected").each(function () {
                            check_verify_method = this.value;
							<?php
							echo $check_js;
							?>
                        });
                        if (is_check_suceess == false) {
                            return false;
                        }

                    }
                }


                // 投票結果數設定
                if (jQuery('input:radio:checked[name="jform[result_num_type]"]').val() == 1) {
                    if (isNaN(jQuery("#jform_result_num").val())) {
                        jQuery("#message_area").showMessage('投票結果數設定需為數字。', jQuery('#jform_result_num'));
                        return false;
                    } else {
                        if (jQuery("#jform_result_num").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫投票結果數。', jQuery('#jform_result_num'));
                            return false;
                        }
                    }

                }

                if (jQuery(".invalid").length !== 0) {
                    jQuery("#message_area").showMessage('尚有欄位未符合欄位要求。');
                    return false;
                }

            }

            Joomla.submitform(task, document.getElementById('survey-form'));

        } else {
            jQuery("#message_area").showMessage('請填寫必填欄位。');
            return false;
        }
    };


    function addtextarea() {

        jQuery(".parther").css("display", "none");
        jQuery("#jform_part").val("").removeAttr("required");
        jQuery("#jform_other").val("").removeAttr("required");
        var addvalue = jQuery("#jform_results_proportions").val();
        if (addvalue == "part" || addvalue == "other") {
            jQuery("#" + addvalue).css("display", "block");
            jQuery("#jform_" + addvalue).attr("required", "required");
        }
    }


    jQuery(document).ready(function () {
        /**
         * @param msg 文字訊息
         * @param target 要渲染css的目標
         */
        jQuery.fn.showMessage = function (msg, target = null) {
            jQuery('html, body').scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
            if (target) {
                target.addClass("invalid");
            }
            jQuery(".btn").prop("disabled", false);

            if (jQuery(".invalid").parents("li.active").length === 0) {
                if (target) {
                    var old_active = jQuery("#configTabs").find("li.active").find("a").attr("href");
                    jQuery(old_active).removeClass("active");
                    target.parents(".tab-pane").last().addClass("active");

                    jQuery("#configTabs").find("li.active").removeClass("active");
                    jQuery("a[href='#" + target.parents(".tab-pane").last().attr("id") + "']").parent("li").addClass("active");
                } else {
                    var old_active = jQuery("#configTabs").find("li.active").find("a").attr("href");
                    jQuery(old_active).removeClass("active");
                    jQuery(".invalid").parents(".tab-pane").last().addClass("active");

                    jQuery("#configTabs").find("li.active").removeClass("active");
                    jQuery("a[href='#" + jQuery(".invalid").parents(".tab-pane").last().attr("id") + "']").parent("li").addClass("active");
                }
            }

        };

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        };

        //檢查日期格式
        jQuery.fn.checkDatePattern = function () {
            var pattern = /\d{4}-(0?[1-9]|1[0-2]{1})-([0-2]?[0-9]|3[0-1])\s([0-1]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])/g;
            return this.attr('value').match(pattern);
        };


        jQuery.fn.checkVerify = function (verify_method, status) {
            if (jQuery('#' + this.attr('id')).is(status) === false) {

                var verify_type = {'idnum': '身分證字號驗證', 'assign': '可投票人名單驗證'};
                var verify_func = {'idnum': '現地投票', 'assign': '分析功能'};
                var verify_selector = {
                    ':checked': jQuery('#' + this.attr("id")).next(),
                    ':selected': jQuery('#' + this.attr("id"))
                };

                if (jQuery("#jform_id").val()) {

                    if (jQuery("#reset_verify").is(":hidden")) {
                        jQuery('#reset_verify').trigger('click');
                    }

                    if (verify_method !== '#verify_method_2') {
                        jQuery(verify_method).trigger('click');
                    }

                }

                jQuery('#message_area').showMessage('啟用' + verify_func[this.attr('value')] + '需選擇' + verify_type[this.attr('value')] + '方式。', verify_selector[status]);
                if (verify_method === '#verify_method_2') {
                    jQuery('#src_select').animate({
                        scrollTop: jQuery('#' + this.attr('id')).offset().top
                    }, 100);
                }
                return false;
            } else {
                return true;
            }
        };


        //檢查上傳圖片
        jQuery("input:file").change(function (e) {

            if (e.target.files[0] && (e.target.accept == "image/*" || e.target.accept == "application/pdf")) {
                var limit = 0;
                var allow_type = [];
                var title;
                var type;
                switch (e.target.accept) {
                    case "image/*":
                        limit = 2097152;
                        allow_type = ["image/jpeg", "image/pjpeg", "image/png", "image/gif"];
                        if (e.target.id = "jform_image") {
                            title = "議題圖片";
                        } else {
                            title = "掃瞄標的物圖片";
                        }
                        type = "jpg/png/gif";
                        break;
                    case "application/pdf":
                        limit = 5242880;
                        allow_type = ["application/pdf"];
                        title = "其他參考資料";
                        type = "pdf";
                        break;
                    default:
                }

                if (e.target.files[0].size > limit) {  //假如檔案大小超過指定大小)
                    jQuery("#message_area").showMessage(title + " - 附件檔超過指定大小(" + limit / 1048576 + "MB)。", jQuery(this).parent());
                    return false;
                }

                if (allow_type.indexOf(e.target.files[0].type) === -1) {  //假如檔案類型不相符
                    jQuery("#message_area").showMessage(title + " - 只允許上傳的檔案類型為：" + type, jQuery(this).parent());
                    return false;
                }

                jQuery("#message_area").hideMessage();
                jQuery(this).parent().removeClass("invalid");

            } else {

                jQuery("#message_area").hideMessage();
                jQuery(this).parent().removeClass("invalid");

            }

        });

        jQuery("#survey-verify input:radio, #survey-verify input:checkbox, #survey-verify input:file, input:checkbox[name=\'jform[vote_pattern][]\']").click(function () {
            jQuery(".invalid").removeClass("invalid");
        });

        // 驗證的選單回復至預設值
        jQuery.fn.resetVerify = function () {
            jQuery(".verify_setting").hide();
            jQuery(".verify_mix").each(function () {
                jQuery(this).attr('checked', false);
            });
            jQuery("#verify_required").attr("value", "0");
            jQuery("#src_select").html(temp_custom_verify);
            jQuery("#dest_select").html("");
        };


        var temp_custom_verify = jQuery("#src_select").html();
		<?php
		if ($this->form->getValue('image')) {
		?>
        jQuery("#new_image_area").hide();
        jQuery("#del_image_btn").bind("click", function () {
            jQuery("#old_image").val("");
            jQuery("#old_image_area").hide();
            jQuery("#new_image_area").show();
        });
		<?php } ?>


		<?php
		if ($this->form->getValue('part')) {
		?>
        jQuery("#jform_part").attr("required", "required");
		<?php } ?>
		<?php
		if ($this->form->getValue('other')) {
		?>
        jQuery("#jform_other").attr("required", "required");
		<?php } ?>

		<?php
		foreach ($this->data as $key => $item) {
		if ($this->form->getValue($key)) {
		?>
        jQuery(".old_pdf_area").next(".new_pdf_area").hide();
        jQuery(".del_pdf_btn").bind("click", function () {
            jQuery(this).next(".old_pdf").val("");
            jQuery(this).parent(".old_pdf_area").hide();
            jQuery(this).parent(".old_pdf_area").next(".new_pdf_area").show();
        });
		<?php }
		} ?>

        jQuery(".other_data").on("click", function () {
            jQuery("#file_name").val(this.id);
            jQuery("#original_name").val(this.title);
            jQuery("#task").val("survey.other_data");
            jQuery("#survey-form").submit();
        });


        jQuery("input:radio[id^=\"jform_is_define\"]").bind("click", function () {
            if (this.value == 0) {
                jQuery("#proposal_process").show();
            } else {
                jQuery("#proposal_process").hide();
            }
        });

        jQuery("input:radio[id^=\"jform_is_public\"]").bind("click", function () {
            if (this.value == 0) {
                jQuery("#un_public_tmpl").show();
            } else {
                jQuery("#un_public_tmpl").hide();
            }
        });


		<?php
		$vote_num_params = json_decode($this->form->getValue('vote_num_params'));
		if ($vote_num_params->method == 2) {
		?>
        jQuery("#jform_announcement_date").val("");
		<?php
		}
		?>
		<?php
		if ($this->form->getValue('is_notice_email') == 0) {
		?>
        jQuery("#notice_email_area").hide();
		<?php } ?>

        jQuery('#jform_is_notice_email label').bind("click", function () {
            if (jQuery(this).hasClass("active") && jQuery.trim(jQuery(this).html()) == "是") {
                jQuery("#notice_email_area").show();
            } else {
                jQuery("#notice_email_area").hide();
            }

        });
		<?php
		if ($this->form->getValue('is_notice_phone') == 0) {
		?>
        jQuery("#notice_phone_area").hide();
		<?php } ?>

        jQuery('#jform_is_notice_phone label').bind("click", function () {
            if (jQuery(this).hasClass("active") && jQuery.trim(jQuery(this).html()) == "是") {
                jQuery("#notice_phone_area").show();
            } else {
                jQuery("#notice_phone_area").hide();
            }

        });

		<?php if ($this->form->getValue('is_place') == 0) { ?>
        jQuery("#is_place_area").hide();
		<?php } ?>

		<?php if($this->form->getValue('place_image')){ ?>
        jQuery("#new_place_image_area").hide();
		<?php } ?>

        jQuery('#jform_is_place label').bind("click", function () {
            if (jQuery(this).hasClass("active") && jQuery.trim(jQuery(this).html()) == "是") {
                jQuery("#is_place_area").show();
            } else {
                jQuery("#message_area").hideMessage();
                jQuery('input[name="jform[place_image]"]').parent().removeClass("invalid");
                jQuery("input[name='jform[place_image]']").val('');
                jQuery("#is_place_area").hide();
            }

        });

        jQuery("#del_place_image_btn").bind("click", function () {
            jQuery("#old_place_image").val("");
            jQuery("#old_place_image_area").hide();
            jQuery("#new_place_image_area").show();
        });


        // 重新開啟選擇驗證的區塊
        jQuery('#reset_verify').bind("click", function () {
            jQuery("#verify_table").show();
            jQuery(this).hide();
            jQuery("#is_old_verify").val("0");
        });
        // 點選不驗證
        jQuery('#verify_method_0').bind("click", function () {
            jQuery("#verify_table_module").hide();
            jQuery("#verify_table_custom").hide();
            jQuery('#verify_method_0').resetVerify();
        });
        // 點選依強度選擇驗證方式
        jQuery('#verify_method_1').bind("click", function () {
            jQuery("#verify_table_module").show();
            jQuery("#verify_table_custom").hide();
            jQuery('#verify_method_1').resetVerify();
        });
        // 點選自訂驗證
        jQuery('#verify_method_2 ').bind("click", function () {
            jQuery("#verify_table_module").hide();
            jQuery("#verify_table_custom").show();
            jQuery('#verify_method_2').resetVerify();
        });
        // 依強度選擇驗證方式 - 是否顯示其他設定
        jQuery('.verify_mix').bind("click", function () {
			<?php echo $mix_js; ?>
        });
        // 自訂驗證-點選加入
        jQuery('#select_add_btn').bind("click", function () {
            src_count = 0;
            jQuery("#src_select").find(":selected").each(function () {
                src_count += 1;
            });
            if (src_count == 0) {
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。', jQuery('#src_select'));
                return false;
            }

            jQuery("#src_select").find(":selected").each(function () {
                jQuery(new Option(this.text, this.value)).appendTo('#dest_select').attr('id', this.id);
                jQuery(this).remove();
            });
            // 自訂驗證 - 是否顯示其他設定
            jQuery(".verify_setting").hide();
            jQuery('#dest_select option').attr('selected', 'selected');
            dest_select_array = jQuery('#dest_select').val();
            if (dest_select_array.length > 0) {
				<?php echo $custom_js; ?>
            }
        });
        // 自訂驗證-點選移除
        jQuery('#select_remove_btn').bind("click", function () {
            dest_count = 0;
            jQuery("#dest_select").find(":selected").each(function () {
                dest_count += 1;
            });
            if (dest_count == 0) {
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。', jQuery('#dest_select'));
                return false;
            }

            jQuery("#dest_select").find(":selected").each(function () {
                jQuery(new Option(this.text, this.value)).appendTo('#src_select');
                jQuery(this).remove();
            });
            // 自訂驗證 - 是否顯示其他設定
            jQuery(".verify_setting").hide();
            jQuery('#dest_select option').attr('selected', 'selected');
            dest_select_array = jQuery('#dest_select').val();
            if (dest_select_array) {
				<?php echo $custom_js; ?>
            }

        });

        jQuery('#configTabs a:first').tab('show');


        // 審查
        jQuery("#btnForm").fancybox({
            'height': 200
        });

        jQuery("#jform_is_analyze0").on("click", function () {
            jQuery("#analyze_column").show();
        });

        jQuery("#jform_is_analyze1").on("click", function () {
            jQuery("#analyze_column").hide();
        });

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
<style>
    .verify_table_module {
        margin-bottom: 10px;
    }

    .verify_table_module td {
        padding: 5px;
    }

    .verify_table_module label {
        display: inline;
    }

    input.small {
        width: 30px;
    }

    input.medium {
        width: 80px;
    }

    textarea.large {
        width: 90%;
    }

    <?php
    if (!$this->form->getValue('part')) {
        ?>
    #part {
        display: none;
    }

    <?php } ?>
    <?php
    if (!$this->form->getValue('other')) {
        ?>
    #other {
        display: none;
    }

    <?php } ?>
    .parther {
        margin-top: 5px;
    }

    .url_color {
        color: red;
    }

    .iframe {
        border: 0 !important;
    }
</style>
<?php $params = JComponentHelper::getParams('com_surveyforce')->toObject(); ?>


<div id="message_area" style="display: none;">
    <div id="system-message" class="alert alert-error">
        <h4 class="alert-heading"></h4>
        <div>
            <p id="message_content"></p>
        </div>
    </div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>"
      enctype="multipart/form-data" method="post" name="survey-form" id="survey-form" class="form-validate">
    <input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
    <fieldset>
        <legend><?php echo (empty($this->item->id)) ? JText::_('COM_SURVEYFORCE_NEW_SURVEY') : JText::_('COM_SURVEYFORCE_EDIT_SURVEY'); ?></legend>
        <div class="row-fluid">
            <div id="j-main-container" class="span7 form-horizontal">
                <ul class="nav nav-tabs" id="configTabs">
                    <li><a href="#survey-details" data-toggle="tab">議題說明</a></li>
                    <li><a href="#survey-settings"
                           data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_SETTINGS'); ?></a></li>
                    <li><a href="#survey-verify"
                           data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_VERIFY'); ?></a></li>
                    <li>
                        <a href="#survey-final" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_FINAL_PAGE2'); ?></a>
                    </li>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane" id="survey-details">

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('title'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('title'); ?>
                            </div>
                        </div>


                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('desc'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('desc'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('image'); ?>
                            <div class="controls">
								<?php
								if ($this->form->getValue('image')) {
									?>
                                    <div id="old_image_area">
                                        <a href="../<?php echo $this->form->getValue('image'); ?>" class="fancybox"
                                           title="預覽檢視">預覽檢視</a>
                                        <input class="btn" type="button" id="del_image_btn" style="width:70px " value="刪除">
                                        <input type="hidden" id="old_image" name="old_image"
                                               value="<?php echo $this->form->getValue('image'); ?>">
                                    </div>
								<?php } ?>

                                <div id="new_image_area">
                                    <div>
										<?php echo $this->form->getInput('image'); ?>
                                    </div>
                                    (請上傳2MB以內的圖片且寬度最小679px，高度不限，未選擇時將用預設圖替代。)
                                </div>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('layout'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('layout'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('vote_way'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('vote_way'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('voters_eligibility'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('voters_eligibility'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('promotion'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('promotion'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline" style="display: none;">
							<?php echo $this->form->getLabel('results_using'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('results_using'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('announcement_method'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('announcement_method'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('at_present'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('at_present'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('discuss_source'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('discuss_source'); ?>
                                <br> (超連結範例：將網址用&lt;a href="<span class="url_color">連結網址</span>" target="_blank"&gt;<span
                                        class="url_color">連結名稱</span>&lt;/a&gt;包起來，
                                <br> 如需輸入兩個網址以上時，請分別用&lt;a href="<span class="url_color">連結網址</span>" target="_blank"&gt;<span class="url_color">連結名稱</span>&lt;/a&gt;包起來，並用<span
                                        class="url_color">&nbsp;&#59;&nbsp;</span>隔開)
                                <br> (網址範例：<span class="url_color">http</span>://example.com 或 <span
                                        class="url_color">https</span>://example.com)
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('results_proportion'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('results_proportion'); ?>
                                <div class="parther" id="part">
									<?php echo $this->form->getInput('part'); ?>％
                                </div>
                                <div class="parther" id="other">
									<?php echo $this->form->getInput('other'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('other_data'); ?>
                            <div class="controls">
								<?php
								$i = 0;
								foreach ($this->data as $key => $item) {
									if ($this->form->getValue($key)) {
										?>
                                        <div class="old_pdf_area">
                                            <a href="javascript:void(0)" class="other_data" id="<?php echo $key; ?>" target="_blank" title="<?php echo $item; ?>"><?php echo $item; ?></a>
                                            <input class="btn del_pdf_btn" type="button" style="width:70px " value="刪除">
                                            <input type="hidden" class="old_pdf" name="old_pdf<?php echo $i > 0 ? $i + 1 : ""; ?>"
                                                   value="<?php echo $this->form->getValue($key); ?>">
                                        </div>
									<?php } ?>
                                    <div class="new_pdf_area">
										<?php echo $this->form->getInput($key); ?>
                                    </div>
									<?php
									$i++;
								}
								?>
                            </div>
                            <div class="controls">(請上傳5MB以內的pdf檔。)</div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('other_url'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('other_url'); ?>
                                <br> (網址範例：<span class="url_color">http</span>://example.com 或 <span
                                        class="url_color">https</span>://example.com)
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('followup_caption'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('followup_caption'); ?>
                                <br> (超連結範例：將網址用&lt;a href="<span class="url_color">連結網址</span>" target="_blank"&gt;<span
                                        class="url_color">連結名稱</span>&lt;/a&gt;包起來)<br> (網址範例：<span class="url_color">http</span>://example.com 或
                                <span
                                        class="url_color">https</span>://example.com)
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('precautions'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('precautions'); ?>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="survey-settings">
                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('publish_up'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('publish_up'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline" style="display:none;">
							<?php echo $this->form->getLabel('publish_down'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('publish_down'); ?>
                                (下架即不出現於歷史議題中)
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('vote_start'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('vote_start'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('vote_end'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('vote_end'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_public'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_public'); ?>
                            </div>
                        </div>
						<?php
						if ($this->form->getValue('is_public') == 0 && $this->item->id) {
							$display = "block";
						} else {
							$display = "none";
						}
						?>
                        <div id="un_public_tmpl" style="display: <?php echo $display; ?>;">
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('un_public_tmpl'); ?>
                                <div class="controls">
									<?php echo $this->form->getInput('un_public_tmpl'); ?>
                                </div>
                            </div>
                        </div>


                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_define'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_define'); ?>
                            </div>
                        </div>
						<?php
						if ($this->form->getValue('is_define') == 0 && $this->item->id) {
							$display = "block";
						} else {
							$display = "none";
						}
						?>
                        <div id="proposal_process" style="display: <?php echo $display; ?>;">
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('proposal_process'); ?>
                                <div class="controls">
									<?php echo $this->form->getInput('proposal_process'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('vote_pattern'); ?>
                            <div class="controls">
								<?php $vote_pattern = $this->item->vote_pattern; ?>
                                <p>
                                    <input type="checkbox" id="jform_vote_pattern0" name="jform[vote_pattern][]" value="1"
										<?php if ($vote_pattern == 1 || $vote_pattern == 3) { ?>
                                            checked
											<?php
										}
										?> /> <label for="jform_vote_pattern0">正式投票</label>
                                </p>

                                <input type="checkbox" id="jform_vote_pattern1" name="jform[vote_pattern][]" value="2"
									<?php if ($vote_pattern == 2 || $vote_pattern == 3) { ?>
                                        checked
										<?php
									}
									?> /> <label for="jform_vote_pattern1">練習投票</label>

                            </div>
                        </div>

                        <div class="control-group form-inline">
                            <label id="jform_display_result-lbl" for="jform_display_result" class="control-label"
                                   aria-invalid="false"> 投票數設定</label>
                            <div class="controls">
                                <p><input type="radio" name="vote_num_type"
                                          value="0" <?php echo ($vote_num_params->vote_num_type == 0) ? "checked" : ""; ?>> 投票期間僅限一票
                                </p>
                                <input type="radio" name="vote_num_type"
                                       value="1" <?php echo ($vote_num_params->vote_num_type == 1) ? "checked" : ""; ?>> 驗證條件每
                                <input type="text" id="jform_vote_day" name="vote_num_type_vote_day"
                                       value="<?php echo $vote_num_params->vote_day; ?>" size="5" class="small">天
                                <input type="text" id="jform_vote_num" name="vote_num_type_vote_num"
                                       value="<?php echo $vote_num_params->vote_num; ?>" size="5" class="small"> 票
                            </div>
                        </div>

                        <div class="control-group form-inline">
                            <label id="jform_display_result-lbl" for="jform_display_result" class="control-label"
                                   aria-invalid="false"> 公布日期</label>
                            <div class="controls">
                                <p>
                                    <input type="radio" name="vote_announcement_date"
                                           value="0" <?php echo ($vote_num_params->method == 0) ? "checked" : ""; ?> /> 不公布
                                </p>
                                <p class="announcement_date">
                                    <input type="radio" name="vote_announcement_date"
                                           value="1" <?php echo ($vote_num_params->method == 1) ? "checked" : ""; ?> /> 公布
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
								<?php echo $this->form->getInput('announcement_date'); ?>
                                <p>
                                    <input type="radio" name="vote_announcement_date"
                                           value="2" <?php echo ($vote_num_params->method == 2) ? "checked" : ""; ?> /> 投票結束時間
                                </p>
                            </div>
                        </div>

                        <div class="control-group form-inline">
                            <label id="jform_display_result-lbl" for="jform_display_result" class="control-label"
                                   aria-invalid="false"> 防止灌票機制</label>
                            <div class="controls">
                                <p><input type="radio" name="vote_num_protect"
                                          value="0" <?php echo ($vote_num_params->vote_num_protect == 0) ? "checked" : ""; ?>> 同IP 不限制
                                </p>
                                <p><input type="radio" name="vote_num_protect"
                                          value="1" <?php echo ($vote_num_params->vote_num_protect == 1) ? "checked" : ""; ?>> 同IP 每<input type="text" id="jform_vote_num_protect_time" name="vote_num_protect_time"
                                                                                                                                           value="<?php echo $vote_num_params->vote_num_protect_time; ?>" size="5"
                                                                                                                                           class="small">秒內只能投1票
                                </p>
                                <input type="radio" name="vote_num_protect"
                                       value="2" <?php echo ($vote_num_params->vote_num_protect == 2) ? "checked" : ""; ?>> 同IP 每天只能投<input type="text" id="jform_vote_num_protect_vote" name="vote_num_protect_vote"
                                                                                                                                            value="<?php echo $vote_num_params->vote_num_protect_vote; ?>" size="5"
                                                                                                                                            class="small">票
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_notice_email'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_notice_email'); ?>
                            </div>
                        </div>

                        <div id="notice_email_area">
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('remind_text'); ?>
                                <div class="controls">
								<textarea name="jform[remind_text]" id="jform_remind_text" cols="5" rows="10"
                                          class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

現在已經開始投票了，請您至以下網址進行投票：【%url%】，投票時間至【%endtime%】止。

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('drumup_text'); ?>
                                <div class="controls">
								<textarea name="jform[drumup_text]" id="jform_drumup_text" cols="5" rows="10"
                                          class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

投票即將於【%endtime%】結束，如您還沒投票，請您儘快至以下網址進行投票：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('end_text'); ?>
                                <div class="controls">
								<textarea name="jform[end_text]" id="jform_end_text" cols="5" rows="10"
                                          class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您參與【%title%】i-Voting投票。

投票結果已公布於i-Voting網站，歡迎您至以下網址觀看結果：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>
                        </div>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_notice_phone'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_notice_phone'); ?>
                                (簡訊費用將由承辦單位負擔)
                            </div>
                        </div>

                        <div id="notice_phone_area">
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('phone_remind_text'); ?>
                                <div class="controls">
								<textarea name="jform[phone_remind_text]" id="jform_phone_remind_text" cols="5"
                                          rows="10" class="inputbox large" aria-invalid="false">
【%title%】i-Voting已經開始投票了，請立即至i-Voting投票系統進行投票。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('phone_drumup_text'); ?>
                                <div class="controls">
								<textarea name="jform[phone_drumup_text]" id="jform_phone_drumup_text" cols="5"
                                          rows="10" class="inputbox large" aria-invalid="false">
【%title%】i-Voting投票即將於【%endtime%】結束，請立即至i-Voting投票系統進行投票。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('phone_end_text'); ?>
                                <div class="controls">
								<textarea name="jform[phone_end_text]" id="jform_phone_end_text" cols="5" rows="10"
                                          class="inputbox large" aria-invalid="false">
【%title%】i-Voting投票結果已公布，請立即至i-Voting投票系統進行查看。
								</textarea> <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                                </div>
                            </div>

                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('sms_user'); ?>
                                <div class="controls">
                                    <input type="text" name="jform[sms_user]" id="jform_sms_user"
                                           value="<?php echo JHtml::_('utility.decode', $this->form->getValue('sms_user')); ?>"
                                           class="input-xlarge" size="30">

                                </div>
                            </div>
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('sms_passwd'); ?>
                                <div class="controls">
                                    <input type="password" name="jform[sms_passwd]" id="jform_sms_passwd"
                                           value="<?php echo JHtml::_('utility.decode', $this->form->getValue('sms_passwd')); ?>"
                                           class="input-xlarge" size="30">
                                </div>
                            </div>

                        </div>


                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_place'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_place'); ?>
                                (驗證方式需選擇身分證字號驗證)
                            </div>
                        </div>

                        <div id="is_place_area">
                            <div class="control-group form-inline">
								<?php echo $this->form->getLabel('place_image'); ?>
                                <div class="controls">
									<?php
									if ($this->form->getValue('place_image')) {
										?>
                                        <div id="old_place_image_area">
                                            <a href="../<?php echo $this->form->getValue('place_image'); ?>"
                                               class="fancybox" title="預覽檢視">預覽檢視</a>
                                            <input class="btn" type="button" id="del_place_image_btn" style="width:70px "
                                                   value="刪除">
                                            <input type="hidden" id="old_place_image" name="old_place_image"
                                                   value="<?php echo $this->form->getValue('place_image'); ?>">
                                        </div>
									<?php } ?>

                                    <div id="new_place_image_area">
                                        <div>
											<?php echo $this->form->getInput('place_image'); ?>
                                        </div>
                                        (請上傳2MB以內的圖片，未選擇時將用預設圖替代。)
                                    </div>
                                </div>
                            </div>
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
                                            <a href="#analyze_column_<?php echo (int) $id; ?>" class="btn hasTooltip" role="button" data-toggle="modal" title="選擇分析欄位"><span class="icon-file"></span> 選擇</a>
										<?php } else { ?>
                                            請先儲存再選擇分析欄位
										<?php } ?>
									<?php } else { ?>
                                        請先新增分析欄位資料再選擇分析欄位
									<?php } ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: none;" id="analyze_column_<?php echo (int) $id; ?>" tabindex="-1" class="modal hide fade">
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

                    </div>


                    <div class="tab-pane" id="survey-verify">
						<?php
						if ($this->item->id) {
							$verify_type   = json_decode($this->form->getValue('verify_type'), true);
							$verify_params = json_decode($this->form->getValue('verify_params'), true);


							if (!is_array($verify_type) || $verify_type[0] == "none") {
								echo "該議題設定為圖形驗證碼。";
							} else {
								?>
                                <table border="1" class="verify_table_module">
                                    <tr>
                                        <th align="center" width="150">驗證項目</th>
                                        <th align="center" width="350">備註</th>
                                    </tr>
									<?php
									foreach ($verify_type as $type) {
										?>
                                        <tr>
                                            <td>
												<?php
												echo $verify_all_array[$type];
												?>
                                            </td>
                                            <td>
												<?php
												$className = 'plgVerify' . ucfirst($type);

												// 顯示params
												if (method_exists($className, 'onGetAdminShowParams')) {
													echo $className::onGetAdminShowParams($verify_params);
												}
												?>
                                            </td>
                                        </tr>
									<?php } ?>
                                </table>
                                驗證組合方式：<?php echo ($this->form->getValue('verify_required')) ? "同時" : "擇一"; ?>
								<?php
							}
							?>
                            <br>
                            <br>
							<?php
							if ($this->can_save == true) {
								?>
                                <input id="reset_verify" type="button" value="重新設定">
							<?php } ?>
						<?php } ?>

                        <table border="0" id="verify_table" class="verify_table"
                               style="display:<?php echo ($this->item->id) ? "none" : "block"; ?>">
                            <tr>
                                <td>
                                    <input type="radio" id="verify_method_0" name="verify_method" value="0"
                                           checked="checked">
                                </td>
                                <td><label for="verify_method_0">圖形驗證碼</label></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    &nbsp;
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="radio" id="verify_method_1" name="verify_method" value="1">
                                </td>
                                <td><label for="verify_method_1" id="label_verify_method_1">依強度選擇驗證方式</label></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <table border="1" id="verify_table_module" class="verify_table_module"
                                           style="display: none;">
										<?php
										$level_label = array ("1" => "驗證強度低", "2" => "驗證強度中", "3" => "驗證強度高");
										foreach ($verify_mix_array as $level => $verify_array) {
											$count = 0;
											foreach ($verify_array as $element => $name) {
												?>
                                                <tr>
													<?php
													if ($count == 0) {
														?>
                                                        <td rowspan="<?php echo count($verify_array); ?>">
															<?php echo $level_label[$level]; ?>
                                                        </td>
													<?php } ?>
                                                    <td>
                                                        <input type="radio" id="verify_mix_<?php echo $element; ?>"
                                                               class="verify_mix" name="verify_mix"
                                                               value="<?php echo $element; ?>">
                                                        <label for="verify_mix_<?php echo $element; ?>"><?php echo $name; ?></label>
                                                        <a href="../filesys/images/system/VerifyExample/<?php echo $element; ?>.png"
                                                           class="show_example fancybox"
                                                           title="<?php echo $name; ?>">(顯示範例)</a>
                                                    </td>
                                                </tr>

												<?php
												$count++;
											}
										}
										?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="verify_method_2" name="verify_method" value="2">
                                </td>
                                <td><label for="verify_method_2">自訂驗證</label></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <table border="0" id="verify_table_custom" class="verify_table_custom"
                                           style="display: none;">
                                        <tr>
                                            <td>
                                                驗證組合方式 <select id="verify_required" name="verify_required">
                                                    <option value="0">擇一</option>
                                                    <option value="1">同時</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table border="0">
                                                    <tr>
                                                        <td>
                                                            <select id="src_select" multiple="multiple" size="8">
																<?php
																foreach ($verify_all_array as $element => $name) {
																	?>
                                                                    <option id="<?php echo $element; ?>"
                                                                            value="<?php echo $element; ?>"><?php echo $name; ?></option>
																<?php } ?>
                                                            </select>
                                                        <td>
                                                        <td>
                                                            <input type="button" id="select_add_btn" value="加入"
                                                                   style="width:50px;"> <br> <br>
                                                            <input type="button" id="select_remove_btn" value="移除"
                                                                   style="width:50px;">

                                                        </td>
                                                        <td>
                                                            <select id="dest_select" name="verify_custom[]"
                                                                    multiple="multiple" size="8"> </select>
                                                        <td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td>
									<?php echo $setting_html; ?>
                                </td>
                            </tr>
                        </table>

                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('verify_precautions'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('verify_precautions'); ?>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane" id="survey-final">
                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('display_result'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('display_result'); ?>
                            </div>
                        </div>

                        <div class="control-group form-inline">
                            <label id="jform_result_num_type-lbl" for="jform_result_num_type" class="control-label"
                                   aria-invalid="false"> 投票結果數設定</label>
                            <div class="controls">
                                <p>
                                    <input type="radio" name="jform[result_num_type]"
                                           value="0" <?php echo ($this->form->getValue('result_num_type') == 0) ? "checked" : ""; ?>> 1個結果
                                    <br>
                                </p>
                                <input type="radio" name="jform[result_num_type]"
                                       value="1" <?php echo ($this->form->getValue('result_num_type') == 1) ? "checked" : ""; ?>>
                                <input type="text" id="jform_result_num" name="jform[result_num]"
                                       value="<?php echo $this->form->getValue('result_num'); ?>" size="5" class="small">個結果
                            </div>
                        </div>


                        <div class="control-group form-inline">
							<?php echo $this->form->getLabel('is_lottery'); ?>
                            <div class="controls">
								<?php echo $this->form->getInput('is_lottery'); ?>
                            </div>
                        </div>

                    </div>


                    <div class="tab-pane" id="survey-rules">
                        <div class="control-group">
                            <div class="controls">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="divForm" style="display:none">

                <div class="tab-pane" id="survey-rules">
                    <div class="control-group">
                        <div class="controls">
                            <label for="fail_reason">不通過原因：</label><textarea id="fail_reason"></textarea> <br>
                            <input class="pass_fail" type="button" onclick="Joomla.submitbutton('survey.pass_fail')" value="送出" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:none">

            <input type="hidden" id="original_name" name="original_name" />
            <input type="hidden" id="file_name" name="file_name" />
            <input type="hidden" id="task" name="task" value="" />
            <input type="hidden" name="is_old_verify" id="is_old_verify"
                   value="<?php echo $this->form->getValue('id'); ?>" />
            <input type="hidden" name="is_old_verify_type" id="is_old_verify_type"
                   value='<?php echo $this->form->getValue('verify_type'); ?>' />
			<?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="jform[created_by]" value="<?php echo $this->form->getValue('created_by'); ?>" />
            <input type="hidden" name="jform[fail_reason]" id="jform_fail_reason" value="" />

			<?php echo $this->form->getInput('asset_id'); ?>
			<?php echo $this->form->getInput('checked_by'); ?>
			<?php echo JHtml::_('form.token'); ?>
        </div>
    </fieldset>
</form>

