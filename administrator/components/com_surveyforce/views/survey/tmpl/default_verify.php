<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

$verify_all_array = [];
$verify_mix_array = [];
if ($this->verify_types) {
    // 依強度選擇驗證方式用的陣列
    $verify_mix_array[1] = [];
    $verify_mix_array[2] = [];
    $verify_mix_array[3] = [];

    $mix_js = '';
    $custom_js = '';
    $setting_html = '';
    $check_js = '';
    foreach ($this->verify_types as $type) {
        // 整理plugin資料 - 依強度選擇驗證方式
        $params = json_decode($type->params);
        if ($params->level > 0) {
            $verify_mix_array[$params->level][$type->element] = $type->name;
            $verify_all_array[$type->element] = $type->name;
        }

        // 載入所有plugin
        JPluginHelper::importPlugin('verify', $type->element);
        $className = 'plgVerify'.ucfirst($type->element);

        // 依強度選擇驗證方式 - 其他設定JS
        if (method_exists($className, 'onGetAdminMixSettingJS')) {
            $mix_js .= $className::onGetAdminMixSettingJS();
        }

        // 自訂驗證 - 其他設定JS
        if (method_exists($className, 'onGetAdminCustomSettingJS')) {
            $custom_js .= $className::onGetAdminCustomSettingJS();
        }

        // 其他設定HTML
        if (method_exists($className, 'onGetAdminSettingHTML')) {
            $setting_html .= $className::onGetAdminSettingHTML();
        }

        // 檢查設定JS
        if (method_exists($className, 'onAdminSettingCheckJS')) {
            $check_js .= $className::onAdminSettingCheckJS();
        }
    }
}

