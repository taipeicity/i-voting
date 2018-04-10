<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$listOrder     = $this->state->get('list.ordering');
$listDirn      = $this->state->get('list.direction');
$saveOrder     = $ordering = $listOrder == 'ordering';
$user          = JFactory::getUser();
$userId        = $user->get('id');
$unit_id       = $user->get('unit_id');
$plugin        = JPluginHelper::getPlugin('system', 'switch');
$exercise_host = json_decode($plugin->params, true);

$extension = 'com_surveyforce';

$saveOrder = $listOrder == 'ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_surveyforce&task=surveys.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$self_gps      = JUserHelper::getUserGroups($user->get('id'));
$print         = JComponentHelper::getParams('com_surveyforce')->get('print');
$show_result   = JComponentHelper::getParams('com_surveyforce')->get('show_result');
$export_result = JComponentHelper::getParams('com_surveyforce')->get('export_result');
$core_review   = JComponentHelper::getParams('com_surveyforce')->get('core_review');
?>
<?php // echo $this->loadTemplate('menu');                             ?>

<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    };
    Joomla.submitbutton = function (task) {
        Joomla.submitform(task);
    };

    jQuery(document).ready(function () {
        jQuery("#btn_clear").bind("click", function () {
            jQuery("#filter_search").val("");
            jQuery("#adminForm").submit();
        });

        jQuery(".disabled").attr("data-original-title", "請先建立選項再做發佈");
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=analyzes'); ?>" method="post" name="adminForm"
      id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
	<?php endif; ?>

    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search"
                       class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?></label>
                <input type="text" name="filter_search" id="filter_search"
                       placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>"
                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i
                            class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                        id="btn_clear"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit"
                       class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable"
                       class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                    <option value="asc" <?php if ($listDirn == 'asc')
						echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                    <option value="desc" <?php if ($listDirn == 'desc')
						echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <table class="table table-striped" id="testimonialsList" style="min-width:1100px;">
            <thead>
            <tr>
                <th width="1%" class="hidden-phone">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', '狀態', 'state', $listDirn, $listOrder); ?>
                </th>

                <th class="nowrap">
					<?php echo JHtml::_('grid.sort', '題目', 'title', $listDirn, $listOrder); ?>
                </th>

                <th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
			<?php
			foreach ($this->items as $i => $item) {
				$ordering   = ($listOrder == 'ordering');
				$canEdit    = $user->authorise('core.edit', $extension . '.analyzes.' . $item->id);
				$canCheckin = $user->authorise('core.create', 'com_checkin');
				$hasFields  = $item->field;
				$canChange  = $user->authorise('core.edit.state', $extension . '.analyzes.' . $item->id) && $canCheckin && $hasFields;
				?>
                <tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">

                    <td class="nowrap center">
		                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>

                    <td class="has-context">
							<?php echo JHtml::_('jgrid.published', $item->state, $i, 'analyzes.', $canChange, 'cb'); ?>
                    </td>

                    <td class="nowrap">
                        <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden; width:200px;">
							<?php if ($canEdit) { ?>
                                <a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=analyze&layout=edit&id=' . $item->id); ?>"
                                   title=" <?php echo $this->escape($item->title); ?>"><?php echo $this->escape($item->title); ?></a>
							<?php } else { ?>
								<?php echo $this->escape($item->title); ?>
							<?php } ?>
                        </div>
                    </td>

                    <td class="center">
						<?php echo $item->id; ?>
                    </td>

                </tr>
			<?php } ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" /> <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

		<?php echo JHtml::_('form.token'); ?>

    </div>

</form>

