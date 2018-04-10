<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$app     = JFactory::getApplication();
$surv_id = $app->input->getInt("surv_id");

$verify_type = str_replace(['"', '[', ']'], '', $this->item->verify_type);
$verify_item = [
	'email' => '電子郵件驗證', 'google' => 'Google驗證', 'facebook' => 'Facebook驗證', 'taipeicard' => '台北卡驗證', 'assign' => '可投票人名單驗證', 'cdc' => '自然人憑證驗證', 'any' => '投票人資料填寫驗證', 'house' => '戶役政系統驗證', 'idnum' => '身分證字號驗證', 'phone' => '手機驗證'
];

unset($option);
foreach (explode(',', $verify_type) as $item) {
	$option[] = JHtml::_('select.option', $item, $verify_item[$item]);
}

$type   = ($this->type) ? $this->type : null;
$select = JHtml::_('select.genericlist', $option, 'verify_type', '', 'value', 'text', $type);

if (preg_match('/assign/', $verify_type)) {
	$suffix = json_decode($this->item->verify_params, true);
}

// 載入plugin
JPluginHelper::importPlugin('verify', $type);
$className = 'plgVerify' . ucfirst($type);
// 取得驗證類型的欄位
if (method_exists($className, 'onGetVotedHtml')) {
	$setting_html = '';
	$setting_html .= $className::onGetVotedHtml($this->item->verify_params, $this->item->id);
}

if (method_exists($className, 'onGetCheckVotedJsCode')) {
	$check_js = '';
	$check_js .= $className::onGetCheckVotedJsCode($this->item->verify_params);
}


?>
<script type="text/javascript">

    Joomla.submitbutton = function (task) {

        if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('voted-form'))) {
            Joomla.submitform(task, document.getElementById('voted-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };

    jQuery(document).ready(function () {

        jQuery.fn.showMessage = function (msg, target = null) {
            jQuery('html, body').scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
            if (target) {
                target.addClass("invalid");
            }
        };

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        };

        jQuery("#search_link").fancybox({
            helpers: {
                overlay: {closeClick: false}
            }
        });

        jQuery("#search_submit").click(function () {
            jQuery("#message_area").hideMessage();
            jQuery(".voted_search").find(".invalid").removeClass("invalid");

            var data = {};
            //檢查欄位
			<?php echo $check_js; ?>
            data['surv_id'] = <?php echo $surv_id; ?>;
            data['verify_type'] = '<?php echo $type; ?>';

            jQuery.ajax({
                url: "../administrator/components/com_surveyforce/views/voted/tmpl/ajax_search_poll.php",
                type: "POST",
                dataType: "json",
                data: data,
                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                },
                success: function (result) {
                    if (result.status == false) {
                        jQuery("#message_area").showMessage(result.msg);
                        return false;
                    } else {
                        jQuery("#search_content").html(result.content);
                        jQuery("#search_link").trigger("click");
                    }
                },
                error: function (error) {
                    jQuery("#message_area").showMessage("查詢失敗。");
                    return false;
                }
            });


        });

        jQuery("#verify_type").change(function () {
            jQuery.fancybox.showLoading();
            jQuery.fancybox.helpers.overlay.open({parent: jQuery("body"), closeClick: false});
            setTimeout(function () {
                jQuery("#voted-form").submit();
            }, 100);

        });


    });


</script>
<style>
    .voted_search input[type=text], #verify_type, select {
        margin-bottom : 0;
    }

    .voted_search input[type=radio] {
        margin : auto;
        width  : 1.5em;
    }

    .voted_search select {
        width : 4%;
    }

    .review_table td {
        border  : 1px solid #ccc;
        padding : 5px;
    }

    #voted_any_edu {
        width : 6%;
    }

    .voted_search label {
        display      : inline;
        margin-right : 1em;
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

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=voted&surv_id=' . $surv_id); ?>" method="POST" name="voted-form" id="voted-form" class="form-validate">

    請選擇驗證方式
	<?php
	echo $select;
	?>
    <input type="hidden" id="suffix" value="<?php echo $suffix['assign']['assign_table_suffix']; ?>" />
    <input type="hidden" id="Itemid" name="Itemid" value="<?php echo $surv_id; ?>" />
    <input type="hidden" name="task" value="" /> <input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>


<div class="voted_search">
    請填寫欲查詢資料<br> <br>
	<?php echo $setting_html; ?>
</div>


<button id="search_submit">送出</button>

<a href="#search_zone" id="search_link" title="預覽畫面" style="display: none;">預覽畫面</a>
<div id="search_zone" style="display: none; width:600px;">
    <div id="search_message" style="color:red;"></div>
    <div id="search_content">
    </div>
</div>