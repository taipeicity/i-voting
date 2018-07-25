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
define('DS', DIRECTORY_SEPARATOR);


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

$stage_name = [1 => "提案檢核", 2 => "提案初審", 3 => "提案討論", 4 => "形成選項", 5 => "宣傳準備與上架", 6 => "投票&結果"];

if ($this->item->stage >= 4) {
	$options_scale = true;
	$agree         = (int) $this->item->options_agree;
	$oppose        = (int) $this->item->options_oppose;
	if ($agree === 0 and $oppose === 0) {
		$options_scale = false;
	} else {
		$document = JFactory::getDocument();
		$script   = '';
		$document->addCustomTag('<script src="https://www.gstatic.com/charts/loader.js"></script>' . "\n");
		$script .= 'google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                [\'Task\', \'Agree & opopse\'],
                [\'贊成：' . $agree . '\', ' . $agree . '],
                [\'反對：' . $oppose . '\', ' . $oppose . ']
            ]);

            var options = {
                legend: {position: \'left\', textStyle: {color: \'#212121\'}},
                fontSize: 20,
				fontName: \'微軟正黑體\',
                tooltip: { trigger: \'none\' },
                pieSliceText: \'none\',
                slices: {
                    0: { color: \'#edc240\' },
                    1: { color: \'#d0d2d3\' }
                },
                pieHole: 0.5,
                backgroundColor: \'transparent\',
                width: 320,
				height: 100,
                sliceVisibilityThreshold: .0001,
				chartArea: { left: 0, top: 10, width: \'100%\', height: \'70%\' }
        };';

		if ($this->item->stage == 4) {
			$script .= 'options.legend.textStyle = {color: \'#ffffff\'};';
		}

		$script .= '
            var chart = new google.visualization.PieChart(document.getElementById(\'donutchart\'));
            chart.draw(data, options);
        }';
		$document->addScriptDeclaration('jQuery(document).ready(function () {' . $script . '});');
	}
}

?>

<script>
    jQuery(document).ready(function () {
        // var detail = jQuery(".detail");

        jQuery(".getPdf").on("click", function () {
            jQuery("#file_name").val(this.id);
            jQuery("#original_name").val(this.title);
            jQuery("#admin-form").submit();
        });
    });
</script>

<!--這支tmpl前後台都會使用到，所以必須使路徑再前後台都能正常載入-->
<script src="/components/com_surveyforce/webpack/assets/dist/vendors.js?t=<?php echo uniqid() ?>"></script>
<script src="/components/com_surveyforce/webpack/assets/dist/index.js?t=<?php echo uniqid() ?>"></script>

<form id="admin-form" action="<?php echo JRoute::_('index.php?option=com_surveyforce', false); ?>" method="POST">

    <ul id="flows">
		<?php foreach ($stage_name

		               as $stage => $name) { ?>
			<?php
			// 六階段class判斷
			$class = "uncompleted";
			if ($this->item->stage > $stage) {
				$class = "completed";
			} else if ($this->item->stage < $stage) {
				$class = "uncompleted";
			} else {
				$class = "processing";
			}
			?>
            <li>
				<?php $this->item->stage >= $stage ? $tag = "a" : $tag = "span"; ?>
				<?php if ($this->item->stage >= $stage) { ?>
                    <a href="#f<?php echo $stage; ?>" class="<?php echo $class; ?>"><span class="flow"><?php echo $stage; ?></span><span class="flow-text"><?php echo $name; ?></span>
                    </a>
				<?php } else { ?>
                    <span class="<?php echo $class; ?>"><span class="flow"><?php echo $stage; ?></span><span class="flow-text"><?php echo $name; ?></span>
                    </span>
				<?php } ?>
            </li>
		<?php } ?>
    </ul>

	<?php if (!$session->get('practice_pattern') && $this->item->is_public == 1) { ?>
        <div class="page-info">
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


            瀏覽人數：<?php echo $this->item->hits; ?><br />
			<?php echo ($this->finish_votes) ? sprintf("已完成投票人數：%d", $this->finish_votes) : ""; ?>
        </div>
	<?php } ?>


    <figure class="main-figure">
		<?php
		$app = JFactory::getApplication();
		?>
        <div class="main-img-outer">
            <img class="main-img" src="<?php echo DS . $this->item->image; ?>" alt="示意圖：<?php echo $this->item->title; ?>" />
        </div>
		<?php if ($this->item->stage >= 5) { ?>
            <img class="main-finalized" src="<?php echo DS; ?>templates/ch/images/main_pic_decoration.png" alt="已定案" />
		<?php } ?>
    </figure>

    <div class="intro">

        <h2 class="subject"><?php echo $this->item->title; ?></h2>
        <div>
			<?php echo $this->item->desc; ?>
        </div>
    </div>

    <div class="main-section">
		<?php if ($this->item->stage >= 1) { ?>
            <section id="f1" class="section-block <?php echo $this->item->stage == 1 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 1 ? "processing" : "completed"; ?>" href="#flows" title="提案檢核">
                    <h3><span class="flow">1</span> 提案檢核</h3></a>
                <ul class="desc">
                    <li><span class="heading">提案人</span><span class="detail"><?php echo $this->item->proposer; ?></span>
                    </li>

                    <li>
                        <span class="heading">初擬投票議題</span><span class="detail"><?php echo $this->item->plan_quest; ?></span>
                    </li>

                    <li>
                        <span class="heading">初擬選項方案</span><span class="detail"><?php echo nl2br($this->item->plan_options); ?></span>
                    </li>

                    <li>
                        <span class="heading">初擬提案計畫書資料</span> <span class="detail">
                            <?php if ($this->item->proposal_download) { ?>
                                <a href="javascript:void(0)" class="getPdf" id="proposal_download" title="<?php echo $this->item->proposal_download; ?>">
                                <?php echo $this->item->proposal_download; ?>
                            </a>
                            <?php } else { ?>
                                <a href="<?php echo $this->item->proposal_url; ?>" target="_blank">公共政策網路參與平台</a>
                            <?php } ?>
                        </span>
                    </li>
					<?php if ($this->item->precautions) { ?>
                        <li>
                            <span class="heading">注意事項</span><span class="detail"><?php echo nl2br($this->item->precautions); ?>
				</span></li>
					<?php } ?>
					<?php if ($this->item->second_the_motion) { ?>
                        <li>
                            <span class="heading">已附議票數</span><span class="detail"><?php echo $this->item->second_the_motion; ?> 張</span>
                        </li>
					<?php } ?>
					<?php if ($this->item->deadline != "0000-00-00 00:00:00") { ?>
                        <li>
                            <span class="heading">截止時間</span><span class="detail"><?php echo JHtml::_('date', $this->item->deadline, "Y年m月d日 H:i"); ?></span>
                        </li>
					<?php } ?>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>
		<?php } ?>

		<?php if ($this->item->stage >= 2) { ?>

            <section id="f2" class="section-block <?php echo $this->item->stage == 2 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 2 ? "processing" : "completed"; ?>" href="#flows" title="提案初審">
                    <h3><span class="flow">2</span> 提案初審</h3></a>
                <ul class="desc">
                    <li>
                        <span class="heading">初審結果說明</span><span class="detail"><?php echo nl2br($this->item->review_result); ?></span>
                    </li>
                    <li>
                        <span class="heading">初審會議紀錄下載</span> <span class="detail">第一次：
                            <a href="javascript:void(0)" class="getPdf" id="review_download" title="<?php echo $this->item->review_download; ?>">
                                    <?php echo $this->item->review_download; ?>
                            </a>
							<?php
							if ($this->item->review_download_ii) {
								echo "<br>";
								?>
                                第二次：
                                <a href="javascript:void(0)" class="getPdf" id="review_download_ii" title="<?php echo $this->item->review_download_ii; ?>">
                                    <?php echo $this->item->review_download_ii; ?>
                                </a>
								<?php
							}
							?>
                        </span>
                    </li>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>

		<?php } ?>

		<?php if ($this->item->stage >= 3) { ?>

            <section id="f3" class="section-block <?php echo $this->item->stage == 3 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 3 ? "processing" : "completed"; ?>" href="#flows" title="提案討論">
                    <h3><span class="flow">3</span> 提案討論</h3></a>
                <ul class="desc">
                    <li>
                        <span class="heading">討論管道</span><span class="detail"><?php echo nl2br($this->item->discuss_source); ?></span>
                    </li>
                    <li>
                        <span class="heading">議題與選項方案規劃</span><span class="detail">
                            <?php echo nl2br($this->item->discuss_plan_options); ?>
				        </span>
                    </li>
                    <li><span class="heading">投票人資格規劃</span><span class="detail">
				<?php echo nl2br($this->item->discuss_qualifications); ?>
				</span></li>
					<?php
					$discuss_verify = json_decode($this->item->discuss_verify, true);
					$type           = json_decode(SurveyforceVote::getVerifyName($discuss_verify), true);

					?>
                    <li>
                        <span class="heading"><span class="less-spacing">預計投票人驗證方式規劃</span></span><span class="detail"><?php echo implode("、", $type); ?></span>
                    </li>
                    <li>
                        <span class="heading">預計投票時間</span><span class="detail"><?php echo $this->item->discuss_vote_time; ?></span>
                    </li>
                    <li>
                        <span class="heading">預計投票通過門檻</span><span class="detail"><?php echo $this->item->discuss_threshold; ?></span>
                    </li>
                    <li>
                        <span class="heading">提案計畫書下載</span><span class="detail"><a href="javascript:void(0)" id="discuss_download" class="getPdf" title="<?php echo $this->item->discuss_download; ?>"><?php echo $this->item->discuss_download; ?></a></span>
                    </li>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>

		<?php } ?>

		<?php if ($this->item->stage >= 4) { ?>

            <section id="f4" class="section-block <?php echo $this->item->stage == 4 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 4 ? "processing" : "completed"; ?>" href="#flows" title="形成選項">
                    <h3><span class="flow">4</span> 形成選項</h3></a>
                <ul class="desc">
                    <li><span class="heading">議題與選項方案凝聚</span><span class="detail">
                    <?php echo nl2br($this->item->options_cohesion); ?>
				</span>
                    </li>
					<?php if ($options_scale) { ?>
                        <li><span class="heading">討論意見比例</span><span class="detail">
                            <span id="donutchart"></span>
				</span></li>
					<?php } ?>

					<?php // class=heading 文字若超過9個字，需再加一個 class=less-spacing 的 span?>
                    <li><span class="heading"><span class="less-spacing">討論意見綜整說明與回應</span></span><span class="detail">
                    <?php echo nl2br($this->item->options_caption); ?>
                </span></li>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>

		<?php } ?>

		<?php if ($this->item->stage >= 5) { ?>

            <section id="f5" class="section-block <?php echo $this->item->stage == 5 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 5 ? "processing" : "completed"; ?>" href="#flows" title="宣傳準備與上架">
                    <h3><span class="flow">5</span> 宣傳準備與上架</h3></a>
                <ul class="desc">
                    <li><span class="heading">議題與選項方案</span><span class="detail">
                    <?php if ($this->preview == true && !$array_ques) { ?>
                        尚未新增題目
                    <?php } else { ?>
	                    <?php
	                    $y = 1;
	                    foreach ($array_ques as $id => $array_que) {
		                    foreach ($array_que as $title_name => $item) {
			                    if (count($array_ques) > 1) {
				                    echo "第" . $y . "題：";
			                    } else {
				                    echo "議題：";
			                    }
			                    echo "{$title_name}<br>選項方案：";
			                    echo "<ol>";
			                    for ($i = 0; $i < count($item); $i++) {
				                    $j = $i + 1;
				                    echo "<li>" . $j . "." . $item[$i] . "</li>";
			                    }
			                    echo "</ol>";
			                    $y++;
		                    }
	                    }
                    }
                    ?>
                </span></li>
                    <li>
                        <span class="heading">投票人資格</span><span class="detail"><?php echo nl2br($this->item->voters_eligibility); ?></span>
                    </li>
                    <li>
                        <span class="heading">投票人驗證方式</span><span class="detail"><?php echo $this->item->voters_authentication ? $this->item->voters_authentication : "圖形驗證"; ?></span>
                    </li>
                    <li>
                        <span class="heading">投票時間</span><span class="detail"><?php echo $this->item->during_vote; ?></span>
                    </li>
                    <li>
                        <span class="heading">投票方式</span><span class="detail"><?php echo nl2br($this->item->vote_way); ?></span>
                    </li>
                    <li>
                        <span class="heading">投票通過門檻</span><span class="detail"><?php echo nl2br($this->item->launched_condition); ?></span>
                    </li>
					<?php
					switch ($this->item->launched_date) {
						case 1:
							$announcement_date = "不公布";
							break;
						case 2:
							$announcement_date = $this->item->announcement_date;
							break;
						case 3:
							$announcement_date = $this->item->vote_end;
							break;
						default:
							$announcement_date = $this->item->vote_end;
							break;
					}
					?>
                    <li>
                        <span class="heading">投票結果公布日期</span><span class="detail"><?php echo $announcement_date; ?></span>
                    </li>

                    <li>
                        <span class="heading">投票結果運用說明</span> <span class="detail">
                            <?php
                            $results_proportion = ["whole" => "完全參採", "part" => "部分參採", "committee" => "送請專業委員會決策考量", "other" => "其他"];
                            echo $results_proportion[$this->item->results_proportion];
                            if ($this->item->results_proportion == "part") {
	                            echo "<br>";
	                            echo $this->item->part;
                            }

                            if ($this->item->results_proportion == "other") {
	                            echo "<br>";
	                            echo nl2br($this->item->other);
                            }
                            ?>
                        </span>
                    </li>
                    <li>
                        <span class="heading">完整提案計畫書下載</span><span class="detail"><a href="javascript:void(0)" class="getPdf" id="launched_download" title="<?php echo $this->item->launched_download; ?>"><?php echo $this->item->launched_download; ?></a></span>
                    </li>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>

		<?php } ?>

		<?php if ($this->item->stage == 6) { ?>

            <section id="f6" class="section-block <?php echo $this->item->stage == 6 ? "processing-block" : "completed-block"; ?>">
                <a class="<?php echo $this->item->stage == 6 ? "processing" : "completed"; ?>" href="#flows" title="投票 & 結果">
                    <h3><span class="flow">6</span> 投票 & 結果</h3></a>
                <ul class="desc">
                    <li>
                        <span class="heading">投票結果說明</span><span class="detail"><?php echo nl2br($this->item->result_instructions); ?></span>
                    </li>
                    <li>
                        <span class="heading">運用方式說明</span><span class="detail"><?php echo nl2br($this->item->how_to_use); ?></span>
                    </li>
                </ul>
                <a class="back-to-top" href="#flows" title="回流程">回流程</a>
            </section>

		<?php } ?>
    </div>
    <div class="back-prev">
        <a href="javascript:<?php echo $this->preview ? 'void(0)' : 'history.go(-1)'; ?>">回上一頁</a>
    </div>


    <input type="hidden" name="task" value="<?php echo ($this->preview == false ? 'intro' : 'surveys') . '.getPdf' ?>" />
    <input type="hidden" id="original_name" name="original_name" />
    <input type="hidden" id="file_name" name="file_name" />
    <input type="hidden" id="survey_id" name="survey_id" value="<?php echo (int) $this->survey_id; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>


<div class="vote">
	<?php if ($this->preview == true) { ?>
        <div class="btns">
            <a href="<?php echo $this->next_link; ?>" class="submit">下一頁</a>
        </div>
	<?php } else {
		$date    = JFactory::getDate();
		$nowDate = $date->toSql();
		if ($this->item->stage > 5) {
			if (strtotime($this->item->vote_start) < strtotime($nowDate)) {
				if (strtotime($this->item->vote_end) < strtotime($nowDate)) { // 已結束
					if (($this->item->display_result == 1 || $this->item->display_result == 2) && $this->item->is_checked == 1) {  // 投票結束後顯示結果
						?>

                        <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->item->id . '&Itemid=' . $this->completed_menuid, false); ?>" class="submit button btn-result">觀看投票結果</a>

						<?php
					}
				} else { // 進行中
					if ($this->item->published && $this->item->is_checked) {
						?>
                        <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=intro.start_vote&sid=' . $this->item->id . '&Itemid=' . $this->voting_menuid, false); ?>" class="submit button btn-result">我要投票 </a>
						<?php
					}
				}
			} else { // 待投票
				if (($this->item->is_notice_email || $this->item->is_notice_phone)) {
					echo $this->loadTemplate('notice');
				}
			}
		}
	} ?>
</div>


<div class="susy-screen">
    <div class="mobile">mobile</div>
    <div class="pad">pad</div>
    <div class="desktop">desktop</div>
</div>

<script>
    var detail = document.getElementsByClassName("detail"), i;
    for (i = 0; i < detail.length; i++) {
        if (!detail.item(i).innerText.trim()) {
            if (detail.item(i).children['donutchart']) {
                continue;
            }
            detail.item(i).innerHTML = "無資料";
        } else {
            if (detail.item(i).innerText.trim() === "第一次：") {
                detail.item(i).innerText = detail.item(i).innerText.trim() + "無資料";
            }
        }
    }
</script>