<?php
/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>

<table class="table table-striped" id="qfld_tbl" cellpadding="10">
	<tr>
		<th width="20px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP'); ?></th>
		<th width="60px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_CHECK_CORRECT_CHOICE'); ?></th>
		<th class="title" width="200px"><?php echo JText::_('COM_JOOMLAQUIZ_OPTION_TEXT'); ?></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEUP'); ?></th>
		<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEDOWN'); ?></th>
		<th width="200px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_POINTS2'); ?></th>
		<th width="auto" align="center"><?php if ($q_om_type == 1) echo JText::_('COM_JOOMLAQUIZ_FEEDBACK_OPTION'); ?></th>
	</tr>

	<?php
	$k = 0;
	$ii = 1;
	$ind_last = count($return['choices']);
	foreach ($return['choices'] as $frow) {
		if ($wysiwyg) {
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center" valign="top"><?php echo $ii ?></td>
				<td valign="top" align="<?php echo ($q_om_type != 10 ? 'center' : 'left') ?>">
					<?php
					if ($q_om_type != 10) {
						?>
						<input <?php echo ($frow->c_right ? 'checked' : ''); ?> type="checkbox" name="jq_checked[]" value="<?php echo $ii ?>" onClick="jq_UnselectCheckbox2(event);" />
						<?php
					} else {
						$random = rand(100000, 1000000);
						?>
						<span>
							<label><input name="jq_radio_<?php echo $random; ?>"<?php echo (!$frow->c_right ? ' checked ' : ''); ?>type="radio" value="0" onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 0);" /> <?php echo JText::_('COM_JOOMLAQUIZ_FALSE2'); ?></label><br />
							<label><input name="jq_radio_<?php echo $random; ?>"<?php echo ($frow->c_right ? ' checked ' : ''); ?>type="radio" value="1" onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 1);" /> <?php echo JText::_('COM_JOOMLAQUIZ_TRUE2'); ?></label>
							<input name="jq_checked[]" type="hidden" value="<?php echo $frow->c_right; ?>" id="jq_checked_<?php echo $random; ?>" />
						</span>
			<?php
		}
		?>
				</td>
				<td align="left" valign="top">
					<div style="float:right;padding-right:15px;"><a href="index.php?option=com_joomlaquiz&task=question.edit_field&id=<?php echo $frow->c_id; ?>&tmpl=component&KeepThis=true&TB_iframe=true&height=370&width=700" title="<?php echo JText::_('COM_JOOMLAQUIZ_EDIT_OPTION'); ?>" onclick="javascript: tb_start(this);return false;"  class="thickbox"><?php echo JText::_('COM_JOOMLAQUIZ_EDIT'); ?></a></div>								
					<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id ?>" />	
					<div id="test_<?php echo $frow->c_id; ?>"><?php echo stripslashes($frow->c_choice) ?></div>
					<textarea name="jq_hid_fields[]" style="display:none" id="ta_<?php echo $frow->c_id; ?>"><?php echo htmlspecialchars(stripslashes($frow->c_choice)) ?></textarea>

				</td>
				<td valign="top" align="center"><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"></a></td>
				<td valign="top"><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"></a><?php } ?></td>
				<td valign="top"><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"></a><?php } ?></td>
				<td align="left" valign="top"><input type="text" name="jq_a_points[]" value="<?php echo $frow->a_point; ?>" maxlength="10" /></td>
				<td valign="top"><?php if ($q_om_type == 1) { ?><textarea cols="50" rows="5" name="jq_incorrect_feed[]"><?php echo htmlspecialchars(stripslashes($frow->c_incorrect_feed)) ?></textarea><?php } ?></td>
			</tr>
				<?php } else { ?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center" valign="top" ><?php echo $ii ?></td>
				<td valign="top" align="<?php echo ($q_om_type != 10 ? 'center' : 'left') ?>">
					<?php
					if ($q_om_type != 10) {
						?>
						<input <?php echo ($frow->c_right ? 'checked' : ''); ?> type="checkbox" name="jq_checked[]" value="<?php echo $ii ?>" onClick="jq_UnselectCheckbox2(event);" />
						<?php
					} else {
						$random = rand(100000, 1000000);
						?>
						<span>
							<label><input name="jq_radio_<?php echo $random; ?>"<?php echo (!$frow->c_right ? ' checked ' : ''); ?>type="radio" value="0" onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 0);" /><?php echo JText::_('COM_JOOMLAQUIZ_FALSE2'); ?></label><br/>
							<label><input name="jq_radio_<?php echo $random; ?>"<?php echo ($frow->c_right ? ' checked ' : ''); ?>type="radio" value="1" onClick="jq_SetHidden('jq_checked_<?php echo $random; ?>', 1);" /><?php echo JText::_('COM_JOOMLAQUIZ_TRUE2'); ?></label>
							<input name="jq_checked[]" type="hidden" value="<?php echo $frow->c_right; ?>" id="jq_checked_<?php echo $random; ?>" />
						</span>
			<?php
		}
		?>
				</td>
				<td valign="top" align="left">
					<input type="text" name="jq_hid_fields[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_choice)) ?>" />
					<input type="hidden" name="jq_hid_fields_ids[]" value="<?php echo $frow->c_id ?>" />
				</td>
				<td align="center" valign="top"><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?>"></a></td>
				<td valign="top"><?php if ($ii > 1) { ?><a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_UP'); ?>"></a><?php } ?></td>
				<td valign="top"><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_JOOMLAQUIZ_MOVE_DOWN'); ?>"></a><?php } ?></td>
				<td align="left" valign="top"><input type="text" name="jq_a_points[]" value="<?php echo $frow->a_point; ?>" maxlength="10" /></td>
				<td><?php if ($q_om_type == 1) { ?><textarea cols="50" rows="5" name="jq_incorrect_feed[]"><?php echo htmlspecialchars(stripslashes($frow->c_incorrect_feed)) ?></textarea><?php } ?></td>
			</tr>
	<?php
	}
	$k = 1 - $k;
	$ii ++;
}
?>
</table>
<hr>
			<?php if ($wysiwyg) { ?>
	<table class="adminlist" style="margin-top:15px;">
		<tr>
			<th width="20px" align="left" colspan="3"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTION'); ?></th>
		</tr>
		<tr>
			<td  align="left"  valign="top" width="280">
			<?php echo JText::_('COM_JOOMLAQUIZ_OPTION_TEXT'); ?> (<small><?php echo JText::_('COM_JOOMLAQUIZ_CLICK_EDIT'); ?></small>):							
				<div style="float:right;"><a href="index.php?option=com_joomlaquiz&task=question.edit_field&id=0&tmpl=component&KeepThis=true&TB_iframe=true&height=370&width=700" title="<?php echo JText::_('COM_JOOMLAQUIZ_EDIT_OPTION'); ?>" onclick="javascript: tb_start(this);return false;" class="thickbox"><?php echo JText::_('COM_JOOMLAQUIZ_EDIT'); ?></a></div>
				<br />
				<div id="test_0"></div>
				<textarea id="ta_0"  name="new_field" style="display:none" id="ta_0"></textarea><br />
			</td>
	<?php if ($q_om_type === 1) { ?>
				<td valign="top" rowspan="2" width="30%" style="padding-left:15px;">
					<!---incorect answer message for each choice-->						
		<?php echo JText::_('COM_JOOMLAQUIZ_FEEDBACK_MESSAGE'); ?><br />
					<div>
						<textarea rows="5" id="wr_mess" cols="50" name="c_wrong_message_var" ></textarea>
					</div>						
				</td>
	<?php } ?>
			<td  align="left" width="auto" valign="top" rowspan="2">
				<br />
				<div>
					<input class="modal-button btn" type="button" name="add_new_field" style="width:70px;margin-left:10px;" value="Add" onclick="javascript:Add_new_tbl_field('test_0', 'qfld_tbl', 'jq_hid_fields[]');" />
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
	<?php echo JText::_('COM_JOOMLAQUIZ_OPTION_POINTS'); ?><br />
				<div>
					<input id="new_field_points" class="text_area" type="text" name="new_field_points" maxlength="10" />
				</div>						
			</td>						
		</tr>
	</table>
			<?php } else { ?>
	<table class="adminlist"  style="margin-top:15px;">
		<tr>
			<th width="20px" align="left" colspan="3"><?php echo JText::_('COM_JOOMLAQUIZ_ADD_NEW_OPTION'); ?></th>
		</tr>
		<tr>
			<td  align="left"  valign="top" width="280">
			<?php echo JText::_('COM_JOOMLAQUIZ_OPTION_TEXT2'); ?><br />
				<div>
					<input id="new_field" class="text_area" style="width:210px " type="text" name="new_field" />
				</div>
			</td>		

	<?php if ($q_om_type === 1) { ?>
				<td align="left" valign="top" rowspan="2" width="30%">						
					<!---incorect answer message for each choice-->						
		<?php echo JText::_('COM_JOOMLAQUIZ_FEEDBACK_MESSAGE'); ?><br />
					<div>
						<textarea rows="5" id="wr_mess" cols="50" name="c_wrong_message_var" ></textarea>
					</div>						
				</td>
	<?php } ?>
			<td align="left" rowspan="2" valign="top" width="auto">
				<br />
				<div>
					<input class="modal-button btn" type="button" name="add_new_field" style="width:70px;margin-left:10px;" value="Add" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'jq_hid_fields[]');" />
				</div>
			</td>
		</tr>
		<tr>
			<td  align="left" valign="top">
	<?php echo JText::_('COM_JOOMLAQUIZ_OPTION_POINTS'); ?><br />
				<div>
					<input id="new_field_points" class="text_area"  type="text" name="new_field_points" maxlength="10" />
				</div>
			</td>					
		</tr>
	</table>
<?php } ?>