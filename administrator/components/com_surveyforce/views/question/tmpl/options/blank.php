<?php
/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>
<?php
$jq_num = 99999999;
for ($z = 0; $z < count($blank_data); $z++) {
	$database = JFactory::getDBO();
	$query = $query = "SELECT b.* FROM #__quiz_t_text as b WHERE b.c_blank_id = " . $blank_data[$z]->c_id . " ORDER BY b.ordering";
	$database->SetQuery($query);
	$ans_row = $database->loadObjectList();
	?>
	<table cellpadding="10" cellspacing="10" style="margin-top:15px;">
		<tr>
			<td><strong><?php echo JText::_('COM_JOOMLAQUIZ_BLANK'); ?><strong/><span id="blnk_num_<?php echo $z ?>"><?php echo $z + 1 ?></span><?php echo JText::_('COM_JOOMLAQUIZ_CODE_FORQUESTION'); ?> {blank<?php echo $z + 1 ?>}</td>
			<td>
				<a href="javascript: void(0);" onclick="javascript:Delete_blnk_row(this); return false;" title="Delete"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete" /></a>
			</td>
		</tr>
	</table>

	<span>&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_POINTS'); ?>&nbsp;</span><input type="text" id="new_points_<?php echo $z; ?>" name="jq_hid_points_<?php echo $z; ?>" value="<?php echo $blank_data[$z]->points; ?>" size="5" class="text_area" /><span>&nbsp;&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_CUSTOMCSS_CLASS'); ?>&nbsp;</span><input type="text" id="new_css_<?php echo $z; ?>" name="jq_hid_css_<?php echo $z; ?>" value="<?php echo $blank_data[$z]->css_class; ?>" size="20" class="text_area" />&nbsp;&nbsp;<label style="display:inline;"><input onchange="javascript: jq_changeCheckbox2(this, <?php echo $jq_num; ?>);" type="checkbox" value="1" <?php echo $blank_data[$z]->gtype ? ' checked="checked" ' : '' ?> name="jq_hid_gtype_chk_<?php echo $z ?>[]" />&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_POINTS_FOR_EACHANSWER'); ?></label><input  type="hidden"  name="jq_hid_gtype_<?php echo $z ?>[]"; id="jq_hid_gtype_<?php echo $jq_num; ?>" value="<?php echo $blank_data[$z]->gtype ? '1' : '0' ?>"  /><?php $jq_num++; ?>
	<br/><br/>
	<table class="table table-striped" id="qfld_tbl_<?php echo $z; ?>" cellpadding="10" cellspacing="10">
		<tr>
			<th width="20px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP'); ?></th>
			<th class="title" width="auto" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_ACCEPTABLE'); ?></th>
			<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_DELETE'); ?></th>
			<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEUP'); ?></th>
			<th width="20px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_MOVEDOWN'); ?></th>
			<th width="200px" align="center" class="title"><?php echo JText::_('COM_JOOMLAQUIZ_ISREGEX'); ?></th>
		</tr>
		<?php
		$k = 0;
		$ii = 1;
		$ind_last = count($ans_row);
		foreach ($ans_row as $frow) {
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $ii ?></td>
				<td align="left">
					<?php echo ($frow->c_text) ?>
					<textarea name="jq_hid_fields_<?php echo $z ?>[]" style="display:none;"><?php echo (($frow->c_text)) ?></textarea>
					<input type="hidden" name="jq_hid_fields_ids_<?php echo $z ?>[]" value="<?php echo $frow->c_id ?>" />									
				</td>
				<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete" /></a></td>
				<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up" /></a><?php } ?></td>
				<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root() ?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down" /></a><?php } ?></td>
				<td><label><input onchange="javascript: jq_changeCheckbox(this, <?php echo $jq_num; ?>);" type="checkbox" value="1" <?php echo $frow->regexp ? ' checked="checked" ' : '' ?> name="jq_hid_regexp_chk_<?php echo $z ?>[]" />&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_REGULAR_EXPRESSION'); ?></label>
					<input  type="hidden"  name="jq_hid_regexp_<?php echo $z ?>[]"; id="jq_hid_regexp_<?php echo $jq_num; ?>" value="<?php echo $frow->regexp ? '1' : '0' ?>"  />
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$ii ++;
			$jq_num++;
		}
		?>
	</table><table class="adminlist"><tr><td>
				<div>
					<input id="new_field_<?php echo $z; ?>" class="text_area" style="width:205px; margin-left:50px" type="text" name="new_field" />
					&nbsp;&nbsp;							
					<label style="display:inline;"><input type="checkbox" value="1" name="new_regexp_<?php echo $z ?>" id="new_regexp_<?php echo $z ?>" />&nbsp;<?php echo JText::_('COM_JOOMLAQUIZ_REGULAR_EXPRESSION'); ?></label>&nbsp;&nbsp;&nbsp;
					<input class="modal-button btn" type="button" name="add_new_field" style="width:70px " value="Add" onclick="javascript:Add_new_tbl_field('new_field_<?php echo $z; ?>', 'new_regexp_<?php echo $z; ?>', 'qfld_tbl_<?php echo $z; ?>', 'jq_hid_fields_<?php echo $z; ?>[]', '<?php echo $z; ?>');" />
					<input type="hidden" name="blnk_arr[]" value="<?php echo $z ?>" />
					<input type="hidden" name="blnk_arr_id[]" value="<?php echo $blank_data[$z]->c_id ?>" />
				</div>
			</td></tr></table>
	<hr/>
<?php }
?>
<table id="id_before">
</table>
<div><input type="button" value="Add new" onclick="javascript:new_blnk_tbl();" class="modal-button btn"/></div>
<fieldset class="adminform" style="margin-top:15px;">
	<legend><?php echo JText::_('COM_JOOMLAQUIZ_DISTRACTORS'); ?></legend>
	<table class="adminlist" id="qfld_tbl_fake" cellpadding="7">
		<tr>
			<th width="20px" align="center"><?php echo JText::_('COM_JOOMLAQUIZ_SHARP'); ?></th>
			<th class="title" width="200px"><?php echo JText::_('COM_JOOMLAQUIZ_DISTRACTOR'); ?></th>
			<th width="20px" align="center" class="title"></th>
			<th width="auto" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		$ii = 1;
		$ind_last = count($fake_data);
		if (count($fake_data)) {
			foreach ($fake_data as $frow) {
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center"><?php echo $ii ?></td>
					<td align="left">
						<?php echo htmlspecialchars(stripslashes($frow->c_text)) ?>
						<input type="hidden" name="jq_hid_fake[]" value="<?php echo htmlspecialchars(stripslashes($frow->c_text)) ?>" />
					</td>							
					<td><a href="javascript: void(0);" onclick="javascript:Delete_tbl_row2(this); return false;" title="Delete"><img src="<?php echo JURI::root(); ?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a></td>
					<td>&nbsp;</td>
				</tr>
				<?php
				$k = 1 - $k;
				$ii ++;
			}
		}
		?>
	</table><br>
	<table class="adminlist"  style="width:100%">
		<tr>
			<th class="title" style="text-align:left;"><?php echo JText::_('COM_JOOMLAQUIZ_ADDNEW_DISTRACTOR'); ?></th>
		</tr>
		<tr>
			<td>
				<div>
					<input id="new_field_fake" class="text_area" style="width:205px " type="text" name="new_field_fake"  />&nbsp;&nbsp;							
					<input  class="modal-button btn" type="button" name="add_new_field" value="Add" onclick="javascript:Add_new_tbl_field2();"/>
				</div>
			</td>
		</tr>
	</table>					
</fieldset>