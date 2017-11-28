<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// No direct access
defined('_JEXEC') or die;

if($this->params->get('lang') == "ch"){
	$link_target=$this->params->get("link_target","_blank");
	$display_search_form=$this->params->get("display_search_form",1);
	$search_engine_id=$this->params->get("search_engine_id");
}elseif($this->params->get('lang') == "en"){
	$link_target=$this->params->get("en_link_target");
	$display_search_form=$this->params->get("en_display_search_form",1);
	$search_engine_id=$this->params->get("en_search_engine_id");
}else{
	$link_target= "_blank";
	$display_search_form = 1;
}
?>

<?php if(!$display_search_form) {?>
<style>
#___gcse_0 {
    display: none !important;
}
</style>
<?php }?>

<div class="gcse-searchbox" data-gname="gselement" data-queryParameterName="gsquery" ><?php echo JText::_("COM_ITPGOOGLESEARCH_LOADING")?></div>
<div class="gcse-searchresults" data-linktarget="<?php echo $link_target;?>" data-gname="gselement" ></div>

<script>

var initGoogleSearchBox = function() {
	
  if (document.readyState == 'complete') {
	  
	  var element = google.search.cse.element.getElement("___gcse_0");
  } else {
	  
    // Document is not ready yet, when CSE element is initialized.
    google.setOnLoadCallback(function() {

       var element = google.search.cse.element.getElement("gselement");
       <?php if(!empty($this->phrase)) {?>
       element.execute('<?php echo htmlspecialchars($this->phrase, ENT_QUOTES, "UTF-8");?>');
       <?php }?>
       console.log(element);
       
    }, true);
    
  }
};

// Insert it before the CSE code snippet so that cse.js can take the script
// parameters, like parsetags, callbacks.
window.__gcse = {
  callback: initGoogleSearchBox
};


(function() {
  var cx = '<?php echo $search_engine_id;?>'; // Insert your own Custom Search engine ID here
  var gcse = document.createElement('script'); gcse.type = 'text/javascript';
  gcse.async = true;
//  gcse.src = (document.location.protocol == 'https' ? 'https:' : 'http:') +
//      '//www.google.com/cse/cse.js?cx=' + cx;
    gcse.src ='https://www.google.com/cse/cse.js?cx=' + cx;
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
})();

</script>
