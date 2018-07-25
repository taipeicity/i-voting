<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite()) {
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}


JHtml::_('bootstrap.tooltip');

$id        = $app->input->getInt('id', 0);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$publishFields  = $this->getPublishFields();
$requiredFields = $this->getRequiredFields();
$nowDate        = $this->nowDate;

$canEdit = false;
if ($this->item->created_by == $this->user_id || in_array($this->core_review, $this->self_gps)) {
	if ($this->end) { // 判斷議題是否已結束
		$canEdit = false;
	} else {
		$canEdit = true;
	}
}

if ($this->canDo->get('core.own')) { // 系統管理者
	$canEdit = true;
}

?>
<script type="text/javascript" src="../media/media/js/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../media/media/css/jquery.fancybox.css?v=2.1.5" media="screen" />
<script type="text/javascript">
    function clr() {
        jQuery("#filter_search").val("");
        jQuery("#adminForm").submit();
    }


    jQuery(document).ready(function () {

        var td = jQuery(".quest_title, .order");
        td.css("line-height", td.height() + "px");

        // 狀態
        jQuery(".publish_quest").on("click", function () {

            var quest = jQuery(".publish_quest");
            var index = quest.index(this);

            if (quest.eq(index).attr("disabled") === "disabled") {
                return false;
            }

            var publish = jQuery(".publish").eq(index).val();
            var analyze_id = jQuery(".analyze_id").eq(index).val();
            var action = "publish";
            var url = "index.php?option=com_surveyforce&task=survey.analyze";
            jQuery.post(url, {publish: publish, analyze_id: analyze_id, action: action}, function () {
                jQuery("#adminForm").submit();
            });
        });

        // 必填
        jQuery(".required_quest").on("click", function () {

            var required_quest = jQuery(".required_quest");
            var index = required_quest.index(this);

            if (required_quest.eq(index).attr("disabled") === "disabled") {
                return false;
            }

            var required = jQuery(".required").eq(index).val();
            var analyze_id = jQuery(".analyze_id").eq(index).val();
            var action = "required";
            var url = "index.php?option=com_surveyforce&task=survey.analyze";
            jQuery.post(url, {required: required, analyze_id: analyze_id, action: action}, function () {
                jQuery("#adminForm").submit();
            });
        });

        jQuery("label").click(function () {
            jQuery(this).removeClass();
        });


        jQuery("#btnFieldLink").fancybox();

        jQuery(".field").on("click", function () {
            jQuery("ul, legend").remove();
            var field = jQuery(".field");
            var index = field.index(this);
            var sid = "<?php echo $this->survey_id ?>";
            var quest_id = jQuery(".quest_id").eq(index).val();
            var action = "fields";
            var url = "index.php?option=com_surveyforce&task=survey.analyze";
            jQuery.post(url, {quest_id: quest_id, action: action, id: sid}, function (result) {
                if (result) {
                    result = JSON.parse(result);
                    var legend = "<legend><h2 class=\"nowrap\">" + result[0]["quest_title"] + "</h2></legend>"
                    var ul = "<ul>";
                    for (var i = 0; i < result.length; i++) {
                        ul += "<li class=\"nowrap\"><strong>" + result[i]["field_title"] + "</strong></li>";
                    }
                    ul += "</ul>";
                    jQuery(".fontsize").append(legend + ul);
                    jQuery("#btnFieldLink").attr("title", result[0]["quest_title"]).trigger('click');
                }
            });
        });
    });
</script>

