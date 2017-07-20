<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

?>
<?php // echo $this->loadTemplate('menu'); ?>

<script type="text/javascript">
    
    
    Joomla.submitbutton = function(task)
    {
        if (task == 'addend.cancel' || document.formvalidator.isValid(document.id('addend-form'))) {
            Joomla.submitform(task, document.getElementById('addend-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }



	jQuery(document).ready(function() {
		jQuery.fn.showMessage = function (msg) {
            jQuery('html, body').scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
        }

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        }

		jQuery("#idnum_link").fancybox({
			helpers : {
				overlay : {closeClick: false}
			}
		});


		jQuery("#btn_add").bind("click", function () {
			jQuery("#message_area").hideMessage();
			if (!jQuery("#id_num").val()) {
				jQuery("#message_area").showMessage('請填寫身分證字號。');
				jQuery("#id_num").focus();
				return false;
			} else {
				reID = /^[A-Za-z]{1}[1-2]{1}[0-9]{8}$/;
				if (!reID.test(jQuery.trim(jQuery("#id_num").val()))) {
					jQuery("#message_area").showMessage("身分證字號格式錯誤");
					jQuery("#id_num").focus();
					return false;
				}
			}


			if (!jQuery("#birth_date").val()) {
				jQuery("#message_area").showMessage('請填寫民國出生年月日。');
				jQuery("#birth_date").focus();
				return false;
			} else {
				reBirth = /^([0-9]{2,3})(0?[1-9]|1[012])(0?[1-9]|[12][0-9]|3[01])$/;
				if (!reBirth.test(jQuery.trim(jQuery("#birth_date").val()))) {
					jQuery("#message_area").showMessage("民國出生年月日格式錯誤");
					jQuery("#birth_date").focus();
					return false;
				}
			}


			// ajax 上傳資料
			jQuery("#message_area").hideMessage();
			jQuery.ajax({
				url: "../plugins/verify/idnum/admin/ajax_upload_addend.php",
				type: "POST",
				dataType:"json",
				data: {'table_suffix': jQuery("#idnum_table_suffix").val(), 'id_num':jQuery("#id_num").val(), 'birth_date':jQuery("#birth_date").val() },
				cache: false,
				async: false,

				beforeSend: function() {
					jQuery.fancybox.showLoading();
				},
				complete: function() {
					jQuery.fancybox.hideLoading();
				},

				success: function (result) {
					if( result.status == false ){
						jQuery("#message_area").showMessage( result.msg );
						return false;
					} else {
						jQuery("#idnum_content").html( result.content );
						jQuery("#idnum_link").trigger("click");
						jQuery("#id_num").val("");
						jQuery("#birth_date").val("");
						return false;
					}
				},
				error: function (result) {
					jQuery("#message_area").showMessage("新增失敗。");
					return false;
				}
			});



		});



		jQuery("#btn_query").bind("click", function () {
			jQuery("#message_area").hideMessage();

			// ajax 查詢
			jQuery("#message_area").hideMessage();
			jQuery.ajax({
				url: "../plugins/verify/idnum/admin/ajax_query_data.php",
				type: "POST",
				dataType:"json",
				data: {'table_suffix': jQuery("#idnum_table_suffix").val()},
				cache: false,
				async: false,

				beforeSend: function() {
					jQuery.fancybox.showLoading();
				},
				complete: function() {
					jQuery.fancybox.hideLoading();
				},

				success: function (result) {
					if( result.status == false ){
						jQuery("#message_area").showMessage( result.msg );
						return false;
					} else {
						jQuery("#idnum_content").html( result.content );
						jQuery("#idnum_link").trigger("click");
						return false;
					}
				},
				error: function (result) {
					jQuery("#message_area").showMessage("查詢失敗。");
					return false;
				}
			});



		});


	});

    
</script>
<style>
	#id_num {
		text-transform:uppercase;
	}

	.note {
		margin-bottom: 20px;
	}

	.field {
		margin-bottom: 10px;
	}

	.idnum_review_table {
		border: 1px solid #ccc;
	}

	.idnum_review_table td {
		border: 1px solid #ccc;
		padding: 5px;
	}
	
</style>

<div id="message_area" style="display: none;">
    <div id="system-message" class="alert alert-error">
        <h4 class="alert-heading"></h4>
        <div>
            <p id="message_content"></p>
        </div>
    </div>
</div>


<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=addend'); ?>" method="post" name="adminForm" id="addend-form" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">
		<fieldset>
			<legend>補送選票</legend>
			<div class="note">
				請於下述欄位中填寫身分證字號和民國出生年。<br>
			</div>
			
			<div class="field">
				身分證字號：
				<input type="text" id="id_num" name="id_num" placeholder="例：A234567890" maxlength="10" autocomplete="off">
			</div>
			<div class="field">
				民國出生年月日：
				<input type="text" id="birth_date" name="birth_date" placeholder="例：560701" maxlength="7" autocomplete="off">
			</div>
			<div class="field">
				<input type="button" id="btn_add" value="新增資料">&nbsp;&nbsp;
				<input type="button" id="btn_query" value="查看已補送資料">
			</div>

		
		</fieldset>
        
    </div>
    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
    <input type="hidden" id="idnum_table_suffix" value="<?php echo $this->verify_params->idnum->idnum_table_suffix; ?>" />
    <input type="hidden" name="task" value="addend.upload" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<a href="#idnum_zone" id="idnum_link" title="預覽畫面" style="display: none;">預覽畫面</a>
<div id="idnum_zone" style="display: none; width:600px;">
	<div id="idnum_message" style="color:red;"></div>
	<div id="idnum_content">
	</div>
</div>

