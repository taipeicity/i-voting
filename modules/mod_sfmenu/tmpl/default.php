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


?>


<div class="mainmainmenu">
<ul class="sf-menu sf-js-enabled menu<?php echo htmlspecialchars_decode( $params->get('moduleclass_sfx') );?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php


//echo "<pre>";
//print_r($list);
//echo "</pre>";

foreach ($list as $i => &$item) :
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
		$class = ' class="'.trim($class) .'"';
	}
	/*******************************************************/
	
	
	//* //
	//第一層級
	if( $item->level == 1 ){
		echo '<li id="item-'.$item->id.$id_sfx.'"'.$class.'>';
		
		//echo level 1 link
		// printLink(&$item); //php5.3 syntax
		printLink($item);    //php5.4 syntax
		
		//have submenu
		if($item->deeper){
			echo "<ul class='submenu_warpper' style='float: none; width: auto; visibility: visible; display: block;'>";
			echo "<li style='float: left; width: 100%; white-space: normal;'>";
			echo "<table class='submenu_table' align='right'>";
			echo "<tbody>";
			echo "<tr>";
		} else {
			echo "</li>";
		}
	//第二層級
	} elseif( $item->level == 2 ) {
		echo "<td style='height: 10px;' valign='top'>";
		echo "<div class='submenu_list'>";
		echo '<span id="level_tow_'.$item->id.'"'.$class.'>';
		
		//echo level 2 link
		// printLink(&$item); //php5.3 syntax
		printLink($item);    //php5.4 syntax
		
		echo "</span>";
		
		//don't have submenu  
		if( ! $item->deeper ){
			echo "</div></td>";
		}
		
	//第三層級
	} elseif( $item->level == 3 ) {
		echo '<div id="level_three_'.$item->id.'"'.$class.'>';
		// printLink(&$item); //php5.3 syntax
		printLink($item);    //php5.4 syntax
		echo "</div>";
		
		 
		if( $item->next_level != 3 ){
			echo "</div></td>";
		}
	}

	// 下一層層級為1
	if( $item->next_level == 1 && $item->level != 1){
		echo "</tr>";
		echo "</tbody>";
		echo "</table>";
		echo "</li>";
		echo "</ul>";
		echo "</li>";
	} 
	
	// */
	
endforeach;
?></ul>
</div>

<?php 
	function printLink( &$item ){
		switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_sfmenu', 'default_'.$item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_sfmenu', 'default_url');
			break;
		endswitch;
	}
?>

