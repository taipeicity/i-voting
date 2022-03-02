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

$script = '';
$stage = $this->stage;
if (! $this->canDo->get('core.own')) {
    for ($i = 0; $i < 6; $i++) {
        if ($this->item->is_checked == 1 && $this->item->is_complete == 1) {
            // 審核通過之後可選擇下一階段
            if ($i != $stage - 1 and $i != $stage) {
                $script .= 'jQuery("#jform_stage'.(int) $i.'").addClass("disabled").css("pointer-events", "none").attr("disabled", "true");';
                $script .= 'jQuery("#jform_stage'.(int) $i.'").next("label").addClass("disabled").css("pointer-events", "none");';
            }
        } else {
            // 審核通過之前只能選擇當前階段
            if ($i != $stage - 1) {
                $script .= 'jQuery("#jform_stage'.(int) $i.'").addClass("disabled").css("pointer-events", "none").attr("disabled", "true");';
                $script .= 'jQuery("#jform_stage'.(int) $i.'").next("label").addClass("disabled").css("pointer-events", "none");';
            }
        }
    }
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {'.$script.'});
');

?>
    <script type="text/javascript">

        jQuery(document).ready(function () {

            jQuery.fn.checkSettingsJs = function () {

                var publish_up = jQuery("#jform_publish_up");

                // 檢查發佈日期格式
                if (!publish_up.checkDatePattern()) {
                    jQuery("#message_area").showMessage('日期格式不符。', publish_up);
                    return false;
                }

                var check_stage = jQuery("#jform_stage").find(":checked");
                return !(check_stage.val() > 4 && !jQuery("#survey-settings").checkSettings_launchedJs());

            };

            jQuery("#jform_stage").on("click", function () {
                var stage = jQuery(this);
                var current_stage = jQuery("#current_stage");
                var session_stage = jQuery("#session_stage");
                var id = jQuery("#jform_id");

                if (id.val() || jQuery("#canDo").val()) {
                    if (parseInt(current_stage.val()) > 4) {
                        if (parseInt(stage.find("input:checked").val()) === parseInt(session_stage.val())) {
                            return false;
                        }
                    }

                    if (parseInt(session_stage.val()) <= 4) {
                        if (parseInt(stage.find("input:checked").val()) < 5) {
                            return false;
                        }
                    } else {
                        if (parseInt(stage.find("input:checked").val()) > 4) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }

                jQuery("#task").val("survey.add_setting");
                jQuery.fancybox.showLoading();
                jQuery.fancybox.helpers.overlay.open({parent: jQuery("body"), closeClick: false});
                setTimeout(function () {
                    jQuery("#survey-form").submit();
                }, 100);
            });
        });
    </script>

    <div class="control-group form-inline">
        <?php echo $this->form->getLabel('stage'); ?>
        <div class="controls">
            <?php echo $this->form->getInput('stage'); ?>
            <span class="edit_stage">切換階段前請先儲存</span>
        </div>
    </div>

<?php
if ($this->canDo_created_by) {
    echo $this->form->renderField('created_by');
}
?>

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
        <?php echo $this->form->getLabel('is_public'); ?>
        <div class="controls">
            <?php echo $this->form->getInput('is_public'); ?>
        </div>
    </div>

<?php echo $this->form->renderField('un_public_tmpl'); ?>

<?php echo $this->form->renderField('is_define'); ?>

	<div class="control-group form-inline">
        <?php echo $this->form->getLabel('survey_type'); ?>
        <div class="controls">
            <?php echo $this->form->getInput('survey_type'); ?>
			(議題類型用於api介接，若選擇問卷調查、活動票選，議題將設定為不公開)
        </div>
    </div>
	
<?php
if ($this->canDo->get('core.own')) {
    if ($session->get('stage', $stage) > 4) {
        echo $this->loadTemplate('settings_launched');
    }
} else {
    if ((int) $this->item->stage > 4 or $session->get('stage') > 4) {
        echo $this->loadTemplate('settings_launched');
    }
}
?>