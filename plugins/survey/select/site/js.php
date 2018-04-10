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


		// check filed
		jQuery.fn.checkField = function () {

			var is_check = true;
			jQuery(".option_select").each(function () {
				element_id = jQuery(this).attr("id");
				if (jQuery(this).find(":selected").val() == 0 || jQuery(this).find(":selected").val() == "") {
					jQuery("#message_area").showMessage("請選擇其中一項。");
					jQuery("#" + element_id).focus();
					is_check = false;
					return false;
				} else {
					if (isNaN(jQuery(this).find(":selected").val())) {
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