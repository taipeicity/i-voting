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

<script src="components/com_surveyforce/assets/js/chart/loader.js"></script>
<script type="text/javascript">


    Joomla.submitbutton = function (task) {
        if (task == 'surveys_traffic.cancel' || document.formvalidator.isValid(document.id('surveys_traffic-form'))) {
            Joomla.submitform(task, document.getElementById('surveys_traffic-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };

    jQuery(document).ready(function () {

    });
	
	<?php
		// 有搜尋才畫圖
		if ($this->total_vote > 0) {
	?>
	 google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);
	 
    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('string', '小時');
      data.addColumn('number', '人數');

      data.addRows([
		<?php echo implode(",\n", $this->vote_traffic_data); ?>
      ]);
	  

      var options = {
        chart: {
          title: '每小時投票流量結果圖',
        },
        width: 900,
        height: 500,
		axes: {
          x: {
            0: {side: 'bottom'}
          }
        }
      };

      var chart = new google.charts.Line(document.getElementById('traffic_chart'));

      chart.draw(data, google.charts.Line.convertOptions(options));
    }
	
	<?php
		}
	?>

</script>

<style>
    #j-sidebar-container {
        width: 200px !important;
    }

    .survey_surveys_traffic {
        margin-top: 10px;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=surveys_traffic'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="surveys_traffic-form" class="form-validate">

    <div class="survey_surveys_traffic">
		<fieldset>
			<legend>資料篩選</legend>
			
			<div class="fieldset_block">
				<label for="filter_survey_id" class="inline_block">議題編號</label>
				<div class="inline_block">
					<select name="filter_survey_id" class="input-xxlarge chosen" id="filter_survey_id">
						<?php
							echo JHtml::_('select.options', $this->surveys, "value", "text", $this->filter_survey_id, true);
						?>
					</select>
				</div>

			</div>
			
			<div class="fieldset_block">
				<label for="filter_date" class="inline_block">日期選擇</label>
				<div class="inline_block">
					<?php
						echo JHtml::_('calendar', $this->filter_date, 'filter_date', 'filter_date', '%Y-%m-%d', array('class'=> 'input-medium') );
					?>
				</div>

			</div>


			<input type="submit" value="確定">
			
			<input type="hidden" name="is_search" value="1">
		</fieldset>
		<br>

		<?php
			if ($this->is_search) {
		?>
			<div class="intro_title">
				<?php echo sprintf("總投票人數：%0d", $this->total_vote); ?>
			</div>
			
			<div id="traffic_chart" style="width: 900px; height: 500px"></div>

		<?php
			}
		?>
    </div>

    <input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>


