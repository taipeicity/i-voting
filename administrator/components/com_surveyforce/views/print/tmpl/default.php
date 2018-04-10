<?php
/**
 * @package            Surveyforce
 * @version            1.2-modified
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
$proposal_process[1] = "初審階段";
$proposal_process[2] = "討論階段";
?>

<style>
    .survey_print {
        width: 800px;
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
        max-width: 200px;
        max-height: 200px;
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
        max-width: 200px;
        max-height: 200px;
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
            <td><?php echo $this->survey_item->desc; ?></td>
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
        <tr>
            <th>版型</th>
            <td>
				<?php
				switch ($this->survey_item->layout) {
					case "default":
						echo "上圖下文";
						break;
					case "blog":
						echo "圖文並排";
						break;
					case "text":
						echo "無圖有文";
						break;
				}
				?>
            </td>
        </tr>
        <tr>
            <th>投票方式</th>
            <td><?php echo $this->survey_item->vote_way; ?></td>
        </tr>
        <tr>
            <th>投票人資格</th>
            <td><?php echo $this->survey_item->voters_eligibility; ?></td>
        </tr>
		<?php if ($this->survey_item->is_define) { ?>
            <tr>
                <th>投票人驗證方式</th>
                <td><?php echo $this->survey_item->voters_authentication; ?></td>
            </tr>
		<?php } ?>
		<?php
		if ($this->survey_item->is_define) {
			if ($this->survey_item->verify_precautions) {
				?>
                <tr>
                    <th>驗證方式注意事項說明</th>
                    <td><?php echo $this->survey_item->verify_precautions; ?></td>
                </tr>
				<?php
			}
		}
		?>
		<?php
		if ($this->survey_item->is_define) {
			if (count($verify_type) == 1) {
				?>
                <tr class="not_define">
                    <th>驗證強度</th>
                    <td>
                        <div class="verifylevel_intro">
                            <img src="../images/system/VerifyLevel/verifylevel_<?php echo $level; ?>.svg" />
                        </div>
                    </td>
                </tr>
				<?php
			}
		}
		?>
		<?php if ($this->survey_item->is_define) { ?>
            <tr>
                <th>投票期間</th>
                <td><?php echo $this->survey_item->during_vote; ?></td>
            </tr>
		<?php } ?>
        <tr>
            <th>宣傳推廣方式</th>
            <td><?php echo $this->survey_item->promotion; ?></td>
        </tr>
        <tr style="display: none;">
            <th>投票結果運用方式</th>
            <td><?php echo $this->survey_item->results_using; ?></td>
        </tr>
        <tr>
            <th>公布方式</th>
            <td><?php echo $this->survey_item->announcement_method; ?></td>
        </tr>
		<?php if ($this->survey_item->is_define) { ?>
            <tr>
                <th>公布日期</th>
				<?php if (!preg_match("/(0000\-00\-00)/", $this->survey_item->announcement_date)) { ?>
                    <td><?php echo JHtml::_('date', $this->survey_item->announcement_date, "Y年n月j日G點i分"); ?></td>
				<?php } else { ?>
                    <td>不公布</td>
				<?php } ?>
            </tr>
		<?php } ?>
        <tr>
            <th>目前進度</th>
            <td><?php echo $this->survey_item->at_present; ?></td>
        </tr>
        <tr>
            <th>討論管道</th>
            <td><?php echo $this->survey_item->discuss_source; ?></td>
        </tr>
        <tr>
            <th>投票結果運用方式</th>
            <td>
				<?php
				switch ($this->survey_item->results_proportion) {
					case "whole":
						echo "完全參採";
						break;
					case "part":
						echo "部分參採" . $this->survey_item->part . "%";
						break;
					case "committee":
						echo "送請專業委員會決策考量";
						break;
					case "other":
						echo "其他(" . $this->survey_item->other . ")";
						break;
				}
				?>
            </td>
        </tr>
		<?php if ($this->survey_item->other_data || $this->survey_item->other_data2 || $this->survey_item->other_data3) { ?>
            <tr>
                <th>其他參考資料</th>
                <td>
					<?php
					echo implode("，", $this->other_data);
					?>
                </td>
            </tr>
		<?php } ?>
		<?php if ($this->survey_item->other_url) { ?>
            <tr>
                <th>其他參考網址</th>
                <td><?php echo $this->survey_item->other_url; ?></td>
            </tr>
		<?php } ?>
		<?php if ($this->survey_item->followup_caption) { ?>
            <tr>
                <th>後續辦理情形</th>
                <td><?php echo $this->survey_item->followup_caption; ?></td>
            </tr>
		<?php } ?>
        <tr>
            <th>注意事項</th>
            <td><?php echo $this->survey_item->precautions; ?></td>
        </tr>


        <tr class="not_define">
            <th>上架時間</th>
            <td><?php echo JHtml::_('date', $this->survey_item->publish_up, JText::_('DATE_FORMAT_LC5')); ?></td>
        </tr>
		<?php if ($this->survey_item->is_define) { ?>
            <tr>
                <th>開始投票時間</th>
                <td><?php echo JHtml::_('date', $this->survey_item->vote_start, JText::_('DATE_FORMAT_LC5')); ?></td>
            </tr>
            <tr>
                <th>投票結束時間</th>
                <td><?php echo JHtml::_('date', $this->survey_item->vote_end, JText::_('DATE_FORMAT_LC5')); ?></td>
            </tr>
		<?php } ?>
        <tr>
            <th>是否公開</th>
            <td><?php echo ($this->survey_item->is_public) ? "是" : "否"; ?></td>
        </tr>
		<?php if ($this->survey_item->is_public == 0) { ?>
            <tr>
                <th>非公開投票外框版型</th>
                <td>版型<?php echo $this->survey_item_un_public_tmpl; ?></td>
            </tr>
		<?php } ?>
        <tr>
            <th>是否成案</th>
            <td><?php echo ($this->survey_item->is_define) ? "是" : "否"; ?></td>
        </tr>
        <?php if($this->survey_item->is_define == 0){ ?>
        <tr>
            <th>提案流程</th>
            <td><?php echo $proposal_process[$this->survey_item->proposal_process]; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <th>投票模式</th>
            <td>
				<?php
				if ($this->survey_item->vote_pattern == 1) {
					echo "正式投票";
				} else if ($this->survey_item->vote_pattern == 2) {
					echo "練習投票";
				} else {
					echo "正式投票與練習投票";
				}
				?>
            </td>
        </tr>
		<?php if ($this->survey_item->is_define) { ?>
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
		<?php } ?>
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

		<?php if ($this->survey_item->is_define) { ?>
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
		<?php } ?>

    </table>
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