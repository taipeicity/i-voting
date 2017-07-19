<small>選項清單</small>
<table class="table table-striped" id="text_table">
	<thead>
	<tr>
		<th width="20" align="center">#</th>
		<th width="200">選項名稱</th>
		<th width="40" align="center">編輯</th>
		<th width="40" align="center">刪除</th>
		<th width="40" align="center" colspan="2">排序</th>
		<th width="auto"></th>
	</tr>
	</thead>
	<tbody id="table_list">
	<?php
	$k = 0;
	$ii = 1;
	$ind_last = count($rows);
	


	foreach ($rows as $frow) {
		
	?>
		
		<tr>
			<td align="center">
				<?php echo $ii ?>
				<input type="hidden" name="option_order[]" value="<?php echo $ii; ?>"/>
			</td>
			<td align="left">
				<?php echo $frow->ftext; ?>
				<input type="hidden" class="option_ftext" name="option_ftext[]" value="<?php echo $frow->ftext; ?>"/>
				<input type="hidden" class="option_id" name="option_id[]" value="<?php echo $frow->id; ?>"/>
			</td>

			<td align="center">
				<a href="javascript: void(0);" class="edit_row" title="編輯">
					<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>">
				</a>
			</td>

			<td align="center">
				<a href="javascript: void(0);" class="del_row" title="刪除">
					<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除">
				</a>
			</td>

			<td align="center">
				<?php if ($ii > 1) { ?>
					<a href="javascript: void(0);" class="up_row" title="向上移動">
						<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動">
					</a>
				<?php } ?>
			</td>
			<td align="center">
				<?php if (($ii < $ind_last)) { ?>
					<a id="down_<?php echo $ii; ?>" href="javascript: void(0);" class="down_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>
	<?php } ?>
			</td>
			<td></td>
		</tr>
		<?php
		$k = 1 - $k;
		$ii++;
	}
	?>
</tbody>
</table>
<input type="hidden" id="del_option_ids" name="del_option_ids" value=""/>

<hr>

<div id="new_table" style="text-align:left;  ">
	<div class="title">新增選項</div>
	<table border="1" class="edit-tbl" >
		<tr>
			<td>選項名稱</td>
			<td>
				<input id="new_ftext" style="width:200px " type="text" name="new_ftext" value="">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input class="btn" type="button" id="add_btn" style="width:70px " value="新增">
				<input class="btn" type="button" id="edit_btn" style="width:70px " value="儲存">
				<input class="btn" type="button" id="cancel_btn" style="width:70px " value="取消">
			</td>
		</tr>
	</table>
	<input type="hidden" id="edit_option_id" value="">
</div>

<hr>
<small>分數設定</small>
<?php
	if ($sub_rows) {
		echo '<div id="old_scroe" style="text-align:left; line-height: 35px; ">';
		echo "最低分：". $sub_rows[0]->title. "<br>";
		echo "最高分：". $sub_rows[(count($sub_rows) - 1)]->title. "<br>";
		echo '<input type="hidden" id="hidden_min_score" value="'. $sub_rows[0]->title. '">';
		echo '<input type="hidden" id="hidden_max_score" value="'. $sub_rows[(count($sub_rows) - 1)]->title. '">';
		echo '<input class="btn" type="button" id="number_reset_btn" style="width:100px " value="重設分數">';
		echo '</div>';
	}
?>
<div id="new_scroe" style="text-align:left; line-height: 35px; <?php echo ($sub_rows) ? "display:none;" : ""; ?> ">
最低分：
<select id="number_min_score" name="number_min_score" style="width:60px;"></select><br>
最高分：
<select id="number_max_score" name="number_max_score" style="width:60px;"></select>
<input type="hidden" id="is_new_sub_option" name="is_new_sub_option" value="<?php echo ($sub_rows) ? "0" : "1"; ?>">
</div>

<br>
<br/>
<br />

<style>
	.edit-tbl {
		border:1px solid #ccc;
	}

	.edit-tbl td {
		padding: 10px;
	}

	#edit_btn, #cancel_btn, #old_file_area {
		display: none;
	}
</style>