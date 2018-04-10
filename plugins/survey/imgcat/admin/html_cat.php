<?php
/**
 *   @package         Surveyforce
 *   @version           1.0-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>
<small>分類清單</small>
<table class="table table-striped" id="text_table">
	<thead>
		<tr>
			<th width="20px" align="center">#</th>
			<th width="200px">分類名稱</th>
			<th width="20px" align="center" class="title">編輯</th>
			<th width="20px" align="center" class="title">刪除</th>
			<th width="40px" align="center" class="title" colspan="2">排序</th>

			<th width="auto"></th>
		</tr>
	</thead>
	<tbody id="table_cat_list">
		<?php
		$k = 0;
		$ii = 1;
		$ind_last = count($cats);

		foreach ($cats as $cat) {
			?>

			<tr>
				<td align="center">
					<?php echo $ii ?>
					<input type="hidden" name="cat_order[]" value="<?php echo $ii; ?>"/>
				</td>
				<td align="left">
					<?php echo $cat->title; ?>
					<input type="hidden" class="cat_text" name="cat_text[]" value="<?php echo $cat->title; ?>"/>
					<input type="hidden" class="cat_id" name="cat_id[]" value="<?php echo $cat->id; ?>"/>
				</td>


				<td align="center">
					<a href="javascript: void(0);" class="edit_cat" title="編輯">
						<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>">
					</a>
				</td>

				<td align="center">
					<a href="javascript: void(0);" class="del_cat" title="刪除">
						<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除">
					</a>
				</td>

				<td align="center">
					<?php if ($ii > 1) { ?>
						<a href="javascript: void(0);" class="up_cat" title="向上移動">
							<img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動">
						</a>
					<?php } ?>
				</td>
				<td align="center">
					<?php if (($ii < $ind_last)) { ?>
						<a id="down_<?php echo $ii; ?>" href="javascript: void(0);" class="down_cat" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>
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
<input type="hidden" id="del_cat_ids" name="del_cat_ids" value=""/>


<hr>


<div id="new_cat_table" style="text-align:left;  ">
	<div class="title">新增分類</div>
	<table border="1" class="edit-tbl" >
		<tr>
			<td>分類名稱*</td>
			<td>
				<input id="new_ctext" style="width:200px " type="text" name="new_ctext" value="">
                <input type="hidden" id="cat_old_ftext" />
                <p>尚餘<span id="ctext_char"></span>個字元</p>
            </td>
		</tr>

		<tr>
			<td colspan="2" align="center">
				<input class="btn" type="button" id="add_cat_btn" style="width:70px " value="新增">
				<input class="btn" type="button" id="edit_cat_btn" style="width:70px " value="儲存">
				<input class="btn" type="button" id="cancel_cat_btn" style="width:70px " value="取消">
			</td>
		</tr>
	</table>
	<input type="hidden" id="edit_cat_id" value="">
	**編修清單後請先存檔。
</div>

<br/>
<br />

<style>
	.edit-tbl {
		border:1px solid #ccc;
	}

	.edit-tbl td {
		padding: 10px;
	}

	#edit_cat_btn, #cancel_cat_btn, #option_cat_note {
		display: none;
	}
</style>