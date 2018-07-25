<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$app = JFactory::getApplication();

$verify_type = json_decode($this->survey_item->verify_type);
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

$document = JFactory::getDocument();

if ($this->survey_item->stage >= 4) {
	$agree  = (int) $this->survey_item->options_agree;
	$oppose = (int) $this->survey_item->options_oppose;

	$options_scale = true;
	if ($agree === 0 and $oppose === 0) {
		$options_scale = false;
	}
}
?>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
	<?php if ($this->survey_item->stage >= 4 && $agree > 0 && $oppose > 0) { ?>
    google.charts.load("current", {packages: ["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Agree & opopse'],
            ['贊成：<?php echo $agree; ?>', <?php echo $agree; ?>],
            ['反對：<?php echo $oppose; ?>', <?php echo $oppose; ?>]
        ]);

        var options = {
            legend: {position: 'left'},
            fontSize: 18,
            fontName: '微軟正黑體',
            tooltip: { trigger: 'none' },
            pieSliceText: 'none',
            slices: {
                0: { color: '#edc240' },
                1: { color: '#d0d2d3' }
            },
            pieHole: 0.5,
            backgroundColor: 'transparent',
            width: 400,
            height: 100,
            sliceVisibilityThreshold: .0001,
            chartArea: { left:0, top:10, width: '100%', height: '80%' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
    }
	<?php } ?>
</script>

<style>
    .survey_print {
        width: 800px;
		font-family: "微軟正黑體", Arial;
		font-size: 14pt;
		line-height: 1.8;
    }

    .item-list {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ccc;
        margin-bottom: 30px;
    }

    .item-list th {
        width: 150px;
        padding: 5px;
        border: 1px solid #ccc;
    }

    .item-list td {
        padding: 5px;
        border: 1px solid #ccc;
    }

    .item-list img {
        max-width: 200px !important;
        max-height: 200px !important;
    }

    .question-list {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ccc;
        margin-bottom: 30px;
    }

    .question-list th {
        width: 100px;
        padding: 5px;
        border: 1px solid #ccc;
    }

    .question-list td {
        padding: 5px;
        border: 1px solid #ccc;
    }

    .question-list img {
        max-width: 200px !important;
        max-height: 200px !important;
    }

    .question-list hr {
        margin: 15px 0;
    }

    .option-list, .suboption-list {
        border-collapse: collapse;
        min-width: 500px;
    }

    .verify_table_module {
        border-collapse: collapse;
        min-width: 300px;
    }
</style>

<div class="survey_print">
    <b>議題資料</b>
    <hr>
    <table border="0" class="item-list">
        <tr>
            <th>名稱</th>
            <td><?php echo $this->survey_item->title; ?></td>
        </tr>

        <tr>
            <th>議題說明</th>
            <td><?php echo nl2br($this->survey_item->desc); ?></td>
        </tr>

        <tr>
            <th>Banner</th>
            <td>
				<?php
				if ($this->survey_item->image) {
					echo '<img src="' . JURI::root() . $this->survey_item->image . '">';
				}
				?>
            </td>
        </tr>

		<?php if ($this->survey_item->stage == 1) { ?>
            <tr>
                <th colspan="2">提案檢核階段</th>
            </tr>

            <tr>
                <th>提案人</th>
                <td><?php echo $this->survey_item->proposer; ?></td>
            </tr>

            <tr>
                <th>初擬投票議題</th>
                <td><?php echo nl2br($this->survey_item->plan_quest); ?></td>
            </tr>

            <tr>
                <th>初擬選項方案</th>
                <td><?php echo nl2br($this->survey_item->plan_options); ?></td>
            </tr>

			<?php
			$proposal = $this->survey_item->proposal_download ? $this->survey_item->proposal_download : $this->survey_item->proposal_url;
			?>
            <tr>
                <th>初擬提案計畫書資料</th>
                <td><?php echo $proposal; ?></td>
            </tr>

			<?php if ($this->survey_item->precautions) { ?>
                <tr>
                    <th>注意事項</th>
                    <td><?php echo nl2br($this->survey_item->precautions); ?></td>
                </tr>
			<?php } ?>

			<?php if ($this->survey_item->second_the_motion) { ?>
                <tr>
                    <th>已附議票數</th>
                    <td><?php echo $this->survey_item->second_the_motion; ?></td>
                </tr>
			<?php } ?>

			<?php if ($this->survey_item->deadline != "0000-00-00 00:00:00") { ?>
                <tr>
                    <th>截止時間</th>
                    <td><?php echo JHtml::_('date', $this->item->deadline, "Y年m月d日 H:i"); ?></td>
                </tr>
			<?php } ?>
		<?php } ?>

		<?php if ($this->survey_item->stage >= 2) { ?>
            <tr>
                <th colspan="2">提案初審階段</th>
            </tr>

            <tr>
                <th>初審結果說明</th>
                <td><?php echo nl2br($this->survey_item->review_result); ?></td>
            </tr>

            <tr>
                <th>初審會議記錄下載</th>
                <td>第一次：<?php echo trim($this->survey_item->review_download); ?>

					<?php
					if ($this->survey_item->review_download_ii) {
						echo "<br>";
						?>
                        第二次：<?php echo trim($this->survey_item->review_download_ii); ?>
						<?php
					}
					?>
                </td>
            </tr>
		<?php } ?>

		<?php if ($this->survey_item->stage >= 3) { ?>
            <tr>
                <th colspan="2">提案討論階段</th>
            </tr>

            <tr>
                <th>討論管道</th>
                <td><?php echo nl2br($this->survey_item->discuss_source); ?></td>
            </tr>

            <tr>
                <th>議題與選項方案規劃</th>
                <td><?php echo nl2br($this->survey_item->discuss_plan_options); ?></td>
            </tr>

            <tr>
                <th>投票人資格規劃</th>
                <td><?php echo nl2br($this->survey_item->discuss_qualifications); ?></td>
            </tr>

            <tr>
                <th>預計投票人驗證方式</th>
                <td><?php echo SurveyforceHelper::getVerifyName($this->survey_item->discuss_verify); ?></td>
            </tr>

            <tr>
                <th>預計投票時間</th>
                <td><?php echo $this->survey_item->discuss_vote_time; ?></td>
            </tr>

            <tr>
                <th>預計投票通過門檻</th>
                <td><?php echo nl2br($this->survey_item->discuss_threshold); ?></td>
            </tr>

            <tr>
                <th>提案計畫書下載</th>
                <td><?php echo trim($this->survey_item->discuss_download); ?></td>
            </tr>
		<?php } ?>

		<?php if ($this->survey_item->stage >= 4) { ?>
            <tr>
                <th colspan="2">形成選項階段</th>
            </tr>

            <tr>
                <th>議題與選項方案凝聚</th>
                <td><?php echo nl2br($this->survey_item->options_cohesion); ?></td>
            </tr>

			<?php if ($options_scale) { ?>
                <tr>
                    <th>討論意見比例</th>
                    <td><span id="donutchart"></span></td>
                </tr>
			<?php } ?>

            <tr>
                <th>討論意見綜整說明與回應</th>
                <td><?php echo nl2br($this->survey_item->options_caption); ?></td>
            </tr>
		<?php } ?>

		<?php if ($this->survey_item->stage >= 5) { ?>
            <tr>
                <th colspan="2">宣傳準備與上架階段</th>
            </tr>

            <tr>
                <th>議題與選項方案</th>
                <td>如下(題目清單)</td>
            </tr>

            <tr>
                <th>投票人資格</th>
                <td><?php echo nl2br($this->survey_item->voters_eligibility); ?></td>
            </tr>

            <tr>
                <th>投票人驗證方式</th>
                <td><?php echo $this->survey_item->voters_authentication; ?></td>
            </tr>

            <tr>
                <th>投票時間</th>
                <td><?php echo $this->survey_item->during_vote; ?></td>
            </tr>

            <tr>
                <th>投票方式</th>
                <td><?php echo nl2br($this->survey_item->vote_way); ?></td>
            </tr>

            <tr>
                <th>投票通過門檻</th>
                <td><?php echo nl2br($this->survey_item->launched_condition); ?></td>
            </tr>

			<?php
			switch ($this->survey_item->launched_date) {
				case 1:
					$announcement_date = "不公布";
					break;
				case 2:
					$announcement_date = $this->survey_item->announcement_date;
					break;
				case 3:
					$announcement_date = $this->survey_item->vote_end;
					break;
				default:
					$announcement_date = $this->survey_item->vote_end;
					break;
			}
			?>
            <tr>
                <th>投票公布日期</th>
                <td><?php echo $announcement_date; ?></td>
            </tr>

            <tr>
                <th>投票結果運用說明</th>
                <td>
					<?php
					$results_proportion = ["whole" => "完全參採", "part" => "部分參採", "committee" => "送請專業委員會決策考量", "other" => "其他"];
					echo $results_proportion[$this->survey_item->results_proportion];
					if ($this->survey_item->results_proportion == "part") {
						echo "<br>";
						echo $this->survey_item->part;
					}

					if ($this->survey_item->results_proportion == "committee") {
						echo "<br>";
						echo nl2br($this->survey_item->committee);
					}
					?>
                </td>
            </tr>

            <tr>
                <th>完整提案計畫書下載</th>
                <td><?php echo trim($this->survey_item->launched_download); ?></td>
            </tr>
		<?php } ?>

		<?php if ($this->survey_item->stage == 6) { ?>
            <tr>
                <th colspan="2">投票、結果公布及執行</th>
            </tr>

            <tr>
                <th>投票結果說明</th>
                <td><?php echo nl2br($this->survey_item->result_instructions); ?></td>
            </tr>

            <tr>
                <th>運用方式說明</th>
                <td><?php echo nl2br($this->survey_item->how_to_use); ?></td>
            </tr>
		<?php } ?>
    </table>

	<?php if ($this->survey_item->stage > 4) { ?>

        <p style="page-break-after:always"></p>
        <b>議題設定</b>
        <hr>
        <table border="0" class="item-list">

            <tr>
                <th>投票數設定</th>
                <td>
					<?php
					$vote_num_params = json_decode($this->survey_item->vote_num_params);
					if ($vote_num_params->vote_num_type == 0) {
						echo "投票期間僅限一票 ";
					} else {
						echo sprintf("驗證條件每%d天%d票", $vote_num_params->vote_day, $vote_num_params->vote_num);
					}
					?>
                </td>
            </tr>
            <tr>
                <th>防止灌票機制</th>
                <td>
					<?php
					if ($vote_num_params->vote_num_protect == 0) {
						echo "同IP 不限制 ";
					} else if ($vote_num_params->vote_num_protect == 1) {
						echo sprintf("同IP 每%d秒內隻能投1票", $vote_num_params->vote_num_protect_time);
					} else {
						echo sprintf("同IP 每天隻能投%d票", $vote_num_params->vote_num_protect_vote);
					}
					?>
                </td>
            </tr>

            <tr>
                <th>電子郵件訊息通知</th>
                <td><?php echo ($this->survey_item->is_notice_email) ? "是" : "否"; ?></td>
            </tr>
			<?php if ($this->survey_item->is_notice_email) { ?>
                <tr>
                    <th>電子郵件-投票前提醒</th>
                    <td><?php echo $this->survey_item->remind_text; ?></td>
                </tr>
                <tr>
                    <th>電子郵件-催票提醒</th>
                    <td><?php echo $this->survey_item->drumup_text; ?></td>
                </tr>
                <tr>
                    <th>電子郵件-投票結束通知提醒</th>
                    <td><?php echo $this->survey_item->end_text; ?></td>
                </tr>
			<?php } ?>
            <tr>
                <th>手機訊息通知</th>
                <td><?php echo ($this->survey_item->is_notice_phone) ? "是" : "否"; ?></td>
            </tr>
			<?php if ($this->survey_item->is_notice_phone) { ?>
                <tr>
                    <th>手機訊息-投票前提醒</th>
                    <td><?php echo $this->survey_item->phone_remind_text; ?></td>
                </tr>
                <tr>
                    <th>手機訊息-催票提醒</th>
                    <td><?php echo $this->survey_item->phone_drumup_text; ?></td>
                </tr>
                <tr>
                    <th>手機訊息-投票結束通知提醒</th>
                    <td><?php echo $this->survey_item->phone_end_text; ?></td>
                </tr>
                <tr>
                    <th>簡訊平台帳號</th>
                    <td><?php echo JHtml::_('utility.decode', $this->survey_item->sms_user); ?></td>
                </tr>
                <tr>
                    <th>簡訊平台密碼</th>
                    <td> (密碼不顯示)</td>
                </tr>
			<?php } ?>

            <tr>
                <th>啟用現地投票</th>
                <td><?php echo ($this->survey_item->is_place) ? "是" : "否"; ?></td>
            </tr>

            <tr>
                <th>驗證方式</th>
                <td>
					<?php
					$verify_type   = json_decode($this->survey_item->verify_type, true);
					$verify_params = json_decode($this->survey_item->verify_params, true);

					if (!is_array($verify_type) || $verify_type[0] == "none") {
						echo "該議題設定為不驗證。";
					} else {
						?>
                        <table border="1" class="verify_table_module">
                            <tr>
                                <th align="center" width="150">驗證項目</th>
                                <th align="center" width="350">備註</th>
                            </tr>
							<?php
							foreach ($verify_type as $type) {
								JPluginHelper::importPlugin('verify', $type);
								$className = 'plgVerify' . ucfirst($type);
								?>
                                <tr>
                                    <td>
										<?php
										echo $className::onGetVerifyName();
										?>
                                    </td>
                                    <td>
										<?php
										// 顯示params
										if (method_exists($className, 'onGetAdminShowParams')) {
											echo $className::onGetAdminShowParams($verify_params);
										}
										?>
                                    </td>
                                </tr>
							<?php } ?>
                        </table>
                        驗證組合方式：<?php echo ($this->survey_item->verify_required) ? "同時" : "擇一"; ?>
						<?php
					}
					?>
                </td>
            </tr>


            <tr>
                <th>票數顯示</th>
                <td>
					<?php
					switch ($this->survey_item->display_result) {
						case 0:
							echo "不顯示";
							break;
						case 1:
							echo "投票中顯示";
							break;
						case 2:
							echo "結束後顯示";
							break;
					}
					?>
                </td>
            </tr>
            <tr>
                <th>投票結果數設定</th>
                <td>
					<?php
					if ($this->survey_item->result_num_type == 0) {
						echo "全部顯示";
					} else {
						echo sprintf("%d個結果", $this->survey_item->result_num);
					}
					?>
                </td>
            </tr>

            <tr>
                <th>是否提供抽獎</th>
                <td><?php echo ($this->survey_item->is_lottery) ? "是" : "否"; ?></td>
            </tr>
        </table>

	<?php } ?>

	<?php if ($this->questions) { ?>
        <p style="page-break-after:always"></p>
        <b>題目清單</b>
        <hr>
		<?php
		foreach ($this->questions as $key => $question) {
			JPluginHelper::importPlugin('survey', $question->question_type);
			?>
            <table border="0" class="question-list">
                <tr>
                    <th>題目</th>
                    <td><?php echo $question->sf_qtext; ?></td>
                </tr>
                <tr>
                    <th>題型</th>
                    <td><?php echo $this->model->getQuestionTypeName($question->question_type); ?></td>
                </tr>
                <tr>
                    <th>選項</th>
                    <td>
						<?php
						$className = 'plgSurvey' . ucfirst($question->question_type);
						if (method_exists($className, 'onGetAdminPrintOptions')) {
							echo $className::onGetAdminPrintOptions($question->id);
						}
						?>
                    </td>
                </tr>
            </table>
			<?php if (($key + 1) < count($this->questions)) { ?>
                <p style="page-break-after:always"></p>
			<?php } ?>


		<?php } ?>
	<?php } ?>
</div>

<script>window.print();</script>