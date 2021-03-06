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

$imgpath = JURI::base(true) . '/modules/mod_sfmenu/assets/link_opens_new_window.gif';
$onw = '<img src="'.$imgpath.'" alt="('.JText::_('JBROWSERTARGET_NEW').')"/>';

if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'"width=93 height=35" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
} 
else { $linktype = $item->title;
}

switch ($item->browserNav) :
	default:
	case 0:
?><a class="menu_link_<?php echo $item->level;?>" title="<?php echo $item->title; ?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
	case 1:
		// _blank
?>
<!--<a class="menu_link_<?php echo $item->level;?>" title="<?php echo $item->title ."(".JText::_('JBROWSERTARGET_NEW').")"; ?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype . $onw; ?></span></a>-->
<a class="menu_link_<?php echo $item->level;?>" title="<?php echo $item->title ."(".JText::_('JBROWSERTARGET_NEW').")"; ?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype ; ?></span></a>
	<?php
		break;
	case 2:
		// window.open
		$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');
?><a class="menu_link_<?php echo $item->level;?>" <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $attribs;?>');return false;" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
endswitch;