?>
<script type="text/javascript">
    jQuery(document).ready(function () {

        var dest_select = jQuery("#dest_select"),
            verify_table = jQuery("#verify_table"),
            src_select = jQuery("#src_select"),
            temp_custom_verify = src_select.html(),
            verify_setting = jQuery(".verify_setting"),
            is_old_verify = jQuery("#is_old_verify"),
            verify_required = jQuery("#verify_required"),
            verify_table_module = jQuery("#verify_table_module"),
            verify_table_custom = jQuery("#verify_table_custom"),
            additional_verify_table = jQuery("#additional_verify_table");
			
		var is_need_data1 = false;

        // 驗證的選單回復至預設值
        jQuery.fn.resetVerify = function () {
            verify_setting.hide();
            jQuery(".verify_mix").each(function () {
                jQuery(this).attr('checked', false);
            });
            verify_required.attr("value", "0");
            src_select.html(temp_custom_verify);
            dest_select.html("");
			
			jQuery("#is_whitelist_zone").hide();
			jQuery("#additional_verify_table").find(".btn-group").find(".btn:last-child").trigger("click");
			jQuery("#is_data1_zone").hide();
			jQuery("#is_data2_zone").hide();
			is_need_data1 = false;
        };

        // 重新開啟選擇驗證的區塊
        jQuery('#reset_verify').on("click", function () {
            verify_table.show();
            jQuery(this).hide();
            is_old_verify.val("0");
        });
        // 點選不驗證
        jQuery('#verify_method_0').on("click", function () {
            verify_table_module.hide();
            verify_table_custom.hide();
            additional_verify_table.hide();
            jQuery('#verify_method_0').resetVerify();
        });
        // 點選依強度選擇驗證方式
        jQuery('#verify_method_1').on("click", function () {
            verify_table_module.show();
            verify_table_custom.hide();
            additional_verify_table.show();
            jQuery('#verify_method_1').resetVerify();
        });
        // 點選自訂驗證
        jQuery('#verify_method_2 ').on("click", function () {
            verify_table_module.hide();
            verify_table_custom.show();
            additional_verify_table.show();
            jQuery('#verify_method_2').resetVerify();
        });
        // 依強度選擇驗證方式 - 是否顯示其他設定
        jQuery('.verify_mix').on("click", function () {
            <?php echo $mix_js; ?>
					
			// 如果有選電子信箱、FB、簡訊驗證，則顯示是否使用補充資料1
			if (jQuery(this).val() == "email" || jQuery(this).val() == "facebook" || jQuery(this).val() == "phone") {
				if (jQuery('input:radio:checked[name="jform[is_additional_verify]"]').val() == 1) {
					jQuery("#is_data1_zone").show();
				}
				is_need_data1 = true;
			} else {
				jQuery("#is_data1_zone").hide();
				is_need_data1 = false;
			}
        });
        // 自訂驗證-點選加入
        jQuery('#select_add_btn').on("click", function () {
            var src_count = 0;
            src_select.find(":selected").each(function () {
                src_count += 1;
            });
            if (src_count === 0) {
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。', jQuery('#src_select'));
                return false;
            }

            src_select.find(":selected").each(function () {
                jQuery(new Option(this.text, this.value)).appendTo('#dest_select').attr('id', this.id);
                jQuery(this).remove();
            });
            // 自訂驗證 - 是否顯示其他設定
            verify_setting.hide();
            jQuery('#dest_select option').attr('selected', 'selected');
            dest_select_array = dest_select.val();
            if (dest_select_array.length > 0) {
                <?php echo $custom_js; ?>
            }
			
			// 如果有同時選台北通與身分證驗證的話，則顯示是否使用身分證驗證的欄位
			if ($.inArray('isso', dest_select_array) >= 0 && $.inArray('idnum', dest_select_array) >= 0) {
				if (dest_select_array.length == 2) {
					jQuery("#is_whitelist_zone").show();
				} else {
					jQuery("#is_whitelist_zone").hide();
				}
			} else {
				jQuery("#is_whitelist_zone").hide();
			}
			
			// 如果有選電子信箱、FB、簡訊驗證，則顯示是否使用補充資料1
			if ($.inArray('email', dest_select_array) >= 0 || $.inArray('facebook', dest_select_array) >= 0 || $.inArray('phone', dest_select_array) >= 0) {
				if (jQuery('input:radio:checked[name="jform[is_additional_verify]"]').val() == 1) {
					jQuery("#is_data1_zone").show();
				}
				is_need_data1 = true;
			} else {
				jQuery("#is_data1_zone").hide();
				is_need_data1 = false;
			}
						
        });
		
        // 自訂驗證-點選移除
        jQuery('#select_remove_btn').on("click", function () {
            var dest_count = 0;
            dest_select.find(":selected").each(function () {
                dest_count += 1;
            });
            if (dest_count === 0) {
                jQuery("#message_area").showMessage('請至少選擇一種驗證方式。', dest_select);
                return false;
            }

            dest_select.find(":selected").each(function () {
                jQuery(new Option(this.text, this.value)).appendTo('#src_select');
                jQuery(this).remove();
            });
            // 自訂驗證 - 是否顯示其他設定
            verify_setting.hide();
            jQuery('#dest_select option').attr('selected', 'selected');
            dest_select_array = dest_select.val();
            if (dest_select_array) {
                <?php echo $custom_js; ?>
            }

			// 如果有同時選台北通與身分證驗證的話，則顯示是否使用身分證驗證的欄位
			if ($.inArray('isso', dest_select_array) >= 0 && $.inArray('idnum', dest_select_array) >= 0) {
					jQuery("#is_whitelist_zone").show();
			} else {
				jQuery("#is_whitelist_zone").hide();
			}
			
			// 如果有選電子信箱、FB、簡訊驗證，則顯示是否使用補充資料1
			if ($.inArray('email', dest_select_array) >= 0 || $.inArray('facebook', dest_select_array) >= 0 || $.inArray('phone', dest_select_array) >= 0) {
				if (jQuery('input:radio:checked[name="jform[is_additional_verify]"]').val() == 1) {
					jQuery("#is_data1_zone").show();
				}
				is_need_data1 = true;
			} else {
				jQuery("#is_data1_zone").hide();
				is_need_data1 = false;
			}
			
        });

        jQuery.fn.checkVerifyJs = function () {
            var check = true;
            // 驗證方式
            if (parseInt(is_old_verify.val()) === 0) {
                if (parseInt(verify_table.find(".verify_method:checked").val()) === 1) {		// 依強度選擇驗證方式
                    var verify_mix = jQuery(".verify_mix:checked");
                    if (verify_mix.length === 0) {
                        jQuery("#message_area").showMessage('依強度選擇驗證方式 - 請選擇其中一種驗證方式。', verify_table.find(".verify_method:checked").parent().next().children());
                        check = false;
                    }
					
					// 若有啟用學生或在籍，要多檢查是否有開啟補充資料(身分證和生日)
					custome_verify = jQuery('input:radio:checked[name="verify_mix"]').val();
					if (custome_verify == "email" || custome_verify == "facebook" || custome_verify == "phone") {
						if (jQuery('input:radio:checked[name="jform[is_student]"]').val() == 1) {
							if (jQuery('input:radio:checked[name="jform[is_idnum]"]').val() == 0) {
								jQuery("#message_area").showMessage('有啟用判斷高中職名單，需開啟填寫身分證字號與生日。', verify_table.find(".verify_method:checked").parent().next().children());
								check = false;
							}
						}
						if (jQuery('input:radio:checked[name="jform[is_local]"]').val() == 1) {
							if (jQuery('input:radio:checked[name="jform[is_idnum]"]').val() == 0) {
								jQuery("#message_area").showMessage('有啟用判斷在籍名單，需開啟填寫身分證字號與生日。', verify_table.find(".verify_method:checked").parent().next().children());
								check = false;
							}
						}
					}
					

                    // 載入JS檢查
                    check_verify_method = verify_mix.val();
                    <?php
                    echo $check_js;
                    ?>
										
					

                } else if (parseInt(verify_table.find(".verify_method:checked").val()) === 2) {	// 自訂驗證

                    if (dest_select.val() == null) {
                        jQuery("#message_area").showMessage('自訂驗證 - 請選擇其中一種驗證方式。', verify_table.find(".verify_method:checked").parent().next().children());
                        check = false;
                    }
					
					// 若有啟用學生或在籍，要多檢查是否有開啟補充資料(身分證和生日)
					if ($.inArray('email', dest_select_array) >= 0 || $.inArray('facebook', dest_select_array) >= 0 || $.inArray('phone', dest_select_array) >= 0) {
						if (jQuery('input:radio:checked[name="jform[is_student]"]').val() == 1) {
							if (jQuery('input:radio:checked[name="jform[is_idnum]"]').val() == 0) {
								jQuery("#message_area").showMessage('有啟用判斷高中職名單，需開啟填寫身分證字號與生日。', verify_table.find(".verify_method:checked").parent().next().children());
								check = false;
							}
						}
						if (jQuery('input:radio:checked[name="jform[is_local]"]').val() == 1) {
							if (jQuery('input:radio:checked[name="jform[is_idnum]"]').val() == 0) {
								jQuery("#message_area").showMessage('有啟用判斷在籍名單，需開啟填寫身分證字號與生日。', verify_table.find(".verify_method:checked").parent().next().children());
								check = false;
							}
						}
					}
					

					/*
                    if (parseInt(verify_required.val()) === 1) {
                        if (dest_select.get(0).options.length < 2) {
                            jQuery("#message_area").showMessage('自訂驗證 - 驗證組合方式為同時，請至少選擇兩種驗證方式。', verify_table.find(".verify_method:checked").parent().next().children());
                            check = false;
                        }
                    }
					 */


                    // 載入JS檢查
                    var is_check_suceess = true;
                    dest_select.find(":selected").each(function () {
                        check_verify_method = this.value;
                        <?php
                        echo $check_js;
                        ?>
                    });
                    if (is_check_suceess === false) {
                        check = false;
                    }

                }
				
				
				// 檢查是曾嚴上傳檔案			
				if (jQuery('input:radio:checked[name="jform[is_student]"]').val() == 1) {
					if (jQuery("#student_table_suffix").val() == "") {
						jQuery("#message_area").showMessage("高中職學生名單驗證 - 請先匯入名單檔案。", jQuery("#student_upload_file"));
						check = false;
					}
				}
				if (jQuery('input:radio:checked[name="jform[is_local]"]').val() == 1) {
					if (jQuery("#local_table_suffix").val() == "") {
						jQuery("#message_area").showMessage("在籍名單驗證 - 請先匯入名單檔案。", jQuery("#local_upload_file"));
						check = false;
					}
				}
				
				
            }

            return check;
        };

		// 是否開啟附加驗證
		jQuery('input[name="jform[is_additional_verify]"]').change(function() {
			if (this.value == "1") {
				if (is_need_data1 == true) {
					jQuery("#is_data1_zone").show();
				} else {
					jQuery("#is_data1_zone").hide();
				}
				jQuery("#is_student_zone").show();
				jQuery("#is_local_zone").show();
			} else {
				jQuery("#additional_verify_table").find(".btn-group").find(".btn:last-child").trigger("click");
				jQuery("#is_data1_zone").hide();
				jQuery("#is_student_zone").hide();
				jQuery("#is_local_zone").hide();
				jQuery("#is_data2_zone").hide();
			}
		});
		
		// 點選是否判斷學生
		jQuery('input[name="jform[is_student]"]').change(function() {
			if (this.value == "1") {
				jQuery("#upload_student_zone").show();
			} else {
				jQuery("#upload_student_zone").hide();
			}
		});

		// 點選是否判斷在籍
		jQuery('input[name="jform[is_local]"]').change(function() {
			if (this.value == "1") {
				jQuery("#upload_local_zone").show();
				jQuery("#is_data2_zone").show();
			} else {
				jQuery("#upload_local_zone").hide();
				jQuery("#is_data2_zone").hide();
			}
		});
		
		
		// 學生名單 - 預覽畫面
        jQuery("#student_link").fancybox({
            helpers: {
                overlay: {closeClick: false}
            }
        });

		// 學生名單 - 選擇要匯入的名單檔案
        jQuery("#student_upload_file").change(function () {
            fname = jQuery(this).val();
            farr = fname.toLowerCase().split(".");
            if (farr.length != 0) {
                len = farr.length;

                switch (farr[len - 1]) {
                    case "csv" :
                        break;
                    default:
                        jQuery("#message_area").showMessage('請重新選擇檔案，僅允許上傳 CSV 檔案。', jQuery('#student_upload_file'));
                        return false;
                }
            }
        });

        // 學生名單 - 上傳檔案
        jQuery("#student_upload_btn").click(function () {
            if (jQuery("#student_upload_file").val() == "") {
                jQuery("#message_area").showMessage('請選擇要上傳的檔案。', jQuery('#student_upload_file'));
                return false;
            }

            fname = jQuery("#student_upload_file").val();
            farr = fname.toLowerCase().split(".");
            if (farr.length != 0) {
                len = farr.length;

                switch (farr[len - 1]) {
                    case "csv" :
                        break;
                    default:
                        jQuery("#message_area").showMessage('請重新選擇檔案，僅允許上傳 CSV 檔案。', jQuery('#student_upload_file'));
                        return false;

                }
            }

            if (jQuery("#student_upload_file")[0].files[0].size > 10485760) {		//假如檔案大小超過10MB)
                jQuery("#message_area").showMessage('附件檔超過指定大小(10MB)。', jQuery('#student_upload_file'));
                return false;
            }

            // ajax 上傳檔案
            jQuery("#message_area").hideMessage();
            var student_formData = new FormData(jQuery("#survey-form")[0]);
            jQuery("#verify_setting_student_result").hide();
            jQuery("#student_table_suffix").val("");
            jQuery.ajax({
                url: "<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/ajax_upload_student_file.php",
                type: "POST",
                dataType: "json",
                data: student_formData,
                cache: false,
                processData: false,
                contentType: false,
                fileElementId: "student_upload_file",
                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },
                success: function (result) {
                    if (result.status == false) {
                        jQuery("#message_area").showMessage(result.msg, jQuery('#student_upload_file'));
                        return false;
                    } else {
                        jQuery("#student_content").html(result.content);
                        jQuery("#student_link").trigger("click");
                        jQuery("#student_table_suffix").val(result.suffix);
                        jQuery("#verify_setting_student_msg").html("已新增資料表 - 代碼：" + result.suffix);
                        jQuery("#verify_setting_student_result").show();
                        jQuery("#verify_setting_student_upload").hide();

                    }
                },
                error: function (result) {
                    jQuery("#message_area").showMessage("上傳檔案失敗。", jQuery('#student_upload_file'));
                    return false;
                }
            });


        });

		// 學生名單 - 重新上傳
        jQuery("#verify_setting_student_show_upload").click(function () {
            jQuery("#student_table_suffix").val("");
            jQuery("#verify_setting_student_result").hide();
            jQuery("#verify_setting_student_upload").show();
        });
		
		
		// 在籍名單 - 預覽畫面
        jQuery("#local_link").fancybox({
            helpers: {
                overlay: {closeClick: false}
            }
        });

		// 在籍名單 - 選擇要匯入的名單檔案
        jQuery("#local_upload_file").change(function () {
            fname = jQuery(this).val();
            farr = fname.toLowerCase().split(".");
            if (farr.length != 0) {
                len = farr.length;

                switch (farr[len - 1]) {
                    case "csv" :
                        break;
                    default:
                        jQuery("#message_area").showMessage('請重新選擇檔案，僅允許上傳 CSV 檔案。', jQuery('#local_upload_file'));
                        return false;
                }
            }
        });

        // 在籍名單 - 上傳檔案
        jQuery("#local_upload_btn").click(function () {
            if (jQuery("#local_upload_file").val() == "") {
                jQuery("#message_area").showMessage('請選擇要上傳的檔案。', jQuery('#local_upload_file'));
                return false;
            }

            fname = jQuery("#local_upload_file").val();
            farr = fname.toLowerCase().split(".");
            if (farr.length != 0) {
                len = farr.length;

                switch (farr[len - 1]) {
                    case "csv" :
                        break;
                    default:
                        jQuery("#message_area").showMessage('請重新選擇檔案，僅允許上傳 CSV 檔案。', jQuery('#local_upload_file'));
                        return false;

                }
            }

            if (jQuery("#local_upload_file")[0].files[0].size > 10485760) {		//假如檔案大小超過10MB)
                jQuery("#message_area").showMessage('附件檔超過指定大小(10MB)。', jQuery('#local_upload_file'));
                return false;
            }

            // ajax 上傳檔案
            jQuery("#message_area").hideMessage();
            var local_formData = new FormData(jQuery("#survey-form")[0]);
            jQuery("#verify_setting_local_result").hide();
            jQuery("#local_table_suffix").val("");
            jQuery.ajax({
                url: "<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/ajax_upload_local_file.php",
                type: "POST",
                dataType: "json",
                data: local_formData,
                cache: false,
                processData: false,
                contentType: false,
                fileElementId: "local_upload_file",
                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },
                success: function (result) {
                    if (result.status == false) {
                        jQuery("#message_area").showMessage(result.msg, jQuery('#local_upload_file'));
                        return false;
                    } else {
                        jQuery("#local_content").html(result.content);
                        jQuery("#local_link").trigger("click");
                        jQuery("#local_table_suffix").val(result.suffix);
                        jQuery("#verify_setting_local_msg").html("已新增資料表 - 代碼：" + result.suffix);
                        jQuery("#verify_setting_local_result").show();
                        jQuery("#verify_setting_local_upload").hide();

                    }
                },
                error: function (result) {
                    jQuery("#message_area").showMessage("上傳檔案失敗。", jQuery('#local_upload_file'));
                    return false;
                }
            });


        });

		// 在籍名單 - 重新上傳
        jQuery("#verify_setting_local_show_upload").click(function () {
            jQuery("#local_table_suffix").val("");
            jQuery("#verify_setting_local_result").hide();
            jQuery("#verify_setting_local_upload").show();
        });
    });
