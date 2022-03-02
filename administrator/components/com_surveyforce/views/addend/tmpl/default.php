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

$user                = JFactory::getUser();
$user_id             = $user->get('id');
$user_name           = $user->get('name');
$idnum_table_suffix  = $this->verify_params->idnum->idnum_table_suffix;
$assign_table_suffix = $this->verify_params->assign->assign_table_suffix;

?>

<script type="text/javascript">


    Joomla.submitbutton = function (task) {
        if (task == 'addend.cancel' || document.formvalidator.isValid(document.id('addend-form'))) {
            Joomla.submitform(task, document.getElementById('addend-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };


    jQuery(document).ready(function () {

        jQuery.fn.showMessage = function (msg, target = null) {
            jQuery('html, body').scrollTop(0);
            jQuery('#message_area #message_content').html(msg);
            jQuery('#system-message-container').html(jQuery('#message_area').html());
            if (target) {
                target.addClass('invalid');
            }
        };

        jQuery.fn.hideMessage = function () {
            jQuery('#system-message-container').html("");
        };

        jQuery('#search_link').fancybox({
            helpers: {
                overlay: {closeClick: false}
            }
        });
		
		// 載入時預設值
		jQuery("#label_jform_idnum_addend_type0").trigger("click");
		jQuery("#idnum_once_zone").show();
		jQuery("#label_jform_assign_addend_type0").trigger("click");
		jQuery("#assign_once_zone").show();
		jQuery("#label_jform_any_addend_type0").trigger("click");
		jQuery("#any_once_zone").show();
		
		jQuery('input[name="jform[idnum_addend_type]"]').change(function() {
			if (this.value == "1") {
				jQuery("#idnum_once_zone").show();
				jQuery("#idnum_batch_zone").hide();
			} else {
				jQuery("#idnum_once_zone").hide();
				jQuery("#idnum_batch_zone").show();
			}
		});
		
		jQuery('input[name="jform[assign_addend_type]"]').change(function() {
			if (this.value == "1") {
				jQuery("#assign_once_zone").show();
				jQuery("#assign_batch_zone").hide();
			} else {
				jQuery("#assign_once_zone").hide();
				jQuery("#assign_batch_zone").show();
			}
		});
		
		jQuery('input[name="jform[any_addend_type]"]').change(function() {
			if (this.value == "1") {
				jQuery("#any_once_zone").show();
				jQuery("#any_batch_zone").hide();
			} else {
				jQuery("#any_once_zone").hide();
				jQuery("#any_batch_zone").show();
			}
		});


		// 新增單筆
        jQuery('.btn_add').bind('click', function () {

            var verify_data = {};
            jQuery('#message_area').hideMessage();
			_type = jQuery(this).data("type");
			
            switch (_type) {

                case 'idnum':

                    var id_num = jQuery('#id_num');
                    if (!id_num.val()) {
                        jQuery('#message_area').showMessage('請填寫身分證/居留證號。', id_num);
                        return false;
                    } else {
						if ( !checkIdNumber(jQuery.trim(jQuery.trim(id_num.val()))) ) {
                            jQuery('#message_area').showMessage('身分證/居留證號格式錯誤', id_num);
                            return false;
                        }
                    }

                    var birth_day = jQuery('#birth_date');
                    if (!birth_day.val()) {
                        jQuery('#message_area').showMessage('請填寫民國出生年月日。', birth_day);
                        return false;
                    } else {
                        reBirth = /^([0-9]{2,3})(0?[1-9]|1[012])(0?[1-9]|[12][0-9]|3[01])$/;
                        if (!reBirth.test(jQuery.trim(birth_day.val()))) {
                            jQuery('#message_area').showMessage('民國出生年月日格式錯誤', birth_day);
                            return false;
                        }
                    }

                    verify_data.table_suffix = jQuery('#idnum_table_suffix').val();
                    verify_data.id_num = id_num.val();
                    verify_data.birth_date = birth_day.val();
                    verify_data.user_id = '<?php echo $user_id; ?>';
                    verify_data.user_name = '<?php echo $user_name; ?>';

                    break;

                case 'assign':
                    for (var i = 0; i < jQuery('#assign').find('.assign_column').length; i++) {
                        if (!jQuery('#assign').find('.assign_column')[i].value) {
                            var title = jQuery("#" + _type).find('.assign_column')[i].title;
                            var id = jQuery("#" + _type).find('input')[i].id;
                            jQuery('#message_area').showMessage('請填寫' + title, jQuery('#' + id));
                            return false;
                        }
						
                        verify_data[jQuery("#" + _type).find('.assign_column')[i].title] = jQuery("#" + _type).find('.assign_column')[i].value;
                    }

                    verify_data.table_suffix = jQuery('#assign_table_suffix').val();
                    verify_data.user_id = '<?php echo $user_id; ?>';
                    verify_data.user_name = '<?php echo $user_name; ?>';

                    break;

                case 'any':
                    var school_name = jQuery('#school_name');
                    if (!school_name.val()) {
                        jQuery('#message_area').showMessage('請填寫學校名稱。', school_name);
                        return false;
                    }

                    verify_data.table_suffix = jQuery('#school_table_suffix').val();
                    verify_data.school_name = school_name.val();
                    verify_data.user_id = '<?php echo $user_id; ?>';
                    verify_data.user_name = '<?php echo $user_name; ?>';
                    break;
                default:
                    jQuery('#message_area').showMessage('新增失敗');
                    return false;
            }

            // ajax 上傳資料
            jQuery('#message_area').hideMessage();
            jQuery.ajax({
                url: '../plugins/verify/' + _type + '/admin/ajax_upload_addend.php',
                type: 'POST',
                dataType: 'json',
                data: verify_data,
                cache: false,
                async: false,

                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },

                success: function (result) {
                    if (result.status == false) {
                        jQuery('#message_area').showMessage(result.msg);
                        return false;
                    } else {
                        jQuery('#search_content').html(result.content);
                        jQuery('#search_link').trigger('click');
                        jQuery('#btn_reset').trigger('click');
                        return false;
                    }
                },
                error: function (result) {
                    jQuery('#message_area').showMessage('新增失敗。');
                    return false;
                }
            });


        });
		
		
		// 新增批次上傳
        jQuery('.btn_upload').bind('click', function () {
			_type = jQuery(this).data("type");
            jQuery('#message_area').hideMessage();			
			
            switch (_type) {
                case 'idnum':
					var upload_file = jQuery('#idnum_upload_file');
					
                    break;
                case 'assign':
					var upload_file = jQuery('#assign_upload_file');
					
                    break;
                case 'any':
					var upload_file = jQuery('#any_upload_file');
					
                    break;
                default:
                    jQuery('#message_area').showMessage('新增失敗');
                    return false;
            }
			
			fname = upload_file.val();
			farr = fname.toLowerCase().split(".");
			if (farr.length != 0) {
				len = farr.length;

				switch (farr[len - 1]) {
					case "csv" :
						break;
					default:
						jQuery("#message_area").showMessage('請重新選擇檔案，僅允許上傳 CSV 檔案。', upload_file);
						return false;

				}
			}

			if (upload_file[0].files[0].size > 10485760) {		//假如檔案大小超過10MB)
				jQuery("#message_area").showMessage('附件檔超過指定大小(10MB)。', upload_file);
				return false;
			}


            // ajax 上傳資料
            jQuery('#message_area').hideMessage();
			var formData = new FormData(jQuery("#addend-form")[0]);
            jQuery.ajax({
                url: '../plugins/verify/' + _type + '/admin/ajax_upload_addend_file.php',
                type: "POST",
				dataType:"json",
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				fileElementId: "upload_file",
				async: false,

                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },

                success: function (result) {
                    if (result.status == false) {
                        jQuery('#message_area').showMessage(result.msg);
                        return false;
                    } else {
                        jQuery('#search_content').html(result.content);
                        jQuery('#search_link').trigger('click');
                        jQuery('#btn_reset').trigger('click');
                        return false;
                    }
                },
                error: function (result) {
                    jQuery('#message_area').showMessage('新增失敗。');
                    return false;
                }
            });


        });

		// 查詢資料
        jQuery('.btn_query').bind('click', function () {
            jQuery('#message_area').hideMessage();
            var search_data = {};
			_type = jQuery(this).data("type");

            switch (_type) {
                case 'idnum':
                    search_data = {'table_suffix': jQuery('#idnum_table_suffix').val()};
                    break;
                case 'assign':
                    for (var i = 1; i <= jQuery('#assign').find('.assign_column').length; i++) {
                        search_data['column_' + i] = jQuery("#" + _type).find('.assign_column')[i - 1].title;
                    }
                    search_data.table_suffix = jQuery('#assign_table_suffix').val();
                    break;
                case 'any':
                    search_data = {'table_suffix': jQuery('#school_table_suffix').val()};
                    break;
                default:
                    jQuery('#message_area').showMessage('查詢失敗');
                    return false;
            }

            // ajax 查詢
            jQuery('#message_area').hideMessage();
            jQuery.ajax({
                url: '../plugins/verify/' + _type + '/admin/ajax_query_data.php',
                type: 'POST',
                dataType: 'json',
                data: search_data,
                cache: false,
                async: false,

                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },

                success: function (result) {
                    if (result.status == false) {
                        jQuery('#message_area').showMessage(result.msg);
                        return false;
                    } else {
                        jQuery('#search_content').html(result.content);
                        jQuery('#search_link').trigger('click');
                        return false;
                    }
                },
                error: function (result) {
                    jQuery('#message_area').showMessage('查詢失敗。');
                    return false;
                }
            });


        });


		// 匯出
		jQuery("#export_assign_sample_file").click(function() {
			console.log("ok");
			jQuery("#task").val("addend.exportAssignSampleFile");
			jQuery('#addend-form').attr("target", "_blank");
			jQuery("#addend-form").submit();
			
			jQuery("#task").val("addend.upload");
			jQuery('#addend-form').attr("target", "");
		});
		

    });


</script>
<style>
    #id_num {
        text-transform: uppercase;
    }

    .note {
        margin-bottom: 20px;
    }

    .field {
        margin-bottom: 10px;
    }

    .idnum_review_table, .assign_review_table, .school_review_table {
        border: 1px solid #ccc;
    }

    .idnum_review_table td, .assign_review_table td, .school_review_table td{
        border: 1px solid #ccc;
        padding: 5px;
    }
	
	.addend_type {
		margin-bottom: 20px;
	}

	.addend_zone {
		display: none;
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

        <ul class="nav nav-tabs" id="configTabs">
			<?php if ($idnum_table_suffix) { ?>
                <li<?php echo ($idnum_table_suffix != '') ? ' class=\'active\'' : ''; ?>>
                    <a href="#idnum" data-toggle="tab">身分證字號</a>
                </li>
				<?php
			}
			if ($assign_table_suffix) {
				?>
                <li <?php echo ($idnum_table_suffix && $assign_table_suffix) ? '' : 'class=\'active\''; ?>>
                    <a href="#assign" data-toggle="tab">可投票人名單</a>
                </li>
				<?php
			}
			if (isset($this->verify_params->any->suffix)) {
				?>
                <li <?php echo ($idnum_table_suffix || $assign_table_suffix) ? '' : 'class=\'active\''; ?>>
                    <a href="#any" data-toggle="tab">學校名單</a>
                </li>
				<?php
			}
			?>
        </ul>

        <div class="tab-content">
			<?php 
				// 身分證字號
				if ($idnum_table_suffix) { ;
					
			?>
			
                <div class="tab-pane <?php echo ($idnum_table_suffix != '') ? 'active' : ''; ?>" id="idnum">
					<fieldset id="jform_idnum_addend_type" class="btn-group radio addend_type">
						<input type="radio" id="jform_idnum_addend_type0" name="jform[idnum_addend_type]" value="1" >
						<label for="jform_idnum_addend_type0" class="btn" id="label_jform_idnum_addend_type0">單筆匯入</label>

						<input type="radio" id="jform_idnum_addend_type1" name="jform[idnum_addend_type]" value="2" >
						<label for="jform_idnum_addend_type1" class="btn ">批次匯入</label>
					</fieldset>
					
					<!--單筆匯入區塊-->
					<div id="idnum_once_zone" class="addend_zone">
						<div class="note">
							請於下述欄位中填寫身分證/居留證號和民國出生年。<br>
						</div>
						<div class="field">
							身分證/居留證號：
							<input type="text" id="id_num" name="id_num" placeholder="例：A123456789 / AC01234567" maxlength="10" autocomplete="off">
						</div>
						<div class="field">
							民國出生年月日：
							<input type="text" id="birth_date" name="birth_date" placeholder="例：560701" maxlength="7" autocomplete="off">
						</div>
						
						<div class="field">
							<input type="button" class="btn_add" value="新增資料" data-type="idnum">&nbsp;&nbsp;
							<input type="button" class="btn_query" value="查看已補送資料" data-type="idnum">
						</div>
                    </div>

					<!--批次匯入區塊-->
					<div id="idnum_batch_zone" class="addend_zone">
						請上傳要匯入的名單檔案<br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input style="margin: 5px" type="file" name="idnum_upload_file" id="idnum_upload_file"><br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="上傳" class="btn_upload" data-type="idnum">

						<br><br>
						<ol style="list-style-type: decimal;">
							<li>請上傳CSV檔案格式。(<a href="<?php echo JURI::root(); ?>images/system/idnum_sample.csv" title="下載範例檔" target="_blank">下載範例檔</a>)</li>
							<li>檔案容量限制為10MB。</li>
							<li>內容第一欄為身分證字號。</li>
							<li>內容第二欄為民國年生日。</li>
						</ol>
						<div class="field">
							<input type="button" class="btn_query" value="查看已補送資料" data-type="idnum">
						</div>
					</div>
                </div>
				<?php
			}
			
			// 可投票人名單
			if ($assign_table_suffix) {
				$plugin       = JPluginHelper::getPlugin('verify', 'assign');
				$pluginParams = new JRegistry($plugin->params);
				$deny_symbol  = $pluginParams->get('deny_symbol');

				?>
                <div class="tab-pane <?php echo ($idnum_table_suffix && $assign_table_suffix) ? '' : 'active'; ?>" id="assign">
					<fieldset id="jform_assign_addend_type" class="btn-group radio addend_type">
						<input type="radio" id="jform_assign_addend_type0" name="jform[assign_addend_type]" value="1" >
						<label for="jform_assign_addend_type0" class="btn" id="label_jform_assign_addend_type0">單筆匯入</label>

						<input type="radio" id="jform_assign_addend_type1" name="jform[assign_addend_type]" value="2" >
						<label for="jform_assign_addend_type1" class="btn ">批次匯入</label>
					</fieldset>
					
					<!--單筆匯入區塊-->
					<div id="assign_once_zone" class="addend_zone">
						<div class="note">
							請於下述欄位中填寫可投票者資料。<br>
						</div>
						<?php
						foreach ($this->assign_column as $assign_column) {
							?>
							<div class="field">
								<?php
								echo $assign_column->title . '：';
								?>
								<input type="text" class="assign_column" id="column_<?php echo $assign_column->column_num; ?>" name="column_<?php echo $assign_column->column_num; ?>" placeholder="例：<?php echo $assign_column->note; ?>" title="<?php echo $assign_column->title; ?>" maxlength="10" autocomplete="off">
								<input type="hidden" name="assign_column[]" value="<?php echo $assign_column->title; ?>" />
							</div>
							<?php
						}
						?>
						<div class="field">
							<input type="button" class="btn_add" value="新增資料" data-type="assign">&nbsp;&nbsp;
							<input type="button" class="btn_query" value="查看已補送資料" data-type="assign">
						</div>
                    </div>

					<!--批次匯入區塊-->
					<div id="assign_batch_zone" class="addend_zone">
						請上傳要匯入的名單檔案<br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input style="margin: 5px" type="file" name="assign_upload_file" id="assign_upload_file"><br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="上傳" class="btn_upload" data-type="assign">

						<br><br>
						<ol style="list-style-type: decimal;">
							<li>請上傳CSV檔案格式。(<a href="javascript:void(0);" id="export_assign_sample_file" title="下載範例檔">下載範例檔</a>)
							<li>檔案容量限制為10MB。</li>
							<li>請依照指定欄位由第2列開始填寫。</li>
							<li>不允許(<?php echo $deny_symbol; ?>)等符號。</li>
						</ol>
						<div class="field">
							<input type="button" class="btn_query" value="查看已補送資料" data-type="assign">
						</div>
					</div>
                
                </div>
				<?php
			}
			
			// 學校名單
			if (isset($this->verify_params->any->suffix)) {
		?>
                <div class="tab-pane <?php echo ($idnum_table_suffix || $assign_table_suffix) ? '' : 'active'; ?>" id="any">
					<fieldset id="jform_any_addend_type" class="btn-group radio addend_type">
						<input type="radio" id="jform_any_addend_type0" name="jform[any_addend_type]" value="1" >
						<label for="jform_any_addend_type0" class="btn" id="label_jform_any_addend_type0">單筆匯入</label>

						<input type="radio" id="jform_any_addend_type1" name="jform[any_addend_type]" value="2" >
						<label for="jform_any_addend_type1" class="btn ">批次匯入</label>
					</fieldset>

					<!--單筆匯入區塊-->
					<div id="any_once_zone" class="addend_zone">
						<div class="note">
							請於下述欄位中填寫學校名稱。<br>
						</div>
						<div class="field">
							學校名稱：
							<input type="text" id="school_name" name="school_name" placeholder="例：台灣大學" autocomplete="off">
						</div>
						
						<div class="field">
							<input type="button" class="btn_add" value="新增資料" data-type="any">&nbsp;&nbsp;
							<input type="button" class="btn_query" value="查看已補送資料" data-type="any">
						</div>
					</div>
						
						
					<!--批次匯入區塊-->
					<div id="any_batch_zone" class="addend_zone">
						請上傳要匯入的學校名單<br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input style="margin: 5px" type="file" name="any_upload_file" id="any_upload_file"><br>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="上傳" class="btn_upload" data-type="any">

						<br><br>
						<ol style="list-style-type: decimal;">
							<li>請上傳CSV檔案格式。(<a href="<?php echo JURI::root(); ?>images/system/school_sample.csv" title="下載範例檔" target="_blank">下載範例檔</a>)</li>
							</li>
							<li>檔案容量限制為10MB。</li>
							<li>內容第一欄為學校名稱。</li>
						</ol>
						<div class="field">
							<input type="button" class="btn_query" value="查看已補送資料" data-type="any">
						</div>
					</div>


                </div>
				<?php
			}
			
		?>
        </div>
        

    </div>
	

    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
	<?php if ($idnum_table_suffix) { ?>
        <input type="hidden" id="idnum_table_suffix" name="idnum_table_suffix" value="<?php echo $idnum_table_suffix; ?>" />
	<?php } ?>
	<?php if ($assign_table_suffix) { ?>
        <input type="hidden" id="assign_table_suffix" name="assign_table_suffix" value="<?php echo $assign_table_suffix; ?>" />
	<?php } ?>
	<?php if (isset($this->verify_params->any->suffix)) { ?>
        <input type="hidden" id="school_table_suffix" name="school_table_suffix" value="<?php echo $this->verify_params->any->suffix; ?>" />
	<?php } ?>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="user_name" value="<?php echo $user_name; ?>" />
    <input type="hidden" name="task" id="task" value="addend.upload" />
    <input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<a href="#search_zone" id="search_link" title="預覽畫面" style="display: none;">預覽畫面</a>
<div id="search_zone" style="display: none; width:600px;">
    <div id="search_message" style="color:red;"></div>
    <div id="search_content">
    </div>
</div>
