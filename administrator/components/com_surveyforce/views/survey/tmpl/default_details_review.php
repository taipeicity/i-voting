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

if ($this->form->getValue("review_download")) {
	$element = SurveyforceHelper::getOldArea("review_download", $this->form->getValue('review_download'), false);
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_review_download_area\")", $element);

	$script .= 'jQuery("#jform_review_download").parent().prev().find("label").append("' . $star . '");';
} else {
	$script .= 'jQuery("#jform_review_download").addClass("required").parent().prev().find("label").append("' . $star . '");';
}

if ($this->form->getValue("review_download_ii")) {
	$element = SurveyforceHelper::getOldArea("review_download_ii", $this->form->getValue('review_download_ii'), false);
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_review_download_ii_area\")", $element);
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#del_review_download_btn").on("click", function () {
            jQuery(this).deleteImage("review_download");
            jQuery("#jform_review_download").addClass("required");
        });

        jQuery("#del_review_download_ii_btn").on("click", function () {
            jQuery(this).deleteImage("review_download_ii");
        });
    });
</script>

<div class="control-group">
	<?php $this->form->setFieldAttribute('review_result', 'required', 'true'); ?>
	<?php echo $this->form->getLabel("review_result"); ?>
    <div class="controls">
		<?php echo $this->form->getInput("review_result"); ?>
    </div>
</div>

<div id="new_review_download_area">
	<?php echo $this->form->renderField('review_download'); ?>
</div>

<div id="new_review_download_ii_area">
	<?php echo $this->form->renderField('review_download_ii'); ?>
</div>
