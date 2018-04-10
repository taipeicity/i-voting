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

<?php if ($_question->is_multi) { ?>

			if (jQuery('.option_checkbox:checked').length > 0) {
				jQuery(".not_check").hide();
				jQuery(".already_check").show();
				for (var x = 0; x < jQuery(".already").length; x++) {
					jQuery(".already")[x].innerHTML = jQuery('.option_checkbox:checked').length;
					jQuery(".yet")[x].innerHTML = <?php echo $_question->multi_limit > 0 ? $_question->multi_limit : $_question->multi_max; ?> - jQuery('.option_checkbox:checked').length;
				}
				for (var x = 0; x < jQuery('.option_checkbox:checked').length; x++) {
                    var num = jQuery('.option_checkbox:checked').parent('td').next('td').children()[x].innerHTML.split('.');

                    if (x > 0) {
						jQuery(".option_active")[0].innerHTML += "、" + num[0];

					} else {
						jQuery(".option_active")[0].innerHTML = num[0];
					}
				}
			}

			jQuery(".option_checkbox").bind("click", function () {

				jQuery("#system-message-container").hide();
				jQuery(".already_check").show();
				jQuery(".not_check").hide();

				_select_nums = 0;
				if (jQuery('.option_checkbox:checked').length == 0) {
					jQuery(".already")[0].innerHTML = jQuery('.option_checkbox:checked').length;
					jQuery(".yet")[0].innerHTML = <?php echo $_question->multi_limit > 0 ? $_question->multi_limit : $_question->multi_max; ?>;
					active(0);
				}

				_select_nums = jQuery('.option_checkbox:checked').length;
				if (_select_nums <= <?php echo $_question->multi_limit; ?> || _select_nums <= <?php echo $_question->multi_max; ?>) {
					active(1);
				}


	<?php if ($_question->multi_limit > 0) { ?> // 限定應投幾項         
					if (_select_nums > <?php echo $_question->multi_limit; ?>) {
						jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
						return false;
					}
	<?php } else { ?> // 可投幾項         
					if (_select_nums > <?php echo $_question->multi_max; ?>) {
						jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
						return false;
					}
	<?php } ?>

				function active(stamp_num) {

					for (var j = 0; j < jQuery(".already").length; j++) {
						jQuery(".already")[j].innerHTML = _select_nums;
						jQuery(".yet")[j].innerHTML = <?php echo $_question->multi_limit > 0 ? $_question->multi_limit : $_question->multi_max; ?> - _select_nums;
					}

					if (stamp_num != 0) {
						for (var x = 0; x < jQuery('.option_checkbox:checked').length; x++) {
                            var num = jQuery('.option_checkbox:checked').parent('td').next('td').children()[x].innerHTML.split('.');

                            if (x > 0) {
								jQuery(".option_active")[0].innerHTML += "、" + num[0];

							} else {
								jQuery(".option_active")[0].innerHTML = num[0];
							}
						}
					} else {
						jQuery(".option_active")[0].innerHTML = "";
						jQuery(".already_check").hide();
						jQuery(".not_check").show();
					}
				}
			});

<?php } else { ?>

			if (jQuery('.option_radio:checked').length > 0) {
				jQuery(".not_check").hide();
				jQuery(".already_check").show();
				for (var x = 0; x < jQuery(".already").length; x++) {
					jQuery(".already")[x].innerHTML = 1;
					jQuery(".yet")[x].innerHTML = 0;
				}
                var num = jQuery('.option_radio:checked').parent('td').next('td').children("label")[0].innerHTML.split('.');
				jQuery(".option_active")[0].innerHTML = num[0];
			}

			jQuery(".option_radio").bind("click", function () {

				jQuery("#system-message-container").hide();
				jQuery(".already_check").show();
				jQuery(".not_check").hide();

				_select_nums = 0;
				_select_nums = jQuery('.option_radio:checked').length;
				active();

				function active() {

					for (var j = 0; j < jQuery(".already").length; j++) {
						jQuery(".already")[j].innerHTML = _select_nums;
						jQuery(".yet")[j].innerHTML = 0;
					}

                    var num = jQuery('.option_radio:checked').parent('td').next('td').children("label")[0].innerHTML.split('.');
                    jQuery(".option_active")[0].innerHTML = num[0];
				}

			});

<?php } ?>
		// check filed
		jQuery.fn.checkField = function () {

<?php if ($_question->is_multi) { ?>
				var selected_option_count = 0;
				jQuery('input:checkbox:checked[name="selected_option[]"]').each(function (i) {
					if (jQuery(this).prop("checked") == true) {
						selected_option_count += 1;
					}
				});

	<?php if ($_question->multi_limit > 0) { ?> // 限定應投幾項
					if (selected_option_count != <?php echo $_question->multi_limit; ?>) {
						jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
						return false;
					}
	<?php } else { ?> // 可投幾項 
					if (selected_option_count < <?php echo $_question->multi_min; ?> || selected_option_count > <?php echo $_question->multi_max; ?>) {
						jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
						return false;
					}
	<?php } ?>

				is_other_field_empty = false;
				jQuery('input:checkbox:checked[name="selected_option[]"]').each(function (i) {
					if (jQuery(this).prop("checked") == true) {
						if (jQuery(this).hasClass('is_other_field')) {
							if (jQuery(this).parent().children(".other_field").val() == "") {
								is_other_field_empty = true;
							}
						}
					}
				});

				if (is_other_field_empty == true) {
					jQuery("#message_area").showMessage('請填寫內容。');
					return false;
				}


<?php } else { ?>
				if (jQuery('input:radio:checked[name="selected_option"]').val() == undefined) {
					jQuery("#message_area").showMessage('請選擇其中一項。');
					return false;
				}

				if (jQuery('input:radio:checked[name="selected_option"]').hasClass('is_other_field')) {
					if (jQuery('input:radio:checked[name="selected_option"]').parent().children(".other_field").val() == "") {
						jQuery("#message_area").showMessage('請填寫內容。');
						return false;
					}
				}

<?php } ?>


		}

	});

</script>