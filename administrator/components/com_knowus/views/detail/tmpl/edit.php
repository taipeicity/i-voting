<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Knowus
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_knowus/css/form.css?' . time());

$isNew = ($this->item->id == 0);
?>
<script type="text/javascript">
  js = jQuery.noConflict();
  js(document).ready(function () {

    js("input:hidden.unit").each(function () {
      var name = js(this).attr("name");
      if (name.indexOf("unithidden")) {
        js("#jform_unit option[value=\"" + js(this).val() + "\"]").attr("selected", true);
      }
    });
    js("#jform_unit").trigger("liszt:updated");
  });

  Joomla.submitbutton = function (task) {
    if (task == "detail.cancel") {
      Joomla.submitform(task, document.getElementById("detail-form"));
    } else {

      if (task != "detail.cancel" && document.formvalidator.isValid(document.id("detail-form"))) {

        Joomla.submitform(task, document.getElementById("detail-form"));
      } else {
        alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
      }
    }
  };
</script>

<form
        action="<?php echo JRoute::_('index.php?option=com_knowus&layout=edit&id=' . (int)$this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="detail-form"
        class="form-validate form-horizontal">


    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
                <?php echo $this->form->renderField('unit'); ?>
                <?php echo $this->form->renderField('catid'); ?>
                <?php
                foreach ((array)$this->item->unit as $value) {
                    if (!is_array($value)) {
                        echo '<input type="hidden" class="unit" name="jform[unithidden][' . $value . ']" value="' . $value . '" />';
                    }
                }
                ?>
                <?php echo $this->form->renderField('content'); ?>

                <?php echo $this->form->renderField('youtube_url'); ?>

                <?php echo $this->form->renderField('img'); ?>

                <?php echo $this->form->renderField('selectimg'); ?>

                <?php if ($this->state->params->get('save_history', 1)) : ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
                    </div>
                <?php endif; ?>

                <div class="hidden">
                    <?php
                    echo $this->form->renderField('id');
                    echo $this->form->renderField('state');
                    echo $this->form->renderField('created');
                    echo $this->form->renderField('created_by');
                    echo $this->form->renderField('modified');
                    echo $this->form->renderField('modified_by');
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>

</form>

<script src="<?php echo Uri::root() . 'media/com_knowus/js/required.js?' . time(); ?>"
        type="application/javascript"></script>
