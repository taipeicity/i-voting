<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		var temp_tr_index;

		// 新增選項
		jQuery("#add_btn").bind("click", function () {

			// 檢查欄位
			if (jQuery("#new_ftext").val() == "") {
                jQuery("#message_area").showMessage("新增選項 - 請填寫選項名稱。", jQuery("#new_ftext"));
				return false;
			}

            if (jQuery("#new_ftext").val().len() > 25) {
                jQuery("#message_area").showMessage("新增選項 - 選項名稱的文字過多，請刪除部分文字。", jQuery("#new_ftext"));
                return false;
            }

			var num = jQuery("td[align='left']").length;
			for (var i = 0; i < num; i++) {
				if (jQuery("#new_ftext").val() == jQuery("td[align='left']")[i].innerText) {
                    jQuery("#message_area").showMessage("新增選項 - 選項名稱重複。", jQuery("#new_ftext"));
					jQuery("#new_ftext").val("");
					return false;
				}
			}

			jQuery("#new_file1").val(jQuery.trim(jQuery("#new_file1").val()));
			if (jQuery("#new_file1").val()) {
				reYoutubeUrl = /^(?:http(?:s)?:\/\/)+(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/;
				if (!reYoutubeUrl.test(jQuery("#new_file1").val())) {
                    jQuery("#message_area").showMessage("新增選項 - Youtube網址錯誤，請檢查是否輸入正確。", jQuery("#new_file1"));
					return false;
				}
			} else {
                jQuery("#message_area").showMessage("新增選項 - 請填寫Youtube網址。", jQuery("#new_file1"));
				return false;
			}



			content = '<tr>';
			content += '<td></td>';
			content += '<td align="left">';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value=""/>';
			content += '</td>';
			content += '<td>';
			content += '<a href="' + jQuery("#new_file1").val() + '" target="_blank">觀看檢視</a>';
			content += '<input type="hidden" class="option_file1" name="option_file1[]" value="' + jQuery("#new_file1").val() + '"/>';
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
		jQuery(document).on("click", '.del_row', function (e) {
			// 記錄刪除ID
			option_id = jQuery(this).parent().parent().children("td").children(".option_id").val();

			if (option_id) {
				ids = jQuery("#del_option_ids").val();
				if (ids) {
					new_ids += "," + option_id;
				} else {
					new_ids = option_id;
				}

				jQuery("#del_option_ids").val(new_ids);
			}

			// 刪除該元素
			jQuery(this).parent().parent().remove();

			jQuery("#table_list").orderTable();

		});


		// 開始編輯
		jQuery(document).on("click", '.edit_row', function (e) {
			jQuery("#add_btn").hide();
			jQuery("#edit_btn").show();
			jQuery("#cancel_btn").show();
			jQuery("#new_table .title").html("編輯選項");
			jQuery("#new_ftext").focus();

			option_ftext = jQuery(this).parent().parent().children("td").children(".option_ftext").val();
			jQuery("#new_ftext").val(option_ftext);
			jQuery("#old_ftext").val(option_ftext);

            var character = 25 - jQuery("#new_ftext").val().len();
            jQuery("#ftext_char").html(character);

			option_file1 = jQuery(this).parent().parent().children("td").children(".option_file1").val();
			jQuery("#new_file1").val(option_file1);

			option_id = jQuery(this).parent().parent().children("td").children(".option_id").val();
			jQuery("#edit_option_id").val(option_id);


			temp_tr_index = jQuery(this).parent().parent().index("#table_list tr");
		});


		// 儲存編輯
		jQuery("#edit_btn").bind("click", function () {
			if (jQuery("#new_ftext").val() == "") {
                jQuery("#message_area").showMessage("編輯選項 - 請填寫選項名稱。", jQuery("#new_ftext"));
				return false;
			}

            if(jQuery("#new_ftext").val().len() > 25){
                jQuery("#message_area").showMessage("編輯選項 - 選項名稱的文字過多，請刪除部分文字。", jQuery("#new_ftext"));
                return false;
            }

			var num = jQuery("td[align='left']").length;
			for (var i = 0; i < num; i++) {
				if (jQuery("#new_ftext").val() == jQuery("td[align='left']")[i].innerText && jQuery("#old_ftext").val() != jQuery("#new_ftext").val()) {
                    jQuery("#message_area").showMessage("編輯選項 - 選項名稱重複。", jQuery("#new_ftext"));
					jQuery("#new_ftext").val("");
					return false;
				}
			}

			jQuery("#new_file1").val(jQuery.trim(jQuery("#new_file1").val()));
			if (jQuery("#new_file1").val()) {
				reYoutubeUrl = /^(?:http(?:s)?:\/\/)+(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/;
				if (!reYoutubeUrl.test(jQuery("#new_file1").val())) {
                    jQuery("#message_area").showMessage("編輯選項 - Youtube網址錯誤，請檢查是否輸入正確。", jQuery("#new_file1"));
					return false;
				}
			} else {
                jQuery("#message_area").showMessage("編輯選項 - 請填寫Youtube網址。", jQuery("#new_file1"));
				return false;
			}



			content = '';
			content += '<td></td>';
			content += '<td align="left">';
			content += jQuery("#new_ftext").val();
			content += '<input type="hidden" class="option_ftext" name="option_ftext[]" value="' + jQuery("#new_ftext").val() + '"/>';
			content += '<input type="hidden" class="option_id" name="option_id[]" value="' + jQuery("#edit_option_id").val() + '"/>';
			content += '</td>';
			content += '<td>';
			content += '<a href="' + jQuery("#new_file1").val() + '" target="_blank">觀看檢視</a>';
			content += '<input type="hidden" class="option_file1" name="option_file1[]" value="' + jQuery("#new_file1").val() + '"/>';
			content += '</td>';

			content += '<td><a href="javascript: void(0);" class="edit_row" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
			content += '<td><a href="javascript: void(0);" class="del_row" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
			content += '<td></td>';
			content += '<td></td>';
			content += '<td></td>';


			jQuery("#table_list tr").eq(temp_tr_index).html(content);

			jQuery("#table_list").orderTable();

			// 儲存完成後，回復成新增模式
			jQuery("#cancel_btn").trigger("click");

		});


		// 編輯取消
		jQuery("#cancel_btn").bind("click", function () {
			jQuery("#edit_btn").hide();
			jQuery("#cancel_btn").hide();
			jQuery("#add_btn").show();
			jQuery("#new_table .title").html("新增選項");

			jQuery("#new_ftext").val("");
			jQuery("#new_file1").val("");

			temp_tr_index = 0;
            jQuery("#ftext_char").html(25);
        });



		// 向上移動
		jQuery(document).on("click", '.up_row', function (e) {
			tr_index = jQuery(this).parent().parent().index("#table_list tr");
			temp_html = jQuery("#table_list tr").eq(tr_index).html();

			jQuery("#table_list tr").eq(tr_index).html(jQuery("#table_list tr").eq((tr_index - 1)).html());
			jQuery("#table_list tr").eq((tr_index - 1)).html(temp_html);


			jQuery("#table_list").orderTable();

		});

		// 向下移動
		jQuery(document).on("click", '.down_row', function (e) {
			tr_index = jQuery(this).parent().parent().index("#table_list tr");
			temp_html = jQuery("#table_list tr").eq(tr_index).html();

			jQuery("#table_list tr").eq(tr_index).html(jQuery("#table_list tr").eq((tr_index + 1)).html());
			jQuery("#table_list tr").eq((tr_index + 1)).html(temp_html);


			jQuery("#table_list").orderTable();

		});


		// 重新排序號碼和向上、下向箭頭
		jQuery.fn.orderTable = function () {
			tr_count = jQuery(this).children("tr").length;

			jQuery(this).children("tr").each(function (index) {
				jQuery(this).children("td").eq(0).html((index + 1) + '<input type="hidden" name="option_order[]" value="' + (index + 1) + '"/>');



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
		jQuery.fn.checkField = function () {
			// 檢查是否有新增選項
			// 是否為複選
			if (parseInt(jQuery('#jform_is_multi').val()) == 1) {
				if (parseInt(jQuery('input:radio:checked[name="option_num_type"]').val()) == 0) {		// 限定應投
					_min_options = jQuery("#jform_multi_limit").val();
				} else {	// 可投幾項
					_min_options = jQuery("#jform_multi_max").val();
				}

				if (jQuery(".option_id").length < _min_options) {
					jQuery("#message_area").showMessage("複選類別 - 請至少新增" + _min_options + "個選項。", jQuery("#new_ftext, #new_file1"));
					return false;
				}
			} else {
				if (jQuery(".option_id").length == 0) {
					jQuery("#message_area").showMessage("單選類別 - 請至少新增1個選項。", jQuery("#new_ftext, #new_file1"));
					return false;
				}
			}


		}

        var new_ftext = jQuery("#new_ftext");
        var character = 25 - new_ftext.val().len();
        if (character > 0) {
            jQuery("#ftext_char").html(character);
        } else {
            jQuery("#ftext_char").html(0);
        }

        new_ftext.keydown(function () {
            jQuery(this).check(this, "#ftext_char", "選項名稱");
        });

        new_ftext.keyup(function () {
            jQuery(this).check(this, "#ftext_char", "選項名稱");
        });

        new_ftext.keypress(function () {
            jQuery(this).check(this, "#ftext_char", "選項名稱");
        });
	});
</script>

