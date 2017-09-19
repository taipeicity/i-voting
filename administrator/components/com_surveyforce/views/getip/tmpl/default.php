<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$select[] = JHTML::_('select.option', '0', '匯出市府IP來源清單', 'id', 'methodName');
$select[] = JHTML::_('select.option', '1', '匯出非市府IP來源清單', 'id', 'methodName');
$Attributes = array(
    "class" => "dropdown-ip-source"
);

$app = JFactory::getApplication();
$surv_id = $app->input->getInt("surv_id");

$menu = JComponentHelper::getParams('com_surveyforce');
$Internal_IP = $menu->get('Internal_IP');
$ip_range = false;
if (empty($Internal_IP)) {
    $ip_range = true;
}

?>

<script type="text/javascript">


    Joomla.submitbutton = function (task)
    {
        if (task == 'getip.cancel' || document.formvalidator.isValid(document.id('getip-form'))) {
            Joomla.submitform(task, document.getElementById('getip-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }

    function check_getip() {
        var has_ip = document.getElementById("check_hasIp").value;
        if (has_ip) {
            alert("請先至選項設定內部IP範圍清單");
            return false;
        }
        var source_type = jQuery("#ip_type").val();
        jQuery("#getip-form").prop("action", "<?php echo JRoute::_('index.php?option=com_surveyforce&view=getip&layout=getcsv&surv_id=' . $surv_id . '&source_type=', false); ?>" + source_type);
        jQuery("#getip-form").submit();
    }

</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=getip'); ?>"  method="post" name="adminForm" id="getip-form" class="form-validate">

    <div class="survey_getip">
        請選擇欲匯出之IP清單
        <?php echo JHTML::_("select.genericlist", $select, "ip_type", $Attributes, "id", "methodName", 0); ?>
        <input type="button" id="submit_getip" class="btn" value="送出" onclick="check_getip()"  />
    </div>	


    <input type="hidden" name="task" value = "" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>" />
    <input type="hidden" id="check_hasIp" value="<?php echo $ip_range; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

