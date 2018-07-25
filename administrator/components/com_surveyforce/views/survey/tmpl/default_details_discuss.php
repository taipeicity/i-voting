<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
$verify_all_array = array ();
if ($this->verify_types) {
	foreach ($this->verify_types as $type) {
		$params = json_decode($type->params);
		if ($params->level > 0) {
			$verify_all_array[$type->element] = $type->name;
		}
	}
}

$star   = '<span class=\"star\">&nbsp;*</span>';
$script = '';
if ($this->form->getValue('discuss_download')) {
	$element = SurveyforceHelper::getOldArea("discuss_download", $this->form->getValue('discuss_download'), false);
	$script  .= SurveyforceHelper::hiddenNewArea("jQuery(\"#new_discuss_download_area\")", $element);
	$script  .= 'jQuery("#jform_discuss_download").parent().prev().find("label").append("' . $star . '");';
} else {
	$script .= 'jQuery("#jform_discuss_download").addClass("required").parent().prev().find("label").append("' . $star . '");';
}

$document = JFactory::getDocument();
$document->addScriptDeclaration('
    jQuery(document).ready(function () {' . $script . '});
');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#del_discuss_download_btn").on("click", function () {
            jQuery(this).deleteImage("discuss_download");
            jQuery("#jform_discuss_download").addClass("required");
        });

        jQuery.fn.checkDetails_discussJs = function () {

            var discuss_source = jQuery("#jform_discuss_source"),
                discuss_vote_start = jQuery("#jform_discuss_vote_start"),
                discuss_vote_end = jQuery("#jform_discuss_vote_end");

            //檢查討論管道欄位
            if (discuss_source.val().match(/href="(.+?)"/g)) {


                var uri = discuss_source.val().match(/href="(.+?)"/g),
                    discuss_source_tag_a = discuss_source.val().match(/<\/a>/g);

                if (discuss_source_tag_a === null || uri.length !== discuss_source_tag_a.length) {
                    jQuery("#message_area").showMessage('討論管道的連結網址必須符合超連結範例', discuss_source);
                    return false;
                }

                for (var i = 0; i < uri.length; i++) {
                    if (!uri[i].match(/https?/g)) {
                        jQuery("#message_area").showMessage('討論管道的連結網址必須為http://example.com或https://example.com。', discuss_source);
                        return false;
                    }
                    var str = discuss_source.val().match(/">(.+)<\//g)[i].replace(/"|<|>|\//g, "");

                    if (str.match(/https?/g)) {
                        jQuery("#message_area").showMessage('討論管道的連結名稱請勿使用網址。', discuss_source);
                        return false;
                    }
                }
            }

            // 檢查開始時間
            if (!discuss_vote_start.checkDatePattern(true)) {
                jQuery("#message_area").showMessage('日期格式不符。', discuss_vote_start);
                return false;
            }

            // 檢查結束時間
            if (!discuss_vote_end.checkDatePattern(true)) {
                jQuery("#message_area").showMessage('日期格式不符。', discuss_vote_end);
                return false;
            }

            // 檢查開始時間是否小於結束時間
            if (Date.parse(discuss_vote_start.val()).valueOf() > Date.parse(discuss_vote_end.val()).valueOf()) {
                jQuery("#message_area").showMessage('結束時間必須大於開始時間。', discuss_vote_end);
                return false;
            }

            return true;
        };
    });
</script>

<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_source', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_source'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('discuss_source'); ?>
        <br> (超連結範例：將網址用<span class="url_background_color">&lt;a href="<span class="url_color">連結網址</span>" target="_blank"&gt;<span
                    class="url_color">連結名稱</span>&lt;/a&gt;</span>包起來，
        <br> 如需輸入兩個網址以上時，請分別用<span class="url_background_color">&lt;a href="<span class="url_color">連結網址</span>" target="_blank"&gt;<span class="url_color">連結名稱</span>&lt;/a&gt;</span>包起來，並用<span class="url_color">&nbsp;&#59;&nbsp;</span>隔開)
        <br> (網址範例：<span class="url_color">http</span>://example.com 或 <span class="url_color">https</span>://example.com)
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_plan_options', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_plan_options'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('discuss_plan_options'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_qualifications', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_qualifications'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('discuss_qualifications'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_verify', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_verify'); ?>
    <div class="controls">
        <fieldset id="jform_discuss_verify" class="checkboxes required discuss_verify">
            <ul>
				<?php $i = 0; ?>
				<?php foreach ($verify_all_array as $key => $type) { ?>
					<?php
					$checked = false;
					if (in_array($key, json_decode($this->form->getValue('discuss_verify'), true))) {
						$checked = true;
					}
					?>
                    <li>
                        <input type="checkbox" id="jform_discuss_verify<?php echo $i; ?>" name="jform[discuss_verify][]" value="<?php echo $key; ?>" aria-required="true" <?php echo $checked ? "checked" : ""; ?>/>
                        <label for="jform_discuss_verify<?php echo $i; ?>"><?php echo $type; ?></label>
                    </li>
					<?php $i++; ?>
				<?php } ?>
            </ul>
        </fieldset>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_vote_time', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_vote_time'); ?>
    <div class="controls discuss_vote_time">
		<?php
		$this->form->setFieldAttribute('discuss_vote_start', 'required', 'true');
		echo JText::_('COM_SURVEYFORCE_DISCUSS_VOTE_START') . $this->form->getInput('discuss_vote_start');
		?>
    </div>
    <div class="controls discuss_vote_time">
		<?php
		$this->form->setFieldAttribute('discuss_vote_end', 'required', 'true');
		echo JText::_('COM_SURVEYFORCE_DISCUSS_VOTE_END') . $this->form->getInput('discuss_vote_end');
		?>
    </div>
</div>
<div class="control-group">
	<?php $this->form->setFieldAttribute('discuss_threshold', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('discuss_threshold'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('discuss_threshold'); ?>
    </div>
</div>


<div id="new_discuss_download_area">
	<?php echo $this->form->renderField('discuss_download'); ?>
</div>
