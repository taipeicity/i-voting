<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$script = '';
if ($this->form->getValue("image")) {
	$element = SurveyforceHelper::getOldArea("image", $this->form->getValue('image'));
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_image_area\")", $element);
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery("#jform_edit_stage").on("change", function () {
            jQuery("#task").val("survey.change_stage");
            jQuery.fancybox.showLoading();
            jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
            setTimeout(function () {
                jQuery("#survey-form").submit();
            }, 100);
        });

        jQuery("#del_image_btn").on("click", function () {
            jQuery(this).deleteImage("image");
        });

        jQuery.fn.checkDetailsJs = function () {

            var edit_stage = jQuery("#edit_stage"),
                details = jQuery("#survey-details"),
                message_area = jQuery("#message_area"),
                title = jQuery("#jform_title"),
                note = jQuery("#jform_note");

            String.prototype.len = function () {
                return this.replace(/[\ufee0-\uffdf]/g, "rr").length;
            };

            if (title.val().len() > 30) {
                jQuery("#message_area").showMessage('議題名稱的文字過多，請刪除部分文字。' + title.val().len(), title);
                return false;
            }

            if (!jQuery("#jform_desc").val()) {
                message_area.showMessage('請填寫必填欄位。', jQuery("#jform_desc-lbl"));
                return false;
            }

            if (note.val().len() > 26) {
                jQuery("#message_area").showMessage('備註的文字過多，請刪除部分文字。', note);
                return false;
            }

            switch (parseInt(edit_stage.val())) {
                case 1:
                    return details.checkDetails_checkJs();
                case 2:
                    return true;
                case 3:
                    return details.checkDetails_discussJs();
                case 4:
                    return details.checkDetails_optionsJs();
                case 5:
                    return details.checkDetails_launchedJs();
                case 6:
                    return true;
                default:
                    message_area.showMessage('請填寫必填欄位。');
                    return false;
            }

        };
    });
</script>

<style>
    .input-title {
        width: 28em;
    }
</style>


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

<div id="new_image_area">
	<?php echo $this->form->renderField('image'); ?>
</div>

<?php echo $this->form->renderField('note'); ?>

<hr>

<div class="control-group form-inline">
	<?php echo $this->form->getLabel('edit_stage'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('edit_stage'); ?>
        <span class="edit_stage">切換階段前請先儲存</span>
    </div>
</div>

<span id="locate"></span>
<?php

switch ($this->edit_stage) {
	case 1:
		/**
		 * check 提案檢核討論
		 */
		echo $this->loadTemplate('details_check');
		break;
	case 2:
		/**
		 * review 提案初審討論
		 */
		echo $this->loadTemplate('details_review');
		break;
	case 3:
		/**
		 * discuss 提案討論階段
		 */
		echo $this->loadTemplate('details_discuss');
		break;
	case 4:
		/**
		 * options 形成選項階段
		 */
		echo $this->loadTemplate('details_options');
		break;
	case 5:
		/**
		 * launched 宣傳準備與上架階段
		 */
		echo $this->loadTemplate('details_launched');
		break;
	case 6:
		/**
		 * result 投票、結果公布及執行
		 */
		echo $this->loadTemplate('details_result');
		break;

}


?>
