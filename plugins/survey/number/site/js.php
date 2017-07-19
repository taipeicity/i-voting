<script type="text/javascript">
	jQuery(document).ready(function(){

		// 判斷是否重複
		jQuery(".option_select").change(function() {
			_select_element_id = jQuery(this).attr("id");
			_select_value = jQuery(this).val();

			jQuery(".option_select").each(function() {
				if (jQuery(this).attr("id") != _select_element_id) {
					if (jQuery(this).val() == _select_value) {
						jQuery("#"+ jQuery(this).attr("id")).attr("value", "");
					}
				}
			});
		});
		

		// check filed
		jQuery.fn.checkField = function() {
			var min_num = <?php echo $_sub_options[0]->title; ?>;
			var max_num = <?php echo $_sub_options[(count($_sub_options) - 1)]->title; ?>;

			var is_check = true;
			jQuery(".option_select").each(function(index) {
				element_id = jQuery(this).attr("id");
				jQuery(this).val( jQuery(this).val() );

				if ( jQuery(this).val() == "") {
					jQuery("#message_area").showMessage("選項" + (index+1) + "請選擇分數。");
					jQuery("#" + element_id).focus();
					is_check = false;
					return false;
				} else {
					
					if (isNaN(jQuery.trim(jQuery(this).val()))) {
						jQuery("#message_area").showMessage("請選擇分數。");
						jQuery("#" + element_id).focus();
						is_check = false;
						return false;
					} else {

						if (jQuery(this).val() < min_num || jQuery(this).val() > max_num) {
							jQuery("#message_area").showMessage("請選擇分數" + min_num + "分~" + max_num + "分");
							jQuery("#" + element_id).focus();
							is_check = false;
							return false;
						}
					}
				}


			});


			if (is_check == false) {
				return false;
			}

        }

	});

</script>