<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$time = JFactory::getDate()->toSql();
// layout 有3種：default / blog / text
$questions = $this->questions;
$options = $this->options;
foreach ($questions as $i => $question):
    $array_ques[$question->sf_qtext][] = $question->ftext;
endforeach;

$id = $this->item->id;
?>
<div class="survey_toolsbar">
    <?php
    if (!$this->print) {
        ?>
        <div class="toolsbar">
            <?php echo JHtml::_('toolsbar._default'); ?>
        </div>
        <?php
    } else {
        ?>
        <div class="btns">
            <?php echo JHtml::_('toolsbar.btn_print'); ?>
        </div>
    <?php } ?>
</div>
<div class="survey_hits">
    瀏覽人數：<?php echo $this->item->hits; ?>
</div>
<div class="survey_hits">
    已完成投票人數：<?php echo sprintf("%d", $this->finish_votes); ?>
</div>
<div class="survey <?php echo $this->item->layout; ?>">
    <?php
    if ($this->item->image) {
        ?>
        <div class="survey_banner">
            <img src="<?php echo JURI::root() . $this->item->image; ?>" alt="<?php echo $this->escape($this->item->title); ?>">
        </div>
    <?php } ?>
    <div class="intro">
        <div class="title">
            <?php echo $this->escape($this->item->title); ?>
        </div>
        <hr>
        <div class="desc">
            <?php echo $this->item->desc; ?>
        </div>
        <hr>
        <div class="other_desc">
            <ul>
                <li><strong>題目與選項方案：</strong><br>

                    <?php
                    $y = 1;
                    foreach ($array_ques as $x => $array_que) {
                        if (count($array_ques) > 1) {
                            echo "第" . $y . "題、";
                        }
                        echo $x;
                        echo "<br>";
                        for ($i = 0; $i < count($array_que); $i++) {
                            $j = $i + 1;
                            echo "(" . $j . ")" . $array_que[$i] . "&nbsp;&nbsp;&nbsp;";
                        }
                        echo "<br>";
                        $y++;
                    }
                    ?>

                </li>
                <li><strong>投票方式：</strong><?php echo ($this->item->is_place) ? "網路與現地投票" : "網路投票"; ?></li>
                <li><strong>投票人資格：</strong><?php echo $this->item->voters_eligibility; ?></li>
                <li><strong>投票人驗證方式：</strong><?php echo $this->item->voters_authentication; ?></li>
                <li><strong>投票期間：</strong><?php echo $this->item->during_vote; ?></li>
                <li><strong>宣傳推廣方式：</strong><?php echo $this->item->promotion; ?></li>    
                <li style="display: none;"><strong>投票結果運用方式：</strong><?php echo $this->item->results_using; ?></li>
                <li><strong>公布方式：</strong><?php echo $this->item->announcement_method; ?></li>
                <li><strong>公布日期：</strong><?php echo $this->item->announcement_date; ?></li>
                <li><strong>目前進度：</strong><?php echo $this->item->at_present; ?></li>
                <li><strong>討論管道：</strong><?php echo $this->item->discuss_source; ?></li>
                <li><strong>投票結果運用方式：</strong>                    
                    <?php
                    switch ($this->item->results_proportion) {
                        case "whole":
                            echo "完全參採";
                            break;
                        case "part":
                            echo "部分參採" . $this->item->part . "%";
                            break;
                        case "committee":
                            echo "送請專業委員會決策考量";
                            break;
                        case "other":
                            echo "其他(" . $this->item->other . ")";
                            break;
                    }
                    ?>
                </li>
                <li><strong>其他參考資料：</strong>
                    <?php
                    if ($this->item->other_data || $this->item->other_data2 || $this->item->other_data3) {
                        if ($this->item->other_data) {
                            $str = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->item->other_data);
                            ?>
                            <a href="<?php echo $this->item->other_data; ?>" target="_blank" title="<?php echo $str; ?>"><?php echo $str; ?></a>
                            <?php
                        }
                        if ($this->item->other_data2) {
                            $str2 = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->item->other_data2);
                            if ($this->item->other_data) {
                                ?>，<?php } ?>
                            <a href="<?php echo $this->item->other_data2; ?>" target="_blank" title="<?php echo $str2; ?>"><?php echo $str2; ?></a>
                            <?php
                        }
                        if ($this->item->other_data3) {
                            $str3 = str_replace("filesys/ivoting/survey/pdf/" . $id . "/", "", $this->item->other_data3);
                            if ($this->item->other_data2 || $this->item->other_data3) {
                                ?>，<?php } ?>
                            <a href="<?php echo $this->item->other_data3; ?>" target="_blank" title="<?php echo $str3; ?>"><?php echo $str3; ?></a>
                            <?php
                        }
                    } else {
                        ?>
                        無
                    <?php } ?>
                </li>
                <li><strong>其他參考網址：</strong>
                    <?php
                    if ($this->item->other_url) {
                        ?>
                        <a href="<?php echo $this->item->other_url; ?>" target="_blank"><?php echo $this->item->other_url; ?></a>
                        <?php
                    } else {
                        ?>
                        無
                    <?php } ?>
                </li>
                <li><strong>後續辦理情形：</strong>
                    <?php
                    if ($this->item->followup_caption) {
                        echo $this->item->followup_caption;
                    } else {
                        ?>
                        無
                    <?php } ?>
                </li>
                <li><strong>注意事項：</strong><?php echo $this->item->precautions; ?></li> 
                <?php ?>


            </ul>
        </div>
    </div>
</div>
<hr>
<div class="vote">
    <?php
    $date = JFactory::getDate();
    $nowDate = $date->toSql();
    if (strtotime($this->item->vote_start) < strtotime($nowDate)) {
        if (strtotime($this->item->vote_end) < strtotime($nowDate)) { // 已結束
            if ($this->item->display_result == 1 || $this->item->display_result == 2) {  // 投票結束後顯示結果
                ?>
                <div class="btns">
                    <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->item->id . '&Itemid=' . $this->completed_menuid, false); ?>" class="submit">觀看投票結果</a>
                </div>
                <?php
            }
        } else { // 進行中
            ?>
            <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=intro.start_vote&sid=' . $this->item->id . '&Itemid=' . $this->voting_menuid, false); ?>"><img src="modules/mod_voting_slider/assets/images/vote_btn.png" alt="我要投票" title="我要投票" /></a>
            <?php
        }
    } else { // 待投票
        if ($this->item->is_notice_email || $this->item->is_notice_phone) {
            echo $this->loadTemplate('notice');
        }
    }
    ?>
</div>
