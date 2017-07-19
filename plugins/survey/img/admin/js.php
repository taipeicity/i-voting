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
			
			if (jQuery("#text_upload_image").val() == "") {
				alert("請選擇圖片。");
				return false;
			}



			// 上傳圖片
			var image_content = '';
			if(jQuery("#text_upload_image")[0].files[0].size > 2097152) {		//假如檔案大小超過2MB)
				alert("附件檔超過指定大小(2MB)。");
				return false;
			}

			// ajax 上傳圖片
			var formData = new FormData(jQuery( "#question-form" )[0]);
			jQuery.ajax({
				url: "../plugins/survey/img/admin/ajax_upload_image.php",
				type: "POST",
				dataType:"json",
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				async: false,
				success: function (result) {

					if( result.status == false ){
						alert( result.msg );
						return false;
					} else {
						image_content = '<a href="../' + result.filepath + '" class="fancybox" title="預覽檢視">預覽檢視</a>';
						image_content += '<input type="hidden" class="option_image" name="option_image[]" value="' + result.filepath + '"/>';
					}
				},
				error: function () {
					alert("上傳檔案失敗。");
					return false;
				}
			});



			content = '<tr>';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value=""/>';
			content += '</td>';
			content += '<td>';
			content += image_content;
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

			option_is_other = jQuery(this).parent().parent().children("td").children(".option_is_other").val();
			jQuery("input[name=is_other][value='" + option_is_other + "']").attr('checked',true);

			option_id = jQuery(this).parent().parent().children("td").children(".option_id").val();
			jQuery("#edit_option_id").val(option_id);


			// 若已有圖片
			option_image = jQuery(this).parent().parent().children("td").children(".option_image").val();
			jQuery("#text_upload_image").val("");
			if (option_image) {
				jQuery("#old_image_area").show();
				jQuery("#text_upload_image").hide();

				jQuery("#old_image_link").attr("href", "../" + option_image);
			} else {
				jQuery("#old_image_area").hide();
				jQuery("#text_upload_image").show();

				jQuery("#old_image_link").attr("href", "");
			}


			temp_tr_index = jQuery(this).parent().parent().index("#table_list tr");
		});


		// 儲存編輯
		jQuery("#edit_btn").bind("click", function() {
			if (jQuery("#new_ftext").val() == "") {
				alert("請填寫選項名稱。");
				return false;
			}

			var image_content = jQuery("#table_list tr").eq( temp_tr_index ).children("td").eq(2).html();
			if (jQuery("#old_image_link").attr("href") == "") {
				if ( jQuery("#text_upload_image").val() ) {
					// 上傳圖片
					if(jQuery("#text_upload_image")[0].files[0].size > 2097152) {		//假如檔案大小超過2MB)
						alert("附件檔超過指定大小(2MB)。");
						return false;
					}

					// ajax 上傳圖片
					var formData = new FormData(jQuery( "#question-form" )[0]);
					jQuery.ajax({
						url: "../plugins/survey/img/admin/ajax_upload_image.php",
						type: "POST",
						dataType:"json",
						data: formData,
						cache: false,
						processData: false,
						contentType: false,
						async: false,
						success: function (result) {

							if( result.status == false ){
								alert( result.msg );
								return false;
							} else {
								image_content = '<a href="../' + result.filepath + '" class="fancybox" title="預覽檢視">預覽檢視</a>';
								image_content += '<input type="hidden" class="option_image" name="option_image[]" value="' + result.filepath + '"/>';
							}
						},
						error: function () {
							alert("上傳檔案失敗。");
							return false;
						}
					});
				} else {
					alert("請選擇圖片。");
					return false;
				}
			}



			content = '';
			content += '<td></td>';
			content += '<td>';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value="' + jQuery("#edit_option_id").val() + '"/>';
			content += '</td>';
			content += '<td>';
			content += image_content;
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
			jQuery("#text_upload_image").val("");

			jQuery("#old_image_area").hide();
			jQuery("#text_upload_image").show();

			temp_tr_index = 0;
		});


		// 刪除已有圖片
		jQuery("#del_image_btn").bind("click", function() {
			jQuery("#old_image_link").attr("href", "");

			jQuery("#old_image_area").hide();
			jQuery("#text_upload_image").show();
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
						jQuery(this).children("td").eq(5).html("");
						jQuery(this).children("td").eq(6).html('<a href="javascript: void(0);"" class="down_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					} else if (tr_count == (index + 1)) {	// 最後一列
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="up_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(6).html("");
					} else {
						jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="up_row" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
						jQuery(this).children("td").eq(6).html('<a href="javascript: void(0);"" class="down_row" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
					}
				}

			});

        }


		// 檢查選項
		jQuery.fn.checkField = function() {
			// 檢查是否有新增選項
			// 是否為複選
			if ( parseInt(jQuery('#jform_is_multi').val()) == 1) {
				if ( parseInt(jQuery('input:radio:checked[name="option_num_type"]').val()) == 0 ) {		// 限定應投
					_min_options = jQuery("#jform_multi_limit").val();
				} else {	// 可投幾項
					_min_options = jQuery("#jform_multi_max").val();
				}

				if (jQuery(".option_id").length < _min_options) {
					jQuery("#message_area").showMessage("複選類別 - 請至少新增"+ _min_options + "個選項。");
					return false;
				}
			} else {
				if (jQuery(".option_id").length == 0) {
					jQuery("#message_area").showMessage("單選類別 - 請至少新增1個選項。");
					return false;
				}
			}


        }


		
	});
</script>

