<?php
/**
*   @package         Sfmenu
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="'.$item->anchor_css.'" ' : '';
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
} 
else { $linktype = $item->title;
}
//$linktype = $item->params->get('menu_image', '') && $item->params->get('menu_text', 1 ) ? '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' : $item->title;

switch ($item->browserNav) :
	default:
	case 0:
?><a class="menu_link_<?php echo $item->level;?>" title="<?php echo $item->title; ?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
	case 1:
		// _blank
?><a class="menu_link_<?php echo $item->level;?>" title="<?php echo $item->title ."(".JText::_('JBROWSERTARGET_NEW').")"; ?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
	case 2:
		// window.open
?><a class="menu_link_<?php echo $item->level;?>" <?php echo $class; ?>href="<?php echo $item->flink.'&amp;tmpl=component'; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a>
<?php
		break;
endswitch;
