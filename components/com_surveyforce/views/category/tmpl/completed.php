<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted Access');

$title_limit = 20;

$current = JFactory::getDate()->format('Y-m-d');
$cdate = explode("-", $current);

if ($this->items) {

    $start_year = JHtml::_('date', $this->first_vote_start, 'Y');
    $end_year = JHtml::_('date', $this->last_vote_end, 'Y');

    $years = array();
    for ($y = $start_year; $y <= $end_year; $y++) {
        array_push($years, $y);
    }

    if (!in_array(date("Y"), $years)) {
        array_push($years, date("Y"));
    }
}
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
        <?php if ($this->items) { ?>

            <div class="mod_voting_list">
                <div class="year">
                    <a href="javascript: void(0)" alt="上一年"><span class="leftarrow" id="prev"></span></a>
                    <div class="year_list">
                        <?php foreach ($years as $ykey => $year) { ?>
                            <div id="year_<?php echo $year; ?>" class="year_block <?php echo ($year == $cdate[0]) ? "active" : ""; ?>" value="<?php echo $year; ?>">
                                <?php echo $year; ?>
                            </div>
                        <?php } ?>
                    </div>
                    <a href="javascript: void(0)" alt="下一年"><span class="rightarrow" id="next"></span></a>
                </div>

                <!-- 月 -->
                <div class="month">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $mm = str_pad($i, 2, "0", STR_PAD_LEFT);
                        ?>
                        <div id="month_<?php echo $mm; ?>" class="month_block <?php echo ($mm == $cdate[1]) ? "active" : ""; ?>" value="<?php echo $mm; ?>">
                            <a href="javascript: void(0)"><?php echo $mm; ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php
            foreach ($this->items as $item) {

                // 計算投票期間跨越的月份
                $date_class = "";
                for ($y = JHtml::_('date', $item->vote_start, 'Y'); $y <= JHtml::_('date', $item->vote_end, 'Y'); $y++) {
                    $start_month = ($y == JHtml::_('date', $item->vote_start, 'Y')) ? JHtml::_('date', $item->vote_start, 'm') : 1;
                    for ($m = $start_month; $m <= 12; $m++) {

                        $date_class .= sprintf(" date_%d_%02d", $y, $m);

                        if ($y == JHtml::_('date', $item->vote_end, 'Y') && $m == JHtml::_('date', $item->vote_end, 'm')) {
                            break;
                        }
                    }
                }
                ?>
                <div class="issue <?php echo $date_class; ?>">
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
                            <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="觀看結果">
                                <img class="lazy" src="<?php echo JURI::root(); ?>modules/mod_voting_slider/assets/images/completed_btn.png" alt="觀看結果" />
                            </a>
                        </div>

                        <hr>

                        <div class="info">
                            開始時間&nbsp;&nbsp;&nbsp;<span><?php echo JHtml::_('date', $item->vote_start, "Y/m/d H:i"); ?></span>
                            <br />
                            結束時間&nbsp;&nbsp;&nbsp;<span><?php echo JHtml::_('date', $item->vote_end, "Y/m/d H:i"); ?></span>
                            <div class="unit" title="發布機關 <?php echo $item->unit_title; ?>">發布機關&nbsp;&nbsp;&nbsp;<?php echo str_replace("臺北市政府", "", $item->unit_title); ?></div>
                        </div>
                    </div>

                </div>
            <?php } ?>


            <div class="issues">

            </div>


            <?php
        } else {
            ?>
            <div class="nodata">
                現在沒有已完成的投票
            </div>
        <?php } ?>
    </div>



</div>
<style>
    .category-content .issue {
        display: none;
    }
</style>
<noscript>
<style>
    .mod_voting_list {
        display: none;
    }
    .category-content .issue {
        display: block;
    }
</style>
</noscript>
<script type="text/javascript">
    (function ($) {
        $(document).ready(function () {
            var current = $(".year .active");
            if (!current.prev(".year_block").length) {
                $("#prev").hide();
            }
            if (!current.next(".year_block").length) {
                $("#next").hide();
            }

            $(".year .year_block").hide();
            $(".year .active").show();


            // 年份
            $("#prev").on("click", function () { // prev
                var active = $(".year .active").prev(".year_block");
                if (active.length) {
                    $(".year_block").removeClass("active");
                    active.addClass("active");

                    $(".year .year_block").hide();
                    $(".year .active").show();

                    var year = active.attr("value");
                    $(".month_block").removeClass("active");
                    $("#month_01").addClass("active");

                    $(".issue").hide();
                    $("#month_01").trigger("click");

                }

                var prev = active.prev(".year_block");
                if (!prev.length) {
                    $(this).hide();
                }

                var next = active.next(".year_block");
                if (next.length) {
                    $("#next").show();
                }

            });

            $("#next").on("click", function () { // next
                var active = $(".year .active").next(".year_block");
                if (active.length) {
                    $(".year_block").removeClass("active");
                    active.addClass("active");

                    $(".year .year_block").hide();
                    $(".year .active").show();

                    var year = active.attr("value");
                    $(".month_block").removeClass("active");
                    $("#month_01").addClass("active");

                    $(".issue").hide();
                    $("#month_01").trigger("click");
                }

                var prev = active.prev(".year_block");
                if (prev.length) {
                    $("#prev").show();
                }

                var next = active.next(".year_block");
                if (!next.length) {
                    $(this).hide();
                }
            });


            // 月份
            $(".month_block").on("click", function () {
                var year = $(".year .active").attr("value");
                var month = $(this).attr("value");

                $(".month_block").removeClass("active");
                $(this).addClass("active");

                $(".issue").hide();

                _html_str = '';
                $(".date_" + year + "_" + month).each(function (index) {
                    _html_str += '<div class="issue">' + $(this).html() + '</div>';
                });
                $(".issues").html(_html_str);
                $(".issues .issue").show();
            });

            $("#month_<?php echo date("m"); ?>").trigger("click");

        });
    })(jQuery);
</script>