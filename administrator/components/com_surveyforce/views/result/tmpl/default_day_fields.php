<?php
/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

unset($field_count);
unset($total_count);

// 題目類型 text、img、textimg
foreach ($this->fields[$this->key] as $fkey => $field) {
    $field_count[$fkey] = (int) $this->weekResults[$this->date][$this->key]->count[$fkey];
    $total_count[$fkey] = $field_count[$fkey];
}
?>
<table class="table">
    <thead>
    <tr>
        <th class="span6"><?php echo ($this->question->quest_type == "number") ? "投票分數" : "投票類別"; ?></th>
        <th class="span7">得票數</th>
    </tr>
    </thead>
    <?php
    foreach ($total_count

    as $ckey => $count) {
    $field_name = $this->fields[$this->key][$ckey];  // 題目類型 text、img、textimg
    ?>
    <tbody>
    <tr>
        <td class="cat">
            <?php echo $field_name; ?>
        </td>
        <td><?php echo $count; ?></td>
    </tr>
    <?php
    }
    ?>
    </tbody>
</table>