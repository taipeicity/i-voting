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
    $field_count[$fkey] = (int) $this->results[$this->key]->count[$fkey];
    $total_count[$fkey] = $field_count[$fkey];

    // 是否有紙本投票
    if ($this->paper[$this->key]) {
        $total_count[$fkey] += $this->paper[$this->key][$fkey];
    }

    // 是否有現地投票
    if ($this->place[$this->key]) {
        $total_count[$fkey] += $this->place[$this->key][$fkey];
    }
}
?>
<div class="ftitle"></div>
<table class="table">
    <thead>
    <tr>
        <th class="span6"><?php echo ($this->question->quest_type == "number") ? "投票分數" : "投票類別"; ?></th>
        <?php if ($this->item->is_place == 1 || $this->paper[$this->key]) { ?>
            <th>網路</th>
        <?php } ?>
        <?php if ($this->item->is_place == 1) { ?>
            <th>現地</th>
        <?php } ?>
        <?php if ($this->paper[$this->key]) { ?>
            <th>紙本</th>
        <?php } ?>
        <?php if ($this->item->is_place == 1 || $this->paper[$this->key]) { ?>
            <th class="span6">總得票數</th>
        <?php } else { ?>
            <th class="span7">得票數</th>
        <?php } ?>
    </tr>
    </thead>
    <?php
    foreach ($total_count

    as $ckey => $count) {
    $field_name = $this->fields[$this->key][$ckey];  // 題目類型 text、img、textimg
    $place_votes = $this->place[$this->key][$ckey];
    $paper_votes = $this->paper[$this->key][$ckey];
    ?>
    <tbody>
    <tr>
        <td class="cat">
            <?php echo $field_name; ?>
        </td>
        <?php if ($this->item->is_place == 1 || $this->paper[$this->key]) { ?>
            <td><?php echo $field_count[$ckey]; ?></td>
        <?php } ?>
        <?php if ($this->item->is_place == 1) { ?>
            <td><?php echo sprintf("%0d", $place_votes); ?></td>
        <?php } ?>
        <?php if ($this->paper[$this->key]) { ?>
            <td><?php echo sprintf("%0d", $paper_votes); ?></td>
        <?php } ?>

        <td><?php echo $count; ?></td>
    </tr>
    <?php
    }
    ?>
    </tbody>
</table>