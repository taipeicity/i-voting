<div class="question_option option_table">
<?php

if ($_options) {

?>
	<table>
		<thead>
		<tr>
			<th class="null">&nbsp;</th>
			<?php foreach ($_sub_options as $sub_option) {  ?>
			<th>
				<?php echo $sub_option->title; ?>
			</th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($_options as $i => $option) {  ?>
		<tr>
			<td class="name">
				<label for="option_<?php echo $i; ?>_0" class="option_label">
					<?php echo $option->ftext; ?>
				</label>
			
			</td>
			<?php foreach ($_sub_options as $j => $sub_option) {  ?>
			<td class="option">
				<input type="radio" id="option_<?php echo $i. "_". $j; ?>" class="option_radio_<?php echo $i; ?>" name="option_field_<?php echo $option->id; ?>" value="<?php echo $sub_option->id; ?>">
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