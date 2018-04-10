<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');

jimport('joomla.filesystem.file');
$user         = JFactory::getUser();
$config       = JFactory::getConfig();
$ivoting_path = $config->get('ivoting_path');
$id           = $this->item->id;

?>


<!--suppress ALL -->
<script type="text/javascript">

    Joomla.submitbutton = function (task) {

        jQuery(".btn").prop("disabled", true);

        if (task == "analyze.cancel" || document.formvalidator.isValid(document.id("analyze-form"))) {

            if (task == "analyze.apply" || task == "analyze.save") {

                if (jQuery("#quest_id").val() !== "" && jQuery(".option_ftext").length === 0) {
                    jQuery("#message_area").showMessage("請新增選項。");
                    jQuery("#configTabs a:last").tab("show");
                    return false;
                }

            }


            Joomla.submitform(task, document.getElementById("analyze-form"));
        } else {
            jQuery("#message_area").showMessage("請填寫必填欄位。");
            return false;
        }
    };


    jQuery(document).ready(function () {
        jQuery.fn.showMessage = function (msg, target) {
            jQuery(".btn").prop("disabled", false);
            jQuery("html, body").scrollTop(0);
            jQuery("#message_area #message_content").html(msg);
            jQuery("#system-message-container").html(jQuery("#message_area").html());
            if (target) {
                target.addClass("invalid");
            }
        };

        jQuery.fn.hideMessage = function () {
            jQuery("#system-message-container").html("");
        };

        jQuery("#configTabs a:first").tab("show");


        // 新增選項
        jQuery("#add_btn").on("click", function () {
            if (!jQuery("#new_ftext").val()) {
                jQuery("#message_area").showMessage("請填寫選項名稱。", jQuery("#new_ftext"));
                return false;
            }

            for (var i = 0; i < jQuery(".option_ftext").length; i++) {
                if (jQuery("#new_ftext").val() === jQuery(".option_ftext").eq(i).val()) {
                    jQuery("#message_area").showMessage("選項名稱重複。", jQuery("#new_ftext"));
                    return false;
                }
            }

            var order = parseInt(jQuery(".option_ftext").length) + 1;
            var td = "<td align=\"center\">" + order + "</td>";
            td += "<td align=\"left\">" + jQuery("#new_ftext").val() + "</td>";
            td += "<td align=\"center\">";
            td += "<a href=\"javascript: void(0);\" class=\"edit_row\" title=\"<?php echo JText::_('COM_SURVEYFORCE_EDIT'); ?>\">";
            td += "<img src=\"<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png\" border=\"0\" alt=\"<?php echo JText::_('COM_SURVEYFORCE_EDIT'); ?>\">";
            td += "</a>";
            td += "<input type=\"hidden\" class=\"option_ftext\" name=\"option_ftext[]\" value=\"" + jQuery("#new_ftext").val() + "\">";
            td += "<input type=\"hidden\" class=\"option_id\" name=\"option_id[]\" value=\"0\">";
            td += "</td>";
            td += "<td align=\"center\">";
            td += "<a href=\"javascript: void(0);\" class=\"del_row\" title=\"<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>\">";
            td += "<img src=\"<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png\" border=\"0\" alt=\"<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>\">";
            td += "</a>";
            td += "</td><td></td>";
            var tr = "<tr>" + td + "</tr>";

            jQuery("#table_list").append(tr);
            jQuery("#new_ftext").val("");
        });


        // 編輯選項
        jQuery(document).on("click", ".edit_row", function () {
            jQuery("#add_btn").hide();
            jQuery("#edit_btn").show();
            jQuery("#cancel_btn").show();
            jQuery(".title").html("編輯選項");

            var index = jQuery(".edit_row").index(this);
            jQuery("#new_ftext").val(jQuery(".edit_row").eq(index).next().val());
            jQuery("#old_ftext").val(jQuery(".edit_row").eq(index).next().val());
            jQuery("#old_row").val(index);


        });

        // 儲存
        jQuery("#edit_btn").on("click", function () {
            if (!jQuery("#new_ftext").val()) {
                jQuery("#message_area").showMessage("請填寫選項名稱。", jQuery("#new_ftext"));
                return false;
            }

            for (var i = 0; i < jQuery(".option_ftext").length; i++) {
                if ((jQuery("#new_ftext").val() === jQuery(".option_ftext").eq(i).val()) && (jQuery("#new_ftext").val() !== jQuery("#old_ftext").val())) {
                    jQuery("#message_area").showMessage("選項名稱重複。", jQuery("#new_ftext"));
                    return false;
                }
            }

            jQuery(".option_ftext").eq(jQuery("#old_row").val()).val(jQuery("#new_ftext").val());
            jQuery(".option_ftext").eq(jQuery("#old_row").val()).parent().prev().html(jQuery("#new_ftext").val());

            jQuery("#new_ftext").val("");
            jQuery(".title").html("新增選項");

            jQuery("#cancel_btn").trigger("click");

        });

        // 取消
        jQuery("#cancel_btn").on("click", function () {
            jQuery("#new_ftext").val("");
            jQuery("#add_btn").show();
            jQuery("#edit_btn").hide();
            jQuery("#cancel_btn").hide();
            jQuery(".title").html("新增選項");
        });

        // 刪除
        jQuery(document).on("click", ".del_row", function () {
            if (jQuery(".title").html() === "編輯選項") {
                jQuery("#message_area").showMessage("請先編輯完選項再做刪除動作。", jQuery("#new_ftext"));
                return false;
            }


            var index = jQuery(".del_row").index(this);
            var delete_id = 0;
            if (parseInt(jQuery("#delete_row").val()) !== 0) {
                delete_id = jQuery("#delete_row").val() + "," + jQuery("#table_list").children("tr").eq(index).children("td").eq(2).children(".option_id").val();
            } else {
                delete_id = jQuery("#table_list").children("tr").eq(index).children("td").eq(2).children(".option_id").val();
            }
            jQuery("#delete_row").val(delete_id);
            jQuery("#table_list").children("tr").eq(index).remove();


            for (var i = 0; i < jQuery(".option_ftext").length; i++) {
                jQuery("#table_list").children("tr").eq(i).children("td").eq(0).html(i + 1);
            }
        });

    });
