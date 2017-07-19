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
	<table border="0">
	<?php foreach ($_options as $i => $option) {  ?>
		<tr>
			<td>
				<?php if ($_question->is_multi) { ?>
				<input type="checkbox" id="option_<?php echo $i; ?>" class="<?php echo ($option->is_other) ? "is_other_field" : "option_checkbox"; ?>" name="selected_option[]" value="<?php echo $option->id; ?>">
			<?php } else { ?>
				<input type="radio" id="option_<?php echo $i; ?>" class="<?php echo ($option->is_other) ? "is_other_field" : ""; ?>" name="selected_option" value="<?php echo $option->id; ?>">
			<?php } ?>
			</td>
			<td>
				<label for="option_<?php echo $i; ?>">
					<?php echo $option->ftext; ?>
				</label>
				
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