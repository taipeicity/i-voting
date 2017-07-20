<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
//JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
//JHtml::_('formbehavior.chosen', 'select');

jimport('joomla.filesystem.file');
$user = JFactory::getUser();
$config = JFactory::getConfig();
$ivoting_path = $config->get('ivoting_path');
$id = $this->item->id;
//SurveyforceHelper::addFileUploadFull('index.php?option=com_surveyforce&task=images.addImage&id=' . (int) $this->item->id, 'survey-form', 0);
// 載入所有plugin
$verify_all_array = array();
$verify_mix_array = array();
if ($this->verify_types) {
    // 依強度選擇驗證方式用的陣列
    $verify_mix_array[1] = array();
    $verify_mix_array[2] = array();
    $verify_mix_array[3] = array();

    $mix_js = '';
    $custom_js = '';
    $setting_html = '';
    foreach ($this->verify_types as $type) {
        // 整理plugin資料 - 依強度選擇驗證方式
        $params = json_decode($type->params);
        if ($params->level > 0) {
            $verify_mix_array[$params->level][$type->element] = $type->name;
            $verify_all_array[$type->element] = $type->name;
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
<?php // echo $this->loadTemplate('menu');                                                                                                         ?>
<script type="text/javascript">

    Joomla.submitbutton = function (task)
    {

        if (task == 'survey.delete') {
            if (confirm("請確認是否要刪除該議題?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.send_check') {
            if (confirm("請確認所有資料已儲存完成，是否確定要送出審核?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.pass_success') {
            if (confirm("請確認要將該議題審核為已通過?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.pass_fail') {
            if (jQuery("#fail_reason").val()) {
                if (confirm("請確認要將該議題審核為不通過?")) {
                    jQuery("#jofrm_fail_reason").val(jQuery("#fail_reason").val());
                    Joomla.submitform(task, document.getElementById('survey-form'));
                } else {
                    return false;
                }
            } else {
                alert('請填寫不通過的原因。');
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.recheck') {
            if (confirm("請確認要將重新審核此議題?")) {
                Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                return false;
            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('survey-form'))) {
            jQuery('#dest_select option').attr('selected', 'selected');
<?php echo $this->form->getField('desc')->save(); ?>



            // 送出前的檢查
            if (task == 'survey.apply' || task == 'survey.save') {


                if (jQuery("#jform_other_url").val()) {
                    var url = jQuery("#jform_other_url").val();
                    var rule = /^(https?:\/\/+[\w\-]+\.[\w\-]+)/i;
                    if (!rule.test(url)) {
                        jQuery("#message_area").showMessage('請輸入有效網址。');
                        jQuery("#jform_other_url").focus();
                        return false;
                    }
                }


                if (jQuery("#jform_desc").val() == "") {
                    jQuery("#message_area").showMessage('請填寫議題說明。');
                    jQuery("#jform_desc").focus();
                    return false;
                }

                // 檢查發佈日期
                if (Date.parse(jQuery("#jform_publish_down").val()).valueOf() < Date.parse("<?php echo date("Y-m-d H:i:s"); ?>").valueOf()) {
                    jQuery("#message_area").showMessage('議題下架時間必須晚於目前時間。');
                    jQuery("#jform_publish_down").focus();
                    return false;
                }

                if (Date.parse(jQuery("#jform_publish_up").val()).valueOf() > Date.parse(jQuery("#jform_publish_down").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題上架時間必須早於下架時間。');
                    jQuery("#jform_publish_up").focus();
                    return false;
                }

                if (Date.parse(jQuery("#jform_vote_end").val()).valueOf() < Date.parse(jQuery("#jform_vote_start").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題開始投票時間必須早於結束投票時間。');
                    jQuery("#jform_vote_start").focus();
                    return false;
                }

                if (Date.parse(jQuery("#jform_publish_up").val()).valueOf() > Date.parse(jQuery("#jform_vote_start").val()).valueOf() || Date.parse(jQuery("#jform_publish_down").val()).valueOf() < Date.parse(jQuery("#jform_vote_end").val()).valueOf()) {
                    jQuery("#message_area").showMessage('議題投票時間不得超出議題上架期間。');
                    jQuery("#jform_vote_start").focus();
                    return false;
                }


                // 投票數設定 - 驗證每天幾票
                if (jQuery('input:radio:checked[name="vote_num_type"]').val() == 1) {
                    if (isNaN(jQuery("#jform_vote_day").val()) || isNaN(jQuery("#jform_vote_num").val())) {
                        jQuery("#message_area").showMessage('投票數設定的驗證條件需為數字。');
                        jQuery("#jform_vote_day").focus();
                        return false;
                    } else {
                        if (jQuery("#jform_vote_day").val() == 0 || jQuery("#jform_vote_num").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫投票數設定的驗證條件。');
                            jQuery("#jform_vote_day").focus();
                            return false;
                        }
                    }

                }


                // 防止灌票機制
                if (jQuery('input:radio:checked[name="vote_num_protect"]').val() == 1) {
                    if (isNaN(jQuery("#jform_vote_num_protect_time").val())) {
                        jQuery("#message_area").showMessage('防止灌票機制的秒數需為數字。');
                        jQuery("#jform_vote_num_protect_time").focus();
                        return false;
                    } else {
                        if (jQuery("#jform_vote_num_protect_time").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫防止灌票機制的秒數條件。');
                            jQuery("#jform_vote_num_protect_time").focus();
                            return false;
                        }
                    }

                }

                if (jQuery('input:radio:checked[name="vote_num_protect"]').val() == 2) {
                    if (isNaN(jQuery("#jform_vote_num_protect_vote").val())) {
                        jQuery("#message_area").showMessage('防止灌票機制的票數需為數字。');
                        jQuery("#jform_vote_num_protect_time").focus();
                        return false;
                    } else {
                        if (jQuery("#jform_vote_num_protect_vote").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫防止灌票機制的票數條件。');
                            jQuery("#jform_vote_num_protect_time").focus();
                            return false;
                        }
                    }

                }


                // 郵件訊息設定
                if (jQuery('input:radio:checked[name="jform[is_notice_email]"]').val() == 1) {
                    if (jQuery("#jform_remind_text").val() == "" || jQuery("#jform_drumup_text").val() == "" || jQuery("#jform_end_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫電子郵件訊息通知的各項訊息。');
                        jQuery("#jform_remind_text").focus();
                        return false;
                    }
                }


                // 手機訊息設定
                if (jQuery('input:radio:checked[name="jform[is_notice_phone]"]').val() == 1) {
                    if (jQuery("#jform_phone_remind_text").val() == "" || jQuery("#jform_phone_drumup_text").val() == "" || jQuery("#jform_phone_end_text").val() == "") {
                        jQuery("#message_area").showMessage('請填寫手機訊息通知的各項訊息。');
                        jQuery("#jform_phone_remind_text").focus();
                        return false;
                    }

                    if (jQuery("#jform_sms_user").val() == "" || jQuery("#jform_sms_passwd").val() == "") {
                        jQuery("#message_area").showMessage('請填寫簡訊平台的帳號及密碼。');
                        jQuery("#jform_sms_user").focus();
                        return false;
                    }
                }


                // 驗證方式
                if (jQuery("#is_old_verify").val() == 0) {
                    if (jQuery('input:radio:checked[name="verify_method"]').val() == 1) {		// 依強度選擇驗證方式
                        if (jQuery('input:radio:checked[name="verify_mix"]').val() == undefined) {
                            jQuery("#message_area").showMessage('依強度選擇驗證方式 - 請選擇其中一種驗證方式。');
                            return false;
                        }

                        // 載入JS檢查
                        check_verify_method = jQuery('input:radio:checked[name="verify_mix"]').val();
<?php
echo $check_js;
?>

                    } else if (jQuery('input:radio:checked[name="verify_method"]').val() == 2) {	// 自訂驗證

                        if (jQuery('#dest_select').val() == null) {
                            jQuery("#message_area").showMessage('自訂驗證 - 請選擇其中一種驗證方式。');
                            return false;
                        }

                        if (jQuery('#verify_required').val() == 1) {
                            if (jQuery("#dest_select").get(0).options.length < 2) {
                                jQuery("#message_area").showMessage('自訂驗證 - 驗證組合方式為同時，請至少選擇兩種驗證方式。');
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
                        jQuery("#message_area").showMessage('投票結果數設定需為數字。');
                        jQuery("#jform_result_num").focus();
                        return false;
                    } else {
                        if (jQuery("#jform_result_num").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫投票結果數。');
                            jQuery("#jform_result_num").focus();
                            return false;
                        }
                    }

                }

            }

            Joomla.submitform(task, document.getElementById('survey-form'));
        } else {
            jQuery("#message_area").showMessage('請填寫必填欄位。');
            return false;
        }
    }

    /* 2017/1/16 Sam start */
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
    /* 2017/1/16 sam end */

    jQuery(document).ready(function () {

        jQuery.fn.showMessage = function (msg) {
            jQuery('html, body').scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
        }

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        }


        // 驗證的選單回復至預設值
        jQuery.fn.resetVerify = function () {
            jQuery(".verify_setting").hide();
            jQuery(".verify_mix").each(function () {
                jQuery(this).attr('checked', false);
            });
            jQuery("#verify_required").attr("value", "0");
            jQuery("#src_select").html(temp_custom_verify);
            jQuery("#dest_select").html("");
        }


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

        /* 2017/1/19 sam start */
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
        /* 2017/1/19 sam end */

        /* 2017/1/17 Sam start */
<?php
if ($this->form->getValue('other_data')) {
    ?>
            jQuery("#new_pdf_area").hide();
            jQuery("#del_pdf_btn").bind("click", function () {
                jQuery("#old_pdf").val("");
                jQuery("#old_pdf_area").hide();
                jQuery("#new_pdf_area").show();
            });
<?php } ?>

<?php
if ($this->form->getValue('other_data2')) {
    ?>
            jQuery("#new_pdf_area2").hide();
            jQuery("#del_pdf_btn2").bind("click", function () {
                jQuery("#old_pdf2").val("");
                jQuery("#old_pdf_area2").hide();
                jQuery("#new_pdf_area2").show();
            });
<?php } ?>

<?php
if ($this->form->getValue('other_data3')) {
    ?>
            jQuery("#new_pdf_area3").hide();
            jQuery("#del_pdf_btn3").bind("click", function () {
                jQuery("#old_pdf3").val("");
                jQuery("#old_pdf_area3").hide();
                jQuery("#new_pdf_area3").show();
            });
<?php } ?>
        /* 2017/1/17 Sam end*/

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

<?php
if ($this->form->getValue('is_place') == 0) {
    ?>
            jQuery("#is_place_area").hide();
<?php } ?>

        jQuery('#jform_is_place label').bind("click", function () {
            if (jQuery(this).hasClass("active") && jQuery.trim(jQuery(this).html()) == "是") {
                jQuery("#is_place_area").show();
            } else {
                jQuery("#is_place_area").hide();
            }

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
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。');
                return false;
            }

            jQuery("#src_select").find(":selected").each(function () {
                jQuery(new Option(this.text, this.value)).appendTo('#dest_select');
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
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。');
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
//		jQuery('#configTabs a').eq(2).tab('show');


        // 審查
        jQuery("#btnForm").fancybox();
    }
    );</script>
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
        width:90%;
    }
    /* 2017/1/16 Sam start */
    <?php
    if (!$this->form->getValue('part')) {
        ?>
        #part{
            display: none;
        }
    <?php } ?>
    <?php
    if (!$this->form->getValue('other')) {
        ?>
        #other{
            display: none;
        }
    <?php } ?>
    .parther{
        margin-top: 5px;
    }

    /* 2017/1/16 Sam end */
    .url_color{
        color: red;
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

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="survey-form" id="survey-form" class="form-validate">
    <input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
    <legend><?php echo (empty($this->item->id)) ? JText::_('COM_SURVEYFORCE_NEW_SURVEY') : JText::_('COM_SURVEYFORCE_EDIT_SURVEY'); ?></legend>
    <div class="row-fluid">
        <div id="j-main-container" class="span7 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li><a href="#survey-details" data-toggle="tab">議題說明</a></li>
                <li><a href="#survey-settings" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_SETTINGS'); ?></a></li>
                <li><a href="#survey-verify" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_VERIFY'); ?></a></li>
                <li><a href="#survey-final" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_FINAL_PAGE2'); ?></a></li>

<!--<li><a href="#survey-rules" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_RULES'); ?></a></li>-->
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
                                    <a href="../<?php echo $this->form->getValue('image'); ?>" class="fancybox" title="預覽檢視">預覽檢視</a>
                                    <input class="btn" type="button" id="del_image_btn" style="width:70px " value="刪除">
                                    <input type="hidden" id="old_image" name="old_image" value="<?php echo $this->form->getValue('image'); ?>">
                                </div>
                            <?php } ?>

                            <div id="new_image_area">
                                <?php echo $this->form->getInput('image'); ?><br>
                                (請上傳2MB以內的圖片，未選擇時將用預設圖替代。)
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
                            <?php echo $this->form->getInput('discuss_source'); ?><br>
                            (超連結範例：將網址用&lt;a href="<span class="url_color">連結網址</span>"&gt;<sapn class="url_color">連結名稱</sapn>&lt;/a&gt;包起來，<br>如需輸入兩個網址以上時，請分別用&lt;a href="<span class="url_color">連結網址</span>"&gt;<sapn class="url_color">連結名稱</sapn>&lt;/a&gt;包起來，並用<span class="url_color">&nbsp;&#59;&nbsp;</span>隔開)
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
                            if ($this->form->getValue('other_data')) {
                                $str = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->form->getValue('other_data'));
                                ?>
                                <div id="old_pdf_area">
                                    <a href="../<?php echo $this->form->getValue('other_data'); ?>" target="_blank" title="<?php echo $str; ?>"><?php echo $str; ?></a>
                                    <input class="btn" type="button" id="del_pdf_btn" style="width:70px " value="刪除">
                                    <input type="hidden" id="old_pdf" name="old_pdf" value="<?php echo $this->form->getValue('other_data'); ?>">
                                </div>
                            <?php } ?>
                            <div id="new_pdf_area">
                                <?php echo $this->form->getInput('other_data'); ?>
                            </div>
                            <?php
                            if ($this->form->getValue('other_data2')) {
                                $str = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->form->getValue('other_data2'));
                                ?>
                                <div id="old_pdf_area2">
                                    <a href="../<?php echo $this->form->getValue('other_data2'); ?>" target="_blank" title="<?php echo $str; ?>"><?php echo $str; ?></a>
                                    <input class="btn" type="button" id="del_pdf_btn2" style="width:70px " value="刪除">
                                    <input type="hidden" id="old_pdf2" name="old_pdf2" value="<?php echo $this->form->getValue('other_data2'); ?>">
                                </div>
                            <?php } ?>
                            <div id="new_pdf_area2">
                                <?php echo $this->form->getInput('other_data2'); ?>
                            </div>

                            <?php
                            if ($this->form->getValue('other_data3')) {
                                $str = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->form->getValue('other_data3'));
                                ?>
                                <div id="old_pdf_area3">
                                    <a href="../<?php echo $this->form->getValue('other_data3'); ?>" target="_blank" title="<?php echo $str; ?>"><?php echo $str; ?></a>
                                    <input class="btn" type="button" id="del_pdf_btn3" style="width:70px " value="刪除">
                                    <input type="hidden" id="old_pdf3" name="old_pdf3" value="<?php echo $this->form->getValue('other_data3'); ?>">
                                </div>
                            <?php } ?>
                            <div id="new_pdf_area3">
                                <?php echo $this->form->getInput('other_data3'); ?>
                            </div>
                        </div>
                        <div class="controls">(請上傳5MB以內的pdf檔。)</div>
                    </div>

                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('other_url'); ?>
                        <div class="controls">
                            <?php echo $this->form->getInput('other_url'); ?><br>     
                            (網址範例：http://XXXX 或 https://XXXX)
                        </div>
                    </div>

                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('followup_caption'); ?>
                        <div class="controls">
                            <?php echo $this->form->getInput('followup_caption'); ?><br>
                            (超連結範例：將網址用&lt;a href="<span class="url_color">連結網址</span>"&gt;<sapn class="url_color">連結名稱</sapn>&lt;/a&gt;包起來)
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

                    <div class="control-group form-inline">
                        <label id="jform_display_result-lbl" for="jform_display_result" class="control-label" aria-invalid="false">
                            投票數設定</label>
                        <div class="controls">
                            <?php
                            $vote_num_params = json_decode($this->form->getValue('vote_num_params'));
                            ?>
                            <input type="radio" name="vote_num_type" value="0" <?php echo ($vote_num_params->vote_num_type == 0) ? "checked" : ""; ?>> 投票期間僅限一票 <br>
                            <input type="radio" name="vote_num_type" value="1" <?php echo ($vote_num_params->vote_num_type == 1) ? "checked" : ""; ?>> 驗證條件每 <input type="text" id="jform_vote_day" name="vote_num_type_vote_day" value="<?php echo $vote_num_params->vote_day; ?>" size="5" class="small">天 <input type="text" id="jform_vote_num" name="vote_num_type_vote_num" value="<?php echo $vote_num_params->vote_num; ?>" size="5" class="small" > 票
                        </div>
                    </div>

                    <div class="control-group form-inline">
                        <label id="jform_display_result-lbl" for="jform_display_result" class="control-label" aria-invalid="false">
                            防止灌票機制</label>
                        <div class="controls">
                            <input type="radio" name="vote_num_protect" value="0" <?php echo ($vote_num_params->vote_num_protect == 0) ? "checked" : ""; ?>> 同IP 不限制 <br>
                            <input type="radio" name="vote_num_protect" value="1" <?php echo ($vote_num_params->vote_num_protect == 1) ? "checked" : ""; ?>> 同IP 每<input type="text" id="jform_vote_num_protect_time" name="vote_num_protect_time" value="<?php echo $vote_num_params->vote_num_protect_time; ?>" size="5" class="small">秒內只能投1票<br>
                            <input type="radio" name="vote_num_protect" value="2" <?php echo ($vote_num_params->vote_num_protect == 2) ? "checked" : ""; ?>> 同IP 每天只能投<input type="text" id="jform_vote_num_protect_vote" name="vote_num_protect_vote" value="<?php echo $vote_num_params->vote_num_protect_vote; ?>" size="5" class="small">票
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
                                <textarea name="jform[remind_text]" id="jform_remind_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

現在已經開始投票了，請您至以下網址進行投票：【%url%】，投票時間至【%endtime%】止。

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('drumup_text'); ?>
                            <div class="controls">
                                <textarea name="jform[drumup_text]" id="jform_drumup_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您登記【%title%】i-Voting投票通知。

投票即將於【%endtime%】結束，如您還沒投票，請您儘快至以下網址進行投票：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('end_text'); ?>
                            <div class="controls">
                                <textarea name="jform[end_text]" id="jform_end_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
親愛的民眾您好：

感謝您參與【%title%】i-Voting投票。

投票結果已公布於i-Voting網站，歡迎您至以下網址觀看結果：【%url%】

臺北市政府 敬上

◎備註：此信件由系統自動發出，請勿直接回覆。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
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
                                <textarea name="jform[phone_remind_text]" id="jform_phone_remind_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
【%title%】i-Voting已經開始投票了，請立即至i-Voting投票系統進行投票。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('phone_drumup_text'); ?>
                            <div class="controls">
                                <textarea name="jform[phone_drumup_text]" id="jform_phone_drumup_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
【%title%】i-Voting投票即將於【%endtime%】結束，請立即至i-Voting投票系統進行投票。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('phone_end_text'); ?>
                            <div class="controls">
                                <textarea name="jform[phone_end_text]" id="jform_phone_end_text" cols="5" rows="10" class="inputbox large" aria-invalid="false">
【%title%】i-Voting投票結果已公布，請立即至i-Voting投票系統進行查看。
                                </textarea>
                                <br>*代碼說明：%title%為議題名稱、%url%為議題網址、%endtime%為投票結束時間
                            </div>
                        </div>

                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('sms_user'); ?>
                            <div class="controls">
                                <input type="text" name="jform[sms_user]" id="jform_sms_user" value="<?php echo JHtml::_('utility.decode', $this->form->getValue('sms_user')); ?>" class="input-xlarge" size="30">

                            </div>
                        </div>
                        <div class="control-group form-inline">
                            <?php echo $this->form->getLabel('sms_passwd'); ?>
                            <div class="controls">
                                <input type="password" name="jform[sms_passwd]" id="jform_sms_passwd" value="<?php echo JHtml::_('utility.decode', $this->form->getValue('sms_passwd')); ?>" class="input-xlarge" size="30">
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
                                    <div id="old_image_area">
                                        <a href="../<?php echo $this->form->getValue('place_image'); ?>" class="fancybox" title="預覽檢視">預覽檢視</a>
                                        <input class="btn" type="button" id="del_image_btn" style="width:70px " value="刪除">
                                        <input type="hidden" id="old_place_image" name="old_place_image" value="<?php echo $this->form->getValue('place_image'); ?>">
                                    </div>
                                <?php } ?>

                                <div id="new_image_area">
                                    <?php echo $this->form->getInput('place_image'); ?><br>
                                    (請上傳2MB以內的圖片，未選擇時將用預設圖替代。)
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane" id="survey-verify">
                    <?php
                    if ($this->item->id) {
                        $verify_type = json_decode($this->form->getValue('verify_type'), true);
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

                    <table border="0" id="verify_table" class="verify_table" style="display:<?php echo ($this->item->id) ? "none" : "block"; ?>" >
                        <tr>
                            <td>
                                <input type="radio" id="verify_method_0" name="verify_method" value="0" checked="checked" >
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
                            <td><label for="verify_method_1">依強度選擇驗證方式</label></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <table border="1" id="verify_table_module" class="verify_table_module" style="display: none;" >
                                    <?php
                                    $level_label = array("1" => "驗證強度低", "2" => "驗證強度中", "3" => "驗證強度高");
                                    foreach ($verify_mix_array as $level => $verify_array) {
                                        $count = 0;
                                        foreach ($verify_array as $element => $name) {
                                            ?>
                                            <tr>
                                                <?php
                                                if ($count == 0) {
                                                    ?>
                                                    <td rowspan="<?php echo count($verify_array); ?>" >
                                                        <?php echo $level_label[$level]; ?>
                                                    </td>
                                                <?php } ?>
                                                <td>
                                                    <input type="radio" id="verify_mix_<?php echo $element; ?>" class="verify_mix" name="verify_mix" value="<?php echo $element; ?>"> <label for="verify_mix_<?php echo $element; ?>"><?php echo $name; ?></label>
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
                                <table border="0" id="verify_table_custom" class="verify_table_custom" style="display: none;" >
                                    <tr>
                                        <td>
                                            驗證組合方式
                                            <select id="verify_required" name="verify_required">
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
                                                        <select id="src_select" multiple="multiple" size="8" >
                                                            <?php
                                                            foreach ($verify_all_array as $element => $name) {
                                                                ?>
                                                                <option value="<?php echo $element; ?>"><?php echo $name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    <td>
                                                    <td>
                                                        <input type="button" id="select_add_btn" value="加入" style="width:50px;">
                                                        <br>
                                                        <br>
                                                        <input type="button" id="select_remove_btn" value="移除" style="width:50px;">

                                                    </td>
                                                    <td>
                                                        <select id="dest_select" name="verify_custom[]"  multiple="multiple" size="8" >
                                                        </select>
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
                </div>    



                <div class="tab-pane" id="survey-final">
                    <div class="control-group form-inline">
                        <?php echo $this->form->getLabel('display_result'); ?>
                        <div class="controls">
                            <?php echo $this->form->getInput('display_result'); ?>
                        </div>
                    </div>

                    <div class="control-group form-inline">
                        <label id="jform_result_num_type-lbl" for="jform_result_num_type" class="control-label" aria-invalid="false">
                            投票結果數設定</label>
                        <div class="controls">
                            <input type="radio" name="jform[result_num_type]" value="0" <?php echo ($this->form->getValue('result_num_type') == 0) ? "checked" : ""; ?>> 1個結果 <br>
                            <input type="radio" name="jform[result_num_type]" value="1" <?php echo ($this->form->getValue('result_num_type') == 1) ? "checked" : ""; ?>>  <input type="text" id="jform_result_num" name="jform[result_num]" value="<?php echo $this->form->getValue('result_num'); ?>" size="5" class="small">個結果
                        </div>
                    </div>

                    <!--	<div class="control-group form-inline">
                    <?php echo $this->form->getLabel('result_orderby'); ?>
                            <div class="controls">
                    <?php echo $this->form->getInput('result_orderby'); ?>
                            </div>
                        </div>-->



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
                            <?php // echo $this->form->getInput('rules');               ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="divForm" style="display:none" >

            不通過原因:<br>
            <textarea name="fail_reason" id="fail_reason" cols="3" rows="5" class="inputbox"></textarea>
            <br /><br />
            <input type="button" onclick="Joomla.submitbutton('survey.pass_fail')" value="送出審核" />

        </div>


        <input type="hidden" name="task" value="" />
        <input type="hidden" name="is_old_verify" id="is_old_verify" value="<?php echo $this->form->getValue('id'); ?>" />
        <input type="hidden" name="is_old_verify_type" id="is_old_verify_type" value='<?php echo $this->form->getValue('verify_type'); ?>' />
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="jform[created_by]" value="<?php echo $this->form->getValue('created_by'); ?>" />
        <input type="hidden" name="jform[fail_reason]" id="jofrm_fail_reason" value="" />

        <?php echo $this->form->getInput('asset_id'); ?>
        <?php echo $this->form->getInput('checked_by'); ?>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
