<?php

/**
*   @package         Surveyforce
*   @version           1.1-modified
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
$model = $this->getModel();
?>
<?php // echo $this->loadTemplate('menu'); ?>

<script type="text/javascript">
    
    
    Joomla.submitbutton = function(task)
    {
        if (task == 'import.cancel' || document.formvalidator.isValid(document.id('import-form'))) {
            Joomla.submitform(task, document.getElementById('import-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }


	function match_file(fname) {
        farr = fname.toLowerCase().split(".");
        if (farr.length != 0) {
            len = farr.length

            switch (farr[len - 1]) {
                case "csv" :
					break;
				default:
					document.getElementById("input_import_file").value = "";
					alert("請重新選擇檔案，僅允許上傳 cvs 檔案。");
            }
        }
    }

	function check_submit() {
		if (document.getElementById("input_import_file").value == "") {
			alert("請選擇要上傳的檔案。");
			return false;
		}

		document.getElementById("import-form").submit();
	}



	jQuery(document).ready(function() {


	});

    
</script>
<style>

	.title {
		font-size: 16px;
		margin: 10px;
	}

	hr {
		width: 50%;
	}

	.tbl {
		border: 1px solid #ccc;
	}

	.tbl td, .tbl th {
		border: 1px solid #ccc;
		padding: 10px;
	}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=import'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="import-form" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">
		<fieldset>
			<legend>上傳檔案</legend>
			<div class="" style="margin-bottom:20px;">
				請先下載議題CSV檔，更新CSV檔內容欄位後，再做上傳動作。<br>
				*請注意：重新上傳時，一律覆蓋先前已上傳的資料。
			</div>
			<div style="margin-bottom:20px;">
				議題CSV檔：<a href="../<?php echo $this->csv_file; ?>" target="_blank" title="議題CSV檔">請點此下載</a>
			</div>
			<div class="">
				檔案：
				<input style="margin: 5px" type="file" name="import_file" id="input_import_file" onchange="match_file(this.value);">
				
				<input type="button" value="上傳" id="buttonGo" onClick="check_submit()" onKeypress="check_submit()">
			</div>
			<br>
			<?php
			
				if ($this->paper_vote) {
					unset($votes);
					unset($votes_sub);
					foreach ($this->paper_vote as $paper_vote) {
						$votes[$paper_vote->field_id] = $paper_vote->vote_num;
						$votes_sub[$paper_vote->sub_field_id] = $paper_vote->vote_num;
					}

			?>
			<hr>
			<div class="title">目前已匯入的紙本資料</div>
			<table class="tbl">
				<tr>
					<th>題目</th>
					<th>選項</th>
					<th>子選項</th>
					<th>票數</th>
				</tr>
				<?php
				if ($this->questions) {
					foreach ($this->questions as $question) {

						// 檢查是否有子選項
						$sub_options = $model->getSubOptions($question->question_id);

						if ($sub_options) {
							foreach ($sub_options as $sub_option) {		// 子選項
								echo "<tr>";
								echo "<td>". $question->question_title. "</td>";
								echo "<td>". $question->option_title. "</td>";
								echo "<td>". $sub_option->sub_option_title. "</td>";
								echo "<td>". $votes_sub[$sub_option->sub_option_id]. "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr>";
							echo "<td>". $question->question_title. "</td>";
							echo "<td>". $question->option_title. "</td>";
							echo "<td></td>";
							echo "<td>". $votes[$question->option_id]. "</td>";
							echo "</tr>";

						}
					}
				}
			?>
			</table>
			<?php } ?>
		</fieldset>
        
    </div>
    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
    <input type="hidden" name="task" value="import.upload" />
    <input type="hidden" name="option" value="com_surveyforce" />
    
    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

