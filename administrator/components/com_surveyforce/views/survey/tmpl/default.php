<?php
/**
 * @package            Surveyforce
 * @version            2.0-modified
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

$session = JFactory::getSession();
$mark    = $session->get('mark', 0);

$li = [
	"survey-details" => 0, "survey-settings" => 1, "survey-verify" => 2, "survey-final" => 3
];

$script = '';
$script .= 'jQuery("#configTabs").find("a").eq(' . $mark . ').tab("show");';

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>

<?php // echo $this->loadTemplate('menu');  ?>

<script type="text/javascript">


    /**
     * 全域類別，可選用橋接無障礙套件 handicapfree。注意，請勿放在 document ready 內。
     * 其中 unlock() 與 lock() 一旦定義，
     * 那麼將會提供給外掛套件 handicapfree 的 listen.js ，用作 callback。
     */
    window.BridgeHandicapfree = new function (){

        var _status = null;

        // 解鎖
        this.unlock = function (){
            jQuery().btndisabled();    
            _status = "unlock";
        }

        // 上鎖
        this.lock = function (){
            _status = "lock";
        }

        // 取得無障礙目前上鎖或解鎖狀態
        this.getLockStatus = function (){

            // 運行編輯器是否在 code 標籤，如果在的話會觸發 lock()
            $.vmodel.get("editor").enforce();
            
            return _status;
        }

    }


    Joomla.submitbutton = function (task) {

        var message_area = jQuery("#message_area"),
            btn = jQuery(".btn"),
            stage = jQuery("#jform_stage"),
            stage_name = jQuery.trim(stage.find(":checked").next("label").text()),
            fail_reason = jQuery("#fail_reason"),
            check = true,
            hfreeStatus = BridgeHandicapfree.getLockStatus();

        if (hfreeStatus === "lock") {
            jQuery().btndisabled()
        } else {
            btn.prop('disabled', true);
        }

        // 點擊非儲存按鈕，都會停止監聽無障礙
        if (jQuery.inArray(task, ['survey.apply', 'survey.save']) < 0) {
            $.vmodel.get("listen").store.isStop = true;
        }


        if (task == 'survey.delete') {
            if (confirm("請確認是否要刪除該議題?")) {
                // Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                btn.btndisabled();
                return false;
            }

            // Joomla.submitform(task, document.getElementById('survey-form'));
        }

        if (task == 'survey.send_check') {
            if (confirm("請確認「" + stage_name + "」所有欄位已儲存完成，是否確定要送出審核?")) {
                var send_check = true;
                jQuery.ajax({
                    url: "index.php?option=com_surveyforce&task=survey.checkStore",
                    type: "POST",
                    async: false,
                    data: {stage: stage.find(":checked").val(), id: jQuery("#jform_id").val()},
                    beforeSend: function () {
                        jQuery.fancybox.showLoading();
                    },
                    complete: function () {
                        jQuery.fancybox.hideLoading();
                    },
                    success: function (result) {
                        if (result === "true") {
                            // Joomla.submitform(task, document.getElementById('survey-form'));
                        } else {
                            jQuery("#message_area").showMessage("「" + stage_name + "」欄位尚未儲存，請儲存後再送審", jQuery('#jform_edit_stage'));
                            send_check = false;
                        }
                    },
                    error: function () {
                        jQuery("#message_area").showMessage("無法檢查欄位。");
                        return false;
                    }
                });
                if(!send_check){
                    return false;
                }
            } else {
                btn.btndisabled();
                return false;
            }
        }

        if (task == 'survey.pass_success') {
            if (confirm("請確認要將該階段「" + stage_name + "」審核為已通過?")) {
                var pass_success = true;
                jQuery.ajax({
                    url: "index.php?option=com_surveyforce&task=survey.checkStore",
                    type: "POST",
                    async: false,
                    data: {stage: stage.find(":checked").val(), id: jQuery("#jform_id").val()},
                    beforeSend: function () {
                        jQuery.fancybox.showLoading();
                    },
                    complete: function () {
                        jQuery.fancybox.hideLoading();
                    },
                    success: function (result) {
                        if (result === "true") {
                            // Joomla.submitform(task, document.getElementById('survey-form'));
                        } else {
                            jQuery("#message_area").showMessage("「" + stage_name + "」欄位尚未儲存，請儲存後再送審", jQuery('#jform_edit_stage'));
                            pass_success = false;
                        }
                    },
                    error: function () {
                        jQuery("#message_area").showMessage("無法檢查欄位。");
                        return false;
                    }
                });
                if(!pass_success){
                    return false;
                }
            } else {
                btn.btndisabled();
                return false;
            }
        }

        if (task == 'survey.pass_fail') {
            if (fail_reason.val()) {
                if (confirm("請確認要將該階段「" + stage_name + "」審核為不通過?")) {
                    jQuery("#jform_fail_reason").val(fail_reason.val());
                    // Joomla.submitform(task, document.getElementById('survey-form'));
                } else {
                    btn.btndisabled();
                    return false;
                }
            } else {
                alert('請填寫不通過的原因。');
                btn.btndisabled();
                return false;
            }

            // Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.recheck') {
            if (confirm("請確認要將重新審核此議題?")) {
                // Joomla.submitform(task, document.getElementById('survey-form'));
            } else {
                btn.btndisabled();
                return false;
            }

            // Joomla.submitform(task, document.getElementById('survey-form'));
        }


        if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('survey-form'))) {
            jQuery('#dest_select option').attr('selected', 'selected');
			<?php echo $this->form->getField('desc')->save(); ?>

            // 送出前的檢查
            if (task == 'survey.apply' || task == 'survey.save') {
                if (parseInt(jQuery("#edit_stage").val()) !== parseInt(stage.find(":checked").val())) {
                    jQuery.ajax({
                        url: "index.php?option=com_surveyforce&task=survey.checkStore",
                        type: "POST",
                        async: false,
                        cache: false,
                        data: {stage: stage.find(":checked").val(), id: jQuery("#jform_id").val()},
                        beforeSend: function () {
                            jQuery.fancybox.showLoading();
                        },
                        complete: function () {
                            jQuery.fancybox.hideLoading();
                        },
                        success: function (result) {
                            if (result === false) {
                                jQuery("#message_area").showMessage("「" + stage_name + "」欄位尚未填寫，請填寫後再儲存", jQuery('#jform_edit_stage'));
                                return false;
                            }
                        },
                        error: function () {
                            jQuery("#message_area").showMessage("無法檢查欄位。");
                            return false;
                        }
                    });
                }

                if (!jQuery("#survey-details").checkDetailsJs()) {
                    return false;
                }

                if (!jQuery("#survey-settings").checkSettingsJs()) {
                    return false;
                }

                if (!jQuery("#survey-verify").checkVerifyJs()) {
                    return false;
                }

            }

            Joomla.submitform(task, document.getElementById('survey-form'));

        } else {
            message_area.showMessage('請填寫必填欄位。');
            return false;
        }
    };


    



    jQuery(document).ready(function () {
        var invalid = jQuery(".invalid"),
            configTabs = jQuery("#configTabs"),
            message_area = jQuery("#message_area"),
            btn = jQuery(".btn");

        /**
         * @param msg 文字訊息
         * @param target 要渲染css的目標
         */
        jQuery.fn.showMessage = function (msg, target = null) {
            jQuery('html, body').scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(message_area.html());

            if (target) {
                target.addClass("invalid");
            }

            var old_active = '',
                invalid = jQuery(".invalid");
                btn = jQuery(".btn");

            btn.btndisabled();

            if (invalid.parents("li.active").length === 0) {
                if (target) {
                    old_active = configTabs.find("li.active").find("a").attr("href");
                    jQuery(old_active).removeClass("active");
                    target.parents(".tab-pane").last().addClass("active");

                    configTabs.find("li.active").removeClass("active");
                    jQuery("a[href='#" + target.parents(".tab-pane").last().attr("id") + "']").parent("li").addClass("active");
                } else {
                    old_active = configTabs.find("li.active").find("a").attr("href");
                    jQuery(old_active).removeClass("active");
                    invalid.parents(".tab-pane").last().addClass("active");

                    configTabs.find("li.active").removeClass("active");
                    jQuery("a[href='#" + invalid.parents(".tab-pane").last().attr("id") + "']").parent("li").addClass("active");
                }
            }

        };

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        };

        jQuery.fn.btndisabled = function(){
            jQuery(".btn").prop("disabled", false);
        };

        //檢查日期格式
        var pattern;
        jQuery.fn.checkDatePattern = function (short = false) {
            if (short) {
                pattern = /\d{4}-(0?[1-9]|1[0-2]{1})-([0-2]?[0-9]|3[0-1])$/g;
            } else {
                pattern = /\d{4}-(0?[1-9]|1[0-2]{1})-([0-2]?[0-9]|3[0-1])\s([0-1]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/g;
            }
            return this.attr('value').match(pattern);
        };

        jQuery.fn.deleteImage = function (field) {
            jQuery("#old_" + field).val("");
            jQuery(".old_" + field + "_area").toggle();
			
			if (jQuery(".new_" + field).length) {
				jQuery(".new_" + field).toggle();
			} else {
				jQuery("#jform_" + field).toggle();
			}
            
            return true;
        };

        //檢查上傳圖片
        jQuery("input:file").change(function () {
            if (this.files[0] && (this.accept === "image/*" || this.accept === "application/pdf")) {
                var limit = 0,
                    allow_type = [],
                    title,
                    type,
                    file = this,
                    img;
                switch (file.accept) {
                    case "image/*":
                        limit = 2097152;
                        allow_type = ["image/jpeg", "image/pjpeg", "image/png", "image/gif"];
                        if (file.id = "jform_image") {
                            title = "議題圖示";
                        } else {
                            title = "掃瞄標的物圖示";
                        }
                        type = "jpg/png/gif";

                        var reader = new FileReader();
                        reader.readAsDataURL(file.files[0]);
                        reader.onload = function (e) {
                            img = new Image();
                            img.src = e.target.result;
                            img.onload = function () {
                                if (this.width < 792 || this.height > 820) {
                                    message_area.showMessage(title + "寬度不可大於820或小於792", jQuery(file).parent());
                                    return false;
                                }
                                if (this.height < 445 || this.height > 480) {
                                    message_area.showMessage(title + "高度不可大於480或小於445", jQuery(file).parent());
                                    return false;
                                }
                            };
                        };
                        break;
                    case "application/pdf":
                        limit = 5242880;
                        allow_type = ["application/pdf"];
                        title = "其他參考資料";
                        type = "pdf";
                        break;
                    default:
                }

                if (file.files[0].size > limit) {  //假如檔案大小超過指定大小)
                    message_area.showMessage(title + " - 附件檔超過指定大小(" + limit / 1048576 + "MB)。", jQuery(file).parent());
                    return false;
                }

                if (allow_type.indexOf(file.files[0].type) === -1) {  //假如檔案類型不相符
                    message_area.showMessage(title + " - 只允許上傳的檔案類型為：" + type, jQuery(file).parent());
                    return false;
                }

                message_area.hideMessage();
                jQuery(file).parent().removeClass("invalid");

            } else {

                message_area.hideMessage();
                jQuery(file).parent().removeClass("invalid");

            }

        });

        jQuery("#survey-verify input:radio, #survey-verify input:checkbox, #survey-verify input:file, input:checkbox[name=\'jform[vote_pattern][]\']").click(function () {
            invalid.removeClass("invalid");
        });

        jQuery(".get_pdf").on("click", function () {
            jQuery("#file_name").val(this.id);
            jQuery("#original_name").val(this.title);
            jQuery("#task").val("survey.getPdf");
            jQuery("#survey-form").submit();
        });

        // 審查
        jQuery("#btnForm").fancybox({
            'height': 200
        });

        configTabs.find("li").on("click", function () {
            jQuery.ajax({
                url: "index.php?option=com_surveyforce&task=survey.mark",
                type: "POST",
                data: {mark: jQuery(this).find("a").attr("href").substring(1)},
                success: function () {
                    return true
                },
                error: function () {
                    return false;
                }
            });
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
        width: 50px;
    }

    input.medium {
        width: 80px;
    }

    textarea.large {
        width: 90%;
    }

    .iframe {
        border: 0 !important;
    }
	
	#is_verify_idnum_zone {
		display: none;
	}
	
	#is_whitelist_zone {
		display: none;
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


<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=survey&layout=edit&id=' . (int) $this->item->id); ?>"
      enctype="multipart/form-data" method="post" name="survey-form" id="survey-form" class="form-validate">
    <input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
    <fieldset>
        <legend><?php echo (empty($this->item->id)) ? JText::_('COM_SURVEYFORCE_NEW_SURVEY') : JText::_('COM_SURVEYFORCE_EDIT_SURVEY'); ?></legend>

        <div class="row-fluid">
            <div id="j-main-container" class="span8 form-horizontal">
                <ul class="nav nav-tabs" id="configTabs">
                    <li><a href="#survey-details" data-toggle="tab">議題說明</a></li>
                    <li><a href="#survey-settings"
                           data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_SETTINGS'); ?></a></li>
					<?php
					$check = false;
					if ($this->canDo->get('core.own')) {
						if ($session->get('stage', $this->stage) > 4) {
							$check = true;
						}
					} else {
						if ((int) $this->item->stage > 4 or $session->get('stage') > 4) {
							$check = true;
						}
					}
					?>
					<?php if ($check) { ?>
                        <li><a href="#survey-verify"
                               data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_VERIFY'); ?></a></li>
                        <li>
                            <a href="#survey-final" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_FINAL_PAGE2'); ?></a>
                        </li>
					<?php } ?>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane" id="survey-details">
						<?php
						echo $this->loadTemplate('details');
						?>
                    </div>

                    <div class="tab-pane" id="survey-settings">
						<?php
						echo $this->loadTemplate('settings');
						?>
                    </div>

                    <div class="tab-pane" id="survey-verify">
						<?php
						echo $this->loadTemplate('verify');
						?>
                    </div>

                    <div class="tab-pane" id="survey-final">
						<?php
						echo $this->loadTemplate('final');
						?>
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
            <!--現在的階段-->
            <input type="hidden" id="store_stage" name="store_stage" value="<?php echo $this->item->is_store; ?>" />
            <input type="hidden" id="edit_stage" name="edit_stage" value="<?php echo $this->edit_stage; ?>" />
            <input type="hidden" id="current_stage" name="current_stage" value="<?php echo $this->item->stage ? $this->item->stage : 1; ?>" />
            <input type="hidden" id="session_stage" name="session_stage" value="<?php echo $this->form->getValue('stage') ? $this->form->getValue('stage') : 1; ?>" />
            <input type="hidden" id="canDo" name="canDo" value="<?php echo $this->canDo->get('core.own') ? 1 : 0; ?>" />
            <input type="hidden" id="task" name="task" value="" />
            <input type="hidden" name="is_old_verify" id="is_old_verify"
                   value="<?php echo $this->form->getValue('id'); ?>" />
            <input type="hidden" name="is_old_verify_type" id="is_old_verify_type"
                   value='<?php echo $this->form->getValue('verify_type'); ?>' />
			<?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="jform[fail_reason]" id="jform_fail_reason" value="" />

			<?php echo $this->form->getInput('asset_id'); ?>
			<?php echo $this->form->getInput('checked_by'); ?>
			<?php echo JHtml::_('form.token'); ?>
        </div>
    </fieldset>
</form>

