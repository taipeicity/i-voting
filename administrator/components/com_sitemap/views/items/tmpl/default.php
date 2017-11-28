<?php
/**
*   @package         Sitemap
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die;
// load tooltip behavior
JHtml::_('behavior.tooltip');
?>

<?php
//print_r($this->state);
$uri = JFactory::getUri();
$return = base64_encode($uri);
$user = JFactory::getUser();
$userId = $user->get('id');
//print_r($this->get('State'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_sitemap'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
			<div id="j-main-container">
			<?php endif; ?>
			<table class="adminlist table-striped table">
				<thead>

				<th width="5%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>)" />
				</th>
				<th align="center">語系</th>
				<th align="center">排除的選單ID</th>
				<th align="center">ID</th>

				</thead>

				<tfoot>
					<tr>
						<td colspan="<?php echo $this->fieldCount + 1; ?>"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>

				<tbody>

					<?php
					foreach ($this->items as $i => $item) {
						?>
						<tr class="row<?php echo $i % 2; ?>">

							<td align="center" width="3%">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>

							<td align="center" >
								<a href="index.php?option=com_sitemap&task=item.edit&id=<?php echo $item->id; ?>"><?php echo $item->title; ?></a>
							</td>

							<td align="center" >
								<?php echo $item->exclude; ?>
							</td>

							<td align="center" width="3%" >
								<?php echo $item->id; ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>

			</table>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
</form>