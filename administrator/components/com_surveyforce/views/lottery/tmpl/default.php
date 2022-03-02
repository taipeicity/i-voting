<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

?>
<?php // echo $this->loadTemplate('menu'); ?>

<script>
    
    
    Joomla.submitbutton = function(task)
    {
        if (task == 'lottery.cancel' || document.formvalidator.isValid(document.id('lottery-form'))) {
            Joomla.submitform(task, document.getElementById('lottery-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }



	jQuery(document).ready(function() {


	});


	function check_export() {
		jQuery("#lottery-form").prop("action", "<?php echo JRoute::_('index.php?option=com_surveyforce&view=lottery&layout=exportdata&surv_id=' . $this->surv_id, false); ?>");
		jQuery("#lottery-form").submit();
	}
    
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=lottery'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="lottery-form" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">
		<fieldset>
			<legend>檔案下載</legend>
			
			<div style="margin-bottom:20px;">
				抽獎名單檔案：
				<input type="button" id="submit_export" class="btn" value="匯出CSV檔案" onclick="check_export()" />

			</div>
			
			<br>

			<br/><br/>
			
		</fieldset>
        
    </div>
	
	<input type="hidden" name="task" value = "" />
    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
    <input type="hidden" name="option" value="com_surveyforce" />
    
    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

