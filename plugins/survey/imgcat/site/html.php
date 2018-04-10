<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
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

<?php if ($_cats) { ?>
    <ul class="cat_tabs tabs">
		<?php
		foreach ($_cats as $key => $cat) {
			?>
            <li class="cat_tab tab<?php echo ($key == 0) ? " active" : ""; ?>">
                <a href="javascript:void(0);" class="tab_link tab_<?php echo $key; ?>" title="<?php echo $cat->title; ?>">
					<?php echo $cat->title; ?>
                </a>
            </li>
			<?php
			$key++;
		}
		?>
    </ul>
<?php } ?>


<div class="option_img cat_question">
	<?php
	if ($_options) {
		// process
		$new_options = array ();
		$temp_catid  = 0;
		foreach ($_options as $option) {
			if ($option->catid != $temp_catid) {
				if (!is_array($new_options[$option->catid])) {
					$new_options[$option->catid] = array ();
				}
			}


			array_push($new_options[$option->catid], $option);

			$temp_catid = $option->catid;
		}

		$_CatTitle = array ();
		foreach ($_cats as $_cat) {
			$_CatTitle[$_cat->id] = $_cat->title;
		}
		?>

        <div class="tab_intro">
			<?php foreach ($_cats as $key => $cat) { ?>
                <div class="cat_tab_content tab_content tab_content_<?php echo $key; ?>">

					<?php
					if ($new_options[$cat->id]) {
						foreach ($new_options[$cat->id] as $i => $option) {
							?>
                            <div class="option fl-l">

                                <div class="info cat_info">

                                    <div class="stamp fl-l">    <?php //戳章 ?>

                                    </div>
                                    <div class="intro fl-l">    <?php //照片+放大 ?>
										<?php
										// 小圖
										$small_img = str_replace("_image_", "_image_s", $option->image);
										?>
                                        <a href="javascript:void(0);" class="cat_img img" title="<?php echo $option->ftext; ?>">
                                            <img src="<?php echo SurveyforceVote::ReplacePath($small_img); ?>" alt="<?php echo $option->ftext; ?>">
                                            <span class="cat_title d-ib"><?php echo sprintf("%d. %s", $i + 1, $option->ftext); ?></span>
                                            <input type="hidden" class="_CatTitle" value="<?php echo $_CatTitle[$option->catid]; ?>" />

                                        </a>

                                        <div class="cat_zoom zoom ps-a">
                                            <a class="cat_magnifier fancybox" href="<?php echo SurveyforceVote::ReplacePath($option->image); ?>" data-fancybox-group="gallery" title="<?php echo $option->ftext; ?>">

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
							<?php
						}
					}
					?>
                </div>
			<?php } ?>
        </div>
		<?php
	}
	?>
</div>
