<?php
/**
 * 紀錄管理 - 清單顯示UI
 * 
 * @version    CVS: 1.0.0
 * @package    com_blockchain
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_blockchain&task=items.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'tableList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}


?>
<script>
	jQuery(document).ready(function(){
		<?php
			// 若是有使用篩選功能
			if ($this->state->get('filter.published') != "" || $this->state->get('filter.type_id') != "" || $this->state->get('filter.survey_id') != "" || $this->state->get('filter.start_time') != "" || $this->state->get('filter.end_time') != "") {
		?>
			jQuery(".js-stools-btn-filter").trigger("click");
		<?php
			}
		?>
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_record&view=blockchains'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="tableList">
				<thead>
					<tr>
						<th width="100px" class="center">
							<?php echo JHtml::_('searchtools.sort', '發生時間', 'a.created', $listDirn, $listOrder); ?>
						</th>
						
						<th width="100px" class="center">
							<?php echo JHtml::_('searchtools.sort', '議題編號', 'a.survey_id', $listDirn, $listOrder); ?>
						</th>
						
						<th width="200px">議題名稱</th>
						
						<th width="200px">失敗訊息</th>
						
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="99">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create',     'com_blockchain.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_blockchain.category.' . $item->catid);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_blockchain.category.' . $item->catid) && $canCheckin;

					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="center">
							<?php echo JHtml::_('date', $item->created, 'Y-m-d H:i:s') ; ?>
						</td>
						
						<td class="center">
							<?php echo $item->survey_id; ?>
						</td>
						
						<td>
							<?php echo $item->survey_title; ?>
						</td>
						
						<td>
							<?php echo $item->msg; ?>
						</td>
						
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
