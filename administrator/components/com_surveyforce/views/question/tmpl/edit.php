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
//JHtml::_('formbehavior.chosen', 'select');
$app           = JFactory::getApplication();
$input         = $app->input;
$surveys_list  = $this->surveys;
$ordering_list = $this->ordering_list;
?>
<?php // echo $this->loadTemplate('menu');  ?>

<script type="text/javascript">

    Joomla.submitbutton = function (task) {
        jQuery(".btn").prop("disabled", true);
        // check field
        if (task == 'question.apply' || task == 'question.save') {
            if (jQuery("#jform_is_multi").find(":selected").val() == 1) {	// 複選
                if (jQuery('input:radio:checked[name="option_num_type"]').val() == 1) {	// 可投
                    if (isNaN(jQuery("#jform_multi_min").val()) || isNaN(jQuery("#jform_multi_max").val())) {
                        if (isNaN(jQuery("#jform_multi_min").val())) {
                            jQuery("#message_area").showMessage('可投選項數目請填寫數字。', jQuery("#jform_multi_min"));
                        } else {
                            jQuery("#message_area").showMessage('可投選項數目請填寫數字。', jQuery("#jform_multi_max"));
                        }
                        return false;
                    } else {
                        if (jQuery("#jform_multi_min").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫選項數的可投數目。', jQuery("#jform_multi_min"));
                            return false;
                        }
                        if (jQuery("#jform_multi_max").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫選項數的可投數目。', jQuery("#jform_multi_max"));
                            return false;
                        }
                    }

                } else {	// 應投
                    if (isNaN(jQuery("#jform_multi_limit").val())) {
                        jQuery("#message_area").showMessage('應投選項數目請填寫數字。', jQuery("#jform_multi_limit"));
                        return false;
                    } else {
                        if (jQuery("#jform_multi_limit").val() == 0) {
                            jQuery("#message_area").showMessage('請填寫選項數的應投數目。', jQuery("#jform_multi_limit"));
                            return false;
                        }
                    }
                }
            }

            if (jQuery("#jform_sf_qtext").val() == "") {
                jQuery("#message_area").showMessage('請填寫題目描述。', jQuery("#jform_sf_qtext"));
                return false;
            }

            if (jQuery("#jform_sf_qtext").val().len() > 30) {
                jQuery("#message_area").showMessage('題目描述的文字過多，請刪除部分文字。', jQuery("#jform_sf_qtext"));
                return false;
            }

			<?php if ($this->item->id) { ?>
            // 檢查選項
            if (jQuery("#question-form").checkField() == false) {
                return false;
            }
			<?php } ?>

        }

        if (task == 'question.cancel' || document.formvalidator.isValid(document.id('question-form'))) {
            Joomla.submitform(task, document.getElementById('question-form'));
        } else {
            jQuery(".btn").prop("disabled", false);
        }
    };


    jQuery(document).ready(function () {
        jQuery.fn.showMessage = function (msg, target = null) {
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
            jQuery(".btn").prop("disabled", false);

            if (target) {

                target.addClass("invalid");

                if (jQuery('.invalid').parents('.active').length === 0) {

                    var id = jQuery('.invalid').parents('.tab-pane')[0].id;

                    var href = jQuery('li.active').find('a').attr('href');
                    jQuery(href).removeClass('active').siblings('#' + id).addClass('active');

                    var position = 'a[href="#' + id + '"]';
                    jQuery('li.active').removeClass('active');
                    jQuery(position).parent('li').addClass('active');

                }

            }

        };

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        };

        jQuery("#jform_is_multi").change(function () {
            if (jQuery("#jform_is_multi").find(":selected").val() == 1) {
                jQuery("#multi_zone").show();
            } else {
                jQuery("#multi_zone").hide();
            }
        });

        jQuery("#cancel_btn").bind("click", function () {
            jQuery("#new_ftext").removeClass("invalid");
        });

        jQuery("#cancel_sub_btn").bind("click", function () {
            jQuery("#new_sub_title").removeClass("invalid");
        });

        String.prototype.len = function () {
            return this.replace(/[\ufee0-\uffdf]/g, "rr").length;
        };

        jQuery.fn.check = function (select, char, field_name, num = 25) {
            var character = select.value.len() - num;
            if (character > 0) {
                jQuery("#message_area").showMessage(field_name + "已達字數上限" + num + "個字，請移除" + character + "個字元。", jQuery(select));
                jQuery(char).html(0);
            } else {
                jQuery(char).html(Math.abs(character));
                jQuery("#message_area").hideMessage();
            }
        };

        var sf_qtext = jQuery("#jform_sf_qtext");
        var character = 30 - sf_qtext.val().len();
        if (character > 0) {
            jQuery("#sf_qtext_char").html(character);
        } else {
            jQuery("#sf_qtext_char").html(0);
        }

        sf_qtext.keydown(function () {
            jQuery(this).check(this, "#sf_qtext_char", "題目描述", 30);
        });

        sf_qtext.keyup(function () {
            jQuery(this).check(this, "#sf_qtext_char", "題目描述", 30);
        });

        sf_qtext.keypress(function () {
            jQuery(this).check(this, "#sf_qtext_char", "題目描述", 30);
        });

    });


