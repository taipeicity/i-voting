<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

$session = JFactory::getSession();
$time    = JFactory::getDate()->toSql();
// layout 有3種：default / blog / text
$questions  = $this->questions;
$options    = $this->options;
$array_ques = [];
$num        = 0;
foreach ($questions as $i => $question):
	$array_ques[$question->id][$question->sf_qtext][] = $question->ftext;
endforeach;

$id          = $this->item->id;
$verify_type = json_decode($this->item->verify_type);
if (count($verify_type) == 1) {
	$plugin = JPluginHelper::getPlugin('verify', $verify_type[0]);
	if ($plugin) {
		// Get plugin params
		$pluginParams = new JRegistry($plugin->params);
		$level        = $pluginParams->get('level');
		if ($level == 0) {
			$level = 1;
		}
	}
}

?>

<script>
    jQuery(document).ready(function () {
        jQuery(".other_data").on("click", function () {
            jQuery("#file_name").val(this.id);
            jQuery("#original_name").val(this.title);
            jQuery("#admin-form").submit();
        });
    });
</script>

<?php if (!$session->get('practice_pattern') && $this->item->public == 1) { ?>
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
		<?php echo ($this->finish_votes) ? sprintf("已完成投票人數：%d", $this->finish_votes) : ""; ?>
    </div>
<?php } ?>

<div class="survey <?php echo $this->item->layout; ?>">
	<?php
	if ($this->item->image) {
		?>
        <div class="survey_banner">
            <img src="<?php echo SurveyforceVote::ReplacePath($this->item->image); ?>" alt="<?php echo $this->escape($this->item->title); ?>">
        </div>
	<?php } ?>
    <div class="intro">
        <div class="description">
            <h2 class="title">
				<?php echo $this->escape($this->item->title); ?>
            </h2>

            <div class="desc">
				<?php echo $this->item->desc; ?>
            </div>
        </div>
        <form id="admin-form" action="<?php echo JRoute::_('index.php?option=com_surveyforce&task=' . ($this->preview == false ? 'intro' : 'surveys') . '.other_data', false); ?>" method="POST">

            <div class="other_desc">
                <ul>
                    <li><strong class="list_title">題目與選項方案</strong><span class="list_desc">
                        <?php if ($this->preview == true && !$array_ques) { ?>
                            尚未新增題目
                        <?php } else { ?>
	                        <?php if ($this->item->is_public == 1) { ?>
                                <br>
	                        <?php } ?>
	                        <?php
	                        $y = 1;
	                        foreach ($array_ques as $id => $array_que) {
		                        foreach ($array_que as $title_name => $item) {
			                        if (count($array_ques) > 1) {
				                        echo "第" . $y . "題、";
			                        }
			                        echo $title_name;
			                        echo "<br>";
			                        for ($i = 0; $i < count($item); $i++) {
				                        $j = $i + 1;
				                        echo "&nbsp;&nbsp;&nbsp;" . "(" . $j . ")" . $item[$i];
			                        }
			                        echo "<br>";
			                        $y++;
		                        }
	                        }
                        }
                        ?>
                </span></li>
					<?php if ($this->item->is_define) { ?>

                        <li>
                            <strong class="list_title">投票方式</strong><span class="list_desc"><?php echo $this->item->vote_way; ?></span>
                        </li>

					<?php } ?>
                    <li>
                        <strong class="list_title">投票人資格</strong><span class="list_desc"><?php echo nl2br($this->item->voters_eligibility); ?></span>
                    </li>
					<?php if ($this->item->is_define) { ?>
                        <li>
                            <strong class="list_title">投票人驗證方式</strong><span class="list_desc"><?php echo $this->item->voters_authentication; ?></span>
                        </li>
					<?php } ?>
					<?php if ($this->item->is_define) { ?>
						<?php if ($this->item->verify_precautions) { ?>
                            <li>
                                <strong class="list_title">驗證方式注意事項說明</strong><span class="list_desc"><?php echo nl2br($this->item->verify_precautions); ?></span>
                            </li>
						<?php } ?>
					<?php } ?>
					<?php if ($this->item->is_define) { ?>
						<?php if (count($verify_type) == 1) { ?>
                            <li><strong class="list_title">驗證強度</strong>
                                <div class="verifylevel_intro">
                                    <img src="/images/system/VerifyLevel/verifylevel_<?php echo $level; ?>.svg" /></div>
                            </li>
						<?php } ?>
					<?php } ?>
					<?php if ($this->item->is_define) { ?>
                        <li>
                            <strong class="list_title">投票期間</strong><span class="list_desc"><?php echo $this->item->during_vote; ?></span>
                        </li>
					<?php } ?>
                    <li>
                        <strong class="list_title">宣傳推廣方式</strong><span class="list_desc"><?php echo nl2br($this->item->promotion); ?></span>
                    </li>
                    <li style="display: none;">
                        <strong class="list_title">投票結果運用方式</strong><span class="list_desc"><?php echo $this->item->results_using; ?></span>
                    </li>
                    <li>
                        <strong class="list_title">公布方式</strong><span class="list_desc"><?php echo nl2br($this->item->announcement_method); ?></span>
                    </li>
					<?php if ($this->item->is_define) { ?>
						<?php if (!preg_match("/(0000\-00\-00)/", $this->item->announcement_date)) { ?>
                            <li>
                                <strong class="list_title">公布日期</strong><span class="list_desc"><?php echo JHtml::_('date', $this->item->announcement_date, "Y年n月j日G點i分"); ?>
                        </span></li>
						<?php } else { ?>
                            <li><strong class="list_title">公布日期</strong><span class="list_desc">不公布</span></li>
						<?php } ?>
					<?php } ?>
                    <li>
                        <strong class="list_title">目前進度</strong><span class="list_desc"><?php echo nl2br($this->item->at_present); ?></span>
                    </li>
                    <li>
                        <strong class="list_title">討論管道</strong><span class="list_desc"><?php echo nl2br($this->item->discuss_source); ?></span>
                    </li>
                    <li><strong class="list_title">投票結果運用方式</strong><span class="list_desc">
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
                </span></li>
					<?php
					foreach ($this->data as $key => $item) {
						$download_data[] = '<a href="javascript:void(0)" class="other_data" id="' . $key . '" target="_blank" title="' . $item . '">' . $item . '</a>';
					}
					if (count($download_data) > 0) {
						?>
                        <li><strong class="list_title">其他參考資料</strong><span class="list_desc">
						<?php echo implode("，", $download_data); ?>
                    </span></li>
						<?php
					}
					if ($this->item->other_url) {
						?>
                        <li><strong class="list_title">其他參考網址</strong><span class="list_desc">
                        <a href="<?php echo $this->item->other_url; ?>" target="_blank"><?php echo $this->item->other_url; ?></a>
                    </span></li>
					<?php }
					if ($this->item->followup_caption) {
						?>
                        <li><strong class="list_title">後續辦理情形</strong><span class="list_desc">
						<?php echo nl2br($this->item->followup_caption); ?>
                    </span></li>
					<?php } ?>
                    <li>
                        <strong class="list_title">注意事項</strong><span class="list_desc"><?php echo nl2br($this->item->precautions); ?></span>
                    </li>
                    <li>
                        <strong class="list_title">QR Code</strong><span class="list_desc"><img class="qrcode" src="<?php echo $this->qrcode; ?>" alt="<?php echo $this->qrcode; ?>" />
                </span></li>

                </ul>
            </div>
            <input type="hidden" id="original_name" name="original_name" />
            <input type="hidden" id="file_name" name="file_name" />
            <input type="hidden" id="survey_id" name="survey_id" value="<?php echo (int) $this->survey_id; ?>" />
			<?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>

