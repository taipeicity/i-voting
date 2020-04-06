<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Knowus
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

// No direct access
defined('_JEXEC') or die;


use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_knowus/assets/css/knowus.css');
$document->addStyleSheet(Uri::root() . 'media/com_knowus/css/list.css');

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_knowus');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_knowus&task=list.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'detailList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_knowus&view=list'); ?>" method="post"
      name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif; ?>

            <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

            <div class="clearfix"></div>
            <table class="table table-striped" id="detailList">
                <thead>
                <tr>
                    <?php if (isset($this->items[0]->ordering)): ?>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                    <?php endif; ?>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <?php if (isset($this->items[0]->state)): ?>
                        <th width="1%" class="nowrap center">
                            <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>


                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', '標題', 'a.`title`', $listDirn, $listOrder); ?>
                    </th>

                    <th class='left'>圖片</th>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', '發佈單位', 'a.`unit`', $listDirn, $listOrder); ?>
                    </th>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', '建立時間', 'a.`created`', $listDirn, $listOrder); ?>
                    </th>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', '修改時間', 'a.`modified`', $listDirn, $listOrder); ?>
                    </th>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_KNOWUS_LIST_ID', 'a.`id`', $listDirn, $listOrder); ?>
                    </th>


                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                <?php foreach ($this->items as $i => $item) :
                $ordering = ($listOrder == 'a.ordering');
                $canCreate = $user->authorise('core.create', 'com_knowus');
                $canEdit = $user->authorise('core.edit', 'com_knowus');
                $canCheckin = $user->authorise('core.manage', 'com_knowus');
                $canChange = $user->authorise('core.edit.state', 'com_knowus');
                ?>
                <tr class="row<?php echo $i % 2; ?>">

                    <?php if (isset($this->items[0]->ordering)) : ?>
                        <td class="order nowrap center hidden-phone">
                            <?php if ($canChange) :
                                $disableClassName = '';
                                $disabledLabel = '';

                                if (!$saveOrder) :
                                    $disabledLabel = Text::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                endif; ?>
                                <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
                                      title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>

                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo
                                $item->ordering; ?>" class="width-20 text-area-order "/>
                            <?php else : ?>
                                <span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>


                    <td class="hidden-phone">
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                    </td>
                    <?php if (isset($this->items[0]->state)): ?>
                        <td class="center">
                            <?php echo JHtml::_('jgrid.published', $item->state, $i, 'list.', $canChange, 'cb'); ?>
                        </td>
                    <?php endif; ?>


                    <td>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_knowus&task=detail.edit&id=' . (int)
                                $item->id); ?>"><?php echo $item->title; ?></a>
                        <?php else : ?>
                            <?php echo $item->title; ?>
                        <?php endif; ?>


                        <div class="small">
                            <?php echo JText::_('JFIELD_ALIAS_LABEL') . ': ' . $this->escape($item->alias); ?>
                        </div>

                        <div class="small">
                            <?php echo JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
                        </div>
                    </td>

                    <td class="<?php echo $item->selectimg == 2 ? 'youtube' : 'img'; ?>">
                        <?php
                        if ($item->selectimg == 1) :
                            echo JHtml::_('image', $item->img, $item->title, ['class' => 'list-image']);
                        else:
                        $thumbnail = "https://i.ytimg.com/vi/{$this->escape($item->videoId)}/hqdefault.jpg";
                        echo JHtml::_('image', $thumbnail, $item->alias, ['class' => 'list-image']);
                        endif;
                        ?>
                    </td>

                    <td><?php echo $item->unit; ?></td>
                    <td><?php echo $item->created; ?></td>
                    <td><?php echo $item->modified; ?></td>

                    <td>
                        <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'list.', $canCheckin); ?>
                        <?php endif; ?>

                        <?php echo $this->escape($item->id); ?>
                    </td>

                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
</form>
<script>
  window.toggleField = function (id, task, field) {

    var f = document.adminForm, i = 0, cbx, cb = f[id];

    if (!cb) return false;

    while (true) {
      cbx = f["cb" + i];

      if (!cbx) break;

      cbx.checked = false;
      i++;
    }

    var inputField = document.createElement("input");

    inputField.type = "hidden";
    inputField.name = "field";
    inputField.value = field;
    f.appendChild(inputField);

    cb.checked = true;
    f.boxchecked.value = 1;
    window.submitform(task);

    return false;
  };
</script>