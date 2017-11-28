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
$document = &JFactory::getDocument();
$document->addScript(JURI::base(true) . '/modules/mod_sfmenu/assets/script/superfish.js');
$document->addStyleSheet(JURI::base(true) . '/modules/mod_sfmenu/assets/css/superfish.css');

// Super fish menu script code
ob_start();
require JModuleHelper::getLayoutPath('mod_sfmenu', 'default_script');
$script = ob_get_contents();
ob_end_clean();

// load Javascript compressor library
// See https://github.com/rgrove/jsmin-php/
require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'rd' . DIRECTORY_SEPARATOR . 'jsmin.php';

$script = str_replace('<script>', '', $script);
$script = JSMin::minify($script);

$document = &JFactory::getDocument();
$document->addScriptDeclaration($script);
$act_id = 0;

$menuid = $params->get('tag_id');
?>

<ul class="sf-menu sf-js-enabled menu<?php echo $class_sfx; ?>"<?php
$tag = '';
if ($params->get('tag_id') != NULL) {
	$tag = $params->get('tag_id') . '';
	echo ' id="' . $tag . '"';
}
?>>

	<?php
	$total_item = count($list);
	$count = 0;
	foreach ($list as $i => &$item) :
		$count++;
		$item->title = JString::str_ireplace('&amp;', '&', $item->title);
		$class = '';
		$id_sfx = '';
		if ($item->id == $active_id) {
			$class .= 'current ';
		}

		if (in_array($item->id, $path)) {
			$class .= 'active ';
			$id_sfx = '_now';
		}

		if ($item->deeper) {
			$class .= 'parent ';
		}

		if ($item->level == 2) {
			$class .= 'submenu_1 ';
		}

		if ($item->level == 3) {
			$class .= 'submenu_2 ';
		}

		if (!empty($class)) {
			$class = ' class="' . trim($class) . '"';
		}
		/*		 * **************************************************** */



		//* //
		//第一層級
		if ($item->level == 1) {
			echo '<li id="item-' . $item->id . $id_sfx . '"' . $class . '>';
			//echo level 1 link
			printLink($item);

			//save level 1 "_now" item id
			if ($id_sfx == '_now'):
				$act_id = $item->id;
			endif;

			//have submenu
			if ($item->deeper) {
				echo "<ul class='submenu_warpper' style='float: none; width: 1000px; position: absolute; left: 0; top: 80px;'>";
//				echo "<div style='float: left; white-space: normal;'>";

				echo "<div class='submenu_table'>";
			} else {
				echo "</li>";
			}
			//第二層級
		} elseif ($item->level == 2) {
			echo "<div class='twoblock'>";
			echo "<div class='submenu_list'>";
			echo '<span id="level_tow_' . $item->id . '"' . $class . '>';

			//echo level 2 link
			printLink($item);

			echo "</span>";

			//don't have submenu
			if (!$item->deeper) {
				echo "</div></div>";
			}

			//第三層級
		} elseif ($item->level == 3) {
			echo '<div id="level_three_' . $item->id . '"' . $class . '>';
			printLink($item);
			echo "</div>";

			if ($item->next_level != 3 && $item->deeper == '') {
				echo "</div></div>";
			}
		}

		// 下一層層級為1
		if ($item->next_level == 1 && $item->level != 1) {
			echo "</div>";
//			echo "</div>";
			echo "</ul>";
			echo "</li>";
		}

		// 當選單為最後一項時，不加入分隔線
		if ($item->next_level == 1 && $count != $total_item) {
//			echo "<li><span>｜</span></li>";
		}

	// */

	endforeach;
	?>
</ul>
<div class="open-mainmenu" id="open-mainmenu">
	<span>
		
	</span>
</div>
<?php

function printLink(&$item) {
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_sfmenu', 'default_' . $item->type);
			break;
		default:
			require JModuleHelper::getLayoutPath('mod_sfmenu', 'default_url');
			break;
	endswitch;
}
?>
