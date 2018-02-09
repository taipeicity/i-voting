<?php
/**
 *   @package         Surveyforce
 *   @version           1.4-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

$title_limit = 20;

$session = &JFactory::getSession();
?>
<div class="category-list completed">
    <div class="menu-list">
        <div class="menu-item">
            <a class="soon" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=soon&Itemid=" . $this->soon_mymuid, false); ?>" title="提案資料內容">
                <span class="image" >
                    <img src="images/system/soon.png" alt="提案資料內容">
                </span>
                <span class="title">提案資料內容</span>
                <span class="num">(<?php echo $this->soon_counts; ?>)</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="voting" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=voting&Itemid=" . $this->voting_mymuid, false); ?>" title="進行中的投票">
                <span class="image" >
                    <img src="images/system/voting.png" alt="進行中的投票">
                </span>
                <span class="title">進行中的投票</span>
                <span class="num">(<?php echo $this->voting_counts; ?>)</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="completed active" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=completed&Itemid=" . $this->completed_mymuid, false); ?>" title="已完成的投票">
                <span class="image" >
                    <img src="images/system/completed.png" alt="已完成的投票">
                </span>
                <span class="title">已完成的投票</span>
                <span class="num">(<?php echo $this->completed_counts; ?>)</span>
            </a>
        </div>
    </div>

    <div class="category-content">        

        <div class="mod_voting_list">
            <div class="css_table">
                <form class="history_search" action="<?php echo JRoute::_("index.php?option=com_surveyforce&task=category.completed_form", false); ?>" method="POST">
                    <div class="css_tr">
                        <div class="css_th">年份：</div>
                        <div class="css_td">
							<?php
							$year = date("Y");
							$j = 1;
							for ($i = $year; $i >= 2015; $i--) {
								?>
								<label class="radio radio-<?php echo $j; ?>"><input type="radio" name="condition" value="<?php echo $i; ?>" <?php echo $this->year == $i ? "checked" : "" ?> /><?php echo $i; ?></label>                                
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
                                <input type="radio" name="condition" value="define" <?php echo $this->is_define == "define" ? "checked" : ""; ?> />已完成
                            </label>
                            <label class="radio radio-is_define">
                                <input type="radio" name="condition" value="undefine" <?php echo $this->is_define == "undefine" ? "checked" : ""; ?> />未成案
                            </label>
                        </div>
                    </div>
                    <div class="css_tr">
                        <div class="css_th">搜尋關鍵字：</div>
                        <div class="css_td btns">
                            <input name="survey_search" type="text" id="survey_search" placeholder="請輸入關鍵字" size="18" value="<?php echo $this->survey_search ? $this->survey_search : ""; ?>" />
                            <input type="button" class="btn" value="搜尋" />
                        </div>
                    </div>
					<?php echo JHtml::_('form.token'); ?>
                </form>
            </div>
        </div>

		<?php
		if ($this->items && $this->completed_counts > 0) {

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
								<img src="<?php echo $item->image; ?>" alt="<?php echo $item->title; ?>" >
							</a>
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
								開始時間&nbsp;&nbsp;&nbsp;<span><?php echo JHtml::_('date', $item->vote_start, "Y/m/d H:i"); ?></span>
								<br />
								結束時間&nbsp;&nbsp;&nbsp;<span><?php echo JHtml::_('date', $item->vote_end, "Y/m/d H:i"); ?></span>
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
					現在沒有已完成的投票
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

	jQuery('.history_search input[type="radio"]').click(function () {
		jQuery('#survey_search').removeAttr('name');
		jQuery.fancybox.showLoading();
		jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
		setTimeout(function () {
			jQuery('.history_search').submit();
		}, 1000);
	});

	jQuery('.btn').click(function () {
		jQuery('input[type="radio"]:checked').removeAttr('name');
		jQuery.fancybox.showLoading();
		jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
		setTimeout(function () {
			jQuery('.history_search').submit();
		}, 1000);
	});

	jQuery("#survey_search").keypress(function (event) {
		if (event.keyCode == 13) {
			jQuery('input[type="radio"]:checked').removeAttr('name');
			jQuery.fancybox.showLoading();
			jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
			setTimeout(function () {
				jQuery('.history_search').submit();
			}, 1000);
		}
	});

</script>