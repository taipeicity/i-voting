<div class="question_option option_select">
<?php

if ($_options) {

?>
	<table border="0">
	<?php foreach ($_options as $i => $option) {  ?>
		<tr>
			<td>
				<label for="option_<?php echo $i; ?>">
					<?php echo $option->ftext; ?>
				</label>
			</td>
			<td>
				<select id="option_<?php echo $i; ?>" class="option_select" name="option_field_<?php echo $option->id; ?>">
					<?php foreach ($_sub_options as $sub_option) {	?>
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