<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die;

$menuid=$params->get('menuid');

$search_for = ($params->get('searchText'))?$params->get('searchText'):JText::_("MOD_ITPGOOGLESEARCH_SEARCH_FOR");
$search_btn = ($params->get('searchButtonText'))?$params->get('searchButtonText'):JText::_("MOD_ITPGOOGLESEARCH_SEARCH");
?>
<div class="mod_search itp-gs<?php echo $moduleclass_sfx; ?>">
	<div class="search">
		<form action="<?php echo JRoute::_('index.php?option=com_itpgooglesearch&view=search&Itemid='.$menuid); ?>" method="get" accept-charset="utf-8">
			<input name="gsquery" type="text" accesskey="S" class="inputbox" placeholder="<?php echo $search_for ; ?>" value="<?php echo $phrase; ?>" />
			<?php if ($params->get("searchButton")) { ?>
				<input type="submit" class="btn" value="<?php echo $search_btn; ?>" />
				<input type="hidden" name="option"  value="com_itpgooglesearch" />
				<input type="hidden" name="view"  value="search" />
				<input type="hidden" name="Itemid"  value="<?php echo $menuid; ?>" />
			<?php } ?>
				
		</form>
	</div>
</div>