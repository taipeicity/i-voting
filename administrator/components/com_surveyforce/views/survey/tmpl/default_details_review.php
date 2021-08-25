<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

$star   = "<span class='star'>&nbsp;*</span>";
$script = '';

for ($i = 1; $i <= 5; $i++) {
	if ($this->form->getValue('proposalplan_download_'. $i)) {
		$element = SurveyforceHelper::getOldArea("proposalplan_download_". $i, $this->form->getValue('proposalplan_download_'. $i), false);
		$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_proposalplan_download_". $i. "_area\")", $element);
		$script  .= ($i == 1) ? 'jQuery("#jform_proposalplan_download_'. $i. '").parent().prev().find("label").append("' . $star . '");' : '';
	} else {
		$script .= ($i == 1) ? 'jQuery("#jform_proposalplan_download_'. $i. '").addClass("required").parent().prev().find("label").append("' . $star . '");' : '';
	}
}

for ($i = 1; $i <= 5; $i++) {
	if ($this->form->getValue('review_download_'. $i)) {
		$element = SurveyforceHelper::getOldArea("review_download_". $i, $this->form->getValue('review_download_'. $i), false);
		$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_review_download_". $i. "_area\")", $element);
		$script  .= ($i == 1) ? 'jQuery("#jform_review_download_'. $i. '").parent().prev().find("label").append("' . $star . '");' : '';
	} else {
		$script .= ($i == 1) ? 'jQuery("#jform_review_download_'. $i. '").addClass("required").parent().prev().find("label").append("' . $star . '");' : '';
	}
}


$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
		

		<?php for ($i = 1; $i <= 5; $i++) { ?>
        jQuery("#del_proposalplan_download_<?php echo $i; ?>_btn").on("click", function () {
            jQuery(this).deleteImage("proposalplan_download_<?php echo $i; ?>");
			<?php if ($i == 1) { ?>
			jQuery("#jform_proposalplan_download_1").addClass("required");
			<?php } ?>
        });
		<?php } ?>
		
		<?php for ($i = 1; $i <= 5; $i++) { ?>
        jQuery("#del_review_download_<?php echo $i; ?>_btn").on("click", function () {
            jQuery(this).deleteImage("review_download_<?php echo $i; ?>");
			<?php if ($i == 1) { ?>
			jQuery("#jform_review_download_1").addClass("required");
			<?php } ?>
        });
		<?php } ?>

    });
</script>

<div class="control-group">
	<?php $this->form->setFieldAttribute('review_result', 'required', 'true'); ?>
	<?php echo $this->form->getLabel("review_result"); ?>
    <div class="controls">
		<?php echo $this->form->getInput("review_result"); ?>
    </div>
</div>

<?php for ($i = 1; $i <= 5; $i++) { ?>
<div id="new_proposalplan_download_<?php echo $i; ?>_area">
	<?php echo $this->form->renderField('proposalplan_download_'. $i); ?>
</div>
<?php } ?>

<?php for ($i = 1; $i <= 5; $i++) { ?>
<div id="new_review_download_<?php echo $i; ?>_area">
	<?php echo $this->form->renderField('review_download_'. $i); ?>
</div>
<?php } ?>



