<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<div class="question_option option_number">
	<?php
	if ($_options) {
		$min = intval($_sub_options[0]->title);
		$max = intval($_sub_options[(count($_sub_options) - 1)]->title);
		?>
		<div class="option_notice"><?php echo sprintf("請選擇分數%d分~%d分，請勿選擇重覆的分數。", $min, $max); ?></div>
		<table border="0">
			<?php foreach ($_options as $i => $option) { ?>
				<tr>
					<td class="label_cell rwd-block">
						<label for="option_<?php echo $i; ?>">
							<?php echo $option->ftext; ?>
						</label>
					</td>
					<td class="option_cell rwd-block">
						<select id="option_<?php echo $i; ?>" class="option_select" name="option_field_<?php echo $option->id; ?>">
							<option value=""></option>
							<?php for ($i = $min; $i <= $max; $i++) { ?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
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