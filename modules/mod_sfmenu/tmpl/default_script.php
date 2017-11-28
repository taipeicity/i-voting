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
<script>
;(function(window, $){
	var $mainmenu;
	var $mainmainmenu;
	var $sfmenu;
	var $menuLinks;

	$(function(){
		$sfmenu = $('ul.sf-menu');
		$menuLinks = $('.menu_link_1');
		$submenu_warpper = $('.submenu_warpper');
		
        $sfmenu.superfish({
            autoArrows:  false, 
            dropShadows: false
        }); 
		
		$mainmenu = $('#open-mainmenu');
		$mainmainmenu = $('.mainmainmenu');
		
		$mainmenu.bind('click', function(){
			$('#topmenu').hide();			
			$mainmainmenu.toggle({speed: 500});
		});
		
		initMenuState();
		
		$(document).bind('responsive', initMenuState);
		
	});
	
	var initMenuState = function(){

			$mainmainmenu.show();
			$sfmenu.find('ul').css('position', 'absolute');
			$menuLinks.unbind('click');
			//$sfmenu.addClass('sf-menu');
	}
	
})(window, jQuery);