<?php
/**
 *   @package         Surveyforce
 *   @version           1.1-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_surveyforce.question');
$saveOrder = $ordering = $listOrder == 'ordering';

$extension = 'com_surveyforce';

$saveOrder = $listOrder == 'ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_surveyforce&task=questions.saveOrderAjax&tmpl=component&surv_id=' . JFactory::getApplication()->input->getCmd('surv_id');
	JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<?php // echo $this->loadTemplate('menu');  ?>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/css/thickbox/thickbox.css" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JURI::root(); ?>administrator/components/com_surveyforce/assets/js/thickbox/thickbox.js" ></script>
<script type="text/javascript">
	Joomla.orderTable = function ()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		} else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	function sf_listItemTask(a, b) {

		var c = document.adminForm, d = document.getElementById(a);
		if (d) {
			for (var f = 0; ; f++) {
				var e = c["cb" + f];
				if (!e)
					break;
				e.checked = !1
			}

			d.checked = !0;
			c.boxchecked.value = 1;
			submitbutton(b);
		}
	}

</script>
<style>
	.title {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		word-wrap: normal;
		width: 500px;
	}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=questions&surv_id=' . JFactory::getApplication()->input->getCmd('surv_id')); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<?php endif; ?>



    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
						this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
<?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
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
        <div class="clearfix"> </div>
        <table class="table table-striped" id="surveyforceList">
            <thead>
                <tr>
					<th width="1%" class="nowrap center hidden-phone">
<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>


                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="1%">
                        &nbsp;
                    </th>
                    <th class="nowrap center">
<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_TEXT', 'sf_qtext', $listDirn, $listOrder); ?> 
                    </th>

					<th width="10%" class="nowrap center">
<?php echo JHtml::_('grid.sort', '題型', 'question_type', $listDirn, $listOrder); ?>
                    </th>


                    <th width="1%" class="nowrap center">
<?php echo JHtml::_('grid.sort', '發佈狀態', 'published', $listDirn, $listOrder); ?>
                    </th>


                    <th width="1%" class="nowrap center">
<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_REORDER', 'ordering', $listDirn, $listOrder); ?>
						<?php if ($canOrder && $saveOrder) : ?>
							<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'questions.saveorder'); ?>
						<?php endif; ?>

                    </th>



					<th width="1%" class="nowrap center">
<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="14">
<?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody><?php $count = count($this->items); ?>

<?php if (isset($this->items[0]) && count($this->items[0])): ?>
					<?php
					foreach ($this->items[0] as $i => $item) :
						$ordering = ($listOrder == 'ordering');
						$canEdit = ( $user->authorise('core.edit', $extension . '.questions.' . $item->id) && $item->sf_qtype != 'Page Break');
						$canCheckin = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canChange = $user->authorise('core.edit.state', $extension . '.questions.' . $item->id) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
							<td class="order nowrap center hidden-phone">
								<?php
								if ($canChange) :
									$disableClassName = '';
									$disabledLabel = '';
									if (!$saveOrder) :
										$disabledLabel = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif;
									?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
										<i class="icon-menu"></i>
									</span>
		<?php else : ?>
									<span class="sortable-handler inactive" >
										<i class="icon-menu"></i>
									</span>
		<?php endif; ?>
							</td>


							<td class="center">
		<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>

							<td class="nowrap has-context"  colspan="2">
								<div class="pull-left title">
									<?php if ($canEdit) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=question.edit&id=' . $item->id); ?>"><?php echo $this->escape(str_replace("&nbsp;", '', strip_tags($item->sf_qtext))); ?></a>
									<?php else : ?>
										<?php echo $this->escape(str_replace("&nbsp;", '', strip_tags($item->sf_qtext))); ?>
		<?php endif; ?>
								</div>
							</td>

							<td class="has-context">
								<div class="center">
		<?php echo $this->question_types[$item->question_type]['name']; ?>
								</div>
							</td>


							<td class="center">
		<?php echo ($item->published) ? "發佈" : "未發佈"; ?>
							</td>


							<td class="order center">
									<?php if ($canChange) : ?>
									<div class="input-prepend">
										<?php if ($saveOrder) : ?>
				<?php if ($listDirn == 'asc') : ?>
												<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'question.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
												<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, $count, true, 'question.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
				<?php elseif ($listDirn == 'desc') : ?>
												<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'question.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
												<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, $count, true, 'question.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
											<?php endif; ?>
										<?php endif; ?>
			<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
										<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="width-20 text-area-order" />
										<input type="hidden" name="item_ids[]" value="<?php echo $item->id; ?>" />
									</div>
								<?php else : ?>
									<?php echo $item->ordering; ?>
		<?php endif; ?>
							</td>




							<td class="center">
		<?php echo $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
<?php endif; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="surv_id" value="<?php echo JFactory::getApplication()->input->getCmd('surv_id'); ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="boxcheckedsection" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>

    </div>
    <script type="text/javascript">

		function SF_sectionChecked(checkbox)
		{
			var value = document.adminForm.boxcheckedsection.value;
			if (checkbox.checked) {
				value++;
			} else {
				value--;
			}

			document.adminForm.boxcheckedsection.value = value;
		}

		jQuery('.icon-downarrow').parent().each(function () {
			var clk = jQuery(this).attr("onclick");
			clk = clk.replace(/listItemTask/g, "sf_listItemTask");
			jQuery(this).attr("onclick", clk);
		});

		jQuery('.icon-uparrow').parent().each(function () {
			var clk = jQuery(this).attr("onclick");
			clk = clk.replace(/listItemTask/g, "sf_listItemTask");
			jQuery(this).attr("onclick", clk);
		});

    </script>


</form>