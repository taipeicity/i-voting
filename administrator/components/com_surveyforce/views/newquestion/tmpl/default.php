<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$option = 'com_surveyforce';
$app = JFactory::getApplication();
$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
$surv_id = $app->getUserStateFromRequest( "question.sf_survey", 'sf_survey', 0 );

if (class_exists('JToolBar')) {
	$bar = JToolBar::getInstance('toolbar');
	// Add a cancel button
	$bar->appendButton( 'Standard', 'next', JText::_('COM_SURVEYFORCE_NEXT'), 'question.add', false, true );
	$bar->appendButton( 'Standard', 'cancel', JText::_('COM_SURVEYFORCE_CANCEL'), 'cancel', false, false ); 
}



?>
<script language="javascript" type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_surveyforce/assets/js/thickbox/thickbox.js" ></script>
<style type="text/css" >
	label { cursor:pointer;width: auto !important;}
	.btn-toolbar {float:right;}
</style>
<script>
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;
        var elem = document.getElementsByName('new_qtype_id');

        var flag = false;
        for(var i=0;i < elem.length;i++){
            if(elem[i].checked == true){
                flag = true;
            }
        }

        if(!flag && pressbutton != 'cancel'){
            alert('<?php echo $this->escape(JText::_('COM_SURVEYFORCE_CHOOSE_TYPE'));?>');
            return false;
        } else {

            if (pressbutton == 'cancel') {
                parent.tb_remove();
                return;
            }

            form.submit();
        }

    }
</script>
<style>
	#question_type_description{
		margin:20px;
		font-size: 16px;
		padding: 10px;
	}
</style>

<form onsubmit="return false" action="index.php?option=com_surveyforce&view=question&layout=edit&surv_id=<?php echo $surv_id; ?>" method="post" name="adminForm" target="_parent" enctype="multipart/form-data">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_SURVEYFORCE_SELECT_NEW_QUESTION_TYPE');?></legend>
	<?php if (class_exists('JToolBar')) { echo $bar->render(); } ?>
		<table width="100%" cellpadding="2" cellspacing="2" class="admintable">
		<?php
			$i = 0;
			while($i < count($this->question_type)) {
				$manifest_cache = json_decode($this->question_type[$i]->manifest_cache);
		?>
			<tr>
				<td width="50%">
					<label for="new_qtype_id_<?php echo $this->question_type[$i]->extension_id; ?>">
						<input type="radio" onclick="isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->question_type[$i]->extension_id; ?>" value="<?php echo $this->question_type[$i]->element; ?>" <?php echo ($new_qtype_id == $this->question_type[$i]->element ? ' checked="checked" ': '')?> />
						<?php echo $manifest_cache->name; ?>
					</label>
				</td>
				<td width="50%">
					<?php
						if(isset($this->question_type[$i+1])) {
							$manifest_cache = json_decode($this->question_type[$i+1]->manifest_cache);
					?>
					<label for="new_qtype_id_<?php echo $this->question_type[$i+1]->extension_id; ?>">
						<input type="radio" onclick="isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->question_type[$i+1]->extension_id; ?>" value="<?php echo $this->question_type[$i+1]->element; ?>" <?php echo ($new_qtype_id == $this->question_type[$i+1]->element ? ' checked="checked" ': '')?> />
						<?php echo $manifest_cache->name; ?>
					<?php } ?>
				</td>
			</tr>
		<?php $i = $i + 2;?>
		<?php } ?>
		</table>
		<div id="question_type_description"></div>
	</fieldset>
			
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_surveyforce" />
	<input type="hidden" name="c_id" value="0" />
	<input type="hidden" name="surv_id" value="<?php echo $surv_id; ?>" />
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
	
</script>