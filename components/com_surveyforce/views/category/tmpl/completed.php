<?php
/**
 * @package            Surveyforce
 * @version            1.5-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

$title_limit = 20;

$session = &JFactory::getSession();

?>
<div class="category-list completed" xmlns="http://www.w3.org/1999/html">
    <div class="menu-list">
	    <?php foreach ($this->home_menu as $item) { ?>
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

        <div class="mod_voting_list">
            <form class="history_search" action="<?php echo JRoute::_("index.php?option=com_surveyforce&task=category.category_completed", false); ?>" method="POST">

                <div class="css_table">
                    <div class="css_tr">
                        <div class="css_th">年份：</div>
                        <div class="css_td">
							<?php
							$year = date("Y");
							$j    = 1;
							for ($i = $year; $i >= 2015; $i--) {
								?>
                                <label class="radio radio-<?php echo $j; ?>"><input type="radio" name="condition" value="<?php echo $i; ?>" <?php echo (int) $this->condition == $i ? "checked" : "" ?> /><?php echo $i; ?>
                                </label>
								<?php
								$j++;
							}
							?>
                        </div>
                    </div>
                    <div class="css_tr">
                        <div class="css_th">案件狀態：</div>
                        <div class="css_td">
                            <label class="radio radio-is_define">
                                <input type="radio" name="condition" value="define" <?php echo $this->condition == "define" ? "checked" : ""; ?> />已完成
                            </label> <label class="radio radio-is_define">
                                <input type="radio" name="condition" value="undefine" <?php echo $this->condition == "undefine" ? "checked" : ""; ?> />未成案
                            </label>
                        </div>
                    </div>
                    <div class="css_tr">
                        <div class="css_th"><label for="survey_search">搜尋關鍵字：</label></div>
                        <div class="css_td btns">
                            <input name="survey_search" type="text" id="survey_search" placeholder="請輸入關鍵字" size="18" value="<?php echo $this->search ? $this->search : ""; ?>" />
                            <input type="button" class="btn" value="搜尋" />
                        </div>
                    </div>
                </div>
                <input type="hidden" id="action" name="action" value="radio" />
				<?php echo JHtml::_('form.token'); ?>

            </form>

        </div>

		<?php
		if ($this->items && $this->counts['completed'] > 0) {

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
							<?php if ($item->is_define == 1) { ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="觀看結果">
                                    <img class="lazy" src="<?php echo JURI::root(); ?>images/system/completed_btn.png" alt="觀看結果" />
                                </a>
							<?php } else { ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="觀看內容">
                                    <img class="lazy" src="<?php echo JURI::root(); ?>images/system/soon_btn.png" alt="觀看內容" />
                                </a>
							<?php } ?>
                        </div>


                        <hr>

                        <div class="info">
							<?php if ($item->is_define == 1) { ?>
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


            <div class="issues">

            </div>


			<?php
		} else {
			if ($session->get('completed_form')) {
				?>
                <div class="nodata">
                    查無此議題
                </div>
				<?php
			} else {
				?>
                <div class="nodata">
                    現在沒有<?php echo $this->params->get("page_title"); ?>
                </div>
				<?php
			}
		}
		?>
    </div>
</div>

<div id="message_area" style="display: none;">
    <div class="alert alert-message">
        <a class="close" data-dismiss="alert">×</a>
        <h4 class="alert-heading">訊息</h4>
        <div>
            <p id="message_content"></p>
        </div>
    </div>
</div>


<script type="text/javascript">

    jQuery(document).ready(function () {

        jQuery('.history_search input[type="radio"]').click(function () {
            jQuery("#action").val("radio");
            jQuery.fancybox.showLoading();
            jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
            setTimeout(function () {
                jQuery('.history_search').submit();
            }, 1000);
        });

        jQuery('.btn').click(function () {
            jQuery("#action").val("search");
            jQuery.fancybox.showLoading();
            jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
            setTimeout(function () {
                jQuery('.history_search').submit();
            }, 1000);
        });

        jQuery("#survey_search").keypress(function (event) {
            jQuery("#action").val("search");
            if (event.keyCode == 13) {
                jQuery.fancybox.showLoading();
                jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
                setTimeout(function () {
                    jQuery('.history_search').submit();
                }, 1000);
            }
        });

    });

</script>