<div class="vote">
	<?php if ($this->preview == true) { ?>
        <div class="btns">
            <a href="<?php echo $this->next_link; ?>" class="submit">下一頁</a>
        </div>
	<?php } else {
		$date    = JFactory::getDate();
		$nowDate = $date->toSql();
		if (strtotime($this->item->vote_start) < strtotime($nowDate)) {
			if (strtotime($this->item->vote_end) < strtotime($nowDate)) { // 已結束
				if (($this->item->display_result == 1 || $this->item->display_result == 2) && $this->item->is_define == 1 && $this->item->is_checked == 1) {  // 投票結束後顯示結果
					?>
                    <div class="btns">
                        <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->item->id . '&Itemid=' . $this->completed_menuid, false); ?>" class="submit">觀看投票結果</a>
                    </div>
					<?php
				}
			} else { // 進行中
				if ($this->item->is_define == 1 && $this->item->published && $this->item->is_checked) {
					?>
                    <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=intro.start_vote&sid=' . $this->item->id . '&Itemid=' . $this->voting_menuid, false); ?>"><img src="images/system/vote_btn.png" alt="我要投票" title="我要投票" /></a>
					<?php
				}
			}
		} else { // 待投票
			if (($this->item->is_notice_email || $this->item->is_notice_phone) && $this->item->is_define == 1) {
				echo $this->loadTemplate('notice');
			}
		}
	} ?>
</div>
