<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$result = $this->result;
?>

<script type="text/javascript">

    Joomla.submitbutton = function (task) {
        if (task == 'survey.cancel' || document.formvalidator.isValid(document.id('analyze-form'))) {
            Joomla.submitform(task, document.getElementById('analyze-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    };


    function check_export() {
        jQuery("#analyze-form").attr("action", "<?php echo JRoute::_('index.php?option=com_surveyforce&view=analyze&layout=exportdata&surv_id=' . $this->surv_id, false); ?>");
        jQuery("#analyze-form").submit();
    }

</script>


<div id="message_area" style="display: none;">
    <div id="system-message" class="alert alert-error">
        <h4 class="alert-heading"></h4>
        <div>
            <p id="message_content"></p>
        </div>
    </div>
</div>


<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=addend'); ?>" method="post" name="adminForm" id="analyze-form" class="form-validate">

        <input type="button" id="submit_export" class="btn" value="匯出CSV檔案" onclick="check_export()" <?php
		if (empty($this->result)) {
			echo "disabled";
		}
		?> /><br /><br />

		<?php
		if ($result) {
			foreach ($result as $item) {
				?>
                <div class="quest_title">分析題目：<?php echo $item["quest_title"]; ?></div>
                <div class="qtable">
                    <table class="table analyze result">
                        <thead>
                        <tr>
                            <th>分析選項</th>
                            <th>票數</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ($item["detail"] as $field_title => $count) { ?>
                            <tr>
                                <td><?php echo $field_title; ?></td>
                                <td><?php echo $count; ?></td>
                            </tr>
						<?php } ?>
                        </tbody>
                    </table>
                </div>

				<?php
			}
		}else{
		    echo "尚無分析結果";
        }
		?>



    <input type="hidden" name="surv_id" value="<?php echo $this->surv_id; ?>" />
    <input type="hidden" name="task" value="addend.upload" />
    <input type="hidden" name="option" value="com_surveyforce" />
	<?php echo JHtml::_('form.token'); ?>
</form>


