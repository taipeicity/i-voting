<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

$verify_all_array = array ();
$verify_mix_array = array ();
if ($this->verify_types) {
	// 依強度選擇驗證方式用的陣列
	$verify_mix_array[1] = array ();
	$verify_mix_array[2] = array ();
	$verify_mix_array[3] = array ();

	$mix_js       = '';
	$custom_js    = '';
	$setting_html = '';
	$check_js     = '';
	foreach ($this->verify_types as $type) {
		// 整理plugin資料 - 依強度選擇驗證方式
		$params = json_decode($type->params);
		if ($params->level > 0) {
			$verify_mix_array[$params->level][$type->element] = $type->name;
			$verify_all_array[$type->element]                 = $type->name;
		}

		// 載入所有plugin
		JPluginHelper::importPlugin('verify', $type->element);
		$className = 'plgVerify' . ucfirst($type->element);

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
            verify_required = jQuery("#verify_required");
            verify_table_module = jQuery("#verify_table_module"),
            verify_table_custom = jQuery("#verify_table_custom");

        // 驗證的選單回復至預設值
        jQuery.fn.resetVerify = function () {
            verify_setting.hide();
            jQuery(".verify_mix").each(function () {
                jQuery(this).attr('checked', false);
            });
            verify_required.attr("value", "0");
            src_select.html(temp_custom_verify);
            dest_select.html("");
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
            jQuery('#verify_method_0').resetVerify();
        });
        // 點選依強度選擇驗證方式
        jQuery('#verify_method_1').on("click", function () {
            verify_table_module.show();
            verify_table_custom.hide();
            jQuery('#verify_method_1').resetVerify();
        });
        // 點選自訂驗證
        jQuery('#verify_method_2 ').on("click", function () {
            verify_table_module.hide();
            verify_table_custom.show();
            jQuery('#verify_method_2').resetVerify();
        });
        // 依強度選擇驗證方式 - 是否顯示其他設定
        jQuery('.verify_mix').on("click", function () {
			<?php echo $mix_js; ?>
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

                    if (parseInt(verify_required.val()) === 1) {
                        if (dest_select.get(0).options.length < 2) {
                            jQuery("#message_area").showMessage('自訂驗證 - 驗證組合方式為同時，請至少選擇兩種驗證方式。', verify_table.find(".verify_method:checked").parent().next().children());
                            check = false;
                        }
                    }


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
            }

            return check;
        };

    });
</script>

<?php

if ($this->item->id) {
	$verify_type   = json_decode($this->form->getValue('verify_type'), true);
	$verify_params = json_decode($this->form->getValue('verify_params'), true);


	if (!is_array($verify_type) || $verify_type[0] == "none") {
		echo "該議題設定為圖形驗證碼。";
	} else {
		?>
        <table border="1" class="verify_table_module">
            <tr>
                <th align="center" width="150">驗證項目</th>
                <th align="center" width="350">備註</th>
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
						$className = 'plgVerify' . ucfirst($type);

						// 顯示params
						if (method_exists($className, 'onGetAdminShowParams')) {
							echo $className::onGetAdminShowParams($verify_params);
						}
						?>
                    </td>
                </tr>
			<?php } ?>
        </table>
        驗證組合方式：<?php echo ($this->form->getValue('verify_required')) ? "同時" : "擇一"; ?>
		<?php
	}
	?>
    <br>
    <br>
	<?php
	if ($this->can_save == true) {
		?>
        <input id="reset_verify" type="button" value="重新設定">
	<?php } ?>
<?php } ?>

<table border="0" id="verify_table" class="verify_table"
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
            <table border="1" id="verify_table_module" class="verify_table_module"
                   style="display: none;">
				<?php
				$level_label = array ("1" => "驗證強度低", "2" => "驗證強度中", "3" => "驗證強度高");
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
            <table border="0" id="verify_table_custom" class="verify_table_custom"
                   style="display: none;">
                <tr>
                    <td>
                        驗證組合方式 <select id="verify_required" name="verify_required">
                            <option value="0">擇一</option>
                            <option value="1">同時</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0">
                            <tr>
                                <td>
                                    <select id="src_select" multiple="multiple" size="8">
										<?php
										foreach ($verify_all_array as $element => $name) {
											?>
                                            <option id="<?php echo $element; ?>"
                                                    value="<?php echo $element; ?>"><?php echo $name; ?></option>
										<?php } ?>
                                    </select>
                                <td>
                                <td>
                                    <input type="button" id="select_add_btn" value="加入"
                                           style="width:50px;"> <br> <br>
                                    <input type="button" id="select_remove_btn" value="移除"
                                           style="width:50px;">

                                </td>
                                <td>
                                    <select id="dest_select" name="verify_custom[]"
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
</table>

