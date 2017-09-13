<?php
/**
*   @package         Surveyforce
*   @version           1.0-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/
?>
<script language="javascript" type="text/javascript">

		var quest_type = <?php echo $q_om_type; ?>;

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = document.getElementById(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 1 - start_index%2;//0;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					Redeclare_element_inputs(tbl_elem.rows[i].cells[2]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down"></a>';;
					} else { tbl_elem.rows[i].cells[5].innerHTML = ''; }
					tbl_elem.rows[i].className = 'row'+row_k;
					count++;
					row_k = 1 - row_k;
				}
			}
		}
		
		function Redeclare_element_inputs(object) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = "hidden";
						input_hidden.name = inp_name;
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
				}
			}
		}
		
		function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
			ReAnalize_tbl_Rows(del_index - 1, tbl_id);
		}

		function Up_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex > 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell2_tmp = element.parentNode.parentNode.cells[2].innerHTML;
				var cell1_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				var cell7_tmp = element.parentNode.parentNode.cells[6].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				var row = table.insertRow(sec_indx - 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				var cell5 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.innerHTML = cell1_tmp;
				cell3.align = 'left';
				cell3.innerHTML = cell2_tmp;
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				cell7.innerHTML = cell7_tmp;
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(cell5);
				row.appendChild(document.createElement("td"));
				row.appendChild(cell7);
				ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
			}
		}


		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				var cell1_tmp = element.parentNode.parentNode.cells[1].innerHTML;
				var cell2_tmp = element.parentNode.parentNode.cells[2].innerHTML;
				var cell7_tmp = element.parentNode.parentNode.cells[6].innerHTML;
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				var row = table.insertRow(sec_indx + 1);
				var cell1 = document.createElement("td");
				var cell2 = document.createElement("td");
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				var cell5 = document.createElement("td");
				var cell7 = document.createElement("td");
				cell1.align = 'center';
				cell1.innerHTML = 0;
				cell2.innerHTML = cell1_tmp;
				cell3.align = 'left';
				cell3.innerHTML = cell2_tmp;
				cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				cell7.innerHTML = cell7_tmp;
				row.appendChild(cell1);
				row.appendChild(cell2);
				row.appendChild(cell3);
				row.appendChild(cell4);
				row.appendChild(cell5);
				row.appendChild(document.createElement("td"));
				row.appendChild(cell7);
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}

		function Add_new_tbl_field(elem_field, elem_field2, elem_field3, tbl_id, field_name, field_name2, field_name3) {
			var new_element_txt = document.getElementById(elem_field).value;
			var new_element_txt2 = document.getElementById(elem_field2).value;
			var new_points = document.getElementById(elem_field3).value;
			document.getElementById(elem_field).value = '';
			document.getElementById(elem_field2).value = '';
			document.getElementById(elem_field3).value = '';
			if (TRIM_str(new_element_txt) == '') {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_ENTER_TEXT');?>");return;
			}
			if (TRIM_str(new_element_txt2) == '') {
				alert("<?php echo JText::_('COM_JOOMLAQUIZ_PLEASE_ENTER_TEXT');?>");return;
			}
			var tbl_elem = document.getElementById(tbl_id);
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			var cell5 = document.createElement("td");
			var cell6 = document.createElement("td");
			var cell7 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "hidden";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			
			var input_hidden2 = document.createElement("input");
			input_hidden2.type = "hidden";
			input_hidden2.name = field_name2;
			input_hidden2.value = new_element_txt2;
			
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.name = "jq_hid_fields_ids[]";
			input_hidden_id.value = "0";
			cell1.align = 'center';
			cell1.innerHTML = 0;
			cell3.innerHTML = new_element_txt2;
			cell3.appendChild(input_hidden2);
			cell2.innerHTML = new_element_txt;
			cell2.appendChild(input_hidden);
			cell2.appendChild(input_hidden_id);
			cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
			cell6.innerHTML = '';
			var input_hidden_p = document.createElement("input");
			input_hidden_p.type = "text";
			input_hidden_p.size = "5";
			input_hidden_p.name = field_name3;
			input_hidden_p.value = new_points;
			input_hidden_p.setAttribute("value", new_points);
			cell7.appendChild(input_hidden_p);
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			row.appendChild(cell7);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}

</script>