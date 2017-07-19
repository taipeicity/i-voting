<script type="text/javascript">

	jQuery(document).ready(function() {
		var temp_tr_index;

		// 新增選項
		jQuery("#add_btn").bind("click", function() {
			
			// 檢查欄位
			if (jQuery("#new_ftext").val() == "") {
				alert("請填寫選項名稱。");
				return false;
			}

		
			content = '<tr>';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value=""/>';
			content += '</td>';
						
			content += '<td><a href="javascript: void(0);" class="edit_row" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
			content += '<td><a href="javascript: void(0);" class="del_row" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += "</tr>";

			jQuery("#table_list").append(content);

			jQuery("#table_list").orderTable();


			jQuery("#cancel_btn").trigger("click");

		});

		// 刪除選項
		jQuery(document).on("click", '.del_row', function(e) {
			// 記錄刪除ID
			option_id = jQuery(this).parent().parent().children("td").children(".option_id").val();
			
			if (option_id) {
				ids = jQuery("#del_option_ids").val();
				if (ids) {
					new_ids += "," + option_id;
				} else {
					new_ids = option_id;
				}
				
				jQuery("#del_option_ids").val( new_ids );
			}

			// 刪除該元素
			jQuery(this).parent().parent().remove();
			
			jQuery("#table_list").orderTable();
			
		});


		// 開始編輯
		jQuery(document).on("click", '.edit_row', function(e) {
			jQuery("#add_btn").hide();
			jQuery("#edit_btn").show();
			jQuery("#cancel_btn").show();
			jQuery("#new_table .title").html("編輯選項");
			jQuery("#new_ftext").focus();

			option_ftext = jQuery(this).parent().parent().children("td").children(".option_ftext").val();
			jQuery("#new_ftext").val(option_ftext);

			option_id = jQuery(this).parent().parent().children("td").children(".option_id").val();
			jQuery("#edit_option_id").val(option_id);


			temp_tr_index = jQuery(this).parent().parent().index("#table_list tr");
		});


		// 儲存編輯
		jQuery("#edit_btn").bind("click", function() {
			if (jQuery("#new_ftext").val() == "") {
				alert("請填寫選項名稱。");
				return false;
			}


			content = '';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value="' + jQuery("#edit_option_id").val() + '"/>';
			content += '</td>';

			content += '<td><a href="javascript: void(0);" class="edit_row" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
			content += '<td><a href="javascript: void(0);" class="del_row" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += '<td></td>';


			jQuery("#table_list tr").eq( temp_tr_index ).html(content);

			jQuery("#table_list").orderTable();

			// 儲存完成後，回復成新增模式
			jQuery("#cancel_btn").trigger("click");

		});


		// 編輯取消
		jQuery("#cancel_btn").bind("click", function() {
			jQuery("#edit_btn").hide();
			jQuery("#cancel_btn").hide();
			jQuery("#add_btn").show();
			jQuery("#new_table .title").html("新增選項");

			jQuery("#new_ftext").val("");

			
			temp_tr_index = 0;
		});




		// 向上移動
		jQuery(document).on("click", '.up_row', function(e) {
			tr_index = jQuery(this).parent().parent().index("#table_list tr");
			temp_html = jQuery("#table_list tr").eq(tr_index).html();
			
			jQuery("#table_list tr").eq(tr_index).html( jQuery("#table_list tr").eq((tr_index - 1)).html() );
			jQuery("#table_list tr").eq((tr_index - 1)).html(temp_html);
			

			jQuery("#table_list").orderTable();

		});

		// 向下移動
		jQuery(document).on("click", '.down_row', function(e) {
			tr_index = jQuery(this).parent().parent().index("#table_list tr");
			temp_html = jQuery("#table_list tr").eq(tr_index).html();

			jQuery("#table_list tr").eq(tr_index).html( jQuery("#table_list tr").eq((tr_index + 1)).html() );
			jQuery("#table_list tr").eq((tr_index + 1)).html(temp_html);


			jQuery("#table_list").orderTable();

		});


		// 重新排序號碼和向上、下向箭頭
		jQuery.fn.orderTable = function() {
			tr_count = jQuery(this).children("tr").length;

            jQuery(this).children("tr").each(function(index) {
				jQuery(this).children("td").eq(0).html((index+1) + '<input type="hidden" name="option_order[]" value="' + (index+1) + '"/>');



				if (tr_count > 1) {
					// 第一列
					if (index == 0) {
						jQuery(this).children("td").eq(4).html("");
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					} else if (tr_count == (index + 1)) {	// 最後一列
						jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(5).html("");
					} else {
						jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					}
				}

			});

        }


		// 子選項
		var temp_sub_tr_index;

		// 新增選項
		jQuery("#add_sub_btn").bind("click", function() {

			// 檢查欄位
			if (jQuery("#new_sub_title").val() == "") {
				alert("請填寫下拉選項名稱。");
				return false;
			}

			content = '<tr>';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_sub_title").val();
			content += '<input type="hidden" class="sub_option_title" name="sub_option_title[]" value="' + jQuery("#new_sub_title").val() + '"/>';
			content += '<input type="hidden" class="sub_option_id" name="sub_option_id[]" value=""/>';
			content += '</td>';

			content += '<td><a href="javascript: void(0);" class="edit_sub_row" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
			content += '<td><a href="javascript: void(0);" class="del_sub_row" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += "</tr>";

			jQuery("#sub_table_list").append(content);

			jQuery("#sub_table_list").orderSubTable();

			jQuery("#cancel_sub_btn").trigger("click");

		});

		// 刪除選項
		jQuery(document).on("click", '.del_sub_row', function(e) {
			// 記錄刪除ID
			sub_option_id = jQuery(this).parent().parent().children("td").children(".sub_option_id").val();

			if (sub_option_id) {
				ids = jQuery("#del_sub_option_ids").val();
				if (ids) {
					new_sub_ids += "," + sub_option_id;
				} else {
					new_sub_ids = sub_option_id;
				}

				jQuery("#del_sub_option_ids").val( new_sub_ids );
			}

			// 刪除該元素
			jQuery(this).parent().parent().remove();

			jQuery("#sub_table_list").orderSubTable();

		});


		// 開始編輯
		jQuery(document).on("click", '.edit_sub_row', function(e) {
			jQuery("#add_sub_btn").hide();
			jQuery("#edit_sub_btn").show();
			jQuery("#cancel_sub_btn").show();
			jQuery("#new_sub_table .title").html("編輯選項");
			jQuery("#new_sub_title").focus();

			sub_option_title = jQuery(this).parent().parent().children("td").children(".sub_option_title").val();
			jQuery("#new_sub_title").val(sub_option_title);

			sub_option_id = jQuery(this).parent().parent().children("td").children(".sub_option_id").val();
			jQuery("#edit_sub_option_id").val(sub_option_id);

			temp_sub_tr_index = jQuery(this).parent().parent().index("#sub_table_list tr");
			
		});


		// 儲存編輯
		jQuery("#edit_sub_btn").bind("click", function() {
			if (jQuery("#new_sub_title").val() == "") {
				alert("請填寫下拉選項名稱。");
				return false;
			}

			content = '';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_sub_title").val();
			content += '<input type="hidden" class="sub_option_title" name="sub_option_title[]" value="' + jQuery("#new_sub_title").val() + '"/>';
			content += '<input type="hidden" class="sub_option_id" name="sub_option_id[]" value="' + jQuery("#edit_sub_option_id").val() + '"/>';
			content += '</td>';

			content += '<td><a href="javascript: void(0);" class="edit_sub_row" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
			content += '<td><a href="javascript: void(0);" class="del_sub_row" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += '<td></td>';


			jQuery("#sub_table_list tr").eq( temp_sub_tr_index ).html(content);

			jQuery("#sub_table_list").orderSubTable();

			// 儲存完成後，回復成新增模式
			jQuery("#cancel_sub_btn").trigger("click");

		});


		// 編輯取消
		jQuery("#cancel_sub_btn").bind("click", function() {
			jQuery("#edit_sub_btn").hide();
			jQuery("#cancel_sub_btn").hide();
			jQuery("#add_sub_btn").show();
			jQuery("#new_sub_table .title").html("新增下拉選項");

			jQuery("#new_sub_title").val("");

			temp_sub_tr_index = 0;
		});



		// 向上移動
		jQuery(document).on("click", '.up_sub_row', function(e) {
			tr_sub_index = jQuery(this).parent().parent().index("#sub_table_list tr");
			temp_html = jQuery("#sub_table_list tr").eq(tr_sub_index).html();

			jQuery("#sub_table_list tr").eq(tr_sub_index).html( jQuery("#sub_table_list tr").eq((tr_sub_index - 1)).html() );
			jQuery("#sub_table_list tr").eq((tr_sub_index - 1)).html(temp_html);


			jQuery("#sub_table_list").orderSubTable();

		});

		// 向下移動
		jQuery(document).on("click", '.down_sub_row', function(e) {
			tr_sub_index = jQuery(this).parent().parent().index("#sub_table_list tr");
			temp_html = jQuery("#sub_table_list tr").eq(tr_sub_index).html();

			jQuery("#sub_table_list tr").eq(tr_sub_index).html( jQuery("#sub_table_list tr").eq((tr_sub_index + 1)).html() );
			jQuery("#sub_table_list tr").eq((tr_sub_index + 1)).html(temp_html);


			jQuery("#sub_table_list").orderSubTable();

		});


		// 重新排序號碼和向上、下向箭頭
		jQuery.fn.orderSubTable = function() {
			tr_count = jQuery(this).children("tr").length;

            jQuery(this).children("tr").each(function(index) {
				jQuery(this).children("td").eq(0).html((index+1) + '<input type="hidden" name="sub_option_order[]" value="' + (index+1) + '"/>');

				if (tr_count > 1) {
					// 第一列
					if (index == 0) {
						jQuery(this).children("td").eq(4).html("");
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_sub_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					} else if (tr_count == (index + 1)) {	// 最後一列
						jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_sub_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(5).html("");
					} else {
						jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_sub_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_sub_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					}
				}

			});

        }



		// 檢查選項
		jQuery.fn.checkField = function() {
			// 檢查是否有新增選項
			if (jQuery(".option_id").length == 0) {
				jQuery("#message_area").showMessage("請至少新增1個選項。");
				return false;
			}

			if (jQuery(".sub_option_id").length == 0) {
				jQuery("#message_area").showMessage("請至少新增1個下拉選單內容的選項。");
				return false;
			}

        }


	});
</script>

