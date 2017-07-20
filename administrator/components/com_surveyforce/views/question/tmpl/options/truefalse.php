<?php
	
	$lists = array();
	$znach = array();
	$znach[] = JHTML::_('select.option',0, JText::_('COM_JOOMLAQUIZ_NO'));
	$znach[] = JHTML::_('select.option',1, JText::_('COM_JOOMLAQUIZ_YES'));
	$znach = JHTML::_('select.genericlist', $znach, 'znach', 'class="text_area" size="1" ', 'value', 'text',  (isset($choice_true) ? intval( $choice_true ) : 0));
	$lists['znach']['input'] = $znach;
	$lists['znach']['label'] = JText::_('COM_JOOMLAQUIZ_RIGHT_CHOICE');
	
?>

<div class="control-group">
	<label id="znach-lbl" for="znach" class="control-label"><?php echo $lists['znach']['label'];?></label>
	<div class="controls">
		<?php echo $lists['znach']['input']; ?>
	</div>
</div>