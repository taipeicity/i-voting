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
			jQuery(".rwd-table tbody tr").each(function (index) {
				if (jQuery('input:radio:checked[class="option_radio_' + index + '"]').val() == undefined) {
					jQuery("#message_area").showMessage('選項' + (index + 1) + '請選擇其中一項。');
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