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

$isVideo = $this->item->selectimg == 2;

if (preg_match("/(\?|&)v=([^&#]+)/", $this->item->youtube_url)) {
    preg_match("/(\?|&)v=([^&#]+)/", $this->item->youtube_url, $matches);
    $videoId = array_pop($matches);
} else if (preg_match("/(\.be\/)+([^\/]+)/", $this->item->youtube_url)) {
    preg_match("/(\.be\/)+([^\/]+)/", $this->item->youtube_url, $matches);
    $videoId = array_pop($matches);
} else if (preg_match("/(\embed\/)+([^\/]+)/", $this->item->youtube_url)) {
    preg_match("/(\embed\/)+([^\/]+)/", $this->item->youtube_url, $matches);
    $videoId = array_pop($matches);
}

$modified = JHtml::_('date', $this->item->modified, 'Y-m-d', null);
$created = JHtml::_('date', $this->item->created, 'Y-m-d', null);

?>

<div class="page-info">
    <div class="toolsbar">
        <?php echo JHtml::_('toolsbar._default'); ?>
    </div>

    <div class="published" itemscope>
        <time datetime="
        <?php echo $this->escape($created); ?>" itemprop="datePublished">
            <?php echo JText::sprintf('發布日期：%s', $this->escape($created)); ?>
        </time>
    </div>
</div>


<div class="intro">
    <h3 class="subject"><?php echo $this->escape($this->item->title); ?></h3>

    <?php if ($isVideo): ?>
        <div class="embed_container">
            <iframe src="https://www.youtube.com/embed/<?php echo $this->escape($videoId); ?>"
                    frameborder="0"
                    allowfullscreen
					title="<?php echo $this->escape($this->item->title); ?>"
					></iframe>
        </div>
    <?php endif; ?>

    <div>
        <div class="cat" itemscope=""><?php echo $this->escape($this->item->unit); ?> | <span
                    itemprop="genre"><?php echo $this->escape($modified); ?></span>
        </div>

        <div class="article">
            <?php echo $this->item->content; ?>
        </div>

    </div>

</div>


<div class="page-info update-date">
    <div class="published" itemscope>
        <time datetime="<?php echo $this->escape($modified); ?>" itemprop="dateModified">
            <?php echo $this->escape($modified); ?>
        </time>
    </div>
</div>

<script type="application/javascript">
  document.querySelector("body").classList.add("view-detail");
</script>
