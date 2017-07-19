<script type="text/javascript">
	jQuery(document).ready(function(){
		// stamp
		jQuery(".option .stamp").bind( "click", function() {
			jQuery("#system-message-container").hide();
			
			<?php if ($_question->is_multi) { ?>

			if ( jQuery(this).hasClass("active") ) {
				jQuery(this).removeClass("active");
				_select_nums -= 1;
			} else {
				<?php if ($_question->multi_limit > 0) { // 應投幾項 ?>
				if (_select_nums >= <?php echo $_question->multi_limit; ?>) {
					jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
					return false;
				}

				<?php } else {	// 可投幾項 ?>
				if ( _select_nums >=  <?php echo $_question->multi_max; ?>) {
					jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
					return false;
				}

				<?php } ?>

				jQuery(this).addClass("active");
				_select_nums += 1;
				
				
			}
			<?php } else { ?>
				jQuery(".option .stamp").removeClass("active");
				jQuery(this).addClass("active");
			<?php } ?>

			jQuery(this).parent().children("div").children(".selected_option").trigger("click");
		});

		jQuery(".option .img, .option .desc").bind( "click", function() {
			jQuery("#system-message-container").hide();

			<?php if ($_question->is_multi) { ?>
			if ( jQuery(this).parent().parent().children(".stamp").hasClass("active") ) {
				jQuery(this).parent().parent().children(".stamp").removeClass("active");
				_select_nums -= 1;
			} else {
				<?php if ($_question->multi_limit > 0) { // 應投幾項 ?>
				if (_select_nums >= <?php echo $_question->multi_limit; ?>) {
					jQuery("#message_area").showMessage("限定應投<?php echo $_question->multi_limit; ?>項。");
					return false;
				}

				<?php } else {	// 可投幾項 ?>
				if ( _select_nums >=  <?php echo $_question->multi_max; ?>) {
					jQuery("#message_area").showMessage("可投<?php echo $_question->multi_min; ?>至<?php echo $_question->multi_max; ?>項。");
					return false;
				}

				<?php } ?>

				jQuery(this).parent().parent().children(".stamp").addClass("active");
				_select_nums += 1;
			}
			<?php } else { ?>
				jQuery(".option .stamp").removeClass("active");
				jQuery(this).parent().parent().children(".stamp").addClass("active");
			<?php } ?>

			

			jQuery(this).parent().parent().children("div").children(".selected_option").trigger("click");
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

				<?php if ($_question->multi_limit > 0) { // 應投幾項 ?>
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
			<?php } else { ?>
				if( jQuery('input:radio:checked[name="selected_option"]').val() == undefined) {
					jQuery("#message_area").showMessage('請選擇其中一項。');
					return false;
				}
			<?php } ?>


        }

	});

</script>