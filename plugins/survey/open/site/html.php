<?php
/**
*   @package         Surveyforce
*   @version           1.0-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<div class="question_option option_open">
	<?php
	if ($_options) {
		?>
		<?php foreach ($_options as $i => $option) { ?>
			<div class="option_<?php echo $i; ?>">
				<input type="hidden" id="option_<?php echo $i; ?>" class="<?php echo ($option->is_other) ? "is_other_field" : ""; ?>" name="selected_option" value="<?php echo $option->id; ?>">
				<?php if ($option->is_other) { ?>
					<textarea class="other_field" name="other_field_<?php echo $option->id; ?>" cols="20" rows="3"></textarea>
				<?php } ?>
			</div>
		<?php } ?>

		<?php
	}
	?>
</div>