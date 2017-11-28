<?php
/**
*   @package         SITEMAP
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// No direct access to this file
defined('_JEXEC') or die;

$itid = JRequest::getVar('Itemid');

$app = JFactory::getApplication();
$menus = $app->getMenu();
$action = $menus->getItem($itid);

//$menuExluded    = explode( ',', $this->config->exclmenus );

$ex = $this->exclude->exclude;
$id = explode(",", $ex);
$c = count($ex);
$menu = $app->getMenu();
$k = $menu->getItems('menutype', $this->exclude->menu);

if ($ex) {
	for ($i = 0; $i < $c; $i++) {
		if ($i == 0) {
			$exmenu = exclu($k, $id[$i]);
		} else {
			$exmenu = exclu($exmenu, $id[$i]);
		}
	}
}
else
	$exmenu = $k;
?>
<div class="component_other com_sitemap">
<!--	<div class="head">
		<div class="title">
			<?php //echo $action->title; ?>
		</div>
	</div>-->

	<div id="sitemap" class="component">
		<div class="sitemap_content">
			<div class="intro">
				<?php echo $this->exclude->editor; ?>
			</div>
			<div class="list">
				<ul class="sitemap_alllist">
					<?php
					/*
					  echo "<pre>";
					  print_r($exmenu);
					  echo "</pre>";
					 */
					echo tree($exmenu, 1, $id);
					;

					function tree($links, $lv, $k = array(), $pid = 1, $a = 1, $str = null) {
						$treehtml = '';
						for ($i = 0; $i < count($links); $i++) {
							if ($lv == $links[$i]->level && $pid == $links[$i]->parent_id) {
								if ($k) {
									if (in_array($links[$i]->id, $k)) {
										continue;
									}
								}

								if (isset($links[$i + 1]->level)) {
									$treehtml .= '<li class="lvo_' . $lv . '">';
								} else if (isset($links[$i + 1]->level)) {
									$treehtml .= '<li class="lvo_' . $lv . '">';
								} else if (isset($links[$i + 1]->level)) {
									$treehtml .= '<li class="lvo_' . $lv . '">';
								} else if (!isset($links[$i + 1]->level)) {
									$treehtml .= '<li class="lvo_' . $lv . '">';
								}

								if ($links[$i]->type == 'url') {
									$fronthref = "{$links[$i]->link}";
								} else if ($links[$i]->type == 'alias') {
									$links[$i]->link = 'index.php?Itemid=' . $links[$i]->params->get('aliasoptions');
									$fronthref = JRoute::_("{$links[$i]->link}");
								} else {
									$fronthref = JRoute::_("{$links[$i]->link}&Itemid={$links[$i]->id}");
								}
								if ($links[$i]->type == 'url') {
									if ($links[$i]->browserNav == 1) {
										$img = "<img title='' alt='{$links[$i]->title}(" . JText::_('COM_SITEMAP_NEWWINDOW') . ")' src='images/system/link_opens_new_window.gif' border='0' >";
									}
									/*
									  else if($links[$i]->browserNav==0){
									  $img= "<img title='' alt='{$links[$i]->title}' src='images/link_opens_new_window.gif' border='0'>";
									  }
									 */
									if ($lv == 1) {
										if ((isset($links[$i - 1]->level) && $links[$i - 1]->level > $lv) || (isset($links[$i - 1]->level) && $links[$i - 1]->level == $lv)) {
											$a++;
										}
									}
									if ($lv != 1) {
										if ((isset($links[$i - 1]->level) && $links[$i - 1]->level < $lv)) {
											$a = 1;
										} else if ((isset($links[$i - 1]->level) && $links[$i - 1]->level > $lv) || (isset($links[$i - 1]->level) && $links[$i - 1]->level == $lv)) {
											$a++;
										}
									}
									if ($str != null) {
										$temp = $str . ' - ' . $a; //lv>1
									} else {
										$temp = $str . $a;  //lv=1
									}
									$show = $temp . " . ";
									if ($links[$i]->browserNav == 1) {
										$treehtml .= "<span><a target='_blank' href='{$fronthref}' title='{$links[$i]->title}(" . JText::_('COM_SITEMAP_NEWWINDOW') . ")'>{$show}{$links[$i]->title}{$img}</a></span>";
									} else if ($links[$i]->browserNav == 0) {
										$treehtml .= "<span><a href='{$fronthref}' title='{$links[$i]->title}'>{$show}{$links[$i]->title}</a></span>";
									}
								} else {
									if ($lv == 1) {
										if ((isset($links[$i - 1]->level) && $links[$i - 1]->level > $lv) || (isset($links[$i - 1]->level) && $links[$i - 1]->level == $lv)) {
											$a++;
										}
									}
									if ($lv != 1) {
										if ((isset($links[$i - 1]->level) && $links[$i - 1]->level < $lv) && (!in_array($links[$i - 1]->id, $k))) {
											$a = 1;
										} else if ((isset($links[$i - 1]->level) && $links[$i - 1]->level > $lv) || (isset($links[$i - 1]->level) && $links[$i - 1]->level == $lv)) {
											$a++;
										}
									}
									if ($str != null) {
										$temp = $str . ' - ' . $a; //lv>1
									} else {
										$temp = $str . $a;  //lv=1
									}
									$show = $temp . " . ";
									if ($links[$i]->browserNav == 1) {
										$treehtml .= "<span><a target='_blank' href='{$fronthref}' title='{$links[$i]->title}(" . JText::_('COM_SITEMAP_NEWWINDOW') . ")'>{$show}{$links[$i]->title}</a></span>";
									} else if ($links[$i]->browserNav == 0) {
										$treehtml .= "<span><a href='{$fronthref}' title='{$links[$i]->title}'>{$show}{$links[$i]->title}</a></span>";
									}
								}
								if (isset($links[$i + 1]->level) && $links[$i + 1]->level > $links[$i]->level && $links[$i + 1]->level <= 3) {
									$treehtml .= '<ul class="sitemap_list" style="display: block;">';
									$treehtml .= tree($links, $lv + 1, $k, $links[$i]->id, $a, $temp);
									$treehtml .= '</ul>';
								}
								$treehtml .='</li>';
							}
						}
						return $treehtml;
					}

					function exclu($menu, $ex) {
						$menus = array_values($menu);
						$m = count($menu);
						for ($p = 0; $p < $m; $p++) {
							if (isset($menus[$p + 1]) && ($menus[$p + 1]->level > $menus[$p]->level) && ($menus[$p + 1]->parent_id == $ex)) {
								unset($menus[$p]);
								array_values($menus);
								exclu($menus, $menus[$p + 1]->id);
							}
						}
						return array_values($menus);
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>