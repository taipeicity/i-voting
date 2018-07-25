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
if ($this->form->getValue('launched_download')) {
	$element = SurveyforceHelper::getOldArea("launched_download", $this->form->getValue('launched_download'), false);
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_launched_download_area\")", $element);
	$script  .= 'jQuery("#jform_launched_download").parent().prev().find("label").append("' . $star . '");';
} else {
	$script  .= 'jQuery("#jform_launched_download").addClass("required").parent().prev().find("label").append("' . $star . '");';
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var announcement_date = jQuery("#jform_announcement_date");

        jQuery("#jform_launched_date1").on("click", function () {
            announcement_date.attr("required", true).attr("aria-required", true).addClass("required");
            var label = jQuery("label[for=" + announcement_date.attr("id") + "]");
            if(label.find("span").length < 1){
                label.html(label.html() + "<span class=\"star\">&nbsp;*</span>");
            }
        });

        jQuery("#jform_launched_date0, #jform_launched_date2").on("click", function () {
            announcement_date.removeAttr("required").removeAttr("aria-required").removeClass("required");
        });

        jQuery("#del_launched_download_btn").on("click", function () {
            jQuery(this).deleteImage("launched_download");
            jQuery("#jform_launched_download").addClass("required");
        });

        jQuery.fn.checkDetails_launchedJs = function () {

            var vote_start = jQuery("#jform_vote_start");

            // 檢查開始時間
            if (!vote_start.checkDatePattern()) {
                jQuery("#message_area").showMessage('日期格式不符。', vote_start);
                return false;
            }

            var vote_end = jQuery("#jform_vote_end");

            // 檢查結束時間
            if (!vote_end.checkDatePattern()) {
                jQuery("#message_area").showMessage('日期格式不符。', vote_end);
                return false;
            }

            //　檢查開始時間是否小於結束時間
            if (Date.parse(vote_start.val()).valueOf() > Date.parse(vote_end.val()).valueOf()) {
                jQuery("#message_area").showMessage('結束時間必須大於開始時間。', vote_end);
                return false;
            }

            var launched_date = jQuery('#jform_launched_date');
            var announcement_date = jQuery('#jform_announcement_date');

            if (parseInt(launched_date.find(":checked").val()) === 2) {
                if (!announcement_date.checkDatePattern()) {
                    jQuery("#message_area").showMessage('日期格式不符。', announcement_date);
                    return false;
                }

                if (Date.parse(announcement_date.val()).valueOf() < Date.parse(vote_end.val()).valueOf()) {
                    jQuery("#message_area").showMessage('公布日期不可早於投票結束時間。', announcement_date);
                    return false;
                }

            }
            return true;
        };

    });

    function addRequired() {
        var part = jQuery("#jform_part");
        var other = jQuery("#jform_other");
        var results_proportions = jQuery("#jform_results_proportions");

        part.val("").removeAttr("required").removeAttr("aria-required").removeClass("required");
        other.val("").removeAttr("required").removeAttr("aria-required").removeClass("required");

        if (results_proportions.val() === "part" || results_proportions.val() === "other") {
            var label = jQuery("label[for='jform_" + results_proportions.val() + "']"),
                html = label.html();
            label.addClass("required");
            if(label.find("span").length < 1){
                label.html(html + "<span class=\"star\">&nbsp;*</span>");
            }
            jQuery("#jform_" + results_proportions.val()).attr("required", true).attr("aria-required", true).addClass("required");
        }
    }
</script>

<div class="control-group">
	<?php $this->form->setFieldAttribute('voters_eligibility', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('voters_eligibility'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('voters_eligibility'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('vote_start', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('vote_start'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('vote_start'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('vote_end', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('vote_end'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('vote_end'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('vote_way', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('vote_way'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('vote_way'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('launched_condition', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('launched_condition'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('launched_condition'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('launched_date', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('launched_date'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('launched_date'); ?>
    </div>
</div>

<?php
if ($this->form->getValue('announcement_date') != "0000-00-00 00:00:00") {
	$this->form->setFieldAttribute('announcement_date', 'required', 'true');
}
echo $this->form->renderField('announcement_date');
?>

<div class="control-group">
	<?php $this->form->setFieldAttribute('results_proportion', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('results_proportion'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('results_proportion'); ?>
    </div>
</div>

<?php
if ($this->form->getValue('part')) {
	$this->form->setFieldAttribute('part', 'required', 'true');
}
echo $this->form->renderField('part');

if ($this->form->getValue('other')) {
	$this->form->setFieldAttribute('other', 'required', 'true');
}
echo $this->form->renderField('other');
?>

<div id="new_launched_download_area">
	<?php echo $this->form->renderField('launched_download'); ?>
</div>

