<script type="text/javascript">
	jQuery(document).ready(function(){
		

		// check filed
		jQuery.fn.checkField = function() {

			var is_check = true;
			jQuery(".option_label").each(function(index) {
				if( jQuery('input:radio:checked[class="option_radio_' + index + '"]').val() == undefined) {
					jQuery("#message_area").showMessage('選項' + (index + 1) +'請選擇其中一項。');
					is_check = false;
					return false;
				}

			});


			if (is_check == false) {
				return false;
			}


        }

	});

</script>