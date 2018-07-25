<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>
<div class="control-group">
	<?php $this->form->setFieldAttribute('result_instructions', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('result_instructions'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('result_instructions'); ?>
    </div>
</div>

<div class="control-group">
	<?php $this->form->setFieldAttribute('how_to_use', 'required', 'true'); ?>
	<?php echo $this->form->getLabel('how_to_use'); ?>
    <div class="controls">
		<?php echo $this->form->getInput('how_to_use'); ?>
    </div>
</div>