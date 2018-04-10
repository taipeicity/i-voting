<?php
/**
 * @package         Surveyforce
 * @version           1.0-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>
<script type="text/javascript">

    jQuery(document).ready(function () {
        var temp_tr_index;

        // 新增分類
        jQuery("#add_cat_btn").bind("click", function () {

            // 檢查欄位
            if (jQuery("#new_ctext").val() == "") {
                jQuery("#message_area").showMessage("新增分類 - 請填寫分類名稱。", jQuery("#new_ctext"));
                return false;
            }

            if (jQuery("#new_ctext").val().len() > 5) {
                jQuery("#message_area").showMessage("新增分類 - 分類名稱的文字過多，請刪除部分文字。", jQuery("#new_ctext"));
                return false;
            }

            var num = jQuery(".cat_text").length;
            for (var i = 0; i < num; i++) {
                if (jQuery("#new_ctext").val() == jQuery(".cat_text")[i].value) {
                    jQuery("#message_area").showMessage("新增分類 - 分類名稱重複。", jQuery("#new_ctext"));
                    jQuery("#new_ctext").val("");
                    return false;
                }
            }


            content = '<tr>';
            content += '<td></td>';
            content += '<td align="left">';
            content += jQuery("#new_ctext").val();
            content += '<input type="hidden" class="cat_text" name="cat_text[]" value="' + jQuery("#new_ctext").val() + '"/>';
            content += '<input type="hidden" class="cat_id" name="cat_id[]" value=""/>';
            content += '</td>';

            content += '<td><a href="javascript: void(0);" class="edit_cat" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
            content += '<td><a href="javascript: void(0);" class="del_cat" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
            content += '<td></td>';
            content += '<td></td>';
            content += '<td></td>';
            content += "</tr>";

            jQuery("#table_cat_list").append(content);

            jQuery("#table_cat_list").orderTableCat();


            jQuery("#cancel_cat_btn").trigger("click");

            jQuery("#ctext_char").html(5);

        });


        // 刪除分類
        jQuery(document).on("click", '.del_cat', function (e) {
            // 記錄刪除ID
            cat_id = jQuery(this).parent().parent().children("td").children(".cat_id").val();

            if (cat_id) {
                ids = jQuery("#del_cat_ids").val();
                if (ids) {
                    new_ids += "," + cat_id;
                } else {
                    new_ids = cat_id;
                }

                jQuery("#del_cat_ids").val(new_ids);
            }

            // 刪除該元素
            jQuery(this).parent().parent().remove();

            jQuery("#table_cat_list").orderTableCat();

        });


        // 開始編輯
        jQuery(document).on("click", '.edit_cat', function (e) {
            jQuery("#add_cat_btn").hide();
            jQuery("#edit_cat_btn").show();
            jQuery("#cancel_cat_btn").show();
            jQuery("#new_cat_table .title").html("編輯分類");
            jQuery("#new_ctext").focus();

            cat_text = jQuery(this).parent().parent().children("td").children(".cat_text").val();
            jQuery("#new_ctext").val(cat_text);
            jQuery("#cat_old_ftext").val(cat_text);

            var character = 5 - jQuery("#new_ctext").val().len();
            jQuery("#ctext_char").html(character);

            cat_id = jQuery(this).parent().parent().children("td").children(".cat_id").val();
            jQuery("#edit_cat_id").val(cat_id);

            temp_tr_index = jQuery(this).parent().parent().index("#table_cat_list tr");
        });


        // 儲存編輯
        jQuery("#edit_cat_btn").bind("click", function () {
            if (jQuery("#new_ctext").val() == "") {
                jQuery("#message_area").showMessage("編輯分類 - 請填寫分類名稱。", jQuery("#new_ctext"));
                return false;
            }

            if (jQuery("#new_ctext").val().len() > 5) {
                jQuery("#message_area").showMessage("編輯分類 - 分類名稱的文字過多，請刪除部分文字。", jQuery("#new_ctext"));
                return false;
            }

            var num = jQuery("#table_cat_list td[align='left']").length;
            for (var i = 0; i < num; i++) {
                if (jQuery("#new_ctext").val() == jQuery("#table_cat_list td[align='left']")[i].innerText && jQuery("#cat_old_ftext").val() != jQuery("#new_ctext").val()) {
                    jQuery("#message_area").showMessage("編輯分類 - 分類名稱重複。", jQuery("#new_ctext"));
                    jQuery("#new_ctext").val("");
                    return false;
                }
            }


            content = '';
            content += '<td></td>';
            content += '<td align="left">';
            content += jQuery("#new_ctext").val();
            content += '<input type="hidden" class="cat_text" name="cat_text[]" value="' + jQuery("#new_ctext").val() + '"/>';
            content += '<input type="hidden" class="cat_id" name="cat_id[]" value="' + jQuery("#edit_cat_id").val() + '"/>';
            content += '</td>';


            content += '<td><a href="javascript: void(0);" class="edit_cat" title="編輯"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png"  border="0" alt="編輯"></a></td>';
            content += '<td><a href="javascript: void(0);" class="del_cat" title="刪除"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png"  border="0" alt="刪除"></a></td>';
            content += '<td></td>';
            content += '<td></td>';
            content += '<td></td>';


            jQuery("#table_cat_list tr").eq(temp_tr_index).html(content);

            jQuery("#table_cat_list").orderTableCat();

            // 儲存完成後，回復成新增模式
            jQuery("#cancel_cat_btn").trigger("click");

        });


        // 編輯取消
        jQuery("#cancel_cat_btn").bind("click", function () {
            jQuery("#edit_cat_btn").hide();
            jQuery("#cancel_cat_btn").hide();
            jQuery("#add_cat_btn").show();
            jQuery("#new_cat_table .title").html("新增分類");

            jQuery("#new_ctext").val("");

            temp_tr_index = 0;
            jQuery("#ctext_char").html(5);

        });


        // 向上移動
        jQuery(document).on("click", '.up_cat', function (e) {
            tr_index = jQuery(this).parent().parent().index("#table_cat_list tr");
            temp_html = jQuery("#table_cat_list tr").eq(tr_index).html();

            jQuery("#table_cat_list tr").eq(tr_index).html(jQuery("#table_cat_list tr").eq((tr_index - 1)).html());
            jQuery("#table_cat_list tr").eq((tr_index - 1)).html(temp_html);


            jQuery("#table_cat_list").orderTableCat();

        });

        // 向下移動
        jQuery(document).on("click", '.down_cat', function (e) {
            tr_index = jQuery(this).parent().parent().index("#table_cat_list tr");
            temp_html = jQuery("#table_cat_list tr").eq(tr_index).html();

            jQuery("#table_cat_list tr").eq(tr_index).html(jQuery("#table_cat_list tr").eq((tr_index + 1)).html());
            jQuery("#table_cat_list tr").eq((tr_index + 1)).html(temp_html);


            jQuery("#table_cat_list").orderTableCat();

        });


        // 重新排序號碼和向上、下向箭頭
        jQuery.fn.orderTableCat = function () {
            tr_count = jQuery(this).children("tr").length;

            jQuery(this).children("tr").each(function (index) {
                jQuery(this).children("td").eq(0).html((index + 1) + '<input type="hidden" name="cat_order[]" value="' + (index + 1) + '"/>');

                if (tr_count > 1) {
                    // 第一列
                    if (index == 0) {
                        jQuery(this).children("td").eq(4).html("");
                        jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_cat" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
                    } else if (tr_count == (index + 1)) {	// 最後一列
                        jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_cat" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
                        jQuery(this).children("td").eq(5).html("");
                    } else {
                        jQuery(this).children("td").eq(4).html('<a href="javascript: void(0);"" class="up_cat" title="向上移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-up.png"  border="0" alt="向上移動"></a>');
                        jQuery(this).children("td").eq(5).html('<a href="javascript: void(0);"" class="down_cat" title="向下移動"><img src="<?php echo JURI::root() ?>administrator/components/com_surveyforce/assets/images/icon-24-down.png"  border="0" alt="向下移動"></a>');
                    }
                }

            });


        };

        jQuery.fn.checkField = function () {
            if (jQuery(".cat_text").length === 0) {
                jQuery("#message_area").showMessage("分類清單 - 請至少新增1個分類。", jQuery("#new_ctext"));
                return false;
            }
        };


        var new_ctext = jQuery("#new_ctext");
        var character = 5 - new_ctext.val().len();
        if (character > 0) {
            jQuery("#ctext_char").html(character);
        } else {
            jQuery("#ctext_char").html(0);
        }

        new_ctext.keydown(function () {
            jQuery(this).check(this, "#ctext_char", "分類名稱", 5);
        });

        new_ctext.keyup(function () {
            jQuery(this).check(this, "#ctext_char", "分類名稱", 5);
        });

        new_ctext.keypress(function () {
            jQuery(this).check(this, "#ctext_char", "分類名稱", 5);
        });


    });
</script>

