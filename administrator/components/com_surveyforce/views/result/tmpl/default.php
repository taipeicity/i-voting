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
//JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

$quest_index = 0;  // 第幾題用
$field_count = array (); // 票數
$total_count = array (); // 總票數
$result_num = $this->item->result_num; // 顯示數目
$qtype = array ("select", "number", "table"); // 有子選項的題目類型
?>

<script type="text/javascript">


	Joomla.submitbutton = function (task)
	{
		if (task == 'result.cancel' || document.formvalidator.isValid(document.id('result-form'))) {
			Joomla.submitform(task, document.getElementById('result-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}

	jQuery(document).ready(function () {


	});


</script>
<style>
    .survey_result {
        width: 500px;
    }

    .survey_title {
        font-size: 18px;
        font-weight: bold;
    }

    .quest_title {
        font-size: 16px;
    }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=result'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="result-form" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">

    </div>
    <input type="hidden" name="task" value = "" />
    <input type="hidden" name="option" value="com_surveyforce" />

    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div class="survey_result">
    <div class="survey_title">
		<?php echo $this->item->title; ?>
    </div>
	<?php if ($this->total_voters) { ?>
		<hr class="hr-condensed">
		<div class="survey_total_voters">
			網路<?php echo ($this->item->is_place) ? "與現地" : ""; ?>總投票人數：<?php echo $this->total_voters; ?>
		</div>

		<?php
		$quest_index = 0;
		foreach ($this->questions as $key => $question) {
			$quest_index++;
			?>
			<div class="result_block">
				<div class="quest_title">
					<?php
					echo (count($this->questions) > 1) ? "第" . $quest_index . "題：" : "";
					echo $question->quest_title;
					?>
				</div>

				<div class="quest_result">
					<?php // 開放式欄位 ?>
					<?php if ($question->quest_type == "open") { ?>
						<div class="qtable">
							<table class="table">
								<thead>
									<tr><th>開放式欄位</th></tr>
								</thead>
								<tbody>
									<?php
									foreach ($this->open as $open) {
										if ($open->question_id == $key) {
											?>
											<tr>
												<td><?php echo $open->other; ?></td>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
							</table>
						</div>
						<?php
					} else {
						unset($field_count);
						unset($total_count);

						if ($this->sub_fields[$key]) {

							// 題目類型 select、number、table
							foreach ($this->fields[$key] as $fkey => $field) {

								foreach ($this->sub_fields[$key] as $sfkey => $sub_field) {
									$index = $fkey . "_" . $sfkey;
									$field_count[$sfkey] = ($this->sub_results[$index]->count) ? ($this->sub_results[$index]->count) : 0;
									$total_count[$sfkey] = $field_count[$sfkey];

									// 是否有紙本投票
									if ($this->sub_paper[$fkey]) {
										$total_count[$sfkey] += $this->sub_paper[$fkey][$sfkey];
									}

									// 是否有現地投票
									if ($this->sub_place[$fkey]) {
										$total_count[$sfkey] += $this->sub_place[$fkey][$sfkey];
									}
								}
								?>
								<div class="ftitle"><?php echo $field; ?></div>
								<table class="table">
									<thead>
										<tr>
											<th><?php echo ($question->quest_type == "number") ? "投票分數" : "投票類別"; ?></th>
											<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
												<th>網路</th>
											<?php } ?>
											<?php if ($this->item->is_place == 1) { ?>
												<th>現地</th>
											<?php } ?>
											<?php if ($this->sub_paper[$fkey]) { ?>
												<th>紙本</th>
											<?php } ?>
											<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
												<th>總得票數</th>
											<?php } else { ?>
												<th>得票數</th>
											<?php } ?>
										</tr>
									</thead>

									<?php
									foreach ($total_count as $ckey => $count) {

										$field_name = $this->sub_fields[$key][$ckey]; // 題目類型 select、number、table
										$place_votes = $this->sub_place[$fkey][$ckey];
										$paper_votes = $this->sub_paper[$fkey][$ckey];
										?>
										<tbody>
											<tr>
												<td class="cat">
													<?php echo $field_name; ?>
												</td>
												<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
													<td><?php echo $field_count[$ckey]; ?></td>
												<?php } ?>
												<?php if ($this->item->is_place == 1) { ?>
													<td><?php echo sprintf("%0d", $place_votes); ?></td>
												<?php } ?>
												<?php if ($this->sub_paper[$fkey]) { ?>
													<td><?php echo sprintf("%0d", $paper_votes); ?></td>
												<?php } ?>

												<td><?php echo $count; ?></td>
											</tr>
											<?php
										}
										?>
									</tbody>
								</table>
								<?php
							}
						} else {

							// 題目類型 text、img、textimg
							foreach ($this->fields[$key] as $fkey => $field) {
								$field_count[$fkey] = (int) $this->results[$key]->count[$fkey];
								$total_count[$fkey] = $field_count[$fkey];

								// 是否有紙本投票
								if ($this->paper[$key]) {
									$total_count[$fkey] += $this->paper[$key][$fkey];
								}

								// 是否有現地投票
								if ($this->place[$key]) {
									$total_count[$fkey] += $this->place[$key][$fkey];
								}
							}
							?>
							<div class="ftitle"><?php // echo $field;             ?></div>
							<table class="table">
								<thead>
									<tr>
										<th><?php echo ($question->quest_type == "number") ? "投票分數" : "投票類別"; ?></th>
										<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
											<th>網路</th>
										<?php } ?>
										<?php if ($this->item->is_place == 1) { ?>
											<th>現地</th>
										<?php } ?>
										<?php if ($this->paper[$key]) { ?>
											<th>紙本</th>
										<?php } ?>
										<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
											<th>總得票數</th>
										<?php } else { ?>
											<th>得票數</th>
										<?php } ?>
									</tr>
								</thead>
								<?php
								foreach ($total_count as $ckey => $count) {
									$field_name = $this->fields[$key][$ckey];  // 題目類型 text、img、textimg
									$place_votes = $this->place[$key][$ckey];
									$paper_votes = $this->paper[$key][$ckey];
									?>
									<tbody>
										<tr>
											<td class="cat">
												<?php echo $field_name; ?>
											</td>
											<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
												<td><?php echo $field_count[$ckey]; ?></td>
											<?php } ?>
											<?php if ($this->item->is_place == 1) { ?>
												<td><?php echo sprintf("%0d", $place_votes); ?></td>
											<?php } ?>
											<?php if ($this->paper[$key]) { ?>
												<td><?php echo sprintf("%0d", $paper_votes); ?></td>
											<?php } ?>

											<td><?php echo $count; ?></td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
							<?php
						}
					}
					?>
				</div>
			</div>

			<?php
		}
	} else {
		echo "尚無投票資料。";
	}
	?>
</div>
