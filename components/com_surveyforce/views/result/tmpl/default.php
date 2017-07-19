<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$sid = $app->input->getInt("sid");
$itemid = $app->input->getInt("Itemid");

$quest_index = 0;  // 第幾題用
$field_count = array(); // 票數
$total_count = array(); // 總票數
$pieid = array(); // 圓餅圖id
$result_num = $this->item->result_num; // 顯示數目
$qtype = array("select", "number", "table"); // 有子選項的題目類型

$king = "<img src='". JURI::root(). "images/system/king.gif' alt='獲選' width='50'>"; // 皇冠 icon

?>

<!-- temp css -->
<style>
	.result_block {
		overflow: auto;
		margin: 20px 0;
	}
	.ftitle {
		clear: both;
	}
</style>

<script src="components/com_surveyforce/assets/js/dist/echarts.js" ></script>

<div class="survey survey_result">
	
	<div class="title">
		<?php echo $this->item->title; ?>
	</div>

	<div class="ordertype">
		投票結果排序方式：
		<a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=result&sid={$sid}&orderby=0&Itemid={$itemid}"); ?>" title="依選項排序" class="<?php if($this->orderby==0){echo "active";} ?>">依選項</a>
		<a href="<?php echo JRoute::_("index.php?option=com_surveyforce&view=result&sid={$sid}&orderby=1&Itemid={$itemid}"); ?>" title="依票數排序" class="<?php if($this->orderby==1){echo "active";} ?>">依票數</a>
	</div>
	
	<?php 
		if ($this->results) {
			
	?>
		<?php foreach ($this->results as $key => $result) { $quest_index++; ?>
			<div class="result_block">
				<div class="quest_title">
					<?php 
						if($result->quest_type != 'open') {
							echo (count($this->results) > 1) ? "第" . $quest_index . "題：" : "題目：";
							echo $result->quest_title; 
						}
					?>
				</div>
				<?php
				if ($result->quest_type != 'open' && strtotime($this->update_time) > 0) {
					echo "<div style='text-align:right'>資料更新時間：". JHtml::_('date', $this->update_time, "Y-m-d H:i"). " (每5分鐘更新)</div>";
				}
				?>
				<div class="quest_result">
					<!-- 沒有子選項的題目類型 text、img、textimg -->
					<?php if (!in_array($result->quest_type, $qtype)) { ?>
						<?php
							if($result->quest_type == 'open') {
								/* 補齊div結尾後再略過 */
								echo "</div></div>";
								continue;
							}
						// $key: question_id
						// $fkey、$ckey: field_id
						?>
					<div class="result_inner">
						<!-- table -->
						<div class="qtable">
							<table class="votetable">
								<thead>
									<tr>
										<th>投票類別</th>
										<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
											<th>網路</th>
										<?php } ?>
										<?php if ($this->item->is_place == 1) { ?>
											<th>現地</th>
										<?php } ?>
										<?php if ($this->paper[$key]) { ?>
											<th>紙本</th>
										<?php } ?>
										<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
											<th>總得票數</th>
										<?php } else { ?>
											<th>得票數</th>
										<?php } ?>
									</tr>
								</thead>

								<tbody>
									<?php
									unset($field_count);
									unset($total_count);
									foreach ($this->fields[$key] as $fkey => $field) {
										$field_count[$fkey] = ($result->count[$fkey]) ? ($result->count[$fkey]) : 0;
										$total_count[$fkey] = $field_count[$fkey];

										// 是否有紙本投票
										if ($this->paper[$key]) {
											$total_count[$fkey] += $this->paper[$key][$fkey];
										}

										// 是否有現地投票
										if ($this->place[$key]) {
											$total_count[$fkey] += $this->place[$key][$fkey];
										}
									}
									
									
									if ($this->orderby == 1) {  // 票數排序
										arsort($total_count);
										$rank_count = array_count_values($total_count);
										$r = 0;
										foreach ($rank_count as $rkey => $count) {
											$r++;
											if ($this->item->result_num_type == 0 && $r == 1) {
												$king_num = $rkey;
												break;
											} elseif ($r == $result_num) {
												$king_num = $rkey;
												break;
											}
										}
									} else if ($this->orderby == 0) {  // 選項排序
										$order_arr = $total_count;
										arsort($order_arr);
										$rank_count = array_count_values($order_arr);
										unset($rank_count[0]);
										$r = 0;
										foreach ($rank_count as $rkey => $count) {
											$r++;
											if ($r == $result_num or $r == count($rank_count)) {
												$king_num = $rkey;
												break;
											}
										}

										$king_arr = array();
										$temp_order = 0;
										foreach ($order_arr as $okey => $order) {
											if ($this->item->result_num_type == 0) {
												if ($rank_count[order] == 1) {
													$king_arr[$okey] = $okey;
													break;
												} else {
													if ($temp_order != $order && $temp_order != 0) {
														break;
													}
													$king_arr[$okey] = $okey;
													$temp_order = $order;
												}
											} else if ($order != 0) {
												if ($order >= $king_num) {
													$king_arr[$okey] = $okey;
												} else {
													break;
												}
											}
										}
									}
																
									$num = 0;
									foreach ($total_count as $ckey => $count) {
										// for pie charts
										$field_name = $this->fields[$key][$ckey];
										$pie[$key][$field_name] = $count;
								?>

										<tr>
											<td class="cat">
												<?php
													if($this->orderby == 1) {
														echo (($count >= $king_num) && $count != 0) ? $king : "";
													} elseif ($this->orderby == 0) {
														if(in_array($ckey, $king_arr)) {
															echo $king;
														}
													}
												?>
												<?php echo $field_name; ?>
											</td>
										<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
											<td><?php echo $field_count[$ckey]; ?></td>
										<?php } ?>
										<?php if ($this->item->is_place == 1) { ?>
											<td><?php echo sprintf("%0d", $this->place[$key][$ckey]); ?></td>
										<?php } ?>
										<?php if ($this->paper[$key]) { ?>
											<td><?php echo $this->paper[$key][$ckey]; ?></td>
										<?php } ?>
									
										<td><?php echo $count; ?></td>
								

										</tr>

										<?php
										// 統計數目
										if ($count) {
											$num++;
										}


									}

									$piechar_height = ($num * 30 ) + 20 + (($this->orderby == 1) ? ($num * 10 ) : 0);
									$piechar_height = ($piechar_height < 300) ? 320 : $piechar_height;
									?>
								</tbody>
							</table>
						</div>

						<br>
						<!-- 圓餅圖 -->
						<div class="qchart" id="pie<?php echo $key ?>" style="height:<?php echo $piechar_height; ?>px;"></div>
						<?php $pieid[$key] = $key; ?>
					</div>

						<!-- 題目類型 select、number、table -->
					<?php } else { ?>
						<?php
						// $key: question_id
						// $fkey: field_id
						// $sfkey、$ckey: sub_field_id
						?>
						<?php foreach ($result->field_title as $fkey => $field_title) { ?>
							<div class="ftitle"><?php echo $field_title; ?></div>
							<div class="result_inner">
							

							<!-- table -->
							<div class="qtable">
								<table class="votetable">
									<thead>
										<tr>
											<tr>
												<th><?php echo ($result->quest_type == "number") ? "投票分數" : "投票類別"; ?></th>
												<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
													<th>網路</th>
												<?php } ?>
												<?php if ($this->item->is_place == 1) { ?>
													<th>現地</th>
												<?php } ?>
												<?php if ($this->paper[$key]) { ?>
													<th>紙本</th>
												<?php } ?>
												<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
													<th>總得票數</th>
												<?php } else { ?>
													<th>得票數</th>
												<?php } ?>
											</tr>
										</tr>
									</thead>

									<tbody>
										<?php
										unset($field_count);
										unset($total_count);
										foreach ($this->sub_fields[$key] as $sfkey => $sub_field) {
											$index = $fkey . "_" . $sfkey;
											$field_count[$sfkey] = ($this->sub_results[$index]->count) ? ($this->sub_results[$index]->count) : 0;
											$total_count[$sfkey] = $field_count[$sfkey];

											// 是否有紙本投票
											if ($this->sub_paper[$fkey]) {
												$total_count[$sfkey]  += $this->sub_paper[$fkey][$sfkey];
											}

											// 是否有現地投票
											if ($this->sub_place[$fkey]) {
												$total_count[$sfkey]  += $this->sub_place[$fkey][$sfkey];
											}
										}
										

										if ($this->orderby == 1) {  // 票數排序
											arsort($total_count);
											$rank_count = array_count_values($total_count);
											$r = 0;
											foreach($rank_count as $rkey => $count) {
												$r++;
												if($this->item->result_num_type == 0 && $r == 1) {
													$king_num = $rkey;
													break;
												}elseif($r == $result_num) {
													$king_num = $rkey;
													break;
												}
											}										
																					
										} else if($this->orderby == 0) {  // 選項排序
											$order_arr = $total_count;
											arsort($order_arr);
											$rank_count = array_count_values($order_arr);
											unset($rank_count[0]);
											$r = 0;
											foreach($rank_count as $rkey => $count) {
												$r++;
												if($r == $result_num or $r == count($rank_count)) {
													$king_num = $rkey;
													break;
												}
											}

	
											$king_arr = array();
											$temp_order = 0;
											foreach($order_arr as $okey => $order) {
												if($this->item->result_num_type == 0) {
													if($rank_count[order] == 1) {
														$king_arr[$okey] = $okey;
														break;
													}else{
														if($temp_order != $order && $temp_order != 0) {
															break;
														}
														$king_arr[$okey] = $okey;
														$temp_order = $order;
													}
												}else if($order != 0) {
													if($order >= $king_num) {
														$king_arr[$okey] = $okey;
													}else{
														break;
													}
												}
											}
										}


										$num = 0;

										foreach ($total_count as $ckey => $count) {
											// for pie charts
											$field_name = $this->sub_fields[$key][$ckey];
											$pie[$key . "_" . $fkey][$field_name] = $count;
									?>
											<tr>
												<td class="cat">
													<?php
														if($this->orderby == 1) {
															echo (($count >= $king_num) && $count != 0) ? $king : "";
														}elseif($this->orderby == 0) {
															if(in_array($ckey, $king_arr)) {
																echo $king;
															}
														}
													?>
													<?php echo $field_name; ?>
												</td>

												<?php if ($this->item->is_place == 1 || $this->paper[$key]) { ?>
													<td><?php echo $field_count[$ckey]; ?></td>
												<?php } ?>
												<?php if ($this->item->is_place == 1) { ?>
													<td><?php echo $this->sub_place[$fkey][$ckey]; ?></td>
												<?php } ?>
												<?php if ($this->paper[$key]) { ?>
													<td><?php echo $this->sub_paper[$fkey][$ckey]; ?></td>
												<?php } ?>

													<td><?php echo $count ?></td>

											</tr>

											<?php
											// 統計數目
											if ($count) {
												$num++;
											}
											
										}

										$piechar_height = ($num * 30 ) + 20 + (($this->orderby == 1) ? ($num * 10 ) : 0);
										$piechar_height = ($piechar_height < 300) ? 320 : $piechar_height;
										?>
									</tbody>
								</table>
							</div>
							<br>
							<!-- 圓餅圖 -->
							<div class="qchart" id="pie<?php echo $key . "_" . $fkey; ?>" style="width: 100%; height:<?php echo $piechar_height; ?>px; float: left;"></div>
							<?php $pieid[$key . "_" . $fkey] = $key . "_" . $fkey; ?>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
  <?php } ?>
  
  <?php if ($this->item->result_desc) { ?>
	<div class="result_desc">
		<?php
			echo $this->item->result_desc;
		?>
	</div>
  <?php } ?>
  
  <?php 
	} else {
		echo "尚無資料顯示";
	}



	// 處理$pie資料
	unset($pie_temp);
	$pie_temp = $pie;
	unset($pie);
	$pie = array();

	if ($pie_temp) {
		foreach ($pie_temp as $key => $item) {
			unset($zero_field_name);
			$zero_field_name = array();

			foreach ($item as $field_name => $value) {
				if ($value) {
					$pie[$key][$field_name] = $value;
				} else {
					if (count($zero_field_name) == 3) {
						$zero_field_name[] = "及其他項目";
					} else if (count($zero_field_name) > 3) {
						continue;
					} else {
						$zero_field_name[] = $field_name;
					}
				}
			}

			if ($zero_field_name) {
				$field_name = implode("、", $zero_field_name);
				$pie[$key][$field_name] = 0;
			}

		}

	}

  ?>
