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
?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#open-mainmenu").one("click", nav1);

		function nav1() {
			jQuery("#open-mainmenu").addClass("active");
			jQuery(".mainmenu_nav").animate({left:"0px"});
			jQuery(".all").animate({left:"161px"});
			jQuery(this).one("click", nav2);}
		function nav2() {
			jQuery("#open-mainmenu").removeClass("active");
			jQuery(".mainmenu_nav").animate({left:"-161px"});
			jQuery(".all").animate({left:"0px"});
			jQuery(this).one("click", nav1);}

//		jQuery('.submenu_s1').siblings('.submenu_s2').hide();
//		jQuery('.submenu_s2').find('.submenu_s3').hide();
//		jQuery('.submenu_s1.active').siblings('.submenu_s2').show();
//		jQuery('.submenu_s2.active').find('.submenu_s3').show();
//
//		jQuery('.submenu_s1').click(function(){
//			jQuery('.submenu_s1').not(this).siblings('.submenu_s2').hide("slow");
//			jQuery(this).siblings('.submenu_s2').toggle("slow");
//		});
//
//		jQuery('.submenu_s2').click(function() {
//			jQuery('.submenu_s2').not(this).find('.submenu_s3').hide("slow");
//			jQuery(this).find('.submenu_s3').toggle("slow");
//		});
	 });
</script>

<div class="mainmenu_nav">
<ul class="mainmenu mainmenu_s menu<?php echo $class_sfx;?>">
<?php
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
		$class .= 'submenu_s1 ';
	}
	
	if ($item->level == 3) {
		$class .= 'submenu_s2 ';
	}
	
	if ($item->level == 4) {
		$class .= 'submenu_s3 ';
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
		printLink_s($item);
		
		//have submenu
		if($item->deeper){
			echo "<div>";
			echo "<div class='submenu_table'>";
		} else {
			echo "</li>";
		}
	//第二層級
	} elseif( $item->level == 2 ) {
		echo "<div class='twoblock'>";
		echo "<div class='submenu_list'>";
		echo '<span '.$class.'>';
		
		//echo level 2 link
		printLink_s($item);
		
		echo "</span>";
		
		//don't have submenu  
		if( ! $item->deeper ){
			echo "</div></div>";
		}
		
	//第三層級
	} elseif( $item->level == 3 ) {
		echo '<div '.$class.'>';
		printLink_s($item);
		
	//第四層級
	}elseif($item->level == 4 ) {
		echo '<div ' . $class . '>';
		printLink_s($item);
		echo "</div>";
	}

	if($item->level == 3 || $item->level == 4) {
		if($item->next_level != 4 ){
			echo "</div>";
		}
		
		if( $item->next_level != 3 && $item->next_level != 4 ){
			echo "</div></div>";
		}	
	}


	// 下一層層級為1
	if( $item->next_level == 1 && $item->level != 1){
		echo "</div>";
		echo "</div>";
		echo "</li>";
	} 
	
	// */
endforeach;
?></ul>
</div>

<?php
	function printLink_s( $item ){
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

