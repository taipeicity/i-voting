<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var options_agree = jQuery("#jform_options_agree"),
            options_oppose = jQuery("#jform_options_oppose");

        jQuery.fn.checkDetails_optionsJs = function () {

            // 檢查贊成欄位
            if (!options_agree.val().match(/\d+/g)) {
                jQuery("#message_area").showMessage('贊成必須為數字。', options_agree);
                return false;
            }

            // 檢查反對欄位
            if (!options_oppose.val().match(/\d+/g)) {
                jQuery("#message_area").showMessage('反對必須為數字。', options_oppose);
                return false;
            }

            return true;
        };
    });
</script>


<div class="control-group">
	<?php $this->form->setFieldAttribute('options_cohesion', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('options_cohesion'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('options_cohesion'); ?>
    </div>
</div>

<div class="control-group">
	<?php echo $this->form->getLabel('options_scale'); ?>
    <div class="controls options_scale">
		<?php echo JText::_('COM_SURVEYFORCE_OPTIONS_AGREE'); ?>
		<?php echo $this->form->getInput('options_agree'); ?>
    </div>
    <div class="controls options_scale">
		<?php echo JText::_('COM_SURVEYFORCE_OPTIONS_OPPOSE'); ?>
		<?php echo $this->form->getInput('options_oppose'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('options_caption', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('options_caption'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('options_caption'); ?>
    </div>
</div>
