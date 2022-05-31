<?php
/**
 * 紀錄管理 - 清單顯示UI
 * 
 * @version    CVS: 1.0.0
 * @package    com_record
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
	$saveOrderingUrl = 'index.php?option=com_record&task=items.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'tableList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

// 是否有依"前台顯示排序"
$is_ordering = false;


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

<form action="<?php echo JRoute::_('index.php?option=com_record&view=items'); ?>" method="post" name="adminForm" id="adminForm">
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
						<th class="center">
							<?php echo JHtml::_('searchtools.sort', 'API類型', 'a.type_id', $listDirn, $listOrder); ?>
						</th>
						
						<th class="center">
							<?php echo JHtml::_('searchtools.sort', '狀態', 'a.state', $listDirn, $listOrder); ?>
						</th>
						
						<th class="center">
							<?php echo JHtml::_('searchtools.sort', '接收參數時間', 'a.request_time', $listDirn, $listOrder); ?>
						</th>
						
						<th class="center">
							<?php echo JHtml::_('searchtools.sort', '回傳參數時間', 'a.response_time', $listDirn, $listOrder); ?>
						</th>
						
						<th class="center">
							<?php echo JHtml::_('searchtools.sort', '作業時間', 'a.execute_second', $listDirn, $listOrder); ?>
						</th>

						<th width="200px">接收參數</th>
						<th width="200px">回傳參數</th>
						<th width="200px">失敗原因</th>
						
						<th width="100px" class="center">
							<?php echo JHtml::_('searchtools.sort', '議題編號', 'a.survey_id', $listDirn, $listOrder); ?>
						</th>

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
					$canCreate  = $user->authorise('core.create',     'com_record.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_record.category.' . $item->catid);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_record.category.' . $item->catid) && $canCheckin;

					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="center">
							<?php echo $item->type_title; ?>
						</td>
						
						<td class="center">
							<?php echo $item->state_title; ?>
						</td>

						<td class="center">
							<?php echo JHtml::_('date', $item->request_time, 'Y-m-d H:i:s') ; ?>
						</td>

						<td class="center">
							<?php echo JHtml::_('date', $item->response_time, 'Y-m-d H:i:s') ; ?>
						</td>

						<td class="center">
							<?php echo number_format($item->execute_second, 3); ?>s
						</td>
					
						<td>
							<button type="button" class="btn request_params_btn" data-id="<?php echo $item->id; ?>">展開</button>
							<div class="request_params" id="request_params_<?php echo $item->id; ?>">
								<?php 
									echo "<pre>";
									print_r(json_decode($item->request_params, true));
									echo "</pre>";
//									echo $item->request_params; 
								?>
							</div>
						</td>
						
						<td>
							<button type="button" class="btn response_params_btn" data-id="<?php echo $item->id; ?>">展開</button>
							<div class="response_params" id="response_params_<?php echo $item->id; ?>">
								<?php 
									echo "<pre>";
									print_r(json_decode($item->response_params, true));
									echo "</pre>";
//									echo $item->response_params; 
								?>
							</div>
						</td>
						
						<td>
							<?php echo $item->msg; ?>
						</td>
						
						<td class="center">
							<?php echo $item->survey_id; ?>
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

<style>
	.request_params, .response_params {
		display: none;
	}
</style>

<script>
	
    jQuery(document).ready(function () {
		
		// 接收參數
		jQuery(".request_params_btn").click(function(e) {
			_id = jQuery(this).data("id");
			jQuery("#request_params_" + _id).toggle();
			
			if (jQuery(this).hasClass("btn-warning")) {
				jQuery(this).html("展開");
			} else {
				jQuery(this).html("收合");
			}
			jQuery(this).toggleClass("btn-warning");
		});
		
		// 回傳參數
		jQuery(".response_params_btn").click(function(e) {
			_id = jQuery(this).data("id");
			jQuery("#response_params_" + _id).toggle();
			
			if (jQuery(this).hasClass("btn-warning")) {
				jQuery(this).html("展開");
			} else {
				jQuery(this).html("收合");
			}
			jQuery(this).toggleClass("btn-warning");
		});
		

    });
	
	


</script>