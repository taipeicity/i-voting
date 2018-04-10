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
<div class="question option_imgtext">
	<?php
	if ($_options) {
		foreach ($_options as $i => $option) {
			?>
			<div class="option">
				<div class="title"><span class="title_text"><?php echo ($i + 1) . '. ' . $option->ftext; ?></span></div>
				<div class="info">
					<div class="info_inner">
						<div class="stamp">

						</div>
						<div class="intro">
							<a href="javascript:void(0);" class="img" title="<?php echo $option->ftext; ?>">
								<img src="<?php echo SurveyforceVote::ReplacePath($option->image); ?>" alt="<?php echo $option->ftext; ?>">
							</a>
							<div class="desc">
								<?php echo $option->desc; ?>
							</div>

							<div class="zoom">
								<a class="fancybox" href="<?php echo SurveyforceVote::ReplacePath($option->image); ?>" data-fancybox-group="gallery" title="<?php echo $option->ftext; ?>">
									點此放大
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
