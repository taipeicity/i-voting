<?php
/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted Access');

$title_limit = 20;

?>
<div class="category-list soon">
    <div class="menu-list">
        <div class="menu-item">
            <a class="soon active" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=soon&Itemid=" . $this->soon_mymuid, false); ?>" title="提案資料內容">
                <span class="image">
                    <img src="images/system/soon.png" alt="提案資料內容">
                </span>
                <span class="title">提案資料內容</span>
                <span class="num">(<?php echo $this->soon_counts; ?>)</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="voting" href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=category&layout=voting&Itemid=" . $this->voting_mymuid, false); ?>" title="進行中的投票">
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
                    <img src="images/system/completed.png" alt="已完成的投票">
                </span>
                <span class="title">已完成的投票</span>
                <span class="num">(<?php echo $this->completed_counts; ?>)</span>
            </a>
        </div>
    </div>

    <div class="category-content">
        <?php if ($this->items && $this->soon_counts >0) { ?>
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
                                    <img src="<?php echo $item->image; ?>" alt="<?php echo $item->title; ?>" >
                                </a>
                            </div>

                            <div class="more">
                                <a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=intro&sid=" . $item->id . "&Itemid=" . $this->itemid, false); ?>" title="觀看內容">
                                    <img class="lazy" src="<?php echo JURI::root(); ?>images/system/soon_btn.png" alt="觀看內容" />
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
            </div>


            <?php
        } else {
            ?>
            <div class="nodata">
                沒有提案資料
            </div>
        <?php } ?>
    </div>



</div>
