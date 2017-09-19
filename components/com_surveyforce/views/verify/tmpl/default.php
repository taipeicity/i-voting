<?php
/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$itemid = $app->input->getInt('Itemid');

//取得驗證碼強度
$verify_type = json_decode($this->verify_type);
if (count($verify_type) == 1) {
    $plugin = JPluginHelper::getPlugin('verify', $verify_type[0]);
    if ($plugin) {
// Get plugin params
        $pluginParams = new JRegistry($plugin->params);
        $level = $pluginParams->get('level');
        if ($level == 0) {
            $level = 1;
        }
    }
}

?>

<div class="survey_verify">
    <div class="page-header">
        請填寫驗證資料
    </div>
    <div class="verify">
        <form id="verify_form" name="verify_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=verify&task=verify.check_verify_form&Itemid=' . $this->itemid, false); ?>" >
            <table class="formtable" width="100%">
                <?php
                if ($this->verify_type) {
                    $verify_types = json_decode($this->verify_type, true);

                    unset($check_jscode);
                    foreach ($verify_types as $type) {
                        JPluginHelper::importPlugin('verify', $type);
                        $className = 'plgVerify' . ucfirst($type);

                        // 取得顯示欄位
                        if (method_exists($className, 'onGetFormHtml')) {
                            $html = $className::onGetFormHtml($this->verify_required, $this->verify_params, $this->survey_id);
                            echo $html;
                        }

                        // 取得JS檢查
                        if (method_exists($className, 'onGetCheckJsCode')) {
                            $check_jscode .= $className::onGetCheckJsCode($this->verify_required, $this->verify_params);
                        }

                        // 點選欄位時，radio button自動選取
                        if (method_exists($className, 'onJsRadioBtn')) {
                            $js_radiobtn .= $className::onJsRadioBtn($this->verify_required);
                        }

                        // Cdc 自然人憑證
                        if (method_exists($className, 'onGetCdcVBsCode')) {
                            $cab = $className::onGetCdcCAB();
                            $cdc = $className::onGetCdcVBsCode();
                            $cdc_check = $className::onGetCheckVBsCode();
                        }
                    }
                }
                ?>
                <tr class="list">
                    <th rowspan="2">
                        <label for="recaptcha_response_field">輸入驗證碼：</label>
                    </th>
                    <td>
                        <input type="text" id="recaptcha_response_field2" name="recaptcha_response_field2" maxlength="50" placeholder="請輸入圖片中的數字" autocomplete="off" >
                    </td>
                </tr>

                <tr>
                    <td>
                        <div id="captcha_field">
                            <?php include_once("rd/securimage/voice_show.php"); ?>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="btns">
                <a class="submit" href="<?php echo $this->back_link; ?>" title="上一步" >
                    上一步
                </a>

                <a id="submit_img" class="submit" href="javascript:void(0);" title="下一步" >
                    下一步
                </a>
                <noscript>
                您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
                </noscript>
                <div><a href="<?php echo $this->category_link; ?>" class="btn ">取消</a></div>
            </div>			
            <input type="hidden" name="task" value="verify.check_verify_form">
            <input type="hidden" id="sid" name="sid" value="<?php echo $this->survey_id; ?>">
            <?php echo JHTML::_('form.token'); ?>
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

<?php
if ((strpos($_SERVER['HTTP_HOST'], "ivoting.taipei") !== false) && SurveyforceVote::getSurveyData($this->survey_id, "verify_failure_num") > 0) {
    ?>
    <script>
        ga('create', 'UA-71563139-3', 'auto', {'name': 'newTracker'});
        ga('newTracker.send', 'pageview');

    </script>
<?php } ?>

<?php //for cdc verify ?>
<?php echo $cab; ?>
<script language="VBScript">
<?php echo $cdc; ?>
</script>
<?php echo $cdc_check; ?>


<script>
    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    }

    jQuery(document).ready(function () {
<?php if (count($this->verify_type) == 1) { ?>
            var td = '<th>驗證強度：</th>';
            td += '<td><div class="verifylevel_verify"><img src="images/system/VerifyLevel/verifylevel_<?php echo $level; ?>.svg" /></div></td>';
            var tr = '<tr>' + td + '</tr>';
            if (jQuery(".list").length > 1) {
                jQuery(".list:first").after(tr);
            } else {
                jQuery(".list:first").before(tr);
            }
<?php } ?>

        jQuery("#recaptcha_response_field").show();
        jQuery("#submit_img").show();

<?php echo $js_radiobtn; ?>

<?php
// 若為擇一選擇，則把第1項設為check
if ($this->verify_type) {
    if ($this->verify_required == false) {
        echo 'jQuery(\'input:radio[name="verify_type"]\')[0].checked = true;';
    }
}
?>

        jQuery('input:text')[1].focus();

        jQuery("#submit_img").bind("click", function () {
            jQuery("#system-message-container").hide();

            // check filed is empty
<?php
// 印出所有js 檢查程式碼
if ($this->verify_type) {
    echo $check_jscode;
}
?>


            if (!jQuery("#recaptcha_response_field2").val()) {
                jQuery("#message_area").showMessage('請填寫驗證碼。');
                return false;
            }


            jQuery.ajax({
                type: "POST",
                url: "<?php echo JURI::base(); ?>rd/securimage/securimage_valid.php",
                dataType: "json",
                data: {'recaptcha_response_field': jQuery("#recaptcha_response_field2").val(), 'sid': jQuery("#sid").val()},
                beforeSend: function () {
                    jQuery.fancybox.showLoading();
                    jQuery.fancybox.helpers.overlay.open({parent: jQuery('body'), closeClick: false});
                },
                complete: function () {
                    jQuery.fancybox.hideLoading();
                    jQuery.fancybox.helpers.overlay.close();
                },
                success: function (result) {
                    if (result.status == false) {
                        if (result.num >= 3) {
                            if (result.num == 3) {
                                jQuery("#message_area").showMessage('驗證碼輸入錯誤，由於驗證碼失敗次數過多，請稍後再試。');
                            } else {
                                jQuery("#message_area").showMessage('驗證碼失敗次數過多，請稍後再試。');
                            }
                        } else {
                            jQuery("#message_area").showMessage('驗證碼輸入錯誤，請重新填寫。');
                        }
                        jQuery("#recaptcha_response_field2").val("");
                        jQuery(".refresh_captcha").trigger("click");

                        return false;
                    } else {
                        jQuery("#verify_form").submit();
                    }
                }
            });
        });


        jQuery("#recaptcha_response_field2").keypress(function (event) {
            if (event.keyCode == 13) {
                jQuery("#submit_img").trigger("click");
            }
        });


    });

</script>