</div>
<div class="mod_return return">
	<div class="return_inner home">
		<a href="index.php"><span><?php echo JText::_('JGLOBAL_BACK_HOME'); ?></span></a>
	</div>
	<noscript>您的瀏覽器不支援script程式碼。請使用"Backspace"按鍵回到上一頁。</noscript>
</div>

<script type="text/javascript" > 
	//路徑配置
    require.config ({ 
		paths: {
			echarts : '<?php echo JURI::base(); ?>components/com_surveyforce/assets/js/dist'
		}
	});
	
	require (
	[
		'echarts' ,
		'echarts/chart/pie'
	],
	drawEcharts
);
	
	function drawEcharts(ec) {
<?php foreach ($pieid as $id) { ?>
					drawPie<?php echo $id; ?>(ec);
<?php } ?>
			}
	
<?php
foreach ($pie as $key => $p) {
	$filed_str = "'" . implode("','", array_keys($p)) . "'";
	$filed_num = count($p);
	$count = 0;
	?>

			function drawPie<?php echo $key; ?> ( ec )  {
				//基於準備好的dom，初始化echarts圖表
				var myChart = ec.init(document.getElementById('pie<?php echo $key; ?>'));

				var option = {
					tooltip : {
						trigger: 'item',
						formatter: "{a} <br/>{b} : {c} ({d}%)"
					},
					legend: {
						orient : 'horizontal',
						x : 'left',
						selectedMode : false,
						data:[<?php echo $filed_str; ?>],
						textStyle: {
							fontSize: 18
						}
					},
					calculable : false,
					series : [
						{
							name:'投票結果',
							type:'pie',
							radius : '<?php echo ($this->device == 3) ? "30%" : "50%"; ?>',
							center: ['50%', '65%'],
							data:[
							<?php
							foreach ($p as $pkey => $num) {
								$count++;
							?>
								{value:<?php echo $num; ?>, name:'<?php echo $pkey; ?>'}

							<?php
								if ($filed_num != $count) {
									echo ",";
								}
							}
							?>
								],
								itemStyle:{
									normal:{
										label:{
											show: true,
											formatter: '{b} : {c} \n ({d}%)',
											textStyle: {
												fontSize: 18
											}
										},
										labelLine :{show:true, length:<?php echo ($this->device == 3) ? "10" : "40"; ?>}
									}
								}
							}
						]
					};

					//為echarts對象加載數據
					myChart.setOption (option, true);
				}

<?php } ?>
</script>
