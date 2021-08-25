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

// 題目類型 select、number、table
foreach ($this->fields[$this->key] as $fkey => $field) {

    foreach ($this->sub_fields[$this->key] as $sfkey => $sub_field) {
        $index = $fkey."_".$sfkey;
        $field_count[$sfkey] = ($this->weekSubResults[$this->date][$index]->count) ? ($this->weekSubResults[$this->date][$index]->count) : 0;
        $total_count[$sfkey] = $field_count[$sfkey];
    }
    ?>
    <div class="ftitle"><?php echo $field; ?></div>
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

        $field_name = $this->sub_fields[$this->key][$ckey]; // 題目類型 select、number、table
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
    <?php
}
