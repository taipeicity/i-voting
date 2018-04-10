<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

$date         = JFactory::getDate();
$datetime     = JFactory::getDate();
$datetime_arr = explode(" ", $datetime);
$date_arr     = explode("-", $datetime_arr[0]);
$startdate    = $date_arr[1] . '/' . $date_arr[2] . "/" . $date_arr[0] . " " . $datetime_arr[1];

$date_arr = array ();

$title_limit = 20;

$status = $this->practice_pattern;
?>
<div class="category-list voting">
	<?php if (!$status) { ?>
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
	<?php } ?>

    <div class="category-content">

		<?php if ($this->items && $this->counts['voting'] > 0) { ?>
            <div class="issues">
				<?php
				foreach ($this->items as $key => $item) {
					$date_arr[$item->id] = $item->vote_end;

					if ($status == true) {
						if ($item->vote_pattern == 1) {
							continue;
						}
					} else {
						if ($item->vote_pattern == 2) {
							continue;
						}
					}

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
                                    <img src="<?php echo SurveyforceVote::ReplacePath($item->image); ?>" alt="<?php echo $item->title; ?>">
                                </a>
                            </div>


                            <div class="more">
                                <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="我要投票">
                                    <img class="lazy" src="<?php echo JURI::root(); ?>images/system/vote_btn.png" alt="我要投票" />
                                </a>
                            </div>

                            <hr>

                            <div class="info">
                                <div class="date" id="date<?php echo $item->id; ?>">
                                    <noscript>
										<?php
										$model = $this->getModel();
										$diff  = $model->getTimeDiff(strtotime($datetime), strtotime($item->vote_end));
										?>
                                    </noscript>
                                    投票倒數
                                    &nbsp;&nbsp;&nbsp;
                                    <span class="day">
                                        <noscript><?php echo $diff['day']; ?></noscript>
                                    </span> 天
                                    &nbsp;
                                    <span class="hour">
                                        <noscript><?php echo $diff['hour']; ?></noscript>
                                    </span> 時
                                    &nbsp;
                                    <span class="min">
                                        <noscript><?php echo $diff['min']; ?></noscript>
                                    </span> 分
                                </div>
                                <div class="unit" title="發布機關 <?php echo $item->unit_title; ?>">發布機關&nbsp;&nbsp;&nbsp;<?php echo str_replace("臺北市政府", "", $item->unit_title); ?></div>
                            </div>
                        </div>


                    </div>
				<?php } ?>
            </div>
			<?php
		} else {
			if ($status) {
				?>
                <div class="nodata">
                    現在沒有練習的投票
                </div>
			<?php } else { ?>
                <div class="nodata">
                    現在沒有<?php echo $this->params->get("page_title"); ?>
                </div>
			<?php } ?>
		<?php } ?>
    </div>
</div>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script src="<?php echo JURI::root(); ?>components/com_surveyforce/assets/js/jquery.timers-1.2.js"></script>
<script>

    jQuery(document).ready(function () {
        var sid = "";
        var startDate = new Array();
        var endDate = new Array();
        var spantime = new Array();

		<?php
		foreach ($date_arr as $key => $edatetime) {
		$edatetime_arr = explode(" ", $edatetime);
		$edate_arr = explode("-", $edatetime_arr[0]);
		$enddate = $edate_arr[1] . '/' . $edate_arr[2] . "/" . $edate_arr[0] . " " . $edatetime_arr[1];
		?>
        sid = "<?php echo $key; ?>";
        startDate[sid] = new Date("<?php echo $startdate; ?>");
        endDate[sid] = new Date("<?php echo $enddate; ?>");
        spantime[sid] = (endDate[sid] - startDate[sid]) / 1000;
        timer<?php echo $key; ?>(spantime[sid]);

        function timer<?php echo $key; ?>(spantime) {
            jQuery(this).everyTime('1s', function (i) {
                spantime--;
                var d = Math.floor(spantime / (24 * 3600));
                var h = Math.floor((spantime % (24 * 3600)) / 3600);
                var m = Math.floor((spantime % 3600) / (60));
                var s = Math.floor(spantime % 60);
                var id = "<?php echo $key; ?>";

                if (spantime > 0) {
                    jQuery("#date" + id + " .day").text(d);
                    jQuery("#date_s_" + id + " .day").text(d);
                    jQuery("#date" + id + " .hour").text(h);
                    jQuery("#date_s_" + id + " .hour").text(h);
                    jQuery("#date" + id + " .min").text(m);
                    jQuery("#date_s_" + id + " .min").text(m);
                } else { // 避免倒數變成負的
                    jQuery("#date" + id + " .day").text(0);
                    jQuery("#date_s_" + id + " .day").text(0);
                    jQuery("#date" + id + " .hour").text(0);
                    jQuery("#date_s_" + id + " .hour").text(0);
                    jQuery("#date" + id + " .min").text(0);
                    jQuery("#date_s_" + id + " .min").text(0);
                }
            });
        }
		<?php } ?>
    });
</script>