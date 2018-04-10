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


        jQuery('#btn_add').bind('click', function () {

            var verify_data = {};
            jQuery('#message_area').hideMessage();
            var tab_content = jQuery('.tab-content');
            switch (tab_content.find('.active').attr('id')) {

                case 'idnum':

                    var id_num = jQuery('#id_num');
                    if (!id_num.val()) {
                        jQuery('#message_area').showMessage('請填寫身份證/護照/居留證號。', id_num);
                        return false;
                    } else {
                        var ID = /^[A-Za-z]{1}[1-2]{1}[0-9]{8}$/; //身分證
                        var reID = /^[A-Za-z]{1}[A-Da-d]{1}[0-9]{8}$/; //居留證
                        if (!ID.test(jQuery.trim(id_num.val())) && !reID.test(jQuery.trim(id_num.val()))) {
                            jQuery('#message_area').showMessage('身份證/居留證號格式錯誤', id_num);
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

                    for (var i = 0; i < jQuery('#assign').find('.field').length; i++) {
                        if (!jQuery('#assign').find('.field').find('input')[i].value) {
                            var title = tab_content.find('.active').find('input')[i].title;
                            var id = tab_content.find('.active').find('input')[i].id;
                            jQuery('#message_area').showMessage('請填寫' + title, jQuery('#' + id));
                            return false;
                        }
                        verify_data[tab_content.find('.active').find('input')[i].title] = tab_content.find('.active').find('input')[i].value;
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
                url: '../plugins/verify/' + tab_content.find('.active').attr('id') + '/admin/ajax_upload_addend.php',
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


        jQuery('#btn_query').bind('click', function () {
            jQuery('#message_area').hideMessage();
            var search_data = {};
            var tab_content = jQuery('.tab-content');

            switch (tab_content.find('.active').attr('id')) {
                case 'idnum':
                    search_data = {'table_suffix': jQuery('#idnum_table_suffix').val()};
                    break;
                case 'assign':
                    for (var i = 1; i <= jQuery('#assign').find('.field').length; i++) {
                        search_data['column_' + i] = tab_content.find('.active').find('input')[i - 1].title;
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
                url: '../plugins/verify/' + tab_content.find('.active').attr('id') + '/admin/ajax_query_data.php',
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
			<?php if ($idnum_table_suffix) { ?>
                <div class="tab-pane <?php echo ($idnum_table_suffix != '') ? 'active' : ''; ?>" id="idnum">

                    <div class="note">
                        請於下述欄位中填寫身份證/護照/居留證號和民國出生年。<br>
                    </div>
                    <div class="field">
                        身份證/居留證號：
                        <input type="text" id="id_num" name="id_num" placeholder="例：A123456789 / AB12345678" maxlength="10" autocomplete="off">
                    </div>
                    <div class="field">
                        民國出生年月日：
                        <input type="text" id="birth_date" name="birth_date" placeholder="例：560701" maxlength="7" autocomplete="off">
                    </div>

                </div>
				<?php
			}
			if ($assign_table_suffix) {
				?>
                <div class="tab-pane <?php echo ($idnum_table_suffix && $assign_table_suffix) ? '' : 'active'; ?>" id="assign">

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
                            <input type="text" id="column_<?php echo $assign_column->column_num; ?>" name="column_<?php echo $assign_column->column_num; ?>" placeholder="例：<?php echo $assign_column->note; ?>" title="<?php echo $assign_column->title; ?>" maxlength="10" autocomplete="off">
                        </div>
						<?php
					}
					?>
                </div>
				<?php
			}
			if (isset($this->verify_params->any->suffix)) {
				?>
                <div class="tab-pane <?php echo ($idnum_table_suffix || $assign_table_suffix) ? '' : 'active'; ?>" id="any">

                    <div class="note">
                        請於下述欄位中填寫學校名稱。<br>
                    </div>
                    <div class="field">
                        學校名稱：
                        <input type="text" id="school_name" name="school_name" placeholder="例：台灣大學" autocomplete="off">
                    </div>
                </div>
				<?php
			}
			?>
        </div>
        <div class="field">
            <input type="button" id="btn_add" value="新增資料">&nbsp;&nbsp;
            <input type="button" id="btn_query" value="查看已補送資料"> <input type="reset" id="btn_reset" value="清空">
        </div>

    </div>


    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
	<?php if ($idnum_table_suffix) { ?>
        <input type="hidden" id="idnum_table_suffix" value="<?php echo $idnum_table_suffix; ?>" />
	<?php } ?>
	<?php if ($assign_table_suffix) { ?>
        <input type="hidden" id="assign_table_suffix" value="<?php echo $assign_table_suffix; ?>" />
	<?php } ?>
	<?php if (isset($this->verify_params->any->suffix)) { ?>
        <input type="hidden" id="school_table_suffix" value="<?php echo $this->verify_params->any->suffix; ?>" />
	<?php } ?>
    <input type="hidden" name="task" value="addend.upload" />
    <input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<a href="#search_zone" id="search_link" title="預覽畫面" style="display: none;">預覽畫面</a>
<div id="search_zone" style="display: none; width:600px;">
    <div id="search_message" style="color:red;"></div>
    <div id="search_content">
    </div>
</div>
