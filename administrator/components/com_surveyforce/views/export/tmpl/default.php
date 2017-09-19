<?php
/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$app = JFactory::getApplication();
$surv_id = $app->input->getInt("surv_id");

$quest_index = 0;  // 第幾題用
$field_count = array (); // 票數
$total_count = array (); // 總票數
$result_num = $this->item->result_num; // 顯示數目
$qtype = array ("select", "number", "table"); // 有子選項的題目類型
?>

<script type="text/javascript">


	Joomla.submitbutton = function (task)
	{
		if (task == 'export.cancel' || document.formvalidator.isValid(document.id('export-form'))) {
			Joomla.submitform(task, document.getElementById('export-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}

	jQuery(document).ready(function () {

	});

	function check_export() {
		jQuery("#export-form").prop("action", "<?php echo JRoute::_('index.php?option=com_surveyforce&view=export&layout=exportdata&surv_id=' . $surv_id, false); ?>");
		jQuery("#export-form").submit();
	}

</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=export'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="export-form" class="form-validate">

	<div class="survey_export">
		<input type="button" id="submit_export" class="btn" value="匯出CSV檔案" onclick="check_export()" <?php
		if (empty($this->results)) {
			echo "disabled";
		}
		?> /><br/><br/>

		<div class="survey_title">
			<?php echo $this->item->title; ?>
		</div>

		<div class="survey_total">
			投票人數：
			<?php echo ($this->total_num) ? $this->total_num : 0; ?>
		</div>

		<?php if ($this->results) { ?>
			<?php
			foreach ($this->results as $key => $result) {
				$quest_index++;
				?>
				<hr class="hr-condensed">
				<div class="result_block">
					<div class="quest_title">
						<?php
						echo (count($this->results) > 1) ? "第" . $quest_index . "題：" : "題目：";
						echo $result->quest_title;
						?>
					</div>
					<div class="quest_result">
						<?php //題目類型 text、img、textimg ?>
						<?php if (!in_array($result->quest_type, $qtype)) { ?>
							<?php //開放式欄位 ?>
							<?php if ($result->quest_type == 'open') { ?>
								<div class="qtable">
									<table class="table">
										<tr><th>開放式欄位</th></tr>
										<?php foreach ($this->open as $open) { ?>
											<tr>
												<td><?php echo $open->other; ?></td>
											</tr>
										<?php } ?>
									</table>
								</div>
								<?php
								continue;
							}
							// $key: question_id 
							// $fkey、$ckey: field_id
							?>
							<?php // table ?>
							<div class="qtable">
								<table class="table">
									<thead>
										<tr>
											<?php //是否有紙本投票 ?>
											<?php if (!$this->paper[$key]) { ?>
												<th>投票類別</th>
												<th>得票數</th>
											<?php } else { ?>
												<th>投票類別</th>
												<th>網路</th>
												<th>紙本</th>
												<th>總得票數</th>
											<?php } ?>
										</tr>
									</thead>

									<tbody>
										<?php
										unset($field_count);
										unset($total_count);
										foreach ($this->fields[$key] as $fkey => $field) {
											$field_count[$fkey] = ($result->count[$fkey]) ? ($result->count[$fkey]) : 0;

											// 是否有紙本投票
											if ($this->paper[$key]) {
												$total_count[$fkey] = $field_count[$fkey] + $this->paper[$key][$fkey];
											}
										}

										// 依票數排序
										if ($this->item->result_orderby == 1) {
											// 是否有紙本投票
											if ($this->paper[$key]) {
												arsort($total_count);
											} else {
												arsort($field_count);
											}
										}

										$num = 0;

										foreach (($this->paper[$key]) ? $total_count : $field_count as $ckey => $count) {
											$field_name = $this->fields[$key][$ckey];
											?>

											<tr>
												<?php // 是否有紙本投票 ?>
												<?php if (!$this->paper[$key]) { ?>

													<td><?php echo $field_name; ?></td>
													<td><?php echo $count; ?></td>

												<?php } else { ?>

													<td><?php echo $field_name; ?></td>
													<td><?php echo $field_count[$ckey]; ?></td>
													<td><?php echo $this->paper[$key][$ckey]; ?></td>
													<td><?php echo $count; ?></td>

												<?php } ?>
											</tr>

											<?php
											// 顯示數目
											$num++;
										}
										?>
									</tbody>
								</table>
							</div>

							<?php //題目類型 select、number、table ?>
						<?php } else { ?>
							<?php
							// $key: question_id 
							// $fkey: field_id
							// $sfkey、$ckey: sub_field_id
							?>
							<?php foreach ($result->field_title as $fkey => $field_title) { ?>
								<div class="ftitle"><?php echo $field_title; ?></div>

								<?php //table ?>
								<div class="qtable">
									<table class="table">
										<thead>
											<tr>
												<?php if (!$this->sub_paper[$fkey]) { ?>
													<th>投票類別</th>
													<th>得票數</th>
												<?php } else { ?>
													<th>投票類別</th>
													<th>網路</th>
													<th>紙本</th>
													<th>總得票數</th>
												<?php } ?>
											</tr>
										</thead>

										<tbody>
											<?php
											unset($field_count);
											unset($total_count);
											foreach ($this->sub_fields[$key] as $sfkey => $sub_field) {
												$index = $fkey . "_" . $sfkey;
												$field_count[$sfkey] = ($this->sub_results[$index]->count) ? ($this->sub_results[$index]->count) : 0;

												// 是否有紙本投票
												if ($this->sub_paper[$fkey]) {
													$total_count[$sfkey] = $field_count[$sfkey] + $this->sub_paper[$fkey][$sfkey];
												}
											}

											// 依票數排序
											if ($this->item->result_orderby == 1) {
												// 是否有紙本投票
												if ($this->sub_paper[$fkey]) {
													arsort($total_count);
												} else {
													arsort($field_count);
												}
											}

											$num = 0;

											foreach (($this->sub_paper[$fkey]) ? $total_count : $field_count as $ckey => $count) {
												// for pie charts
												$field_name = $this->sub_fields[$key][$ckey];
												?>
												<tr>
													<?php // 是否有紙本投票 ?>
													<?php if (!$this->sub_paper[$fkey]) { ?>

														<td><?php echo $field_name; ?></td>
														<td><?php echo $count; ?></td>

													<?php } else { ?>

														<td><?php echo $field_name; ?></td>
														<td><?php echo $field_count[$ckey]; ?></td>
														<td><?php echo $this->sub_paper[$fkey][$ckey]; ?></td>
														<td><?php echo $count ?></td>

													<?php } ?>
												</tr>

												<?php
												// 顯示數目
												$num++;
											}
											?>
										</tbody>
									</table>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<?php
			}
		} else {
			echo "尚無資料顯示";
		}
		?>
	</div>

    <input type="hidden" name="task" value = "" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<style>
	#j-sidebar-container {
		width: 200px !important;
	}

	.survey_export {
		margin-top: 10px;
	}
</style>
