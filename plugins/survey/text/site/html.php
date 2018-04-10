<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<?php if ($_question->is_multi) { ?>
	<div class="option_notice">
		<?php
		if ($_question->multi_limit > 0) {
			echo sprintf("選項應投%d項", $_question->multi_limit);
		} else {
			echo sprintf("選項可投%d項至%d項", $_question->multi_min, $_question->multi_max);
		}
		?>
	</div>
<?php } ?>
<div class="question_option option_text">	
	<?php
	if ($_options) {
		?>
		<table class="text_options">
			<?php foreach ($_options as $i => $option) { ?>
				<tr>
					<td style="width: 1em;">
						<?php if ($_question->is_multi) { ?>
							<input type="checkbox" id="option_<?php echo $i; ?>" class="<?php echo ($option->is_other) ? "is_other_field" : "option_checkbox"; ?>" name="selected_option[]" value="<?php echo $option->id; ?>">
						<?php } else { ?>
							<input type="radio" id="option_<?php echo $i; ?>" class="<?php echo ($option->is_other) ? "is_other_field" : "option_radio"; ?>" name="selected_option" value="<?php echo $option->id; ?>">
						<?php } ?>
					</td>
					<td class="text_item">

						<label for="option_<?php echo $i; ?>"><?php echo ($i + 1) . '. ' . $option->ftext; ?></label>

					</td>
					<td>
						<?php if ($option->is_other) { ?>
							<textarea class="other_field" name="other_field_<?php echo $option->id; ?>" cols="20" rows="3"></textarea>
						<?php } ?>
					</td>

				</tr>
			<?php } ?>


		</table>

		<?php
	}
	?>
</div>