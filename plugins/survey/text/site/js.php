<script type="text/javascript">
	jQuery(document).ready(function(){
		


		jQuery(".option_checkbox").bind( "click", function() {
			jQuery("#system-message-container").hide();

			_select_nums = 0;
			jQuery('input:checkbox:checked[name="selected_option[]"]').each(function(i) {
				if ( jQuery(this).prop( "checked" ) == true ) {
					_select_nums += 1;
				} else {
					_select_nums -= 1;
				}
			});

			<?php if ($_question->multi_limit > 0) { // 限定應投幾項 ?>
				if (_select_nums > <?php echo $_question->multi_limit; ?> ) {
					jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
					return false;
				}
			<?php } else {	// 可投幾項 ?>
				if ( _select_nums > <?php echo $_question->multi_max; ?>) {
					jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
					return false;
				}
			<?php } ?>


		});

		// check filed
		jQuery.fn.checkField = function() {

		<?php if ($_question->is_multi) { ?>
			var selected_option_count = 0;
			jQuery('input:checkbox:checked[name="selected_option[]"]').each(function(i) {
				if ( jQuery(this).prop( "checked" ) == true ) {
					selected_option_count += 1;
				}
			});

			<?php if ($_question->multi_limit > 0) { // 限定應投幾項 ?>
				if (selected_option_count != <?php echo $_question->multi_limit; ?> ) {
					jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
					return false;
				}
			<?php } else {	// 可投幾項 ?>
				if (selected_option_count < <?php echo $_question->multi_min; ?> ||  selected_option_count > <?php echo $_question->multi_max; ?>) {
					jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
					return false;
				}
			<?php } ?>

			is_other_field_empty = false;
			jQuery('input:checkbox:checked[name="selected_option[]"]').each(function(i) {
				if ( jQuery(this).prop( "checked" ) == true ) {
					if ( jQuery(this).hasClass('is_other_field') ) {
						if ( jQuery(this).parent().children(".other_field").val() == "") {
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
			if( jQuery('input:radio:checked[name="selected_option"]').val() == undefined) {
				jQuery("#message_area").showMessage('請選擇其中一項。');
				return false;
			} 

			if ( jQuery('input:radio:checked[name="selected_option"]').hasClass('is_other_field') ) {
				if (jQuery('input:radio:checked[name="selected_option"]').parent().children(".other_field").val() == "") {
					jQuery("#message_area").showMessage('請填寫內容。');
					return false;
				}
			}

		<?php } ?>


        }

	});

</script>