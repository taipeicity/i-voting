<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
$star   = '<span class=\"star\">&nbsp;*</span>';
$script = '';
$script .= 'var new_proposal_download_area = jQuery("#new_proposal_download_area");';
$script .= 'var proposal_url = jQuery("#jform_proposal_url");';
$script .= 'var proposal = jQuery("#jform_proposal");';
$script .= 'var second_the_motion = jQuery("#jform_second_the_motion");';
$script .= 'var deadline = jQuery("#jform_deadline");';

if ($this->item->id) {
	if ($this->form->getValue("proposal_download")) {
		$element = SurveyforceHelper::getOldArea("proposal_download", $this->form->getValue('proposal_download'), false);
		$script  .= SurveyforceHelper::hiddenNewArea("new_proposal_download_area", $element);
		$script  .= 'new_proposal_download_area.find("label").append("' . $star . '");';
	} else {
		$script .= 'new_proposal_download_area.find("label").append("' . $star . '");';
		$script .= 'new_proposal_download_area.next().find("div").find("label").append("' . $star . '");';
		$script .= 'new_proposal_download_area.next().find("div").find("label").addClass("required");';
		$script .= 'proposal_url.addClass("required");';

		if ($this->item->is_api == 1) {

			$script .= 'jQuery.fn.inputDisable = function (){
				jQuery(this).addClass("disabled").css("pointer-events", "none").attr("disabled", "true");
			};';

			$script .= 'proposal_url.inputDisable();';
			$script .= 'proposal.inputDisable();';
			$script .= 'second_the_motion.inputDisable();';
			$script .= 'deadline.inputDisable();';
			$script .= 'deadline.next().inputDisable();';
		}
	}
} else {
	$script .= 'jQuery("#jform_proposal_download").addClass("required").parent().prev().find("label").append("' . $star . '");';
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var proposal_download = jQuery("#jform_proposal_download"),
            proposal_url = jQuery("#jform_proposal_url"),
            old_proposal_download = jQuery("#old_proposal_download");

        jQuery("#jform_proposal").on("click", function () {
            var proposal = jQuery(this);
            jQuery(".new_proposal").removeClass("required");
            if (parseInt(proposal.find(":checked").val()) === 1) {
                if (!old_proposal_download.val()) {
                    proposal_download.addClass("required");
                }
            } else {
                var label = jQuery("label[for=" + proposal_url.attr("id") + "]");
                if (label.find("span").length < 1) {
                    label.html(label.html() + "<span class=\"star\">&nbsp;*</span>");
                }
                proposal_url.addClass("required");
            }
        });


        jQuery("#del_proposal_download_btn").on("click", function () {
            old_proposal_download.val("");
            jQuery(".old_proposal_download_area").toggle();
            jQuery(".new_proposal_download").toggle().addClass("required").attr("required", true).attr("aria-required", true);
        });

        jQuery.fn.checkDetails_checkJs = function () {

            var deadline = jQuery("#jform_deadline"),
                second_the_motion = jQuery("#jform_second_the_motion");

            if (parseInt(jQuery("#jform_proposal").find(":checked").val()) === 1) {
                proposal_url.val("");
            } else {
                proposal_download.val("");
                old_proposal_download.val("");
                // 檢查提案連結
                if (!proposal_url.val().match(/^https?:\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@\-\/]))?/)) {
                    jQuery("#message_area").showMessage('提案連結必須為網址。', second_the_motion);
                    return false;
                }
            }

            // 檢查已附議票數
            if (second_the_motion.val()) {
                if (!second_the_motion.val().match(/\d+/g)) {
                    jQuery("#message_area").showMessage('已附議票數必須為數字。', second_the_motion);
                    return false;
                }
            }

            // 檢查截止時間
            if (!deadline.checkDatePattern() && deadline.val()) {
                jQuery("#message_area").showMessage('日期格式不符。', deadline);
                return false;
            }

            return true;
        };
    });
</script>


<div class="control-group">
	<?php $this->form->setFieldAttribute('proposer', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('proposer'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('proposer'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('plan_quest', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('plan_quest'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('plan_quest'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('plan_options', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('plan_options'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('plan_options'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('proposal', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('proposal'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('proposal'); ?>
    </div>
</div>

<div id="new_proposal_download_area">
	<?php echo $this->form->renderField('proposal_download'); ?>
</div>

<?php echo $this->form->renderField('proposal_url'); ?>

<div class="control-group ">
	<?php echo $this->form->getLabel('precautions'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('precautions'); ?>
    </div>
</div>

<div class="control-group">
	<?php echo $this->form->getLabel('second_the_motion'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('second_the_motion'); ?>
    </div>
</div>

<div class="control-group">
	<?php echo $this->form->getLabel('deadline'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('deadline'); ?>
    </div>
</div>