<?php
/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<div class="question_option option_table">
	<?php
	if ($_options) {
		?>
		<table class="rwd-table">
			<thead>
				<tr>
					<th class="null">&nbsp;</th>
					<?php foreach ($_sub_options as $sub_option) { ?>
						<th>
							<?php echo $sub_option->title; ?>
						</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($_options as $i => $option) { ?>
					<tr>
						<th class="name">
							
								<?php echo $option->ftext; ?>
							

						</th>
						<?php foreach ($_sub_options as $j => $sub_option) { ?>
							<td class="option">
								<label for="option_<?php echo $i . "_" . $j; ?>" data-th="<?php echo $sub_option->title; ?>" class="option_label">
									<input type="radio" id="option_<?php echo $i . "_" . $j; ?>" class="option_radio_<?php echo $i; ?>" name="option_field_<?php echo $option->id; ?>" value="<?php echo $sub_option->id; ?>">
								</label>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>

		</table>
		<?php
	}
	?>
</div>