</script>

<style>
	.student_review_table {
		border: 1px solid #ccc;
	}

	.student_review_table td {
		border: 1px solid #ccc;
		padding: 5px;
	}

	#verify_setting_student_result {
		display: none;
	}

	#verify_setting_student_msg, #verify_setting_student_msg2 {
		color: blue;
	}
	
	.local_review_table {
		border: 1px solid #ccc;
	}

	.local_review_table td {
		border: 1px solid #ccc;
		padding: 5px;
	}

	#verify_setting_local_result {
		display: none;
	}

	#verify_setting_local_msg, #verify_setting_local_msg2 {
		color: blue;
	}
</style>

<?php

if ($this->item->id) {
    $verify_type = json_decode($this->form->getValue('verify_type'), true);
    $verify_params = json_decode($this->form->getValue('verify_params'), true);

    if (! is_array($verify_type) || $verify_type[0] == "none") {
        echo "該議題設定為圖形驗證碼。";
    } else {
        ?>
        <table class="verify_table_module">
            <tr>
                <th>驗證項目</th>
                <th>備註</th>
            </tr>
            <?php
            foreach ($verify_type as $type) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo $verify_all_array[$type];
                        ?>
                    </td>
                    <td>
                        <?php
                        $className = 'plgVerify'.ucfirst($type);

                        // 顯示params
                        if (method_exists($className, 'onGetAdminShowParams')) {
                            echo $className::onGetAdminShowParams($verify_params);
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
<!--        <p>驗證組合方式：<?php echo ($this->form->getValue('verify_required')) ? "同時" : "擇一"; ?></p>-->
        <p>交叉驗證：<?php echo ($this->form->getValue('cross_validation')) ? "是" : "否"; ?></p>
		<?php
			// 如果有同時選台北通與身分證驗證的話
			if (in_array("isso", $verify_type) && in_array("idnum", $verify_type) && count($verify_type) == 2) {
		?>
        <p>台北通身分白名單檢核：<?php echo ($this->form->getValue('is_whitelist')) ? "是" : "否"; ?></p>
		<?php
			}
        ?>
		<p>啟用附加驗證：<?php echo ($this->form->getValue('is_additional_verify')) ? "是" : "否"; ?></p>
		<?php
			// 有啟用附加驗證
			if ($this->item->is_additional_verify) {
		?>
		<p>啟用補充身份證字號與生日資料：<?php echo ($this->form->getValue('is_idnum')) ? "是" : "否"; ?></p>
		<p>啟用判斷高中職名單：<?php echo ($this->form->getValue('is_student')) ? "是 (資料表代碼: ". $this->item->student_table_suffix. ")" : "否"; ?></p>
		<p>啟用判斷在籍名單：<?php echo ($this->form->getValue('is_local')) ? "是 (資料表代碼: ". $this->item->local_table_suffix. ")" : "否"; ?></p>
		<p>啟用補充公司或大學名稱資料：<?php echo ($this->form->getValue('is_company')) ? "是" : "否"; ?></p>
	<?php
			}
    }
    ?>
    <br>
    <?php
    if ($this->can_save == true) {
        ?>
        <input id="reset_verify" type="button" value="重新設定">
    <?php } ?>
<?php } ?>

<table id="verify_table" class="verify_table"
       style="display:<?php echo ($this->item->id) ? "none" : "block"; ?>">
    <tr>
        <td>
            <input type="radio" id="verify_method_0" name="verify_method" value="0" class="verify_method"
                   checked="checked">
        </td>
        <td><label for="verify_method_0">圖形驗證碼</label></td>
    </tr>
    <tr>
        <td colspan="2">
            &nbsp;
        </td>
    </tr>

    <tr>
        <td>
            <input type="radio" id="verify_method_1" name="verify_method" value="1" class="verify_method">
        </td>
        <td><label for="verify_method_1" id="label_verify_method_1">依強度選擇驗證方式</label></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <table id="verify_table_module" class="verify_table_module"
                   style="display: none;">
                <?php
                $level_label = ["1" => "驗證強度低", "2" => "驗證強度中", "3" => "驗證強度高"];
                foreach ($verify_mix_array as $level => $verify_array) {
                    $count = 0;
                    foreach ($verify_array as $element => $name) {
                        ?>
                        <tr>
                            <?php
                            if ($count == 0) {
                                ?>
                                <td rowspan="<?php echo count($verify_array); ?>">
                                    <?php echo $level_label[$level]; ?>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="radio" id="verify_mix_<?php echo $element; ?>"
                                       class="verify_mix" name="verify_mix"
                                       value="<?php echo $element; ?>">
                                <label for="verify_mix_<?php echo $element; ?>"><?php echo $name; ?></label>
                                <a href="../filesys/images/system/VerifyExample/<?php echo $element; ?>.png"
                                   class="show_example fancybox"
                                   title="<?php echo $name; ?>">(顯示範例)</a>
                            </td>
                        </tr>

                        <?php
                        $count++;
                    }
                }
                ?>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <input type="radio" id="verify_method_2" name="verify_method" value="2" class="verify_method">
        </td>
        <td><label for="verify_method_2">自訂驗證</label></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <table id="verify_table_custom" class="verify_table_custom" style="display: none;">
                <tr style="display: none;">
                    <td>
                        <?php echo $this->form->renderField('verify_required'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->form->renderField('cross_validation'); ?>
                    </td>
                </tr>
                
                <tr id="is_whitelist_zone">
                    <td>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('is_whitelist'); ?></div>
							<div class="controls">
								<?php echo $this->form->getInput('is_whitelist'); ?>
								<br>
								(有符合匯入身分證字號名單的APP使用者才能看到此議題和進行投票)
							</div>
						</div>						
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <label><select id="src_select" multiple="multiple" size="8">
                                            <?php
                                            foreach ($verify_all_array as $element => $name) {
                                                ?>
                                                <option id="<?php echo $element; ?>"
                                                        value="<?php echo $element; ?>"><?php echo $name; ?></option>
                                            <?php } ?>
                                        </select></label>
                                <td>
                                <td>
                                    <input type="button" id="select_add_btn" value="加入"
                                           style="width:50px;"> <br> <br>
                                    <input type="button" id="select_remove_btn" value="移除"
                                           style="width:50px;">

                                </td>
                                <td>
                                    <label for="dest_select"></label><select id="dest_select" name="verify_custom[]"
                                                                             multiple="multiple" size="8"> </select>
                                <td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>
            <?php echo $setting_html; ?>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>
			<table id="additional_verify_table" style="display: none;">
				<tr>
                    <td>
						<?php echo $this->form->renderField('is_additional_verify'); ?>
                    </td>
                </tr>
				<tr id="is_data1_zone" style="display: none;">
                    <td>
						<?php echo $this->form->renderField('is_idnum'); ?>
                    </td>
                </tr>
				
				<tr id="is_student_zone" style="display: none;">
                    <td>
						<?php echo $this->form->renderField('is_student'); ?>
                    </td>
                </tr>
				
				<tr id="upload_student_zone" style="display: none;">
                    <td>
						<!--上傳高中職學生名單-->
						<div id="verify_setting_student" style="border:1px solid #ccc; padding:10px; width:300px; margin-bottom:10px;">
							<div style="">高中職學生名單驗證 - 進階設定</div>
							<hr style="margin:5px 0px;">
							<ul class="nav nav-tabs">
								<li class="active"><a data-toggle="tab" href="#student-upload">上傳檔案</a></li>
							</ul>

							<div class="tab-content">
								<div id="student-upload" class="tab-pane fade in active">
									<div id="verify_setting_student_result">
										<div id="verify_setting_student_msg"></div>
										<input type="button" id="verify_setting_student_show_upload" value="重新上傳">
										<input type="hidden" id="student_table_suffix" name="student_table_suffix" value="" >
									</div>
									<div id="verify_setting_student_upload">
										請上傳要匯入的名單檔案<br>&nbsp;&nbsp;&nbsp;&nbsp;
										<input style="margin: 5px" type="file" name="student_upload_file" id="student_upload_file" accept=".csv"><br>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" value="上傳" id="student_upload_btn" >

										<br><br>
										<ol style="list-style-type: decimal;">
											<li>請上傳CSV檔案格式。(<a href="<?php echo JURI::root(); ?>images/system/student_sample.csv" title="下載範例檔" target="_blank">下載範例檔</a>)</li>
											<li>檔案容量限制為10MB。</li>
											<li>內容第一欄為身分證字號。</li>
											<li>內容第二欄為民國年生日。</li>
											<li>新上傳的檔案，一律覆蓋先前的資料。</li>
										</ol>
									</div>


								</div>


							</div>

							<a href="#student_zone" id="student_link" title="高中職名單預覽畫面" style="display: none;">高中職名單預覽畫面</a>
						</div>
						
						<!--高中職名單預覽畫面-->
						<div id="student_zone" style="display: none; width:600px;">
							<div id="student_message" style="color:red;"></div>
							<div id="student_content">
							</div>
						</div>

                    </td>
                </tr>
				
				<tr id="is_local_zone" style="display: none;">
                    <td>
						<?php echo $this->form->renderField('is_local'); ?>
                    </td>
                </tr>
				
				<tr id="upload_local_zone" style="display: none;">
                    <td>
						<!--上傳在籍名單-->
						<div id="verify_setting_local" style="border:1px solid #ccc; padding:10px; width:300px; margin-bottom:10px;">
							<div style="">在籍名單驗證 - 進階設定</div>
							<hr style="margin:5px 0px;">
							<ul class="nav nav-tabs">
								<li class="active"><a data-toggle="tab" href="#local-upload">上傳檔案</a></li>
							</ul>

							<div class="tab-content">
								<div id="local-upload" class="tab-pane fade in active">
									<div id="verify_setting_local_result">
										<div id="verify_setting_local_msg"></div>
										<input type="button" id="verify_setting_local_show_upload" value="重新上傳">
										<input type="hidden" id="local_table_suffix" name="local_table_suffix" value="" >
									</div>
									<div id="verify_setting_local_upload">
										請上傳要匯入的名單檔案<br>&nbsp;&nbsp;&nbsp;&nbsp;
										<input style="margin: 5px" type="file" name="local_upload_file" id="local_upload_file" accept=".csv"><br>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" value="上傳" id="local_upload_btn" >

										<br><br>
										<ol style="list-style-type: decimal;">
											<li>請上傳CSV檔案格式。(<a href="<?php echo JURI::root(); ?>images/system/local_sample.csv" title="下載範例檔" target="_blank">下載範例檔</a>)</li>
											<li>檔案容量限制為10MB。</li>
											<li>內容第一欄為身分證字號。</li>
											<li>內容第二欄為民國年生日。</li>
											<li>新上傳的檔案，一律覆蓋先前的資料。</li>
										</ol>
									</div>


								</div>


							</div>

							<a href="#local_zone" id="local_link" title="在籍名單預覽畫面" style="display: none;">在籍名單預覽畫面</a>
						</div>
						
						<!--預覽名單畫面-->
						<div id="local_zone" style="display: none; width:600px;">
							<div id="local_message" style="color:red;"></div>
							<div id="local_content">
							</div>
						</div>

                    </td>
                </tr>
				
				<tr id="is_data2_zone" style="display: none;">
                    <td>
						<?php echo $this->form->renderField('is_company'); ?>
                    </td>
                </tr>
			</table>
			
        </td>
    </tr>

	
</table>

