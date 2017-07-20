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


?>
<?php // echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">

	Joomla.submitbutton = function(task)
	{


		if (task == 'resultnote.cancel' || document.formvalidator.isValid(document.id('survey-form'))) {
			// 送出前的檢查
			<?php echo $this->form->getField('result_desc')->save(); ?>

			if (task == 'resultnote.apply' || task == 'resultnote.save') {
				if (jQuery("#jform_result_desc").val() == "") {
					jQuery("#message_area").showMessage('請填寫結果說明。');
					jQuery("#jform_result_desc").focus();
					return false;
				}
			}

			Joomla.submitform(task, document.getElementById('survey-form'));

		} else {
			jQuery("#message_area").showMessage('請填寫必填欄位。');
			return false;
		}
	}




	jQuery(document).ready(function() {
		jQuery.fn.showMessage = function(msg) {
			jQuery('html, body').scrollTop(0);
			jQuery("#message_area #message_content").html(msg);
			jQuery("#system-message-container").html(jQuery("#message_area").html());
		}

		jQuery.fn.hideMessage = function() {
			jQuery("#system-message-container").html("");
		}

	});
</script>
<style>

</style>

<div id="message_area" style="display: none;">
	<div id="system-message" class="alert alert-error">
		<h4 class="alert-heading"></h4>
		<div>
			<p id="message_content"></p>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=resultnote&id=' . (int) $this->surv_id); ?>" method="post" name="survey-form" id="survey-form" class="form-validate">

<legend>編輯投票結果說明</legend>
<div class="row-fluid">
<div id="j-main-container" class="span7 form-horizontal">

<div class="tab-content">

	<div class="control-group form-inline">
        <?php echo $this->form->getLabel('result_desc'); ?>
        <div class="controls">
			<?php echo $this->form->getInput('result_desc'); ?>
        </div>
    </div>


</div>
</div>


<input type="hidden" name="task" value="" />
<input type="hidden" name="surv_id" value="<?php echo (int) $this->surv_id; ?>" />
<?php echo $this->form->getInput('id'); ?>

<?php echo JHtml::_('form.token'); ?>
</div>
</form>
