<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted Access');


$date = JFactory::getDate();
$datetime = JFactory::getDate();
$datetime_arr = explode(" ", $datetime);
$date_arr = explode("-", $datetime_arr[0]);
$startdate = $date_arr[1] . '/' . $date_arr[2] . "/" . $date_arr[0] . " " . $datetime_arr[1];

$date_arr = array();


$title_limit = 20;
?>
<div class="category-list voting">
    <div class="menu-list">
		<div class="menu-item">
            <a class="soon" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=soon&Itemid=" . $this->soon_mymuid, false); ?>" title="提案資料內容">
                <span class="image">
                    <img src="images/system/soon.png" alt="提案資料內容">
                </span>
                <span class="title">提案資料內容</span>
                <span class="num">(<?php echo $this->soon_counts; ?>)</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="voting active" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=voting&Itemid=" . $this->voting_mymuid, false); ?>" title="進行中的投票">
                <span class="image">
                    <img src="images/system/voting.png" alt="進行中的投票">
                </span>
                <span class="title">進行中的投票</span>
                <span class="num">(<?php echo $this->voting_counts; ?>)</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="completed" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=completed&Itemid=" . $this->completed_mymuid, false); ?>" title="已完成的投票">
                <span class="image">
                    <img src="images/system/completed.png" alt="已完成的投票" />
                </span>
                <span class="title">已完成的投票</span>
                <span class="num">(<?php echo $this->completed_counts; ?>)</span>

            </a>
        </div>
    </div>

    <div class="category-content">
        <?php if ($this->items) { ?>
            <div class="issues">
                <?php
                foreach ($this->items as $key => $item) {
                    $date_arr[$item->id] = $item->vote_end;
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
                                <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="我要投票">
                                    <img class="lazy" src="<?php echo JURI::root(); ?>modules/mod_voting_slider/assets/images/vote_btn.png" alt="我要投票" />
                                </a>
                            </div>

                            <hr>

                            <div class="info">
                                <div class="date" id="date<?php echo $item->id; ?>">
                                    <noscript>
                                    <?php
                                    $model = $this->getModel();
                                    $diff = $model->getTimeDiff(strtotime($datetime), strtotime($item->vote_end));
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
            ?>
            <div class="nodata">
                現在沒有進行中的投票
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script src="<?php echo JURI::root(); ?>components/com_surveyforce/assets/js/jquery.timers-1.2.js" ></script>
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