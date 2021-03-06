<?php
/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div class="category-list">
	<?php if ($this->items) { ?>
		<div class="category-content">
			<div class="page-header">
				<h2><?php echo $this->page_title; ?></h2>
			</div>
			<div class="issues">
				<?php foreach ($this->items as $item) { ?>
					<div class="issue">
						<div class="img">
							<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=place_category.start_vote&sid=' . $item->id . '&Itemid=' . $this->itemid, false); ?>">
								<img src="<?php echo $item->image; ?>" alt="<?php echo $item->title; ?>" >
							</a>
						</div>
						<div class="title">
							<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=place_category.start_vote&sid=' . $item->id . '&Itemid=' . $this->itemid, false); ?>">
								<?php
								 if(utf8_strlen($item->title) > $title_limit )
									$item->title = utf8_substr($item->title, 0, $title_limit) . '...';
								echo $item->title;
								?>
							</a>
						</div>

						<div class="info">
							<span>開始投票時間：</span><?php echo JHtml::_('date', $item->vote_start, JText::_('DATE_FORMAT_LC4')); ?>
							<br />
							<span>發佈單位：</span><?php echo $item->unit_title; ?>
						</div>

						<div class="more">
							<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&task=place_category.start_vote&sid=' . $item->id . '&Itemid=' . $this->itemid, false); ?>">開始投票</a>
						</div>
					</div>
				<?php } ?>
			</div>

		</div>
		<?php
	}
	?>



</div>
