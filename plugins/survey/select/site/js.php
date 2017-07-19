<script type="text/javascript">
	jQuery(document).ready(function(){
		

		// check filed
		jQuery.fn.checkField = function() {

			var is_check = true;
			jQuery(".option_select").each(function() {
				element_id = jQuery(this).attr("id");
				if (jQuery(this).find(":selected").val() == 0 || jQuery(this).find(":selected").val() == "") {
					jQuery("#message_area").showMessage("請選擇其中一項。");
					jQuery("#" + element_id).focus();
					is_check = false;
					return false;
				} else {
					if (isNaN( jQuery(this).find(":selected").val() )) {
						jQuery("#message_area").showMessage("下拉選單的選項不正確，請重新選擇。");
						jQuery("#" + element_id).focus();
						is_check = false;
						return false;
					}
				}
			});


			if (is_check == false) {
				return false;
			}

        }

	});

</script>