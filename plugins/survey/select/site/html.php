<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<div class="question_option option_select">
	<?php
	if ($_options) {
		?>
		<table border="0">
			<?php foreach ($_options as $i => $option) { ?>
				<tr>
					<td class="label_cell rwd-block">
						<label for="option_<?php echo $i; ?>">
							<?php echo $option->ftext; ?>
						</label>
					</td>
					<td class="option_cell rwd-block">
						<select id="option_<?php echo $i; ?>" class="select_item" name="option_field_<?php echo $option->id; ?>">
							<?php foreach ($_sub_options as $sub_option) { ?>
								<option value="<?php echo $sub_option->id; ?>"><?php echo $sub_option->title; ?></option>
							<?php } ?>
						</select>
					</td>

				</tr>
			<?php } ?>


		</table>
		<?php
	}
	?>
</div>