</script>

<style>
    .edit-tbl {
        border: 1px solid #ccc;
    }

    .edit-tbl td {
        padding: 10px;
    }

    #edit_btn, #cancel_btn {
        display: none;
    }

    legend + .control-group {
        margin-top: 0;
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

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $id); ?>"
      enctype="multipart/form-data" method="post" name="analyze-form" id="analyze-form" class="form-validate">
    <div class="row-fluid">
        <div id="j-main-container" class="span7 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li class="active">
                    <a href="#analyze-quests" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_ANALYZES_QUESTS'); ?></a>
                </li>
                <li>
                    <a href="#analyze-fields" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_ANALYZES_FIELDS'); ?></a>
                </li>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="analyze-quests">
                    <legend><?php echo (empty($this->item->id)) ? JText::_('COM_SURVEYFORCE_NEW_ANALYZES') : JText::_('COM_SURVEYFORCE_EDIT_ANALYZES'); ?></legend>
                    <div class="control-group form-inline">
						<?php echo $this->form->getLabel('title'); ?>
                        <div class="controls">
							<?php echo $this->form->getInput('title'); ?>
                        </div>
                    </div>


                    <div class="control-group form-inline">
						<?php echo $this->form->getLabel('state'); ?>
                        <div class="controls">
							<?php echo $this->form->getInput('state'); ?>
                        </div>
                    </div>


                </div>


                <div class="tab-pane" id="analyze-fields">
					<?php if ($id) { ?>
                        <legend><?php echo JText::_('選項清單'); ?></legend>
                        <div class="control-group form-inline">
                            <table class="table table-striped" id="text_table">
                                <thead>
                                <tr>
                                    <th width="20px" align="center">#</th>
                                    <th width="200px">選項名稱</th>
                                    <th width="20px" align="center">編輯</th>
                                    <th width="20px" align="center">刪除</th>

                                    <th width="auto"></th>
                                </tr>
                                </thead>
                                <tbody id="table_list">
								<?php
								$ii = 1;


								foreach ($this->fields as $fields) {
									if ($fields->fid) {
										?>
                                        <tr>
                                            <td align="center">
												<?php echo $ii ?>
                                            </td>
                                            <td align="left">
												<?php echo $fields->field_title; ?>
                                            </td>

                                            <td align="center">
                                                <a href="javascript: void(0);" class="edit_row" title="<?php echo JText::_('COM_SURVEYFORCE_EDIT'); ?>">
                                                    <img src="<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/images/icon-24-edit.png" border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_EDIT'); ?>">
                                                </a>
                                                <input type="hidden" class="option_ftext" name="option_ftext[]" value="<?php echo $fields->field_title; ?>" />
                                                <input type="hidden" class="option_id" name="option_id[]" value="<?php echo $fields->fid; ?>" />
                                            </td>

                                            <td align="center">
                                                <a href="javascript: void(0);" class="del_row" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>">
                                                    <img src="<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/images/icon-24-delete.png" border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>">
                                                </a>
                                            </td>
                                            <td></td>
                                        </tr>
										<?php
										$ii++;
									}
								}
								?>
                                </tbody>
                            </table>


                            <hr>


                            <div id="new_table" style="text-align:left;  ">
                                <div class="title">新增選項</div>
                                <table border="1" class="edit-tbl">
                                    <tr>
                                        <td>選項名稱</td>
                                        <td>
                                            <input id="new_ftext" style="width:200px " type="text" name="new_ftext" value="">
                                            <input type="hidden" id="old_ftext" /> <input type="hidden" id="old_row" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2" align="center">
                                            <input class="btn" type="button" id="add_btn" style="width:70px " value="新增">
                                            <input class="btn" type="button" id="edit_btn" style="width:70px " value="儲存">
                                            <input class="btn" type="button" id="cancel_btn" style="width:70px " value="取消">
                                        </td>
                                    </tr>
                                </table>
                                <input type="hidden" id="edit_option_id" value="">
                            </div>


                            <br /> <br />
                        </div>
					<?php } else { ?>
                        請先新增題目在編輯選項
					<?php } ?>
                </div>

            </div>
        </div>
    </div>

    <div id="divForm" style="display:none">
        <input type="hidden" name="task" value="" /> <input type="hidden" name="delete_row" id="delete_row" value="0" />
        <input type="hidden" name="quest_id" id="quest_id" value="<?php echo $id; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo JHtml::_('form.token'); ?>
    </div>
</form>

