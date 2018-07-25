<?php
/**
 * @package            Surveyforce
 * @version            1.1-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

?>

<script>
    jQuery(document).ready(function () {
        jQuery('.fancybox').fancybox();
    });
</script>


<div class="back<?php echo ($this->item->is_public == 0) ? ' un_public_layout layout' . $this->item->un_public_tmpl : ''; ?>">
	<?php if ($this->url_param == "google" || $this->url_param == "facebook") { ?>
        <div class="preview_hover_verify"></div>
	<?php } ?>
    <?php if ($this->view == "intro") { ?>
        <div class="preview_hover_intro"></div>
	<?php } ?>
	<?php
	echo $this->content;
	?>
</div>


