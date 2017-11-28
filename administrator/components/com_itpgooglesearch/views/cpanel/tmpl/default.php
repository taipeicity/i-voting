<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// no direct access
defined('_JEXEC') or die;

?>
<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif;?>
    <div class="span8">
	&nbsp;
	</div>
	
	<div class="span4">
        <a href="http://itprism.com/free-joomla-extensions/others/google-search-integration"  target="_blank" ><img src="../media/com_itpgooglesearch/images/extension_logo.png" alt="<?php echo JText::_("COM_ITPGOOGLESEARCH");?>"/></a>
        <a href="http://itprism.com" title="<?php echo JText::_("COM_ITPGOOGLESEARCH_ITPRIMS_PRODUCT");?>" target="_blank"><img src="../media/com_itpgooglesearch/images/product_of_itprism.png" alt="<?php echo JText::_("COM_ITPGOOGLESEARCH_ITPRIMS_PRODUCT");?>" /></a>
        <p><?php echo JText::_("COM_ITPGOOGLESEARCH_YOUR_VOTE"); ?></p>
        <p><?php echo JText::_("COM_ITPGOOGLESEARCH_SPONSORSHIP"); ?></p>
        <p><?php echo JText::_("COM_ITPGOOGLESEARCH_SUBSCRIPTION"); ?></p>
        <div class="clr"></div>
    
        <table class="table table-striped">
        <tbody>
            <tr>
                <td><?php echo JText::_("COM_ITPGOOGLESEARCH_INSTALLED_VERSION");?></td>
                <td><?php echo $this->version->getMediumVersion();?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_ITPGOOGLESEARCH_RELEASE_DATE");?></td>
                <td><?php echo $this->version->releaseDate?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_ITPGOOGLESEARCH_COPYRIGHT");?></td>
                <td><?php echo $this->version->copyright;?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_ITPGOOGLESEARCH_LICENSE");?></td>
                <td><?php echo $this->version->license;?></td>
            </tr>
        </tbody>
        </table>
    </div>
</div>