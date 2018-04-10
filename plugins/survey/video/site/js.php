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
		// stamp
		jQuery(".option .stamp").bind("click", function () {
			jQuery("#system-message-container").hide();
			jQuery(".already_check").show();
			jQuery(".not_check").hide();
<?php if ($_question->is_multi) { ?>

				if (jQuery(this).hasClass("active")) {
					jQuery(this).removeClass("active");
					_select_nums -= 1;
					_select_nums == 0 ? active(0) : active(1);
				} else {
	<?php if ($_question->multi_limit > 0) { ?>// 應投幾項 
						if (_select_nums >= <?php echo $_question->multi_limit; ?>) {
							jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
							return false;
						}

	<?php } else { ?>// 可投幾項  
						if (_select_nums >= <?php echo $_question->multi_max; ?>) {
							jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
							return false;
						}

	<?php } ?>

					jQuery(this).addClass("active");
					_select_nums += 1;
					if (_select_nums <= <?php echo $_question->multi_limit; ?> || _select_nums <= <?php echo $_question->multi_max; ?>) {
						active(1);
					}

				}
<?php } else { ?>
				jQuery(".option .stamp").removeClass("active");
				jQuery(this).addClass("active");
				_select_nums = 1;
				active(1);
<?php } ?>

			jQuery(this).parent().children("div").children(".selected_option").trigger("click");

			function active(stamp_num) {
				for (var j = 0; j < jQuery(".already").length; j++) {
					jQuery(".already")[j].innerHTML = _select_nums;
<?php if ($_question->is_multi) { ?>
						jQuery(".yet")[j].innerHTML = <?php echo $_question->multi_limit > 0 ? $_question->multi_limit : $_question->multi_max; ?> - _select_nums;
<?php } else { ?>
						jQuery(".yet")[j].innerHTML = 0;
<?php } ?>
				}

				if (stamp_num != 0) {
					for (var x = 0; x < jQuery(".option .stamp.active").length; x++) {
					    var num = jQuery(".option .stamp.active").parent().parent().parent().children(".title").children(".title_text")[x].innerHTML;
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



		// check filed
		jQuery.fn.checkField = function () {
<?php if ($_question->is_multi) { ?>
				var selected_option_count = 0;
				jQuery('input:checkbox:checked[name="selected_option[]"]').each(function (i) {
					if (jQuery(this).prop("checked") == true) {
						selected_option_count += 1;
					}
				});

	<?php if ($_question->multi_limit > 0) { ?>// 應投幾項 
					if (selected_option_count != <?php echo $_question->multi_limit; ?>) {
						jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
						return false;
					}
	<?php } else { ?>// 可投幾項 
					if (selected_option_count < <?php echo $_question->multi_min; ?> || selected_option_count > <?php echo $_question->multi_max; ?>) {
						jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
						return false;
					}
	<?php } ?>
<?php } else { ?>
				if (jQuery('input:radio:checked[name="selected_option"]').val() == undefined) {
					jQuery("#message_area").showMessage('請選擇其中一項。');
					return false;
				}
<?php } ?>


		}

	});

</script>