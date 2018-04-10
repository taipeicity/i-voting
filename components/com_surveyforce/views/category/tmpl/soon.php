<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

$title_limit = 20;

$session = &JFactory::getSession();
?>


<script type="text/javascript">

    jQuery(document).ready(function () {

        jQuery('.rwd-block input[type="radio"]').click(function () {
            jQuery.fancybox.showLoading();
            jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
            setTimeout(function () {
                jQuery(".pre_search").submit();
            }, 500);
        });
    });


</script>


<div class="category-list soon">
    <div class="menu-list">
	    <?php foreach ($this->home_menu as $num => $item) { ?>
            <div class="menu-item">
			    <?php
			    $class = $item->query["layout"];
			    if($this->itemid == $item->id){
				    $class .= " active";
			    }
			    ?>
                <a class="<?php echo $class; ?>" href="<?php echo JRoute::_($item->link . "&Itemid=" . $item->id, false); ?>" title="<?php echo $item->title; ?>">
                    <span class="image">
                        <img src="images/system/<?php echo $item->query["layout"]; ?>.png" alt="<?php echo $item->title; ?>">
                    </span> <span class="title"><?php echo $item->title; ?></span>
                    <span class="num">(<?php echo $this->counts[$item->query["layout"]]; ?>)</span> </a>
            </div>
	    <?php } ?>
    </div>
    <div class="category-content">

        <div class="mod_soon_list">

            <form class="pre_search" action="<?php echo JRoute::_("index.php?option=com_surveyforce&task=category.category_soon", false); ?>" method="POST">

                <span class="rwd-block"><input type="radio" name="condition" id="accidence" value="1" <?php echo ($this->condition == 1 || $this->condition == null) ? "checked" : ""; ?> />
						<label for="accidence" class="radio">初審階段</label></span>

                <span class="rwd-block"><input type="radio" name="condition" id="discuss" value="2" <?php echo $this->condition == 2 ? "checked" : ""; ?> />
						<label for="discuss" class="radio">討論階段</label></span>

                <span class="rwd-block"><input type="radio" name="condition" id="shelves" value="0" <?php echo $this->condition == "0" ? "checked" : ""; ?> />
						<label for="shelves" class="radio">上架定案</label></span>

				<?php echo JHtml::_('form.token'); ?>
            </form>


			<?php if ($this->items && $this->counts['soon'] > 0) { ?>
                <div class="issues">
					<?php
					foreach ($this->items as $item) {
						?>
                        <div class="issue">
                            <div class="issue_inner">
                                <div class="title">
                                    <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=intro&sid=' . $item->id . '&Itemid=' . $this->itemid, false); ?>">
										<?php
										if (utf8_strlen($item->title) > $title_limit)
											$item->title = utf8_substr($item->title, 0, $title_limit) . '...';
										echo $item->title;
										?>
                                    </a>
                                </div>

                                <div class="img">
                                    <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=intro&sid=' . $item->id . '&Itemid=' . $this->itemid, false); ?>">
                                        <img src="<?php echo $item->image; ?>" alt="<?php echo $item->title; ?>"> </a>
                                </div>

                                <div class="more">
                                    <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="觀看內容">
                                        <img class="lazy" src="<?php echo JURI::root(); ?>images/system/soon_btn.png" alt="觀看內容" />
                                    </a>
                                </div>

                                <hr>

                                <div class="info">
									<?php if ($item->is_define) { ?>
                                        開始時間&nbsp;&nbsp;&nbsp;
                                        <span><?php echo JHtml::_('date', $item->vote_start, "Y/m/d H:i"); ?></span>
                                        <br />
                                        結束時間&nbsp;&nbsp;&nbsp;
                                        <span><?php echo JHtml::_('date', $item->vote_end, "Y/m/d H:i"); ?></span>
									<?php } ?>
                                    <div class="unit" title="發布機關 <?php echo $item->unit_title; ?>">發布機關&nbsp;&nbsp;&nbsp;<?php echo str_replace("臺北市政府", "", $item->unit_title); ?></div>
                                </div>
                            </div>

                        </div>
					<?php } ?>
                </div>


				<?php
			} else {
				if ($this->condition == 1 || $this->condition === NULL) {
					$condition = "初審討論";
				} else if ($this->condition == 2) {
					$condition = "討論階段";
				} else {
					$condition = "上架定案";
				}
				?>
                <div class="nodata">
                    沒有<?php echo $condition; ?>
                </div>
			<?php } ?>
        </div>


    </div>
