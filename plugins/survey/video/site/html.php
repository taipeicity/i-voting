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
<div class="question option_video">
<?php
if ($_options) {
	foreach ($_options as $option) {
?>
	<div class="option">
		<div class="title">
			<?php echo $option->ftext; ?>
		</div>
		<div class="info">
			<div class="info_inner">
				<div class="stamp">

				</div>
				<div class="intro">
					<iframe width="100%" src="https://www.youtube.com/embed/<?php echo $option->file1; ?>" frameborder="0" allowfullscreen></iframe>
					<br>
					
					<div class="zoom">
						<a href="https://www.youtube.com/watch?v=<?php echo $option->file1; ?>" title="<?php echo $option->ftext; ?>" target="_blank">
							Youtube上觀看
						</a>
					</div>
				</div>

				<div style="display: none;">
				<?php if ($_question->is_multi) { ?>
					<input type="checkbox" id="option_<?php echo $i; ?>" class="selected_option" name="selected_option[]" value="<?php echo $option->id; ?>">
				<?php } else { ?>
					<input type="radio" id="option_<?php echo $i; ?>" class="selected_option" name="selected_option" value="<?php echo $option->id; ?>">
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php
	}
}
?>
</div>
