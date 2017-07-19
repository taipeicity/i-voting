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

// 載入plugin
JPluginHelper::importPlugin('survey', $this->question->question_type);
$className = 'plgSurvey' . ucfirst($this->question->question_type);


?>
<div class="survey_question">
	<div class="question">
		<form id="question_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=place_question&task=place_question.check_question_form&Itemid=' . $this->itemid, false); ?>" >

			<div class="header">
			<?php
				echo ($this->questions_num == 1) ? sprintf("%s", $this->question->sf_qtext) : sprintf("第%d題 - %s", $this->count, $this->question->sf_qtext);
			?>
			</div>


				<?php
					// 載入html和JS
					if (method_exists($className, 'onGetOptionsHtml')) {
						$html = $className::onGetOptionsHtml($this->question, $this->options, $this->sub_options);

						echo $html;
					}
				?>



			<div class="btns">
				<a id="submit_img" class="submit red" href="javascript:void(0);" >
				<?php
					echo ($this->questions_num == 1 || $this->questions_num == $this->count) ? "確定送出" : "下一題";
				?>

				</a>
				<noscript>
					您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
				</noscript>
				
			</div>
			<input type="hidden" name="task" value="place_question.check_question_form">
			<input type="hidden" name="sid" value="<?php echo $this->survey_id; ?>">
			<input type="hidden" name="qid" value="<?php echo $this->question_id; ?>">
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	

</div>

<div id="message_area" style="display: none;">
	<div class="alert alert-message">
		<a class="close" data-dismiss="alert">×</a>
		<h4 class="alert-heading">訊息</h4>
		<div>
			<p id="message_content"></p>
		</div>
	</div>
</div>

<script>
	jQuery.fn.showMessage = function(msg) {
		jQuery('html, body').scrollTop(0);
		jQuery("#message_area #message_content").html(msg);
		jQuery("#system-message-container").html(jQuery("#message_area").html());
		jQuery("#system-message-container").show();
   }


	jQuery(document).ready(function(){

		jQuery("#btn_reset").bind( "click", function() {
			jQuery("#question_form")[0].reset();
			jQuery(".option .stamp").removeClass("active");
			_select_nums = 0;
		});

		jQuery("#submit_img").bind( "click", function() {
			jQuery("#system-message-container").hide();

			// 選項的檢查
			if (jQuery("#question_form").checkField() == false) {
				return false;
			}

			jQuery.fancybox.showLoading();
			jQuery("#question_form").submit();


		});
	});
</script>
<style>
	.survey_question .question > div:nth-child(3n+1) {
		clear: none;
	}
	.survey_question .question > div:nth-child(4n+1) {
		clear: both;
	}

	.survey_question .question > div {
		width: calc(100%/4);
	}

	.survey_question .info .intro {
		height: 280px;
	}

	.survey_question .info .intro > a {
		height: 120px;
	}

	.option .desc {
		cursor: pointer;
		height: 100px;
	}
</style>
