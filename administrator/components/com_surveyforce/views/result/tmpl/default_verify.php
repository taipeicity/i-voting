<?php
/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>

<h3>依照驗證方式（每10分鐘進行更新）</h3>
<?php 
	if (empty($this->verifyResults)) {
		echo "無資料";
	} else { 
		// 若驗證方式有台北通 + (身分證認證 OR 投票人資料填寫)
		if ( strpos($this->survey_item->verify_type, "taipeicard") !== false) {
		if ( strpos($this->survey_item->verify_type, "idnum") !== false || strpos($this->survey_item->verify_type, "any") !== false) {
		
?>
	<table class="table">
			<thead>
				<tr>
					<th width="150px"></th>
					<th width="50px">網站</th>
					<th width="50px">台北通</th>
					<th width="50px">總計</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$total_api_count = 0;
				$total_count = 0;
				foreach ($this->verifyResults as $verifyResult) { 
			?>
				<tr>
					<td><?php echo $this->verifyAllTypes[$verifyResult->verify_type]; ?></td>
					<td><?php echo sprintf("%0d", $verifyResult->count - $this->verifyApiResults[$verifyResult->verify_type]); ?></td>
					<td><?php echo sprintf("%0d", $this->verifyApiResults[$verifyResult->verify_type]); ?></td>
					<td><?php echo sprintf("%0d", $verifyResult->count); ?></td>
				</tr>
			<?php 
					$total_api_count += $this->verifyApiResults[$verifyResult->verify_type];
					$total_count += $verifyResult->count;
				} 
			?>
				<tr>
					<td>總計</td>
					<td><?php echo $total_count - $total_api_count; ?></td>
					<td><?php echo $total_api_count; ?></td>
					<td><?php echo $total_count; ?></td>
				</tr>
			</tbody>
		</table>
<?php
			
		} else {
			// 一般的顯示方式
?>
    <table class="table verify">
        <thead>
        <tr>
            <th>驗證方式</th>
            <th>投票人數</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->verifyResults as $verifyResult) { ?>
            <tr>
                <td><?php echo $this->verifyAllTypes[$verifyResult->verify_type]; ?></td>
                <td><?php echo sprintf("%0d", $verifyResult->count); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php 
		}
	}
}

?>
