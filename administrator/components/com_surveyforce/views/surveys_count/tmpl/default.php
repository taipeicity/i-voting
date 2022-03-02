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

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', '.chosen');

?>

<script type="text/javascript">
	// survey array
	var survey_id = [];
	var survey_title = [];
	<?php
		$count = 0;
		$tmp_year = 0;
		foreach ($this->year_surveys as $year => $surveys) {
			echo "survey_id[". $year. "] = [];\n";
			echo "survey_title[". $year. "] = [];\n";
			$count = 0;

			foreach ($surveys as $survey) {
				echo "survey_id[". $year. "][". $count. "] = \"". $survey["id"]. "\";\n";
				echo "survey_title[". $year. "][". $count. "] = \"". $survey["title"]. "\";\n";
				$count++;
			}
			
		}
	?>
		
		// 服務下拉選單
	function change_year( year ) {
		if (year == "") {
			return false;
		}

		document.getElementById("filter_survey_ids").selectedIndex = 0;
		for(ctr = 0; ctr < survey_title[year].length; ctr++) {
			document.getElementById("filter_survey_ids").options[ctr] = new Option(survey_title[year][ctr], survey_id[year][ctr]);
		}
		document.getElementById("filter_survey_ids").length = survey_title[year].length;
	
		jQuery("#filter_survey_ids").trigger("liszt:updated");
	}
		
    Joomla.submitbutton = function (task) {
        if (task == 'surveys_count.cancel' || document.formvalidator.isValid(document.id('surveys_count-form'))) {
            Joomla.submitform(task, document.getElementById('surveys_count-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };

    jQuery(document).ready(function () {
		// 預設年份
		jQuery("#filter_year").trigger("change");
		
		<?php
			if ($this->is_search && $this->filter_survey_ids) {
		?>
				jQuery("#filter_survey_ids").val([<?php echo implode(",", $this->filter_survey_ids); ?>]);
				jQuery("#filter_survey_ids").trigger("liszt:updated");
		<?php
			}
		?>
			
    });

</script>

<style>
    #j-sidebar-container {
        width: 200px !important;
    }

    .survey_surveys_count {
        margin-top: 10px;
    }
</style>


<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=surveys_count'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="surveys_count-form" class="form-validate">

    <div class="survey_surveys_count">
		<fieldset>
			<legend>資料篩選</legend>
			<div class="fieldset_block">請先選擇資料年份後再選擇要排除的議題，排除之議題將不會列入計算數據</div>
			<div class="fieldset_block">
				<label for="filter_year" class="inline_block">資料年份</label>
				<select id="filter_year" class="input-medium" name="filter_year" onchange="change_year(this.value)">
					<?php
						echo JHtml::_('select.options', $this->years, "value", "text", $this->filter_year, true);
					?>
				</select>
			</div>

			<div class="fieldset_block">
				<label for="filter_survey_ids" class="inline_block">排除議題編號</label>
				<select id="filter_survey_ids" name="filter_survey_ids[]" class="input-xxlarge chosen" multiple>
				</select>
			</div>


			<input type="submit" id="submit_btn" value="確定">
			
			<input type="hidden" name="is_search" value="1">
		</fieldset>
		<br>

		<?php
			if ($this->is_search) {
		?>
			<div class="intro_title">
				<?php echo sprintf("統計議題數：%0d", $this->total_survey); ?>
			</div>
			<div class="intro_title">
				<?php echo sprintf("總投票人數：%0d", $this->total_vote); ?>
			</div>
			
		
			<h3>依驗證方式</h3>
			<table class="table">
				<thead>
				<tr>
					<th>分析選項</th>
					<th>網站</th>
					<th>台北通</th>
					<th>總計</th>
				</tr>
				</thead>

				<tbody>

				<?php 
					$api_total = 0;
					$website_total = 0;
					foreach ($this->verify_types as $element => $name) { 
				?>
					<tr>
						<td><?php echo $name; ?></td>
						<td><?php echo ($this->verify[$element] - $this->api_verify[$element]); ?></td>
						<td><?php echo intval($this->api_verify[$element]); ?></td>
						<td><?php echo intval($this->verify[$element]); ?></td>
					</tr>
				<?php 
						$api_total += $this->api_verify[$element];
						$total += $this->verify[$element];
					} 
				?>
				<tr>
					<td>總共</td>
					<td><?php echo $total - $api_total; ?></td>
					<td><?php echo $api_total; ?></td>
					<td><?php echo $total ?></td>
				</tr>
				</tbody>
			</table>


			<h3>性別分析</h3>
			<table class="table">
				<thead>
				<tr>
					<th>分析選項</th>
					<th>網站</th>
					<th>台北通</th>
					<th>總計</th>
				</tr>
				</thead>

				<tbody>
				<tr>
					<td>男</td>
					<td><?php echo ($this->male - $this->api_male); ?></td>
					<td><?php echo $this->api_male; ?></td>
					<td><?php echo $this->male; ?></td>
				</tr>
				<tr>
					<td>女</td>
					<td><?php echo ($this->female - $this->api_female); ?></td>
					<td><?php echo $this->api_female; ?></td>
					<td><?php echo $this->female; ?></td>
				</tr>
				<tr>
					<td>總共</td>
					<td><?php echo ($this->male + $this->female - $this->api_male - $this->api_female); ?></td>
					<td><?php echo ($this->api_male + $this->api_female); ?></td>
					<td><?php echo ($this->male + $this->female); ?></td>
				</tr>
				</tbody>
			</table>

			<h3>年齡分析</h3>
			<table class="table">
				<thead>
				<tr>
					<th>分析選項</th>
					<th>網站</th>
					<th>台北通</th>
					<th>總計</th>
				</tr>
				</thead>

				<tbody>

				<?php 
					$api_total = 0;
					$website_total = 0;
					foreach ($this->age_label as $age_id => $age_label) {
						if ($age_id == 9) {	// 超過90歲
					?>
						<tr>
							<td><?php echo $age_label; ?></td>
							<td><?php echo ($this->age[9] + $this->age[0] - $this->api_age[9] - $this->api_age[0]); ?></td>
							<td><?php echo intval($this->api_age[9] + $this->api_age[0]); ?></td>
							<td><?php echo intval($this->age[9] + $this->age[0]); ?></td>
						</tr>
					<?php 
							$api_total += $this->api_age[9] + $this->api_age[0];
							$website_total += $this->age[9] + $this->age[0];
						} else {
				?>
					<tr>
						<td><?php echo $age_label; ?></td>
						<td><?php echo ($this->age[$age_id] - $this->api_age[$age_id]); ?></td>
						<td><?php echo intval($this->api_age[$age_id]); ?></td>
						<td><?php echo intval($this->age[$age_id]); ?></td>
					</tr>
				<?php 
							$api_total += $this->api_age[$age_id];
							$website_total += $this->age[$age_id];
						}
					}
				?>
					
				<tr>
					<td>總共</td>
					<td><?php echo ($website_total - $api_total); ?></td>
					<td><?php echo $api_total; ?></td>
					<td><?php echo $website_total; ?></td>
				</tr>
				</tbody>
			</table>
		<?php
			}
		?>
    </div>

    <input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>

