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
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
?>

<div class="row">
    <?php foreach ($this->items as $item): ?>
        <?php
        $created = JHtml::_('date', $item->created, 'Y-m-d', null);
        $className = $item->selectimg == 2 ? ' youtube' : '';
        $isImg = $item->selectimg == 1;
        ?>

        <a href="<?php echo Route::_("index.php?option=com_knowus&id={$item->id}&Itemid={$this->itemid}",
            false); ?>" title="<?php echo $this->escape
        ($item->title); ?>" class="column">

            <figure class="content">
                <div class="img<?php echo $this->escape($className); ?>">
                    <?php if ($isImg): ?>
                        <img src="<?php echo $this->escape($item->img); ?>" alt="<?php echo $this->escape
                        ($item->title); ?>">
                    <?php else: ?>
                        <img src="https://i.ytimg.com/vi/<?php echo $this->escape($item->videoId); ?>/hqdefault.jpg;
                        ?>" alt="<?php echo $this->escape($item->title); ?>">
                    <?php endif; ?>
                </div>
                <figcaption>
                    <div class="tag">
                        <div class="unit"><?php echo $this->escape($item->unit); ?> | <?php echo $this->escape
                            ($created); ?></div>
                        <div class="cat"><?php echo $this->escape($item->category_title); ?></div>
						</div>
                    <h3 class="subject ellipsis"><?php echo $this->escape($item->title); ?></h3>
                    <p class="two_lines"><?php echo strip_tags($item->content); ?></p>
                </figcaption>
            </figure>
        </a><!-- .column -->
    <?php endforeach; ?>
</div>  <!-- .row -->

<div class="pagination">

</div>