<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app    = JFactory::getApplication();
$itemid = $app->input->getInt('Itemid');


?>

<div class="survey_verify">
    <div class="page-header">
        請選擇驗證方式
    </div>
    <div class="verify">
        <form id="verify_form" name="verify_form" method="post" action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=verify_opt&task=verify_opt.check_verify_form&Itemid=' . $this->itemid, false); ?>">
            <table class="formtable verify">
				<?php

				if ($this->verify_type) {
					$verify_types = json_decode($this->verify_type, true);

					unset($check_jscode);
					foreach ($verify_types as $type) {
						JPluginHelper::importPlugin('verify', $type);
						$className = 'plgVerify' . ucfirst($type);

						// 取得顯示欄位
						if (method_exists($className, 'onGetVerifyOpt')) {
							echo $className::onGetVerifyOpt();

						}


					}
				}
				?>


            </table>
            <br>
            <div class="btns">

				<?php if ($this->preview == true) { ?>
                    <a href="<?php echo $this->back_link; ?>" class="submit">上一頁</a>
                    <a id="next_preview" href="javascript:void(0)" class="submit">下一頁</a>
				<?php } else { ?>
                    <a class="submit" href="<?php echo $this->back_link; ?>" title="上一步"> 上一步 </a>

                    <a id="submit_img" class="submit" href="javascript:void(0);" title="下一步"> 下一步 </a>
                    <noscript>
                        您的瀏覽器不支援script程式碼,請開啟javascript功能才能進行送出功能。
                    </noscript>
                    <div>
                        <a href="<?php echo $this->category_link; ?>" class="btn ">取消</a>
                    </div>
				<?php } ?>
            </div>
            <input type="hidden" name="task" value="verify_opt.check_verify_form">
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


<script>
    jQuery.fn.showMessage = function (msg) {
        jQuery('html, body').scrollTop(0);
        jQuery("#message_area #message_content").html(msg);
        jQuery("#system-message-container").html(jQuery("#message_area").html());
        jQuery("#system-message-container").show();
    }

    jQuery(document).ready(function () {
        jQuery("#recaptcha_response_field").show();
        jQuery("#captcha_field").show();
        jQuery("#submit_img").show();

		<?php echo $js_radiobtn; ?>

		<?php
		// 若為擇一選擇，則把第1項設為check
		if ($this->verify_type) {
			if ($this->verify_required == false) {
				if ($this->preview == false) {
					echo 'jQuery(\'input:radio[name="verify_type"]\')[0].checked = true;';
				} else {
					$type = SurveyforceHelper::getPreviewData($this->item->id, "preview_type");
					if ($type) {
						echo 'jQuery(\'#verify_' . $type . '\').attr(\'checked\', true);';
					} else {
						echo 'jQuery(\'input:radio[name="verify_type"]\')[0].checked = true;';
					}
				}
			}

		}
		?>
		<?php if($this->preview == false){ ?>
        jQuery("#submit_img").bind("click", function () {
            jQuery("#system-message-container").hide();

            // check filed is empty
			<?php
			// 印出所有js 檢查程式碼
			if ($this->verify_type) {
				echo $check_jscode;
			}

			?>


            jQuery("#verify_form").submit();

        });
		<?php }else{ ?>
        jQuery("#next_preview").bind("click", function () {
            var url = "<?php echo $this->next_link; ?>";
            jQuery(this).attr("href", url + "&type=" + jQuery("input[type=radio]:checked").val());
            jQuery("this").trigger("click");
        });

		<?php } ?>
    });

</script>