<style>
    li {
        margin-bottom: 5%;
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

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=survey&layout=column&tmpl=component&id=' . $id . '&' . JSession::getFormToken() . '=1'); ?>"
      method="post" name="adminForm" id="adminForm" class="form-inline">

    <fieldset class="filter">
        <div class="btn-toolbar">
            <div class="btn-group">
                <input type="text" class="hasTooltip" data-placement="bottom" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="搜尋" size="30" title="<?php echo JText::_('COM_SURVEYFORCE_FILTER_SEARCH'); ?>" />
            </div>
            <div class="btn-group">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
                    <span class="icon-search"></span><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
                </button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="javascript:clr();">
                    <span class="icon-remove"></span><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>
            <div class="btn-group pull-right">
                <label for="sortRequired" class="element-invisible"><?php echo JText::_('篩選發佈'); ?></label>
                <select name="sortRequired" id="sortRequired" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_("- 選擇是否必填 -"); ?></option>
					<?php echo JHtml::_('select.options', $requiredFields, 'value', 'text', $this->state->get('filter.required'), true); ?>

                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortPublish" class="element-invisible"><?php echo JText::_('篩選發佈'); ?></label>
                <select name="sortPublish" id="sortPublish" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_("- 選擇是否發布 -"); ?></option>
					<?php echo JHtml::_('select.options', $publishFields, 'value', 'text', $this->state->get('filter.publish'), true); ?>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label style="line-height: 25px;">
					<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
                </label>
            </div>
        </div>

        <hr class="hr-condensed" />
    </fieldset>

	<?php if (empty($this->AnalyzeColumns)) { ?>
        <div class="alert alert-no-items">
			<?php echo JText::_('查無資料'); ?>
        </div>
	<?php } else { ?>
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th width="3%" class="nowrap center">
					<?php echo JHtml::_('utility.sort', '發佈', 'a.publish', $listDirn, $listOrder); ?>
                </th>
                <th width="3%" class="nowrap center">
					<?php echo JHtml::_('utility.sort', '必填', 'a.required', $listDirn, $listOrder); ?>
                </th>
                <th style="min-width:100px" class="nowrap">
					<?php echo JHtml::_('utility.sort', '題目', 'quest_title', $listDirn, $listOrder); ?>
                </th>
                <th width="3%" class="nowrap center">
                    選項
                </th>
            </tr>
            </thead>
            <tbody>

			<?php $i = 0; ?>
			<?php foreach ($this->AnalyzeColumns as $quest_title => $items) { ?>
                <tr class="row<?php echo $i % 2; ?> quest_row">
					<?php
					$attr = "title=\"" . JHtml::tooltipText(($items["publish"] == 0) ? "發佈此項目" : "已經發佈") . "\"";
					if ($this->item->is_checked && !$this->canDo->get('core.own')) {
						$attr .= " disabled";
					}

					if (!$canEdit) {
						$attr .= " disabled";
					}
					?>

                    <td class="center">
                        <a href="javascript: void(0)" class="btn hasTooltip publish_quest" <?php echo $attr; ?>>
                            <span class="icon-<?php echo ($items["publish"] == 0) ? "delete" : "save"; ?>" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td class="center">
						<?php
						if ($items["publish"] == 0) {
							$attr = "title=\"" . JHtml::tooltipText("請先發佈此題目") . "\" disabled";
						} else {
							$attr = "title=\"" . (JHtml::tooltipText(($items["required"] == 0) ? "發佈此項目" : "已經發佈")) . "\"";
						}

						if ($this->item->is_checked && !$this->canDo->get('core.own')) {
							$attr .= " disabled";
						}

						if (!$canEdit) {
							$attr .= " disabled";
						}
						?>
                        <a href="javascript: void(0)" class="btn hasTooltip required_quest" <?php echo $attr; ?> >
                            <span class="icon-<?php echo ($items["required"] == 0) ? "delete" : "save"; ?>" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td class="quest_title">
						<?php echo $quest_title; ?>
                        <input type="hidden" class="quest_id" value="<?php echo $items['quest_id']; ?>" />
                        <input type="hidden" class="publish" value="<?php echo $items['publish']; ?>" />
                        <input type="hidden" class="required" value="<?php echo $items['required']; ?>" />
                        <input type="hidden" class="analyze_id" value="<?php echo $items['analyze_id']; ?>" />
                    </td>
                    <td>
                        <input type="button" class="btn hasTooltip field" value="觀看" title="觀看選項">
                    </td>
                </tr>
				<?php $i++; ?>

			<?php } ?>
            </tbody>
        </table>
	<?php } ?>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
    </div>
</form>


<a href="#divFieldLink" id="btnFieldLink" title="選項" style="display:none">選項</a>
<div id="divFieldLink" style="display:none;">
    <fieldset class="fontsize">

    </fieldset>
</div>