</script>

<div id="message_area" style="display: none;">
    <div id="system-message" class="alert alert-error">
        <h4 class="alert-heading"></h4>
        <div>
            <p id="message_content"></p>
        </div>
    </div>
</div>


<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=question&layout=edit&id=' . (int) $this->item->id); ?>"
      enctype="multipart/form-data" method="post" name="adminForm" id="question-form" class="form-validate">
    <div id="j-main-container" class="span7 form-horizontal">
        <ul class="nav nav-tabs" id="questionTabs">
            <li class="active"><a href="#question-details" data-toggle="tab">題目</a></li>
			<?php
			$cat_type = array ("imgcat");
			if (in_array($this->item->question_type, $cat_type)) {
				?>
                <li><a href="#question-cats" data-toggle="tab">分類</a></li>
			<?php } ?>
            <li><a href="#question-options" data-toggle="tab">選項</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="question-details">
                <fieldset class="adminform">
                    <legend><?php echo (empty($this->item->id)) ? "新增題目" : "編輯題目"; ?></legend>

                    <div class="control-group" style="display: none;">
                        <div class="control-label">
							<?php echo $this->form->getLabel('sf_survey'); ?>
                        </div>
                        <div class="controls">
							<?php echo $this->form->getInput('sf_survey'); ?>
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">
                            <label class="control-label">題型</label>
                        </div>
                        <div class="controls">
							<?php echo $this->question_type_item->name; ?>
                        </div>
                    </div>

					<?php
					// 只有文字、圖片、圖文式、影音、簡報
					$allow_type = array ("text", "img", "imgtext", "video", "briefing", "imgcat");
					if (in_array($this->item->question_type, $allow_type)) {
						?>
                        <div class="control-group">
                            <div class="control-label">
								<?php echo $this->form->getLabel('is_multi'); ?>
                            </div>
                            <div class="controls">
								<?php echo $this->form->getInput('is_multi'); ?>
                            </div>
                        </div>

                        <div id="multi_zone"
                             style="<?php echo ($this->form->getValue('is_multi') == 1) ? "" : "display: none;"; ?>">
                            <div class="control-group form-inline">
                                <label id="jform_display_result-lbl" for="jform_display_result" class="control-label"
                                       aria-invalid="false"> 選項數設定</label>
                                <div class="controls">
                                    <input type="radio" name="option_num_type"
                                           value="0" <?php echo ($this->form->getValue('multi_limit') > 0) ? "checked" : ""; ?>> 限定應投<input type="text" id="jform_multi_limit" name="jform[multi_limit]"
                                                                                                                                            value="<?php echo $this->form->getValue('multi_limit'); ?>" size="5"
                                                                                                                                            style="width:20px">項
                                    <br> <input type="radio" name="option_num_type"
                                                value="1" <?php echo ($this->form->getValue('multi_limit') == 0) ? "checked" : ""; ?>> 可投
                                    <input type="text" id="jform_multi_min" name="jform[multi_min]"
                                           value="<?php echo $this->form->getValue('multi_min'); ?>" size="5"
                                           style="width:20px"> 至 <input type="text" id="jform_multi_max"
                                                                        name="jform[multi_max]"
                                                                        value="<?php echo $this->form->getValue('multi_max'); ?>"
                                                                        size="5" style="width:20px"> 項
                                </div>
                            </div>
                        </div>

					<?php } ?>

                    <div class="control-group">
                        <div class="control-label">
							<?php echo $this->form->getLabel('sf_qtext'); ?>
                        </div>
                        <div class="controls">
							<?php echo $this->form->getInput('sf_qtext'); ?>
                            <p>尚餘<span id="sf_qtext_char"></span>個字元</p>
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">
							<?php echo $this->form->getLabel('published'); ?>
                        </div>
                        <div class="controls">
							<?php echo $this->form->getInput('published'); ?>
                        </div>
                    </div>

                    <br />
                </fieldset>
            </div>

			<?php
			$cat_type = array ("imgcat");
			if (in_array($this->item->question_type, $cat_type)) {
				?>
                <div class="tab-pane" id="question-cats">
					<?php
					if ($this->item->id) {
						?>
                        <fieldset class="adminform">
							<?php echo $this->cats; ?>
                            <br /> <br />
                        </fieldset>
					<?php } else { ?>
                        請先儲存題目後，再繼續新增分類。
						<?php
					}
					?>
                </div>

			<?php } ?>

            <div class="tab-pane" id="question-options">
				<?php
				if ($this->item->question_type == "open") {
					echo $this->options;
				} else {
					if ($this->item->id) {
						?>
                        <fieldset class="adminform">
							<?php echo $this->options; ?>
                            <br /> <br />
                        </fieldset>
					<?php } else { ?>
                        請先儲存題目後，再繼續新增選項。
						<?php
					}
				}
				?>
            </div>

        </div>
    </div>
    <input type="hidden" name="task" value="" /> <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="quest_id" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[sf_qtype]" value="<?php echo $this->item->sf_qtype; ?>" />
    <input type="hidden" name="jform[question_type]" value="<?php echo $this->item->question_type; ?>" />
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

