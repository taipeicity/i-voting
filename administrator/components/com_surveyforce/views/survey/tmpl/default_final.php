<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
$script = '';
if($this->form->getValue('result_num_type') == 1) {
	$script .= 'jQuery("#jform_result_num_type").find("input.small").addClass("required");';
}else{
	$script .= 'jQuery("#jform_result_num_type").find("input.small").val("")';
}
$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#jform_result_num_type").on("click", function(){
           var result_num_type = jQuery(this);
            if(parseInt(result_num_type.find(":checked").val()) === 0){
                result_num_type.find("input.small").removeClass("required").val("");
            }else{
                result_num_type.find("input.small").addClass("required");
            }
        });
    });
</script>

<div class="control-group form-inline">
	<?php echo $this->form->getLabel('display_result'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('display_result'); ?>
    </div>
</div>

<div class="control-group form-inline">
    <label id="jform_result_num_type-lbl" for="jform_result_num_type" class="control-label"
           aria-invalid="false"> 投票結果數設定</label>
    <div class="controls">
        <fieldset id="jform_result_num_type" class="checkboxes required result_num_type">
            <ul>
                <li>
                    <label>
                        <input type="radio" name="jform[result_num_type]" value="0" <?php echo ($this->form->getValue('result_num_type') == 0) ? "checked" : ""; ?>> 1個結果
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="jform[result_num_type]" value="1" <?php echo ($this->form->getValue('result_num_type') == 1) ? "checked" : ""; ?>>
                        <input type="number" id="jform_result_num" name="jform[result_num]" value="<?php echo $this->form->getValue('result_num'); ?>" size="5" class="small">個結果
                    </label>
                </li>
            </ul>
        </fieldset>
    </div>
</div>


<div class="control-group form-inline">
	<?php echo $this->form->getLabel('is_lottery'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('is_lottery'); ?>
    </div>
</div>

