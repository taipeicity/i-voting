<?php
/**
 *   @package         Surveyforce
 *   @version           1.1-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>
<table class="table table-striped" id="qfld_tbl" cellpadding="10">
	<tr>
		<th width="20px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP'); ?></th>
		<th class="title" width="200px"><?php echo JText::_('COM_JOOMLAQUIZ_QUESTION_OPTIONS2'); ?></th>
		<th class="title" width="200px"></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEUP'); ?></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEDOWN'); ?></th>
		<th width="auto" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_POINTS2'); ?></th>
	</tr>
	<?php
	$k = 0;
	$ii = 1;
	$ind_last = count($matching);
	foreach ($matching as $frow) {
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $ii ?></td>
			<td align="left">
				<?php echo htmlspecialchars(stripslashes($frow->c_left_text)) ?>
				<input type="hidden" name="jq_hid_fields_left[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_left_text)) ?>" />
				<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id ?>" />
			</td>
			<td align="left">
				<?php echo htmlspecialchars(stripslashes($frow->c_right_text)) ?>
				<input type="hidden" name="jq_hid_fields_right[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_right_text)) ?>" />
			</td>
			<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"></a></td>
			<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"></a><?php } ?></td>
			<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"></a><?php } ?></td>
			<td><input class="text_area" size="5" type="text" name="jq_hid_fields_points[]" value="<?php echo $frow->a_points ?>"  /></td>
		</tr>
		<?php
		$k = 1 - $k;
		$ii ++;
	}
	?>
</table><br>
<table class="adminlist" style="width:100%">
	<tr>
		<th class="title" style="text-align:left;"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTIONS'); ?></th>
	</tr>
	<tr>
		<td>
			<div class="form-inline">
				<input id="new_field_left" class="text_area" type="text" name="new_field_left"  />&nbsp;&nbsp;
				<input id="new_field_right" class="text_area" type="text" name="new_field_right"  />&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo JText::_('COM_JOOMLAQUIZ_POINTS2'); ?>:&nbsp;<input id="new_field_points" class="text_area" size="5" type="text" name="new_field_points"  />&nbsp;&nbsp;
				<input class="modal-button btn" type="button" name="add_new_field" style="width:70px; margin-top:10px" value="Add" onclick="javascript:Add_new_tbl_field('new_field_left', 'new_field_right', 'new_field_points', 'qfld_tbl', 'jq_hid_fields_left[]', 'jq_hid_fields_right[]', 'jq_hid_fields_points[]');" />
			</div>
		</td>
	</tr>
</table>