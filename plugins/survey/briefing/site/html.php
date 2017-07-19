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
<div class="question option_img">
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
					<a href="javascript:void(0);" class="img" title="<?php echo $option->ftext; ?>">
						<img src="<?php echo JURI::root() . $option->image; ?>" alt="<?php echo $option->ftext; ?>">
					</a>
					
					<?php if ($option->file1) { ?>
						<div class="attached">
							<a href="<?php echo JURI::root(). "/". $option->file1; ?>" target="_blank" title="下載簡報檔(另開新視窗)">下載簡報檔</a>
						</div>
					<?php } ?>
					
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
