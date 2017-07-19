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