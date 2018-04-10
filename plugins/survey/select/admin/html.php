<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<small>選項清單</small>
<table class="table table-striped" id="text_table">
	<thead>
		<tr>
			<th width="20" align="center">#</th>
			<th width="200">選項名稱</th>
			<th width="40" align="center" class="title">編輯</th>
			<th width="40" align="center" class="title">刪除</th>
			<th width="40" align="center" class="title" colspan="2">排序</th>

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
                <input type="hidden" id="old_ftext" />
                <p>尚餘<span id="ftext_char"></span>個字元</p>
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
<small>下拉選單內容設定</small>
<table class="table table-striped">
	<thead>
		<tr>
			<th width="20" align="center">#</th>
			<th width="200">下拉選單名稱</th>
			<th width="40" align="center">編輯</th>
			<th width="40" align="center">刪除</th>
			<th width="40" align="center" colspan="2">排序</th>
			<th width="auto"></th>
		</tr>
	</thead>
	<tbody id="sub_table_list">
		<?php
		$k = 0;
		$ii = 1;
		$ind_last = count($sub_rows);


		foreach ($sub_rows as $row) {
			?>
			<tr>
				<td align="center">
					<?php echo $ii ?>
					<input type="hidden" name="sub_option_order[]" value="<?php echo $ii; ?>"/>
				</td>
				<td align="left">
					<?php echo $row->title; ?>
					<input type="hidden" class="sub_option_title" name="sub_option_title[]" value="<?php echo $row->title; ?>"/>
					<input type="hidden" class="sub_option_id" name="sub_option_id[]" value="<?php echo $row->id; ?>"/>
				</td>

				<td align="center">
					<a href="javascript: void(0);" class="edit_sub_row" title="編輯">
						<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>">
					</a>
				</td>

				<td align="center">
					<a href="javascript: void(0);" class="del_sub_row" title="刪除">
						<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除">
					</a>
				</td>

				<td align="center">
					<?php if ($ii > 1) { ?>
						<a href="javascript: void(0);" class="up_sub_row" title="向上移動">
							<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動">
						</a>
					<?php } ?>
				</td>
				<td align="center">
					<?php if (($ii < $ind_last)) { ?>
						<a id="down_<?php echo $ii; ?>" href="javascript: void(0);" class="down_sub_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>
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
<input type="hidden" id="del_sub_option_ids" name="del_sub_option_ids" value=""/>

<hr>

<div id="new_sub_table" style="text-align:left;  ">
	<div class="title">新增下拉選單內容</div>
	<table border="1" class="edit-tbl" >
		<tr>
			<td>選項名稱</td>
			<td>
				<input id="new_sub_title" style="width:200px " type="text" name="new_sub_title" value="">
                <input type="hidden" id="sub_old_ftext" />
                <p>尚餘<span id="sub_title_char"></span>個字元</p>
            </td>
		</tr>

		<tr>
			<td colspan="2" align="center">
				<input class="btn" type="button" id="add_sub_btn" style="width:70px " value="新增">
				<input class="btn" type="button" id="edit_sub_btn" style="width:70px " value="儲存">
				<input class="btn" type="button" id="cancel_sub_btn" style="width:70px " value="取消">
			</td>
		</tr>
	</table>
	<input type="hidden" id="edit_sub_option_id" value="">
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

	#edit_btn, #cancel_btn, #old_file_area,
	#edit_sub_btn, #cancel_sub_btn {
		display: none;
	}
</style>