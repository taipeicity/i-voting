<?php if ($wysiwyg) { ?>		
		<link rel="stylesheet" href="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/css/thickbox/thickbox.css" type="text/css" />
		<script language="javascript" type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_joomlaquiz/assets/js/thickbox/thickbox.js" ></script>
<?php }?>
<script language="javascript" type="text/javascript">
<!--
		var quest_type = <?php echo $q_om_type; ?>;
			
		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = document.getElementById(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 1 - start_index%2;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
										
					Redeclare_element_inputs2(tbl_elem.rows[i].cells[1], i);
					if (i > 1) { 
						tbl_elem.rows[i].cells[4].innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[5].innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/downarrow.png"  border="0" alt="Move Down"></a>';;
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
				var i = 0;
				while (i < children.length) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'text') {
							var inp_size = children[i].size;
						}
						if (inp_type.toLowerCase() == 'checkbox') {
							var inp_check = children[i].checked;
						}
						object.removeChild(object.childNodes[i]);
						var input_hidden = document.createElement("input");
						input_hidden.type = inp_type;
						if (inp_type.toLowerCase() == 'text') {
							input_hidden.size = inp_size;
						}
						if (inp_type.toLowerCase() == 'checkbox') {
							input_hidden.checked = inp_check;
							input_hidden.onchange=input_hidden.onclick = new Function('jq_UnselectCheckbox(this)');
						}
						
						input_hidden.setAttribute('name',inp_name);
						input_hidden.value = inp_value;
						object.appendChild(input_hidden);
					}
					i ++;
				}
			}
		}

		
		function Redeclare_element_inputs4(object,object2) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'textarea') {
						var inp_name = object.innerHTML;
						
						object.removeChild(object.childNodes[i]);
	
						object2.innerHTML = inp_name;
					}
				}
			}
		}
		
		function Redeclare_element_inputs3(object,object2) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {
						var inp_name = children[i].name;
						var inp_value = children[i].value;
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'checkbox') {
							var inp_check = children[i].checked;
						}
						var input_hidden = document.createElement("input");
						input_hidden.type = inp_type;
						input_hidden.setAttribute('name',inp_name);
						input_hidden.value = inp_value;
						if (inp_type.toLowerCase() == 'checkbox') {
							input_hidden.checked = inp_check;
							input_hidden.onchange=input_hidden.onclick = new Function('jq_UnselectCheckbox(this)');
						}
						object2.appendChild(input_hidden);
					}
				}
			}
		}
		
		function Redeclare_element_inputs2(object, gg) {
			if (object.hasChildNodes()) {
				var children = object.childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].nodeName.toLowerCase() == 'input') {						
						var inp_type = children[i].type;
						if (inp_type.toLowerCase() == 'checkbox') {
							object.childNodes[i].value = gg;

						}
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

				var cell1 = document.createElement("td");
				cell1.align = 'center';
				var row = table.insertRow(sec_indx - 1);
				row.appendChild(cell1);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				
				var r7 = element.parentNode.parentNode.cells[3];
			
				var r8 = element.parentNode.parentNode.cells[4];

				var cell5 = document.createElement("td");
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(r7);
				row.appendChild(r8);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
			}
		}

		function Down_tbl_row(element) {
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;

				var cell1 = document.createElement("td");
				cell1.align = 'center';
				var row = table.insertRow(sec_indx + 2);
				row.appendChild(cell1);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				row.appendChild(element.parentNode.parentNode.cells[1]);
				var r7 = element.parentNode.parentNode.cells[3];
				var r8 = element.parentNode.parentNode.cells[4];
				
				var cell5 = document.createElement("td");
				cell5.innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
				row.appendChild(cell5);
				var cell6 = document.createElement("td");
				cell6.innerHTML = '&nbsp;';
				row.appendChild(cell6);
				row.appendChild(r7);
				row.appendChild(r8);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				
				ReAnalize_tbl_Rows(sec_indx, tbl_id);
			}
		}
		<?php if ($wysiwyg) { ?>
		var number = 99999;
		function Add_new_tbl_field(elem_field, tbl_id, field_name) {
			number++;
			var new_element_txt = document.getElementById(elem_field).innerHTML;
			var add_new_field = document.getElementById("new_field_points").value;
			
			document.getElementById(elem_field).innerHTML = '';
			document.getElementById('test_0').innerHTML = '';
			document.getElementById('ta_0').innerHTML = '';
			if (TRIM_str(new_element_txt) == '') {
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
			var cell8 = document.createElement("td");
			
			var ta_hidden = 	document.createElement("textarea");
			ta_hidden.setAttribute('name',field_name);
			ta_hidden.setAttribute('id','ta_'+number);
			try{
			ta_hidden.innerHTML = new_element_txt;
			}catch(e){}
			try{
			ta_hidden.value = new_element_txt;
			}catch(e){}
			ta_hidden.style.display = 'none';
			
				
			
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.setAttribute('name','jq_hid_fields_ids[]');
			
			input_hidden_id.value = "0";
			var input_check = createNamedElement("input","jq_checked[]");
			
			if(quest_type != 10)
			{
				input_check.type = "checkbox";
				input_check.setAttribute('name','jq_checked[]');
			
				input_check.checked = false;

			} else {
				var random_number = Math.floor(Math.random() * 1000000);
				var input_check_id = 'jq_checked_' + random_number;

				input_check.type = "hidden";
				input_check.setAttribute('name','jq_checked[]');
				input_check.value = 0;
				input_check.id = input_check_id;
				
				var br_elem = document.createElement("br");
				var span_radio = document.createElement("span");
				var label_radio_0 = document.createElement("label");
				label_radio_0.innerHTML = ' False';
				var input_radio_0 = createNamedElement("input","jq_radio_" + random_number);
				input_radio_0.setAttribute('name',"jq_radio_" + random_number);
				input_radio_0.setAttribute('checked', 1);
				input_radio_0.checked = 1;
				input_radio_0.type = "radio";
				input_radio_0.value = "0";
				input_radio_0.onclick = new Function('jq_SetHidden("' + input_check_id + '", 0)');
				span_radio.appendChild(input_radio_0);
				span_radio.appendChild(label_radio_0);
				span_radio.appendChild(br_elem);

				var label_radio_1 = document.createElement("label");
				label_radio_1.innerHTML = ' True';
				var input_radio_1 = createNamedElement("input","jq_radio_" + random_number);
				input_radio_1.setAttribute('name',"jq_radio_" + random_number);
				input_radio_1.type = "radio";
				input_radio_1.value = "1";
				input_radio_1.onclick = new Function('jq_SetHidden("' + input_check_id + '", 1)');
				span_radio.appendChild(input_radio_1);
				span_radio.appendChild(label_radio_1);
				
				span_radio.appendChild(input_check);
			}
			
			var points = document.createElement("input");
			points.type = "text";
			points.name = "jq_a_points[]";
			points.value = parseFloat(add_new_field)?parseFloat(add_new_field):0;
			points.setAttribute('maxlength', 10);
			document.getElementById("new_field_points").value  = '';
			
			if(quest_type == 1)
			{
				var inc_text = 	document.createElement("textarea");
				inc_text.setAttribute('name','jq_incorrect_feed[]');
				
				inc_text.row = 5;
				inc_text.cols = 50;
				inc_text.value = document.getElementById('wr_mess').value;
				cell8.appendChild(inc_text);
				document.getElementById('wr_mess').value = '';
				
			}
			
			cell1.setAttribute('valign','top');
			cell1.setAttribute('align','center');
			cell1.innerHTML = 0;
			cell2.setAttribute('valign','top');
			cell2.setAttribute('align','center');
			if(quest_type == 10)
			{
				cell2.appendChild(span_radio);

			} else {
				input_check.onchange=input_check.onclick = new Function('jq_UnselectCheckbox(this)');
				cell2.appendChild(input_check);
			}
			
			cell3.setAttribute('valign','top');
			cell3.innerHTML = '<div style="float:right;padding-right:15px;"><a href="index.php?option=com_joomlaquiz&task=edit_field&id='+number+'&tmpl=component&KeepThis=true&TB_iframe=true&height=370&width=700" title="Edit option" onclick="javascript: tb_start(this);return false;"  class="thickbox">Edit</a></div>'
			+'<div id="test_'+number+'">'+new_element_txt+'</div>';
			
			cell3.appendChild(ta_hidden);
			cell3.appendChild(input_hidden_id);
			cell4.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			cell5.innerHTML = '<'+'a href="javascript: void(0);" onclick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
			cell6.innerHTML = '';
			cell7.appendChild(points);
			
			cell1.valign = "top";
			cell2.valign = "top";
			cell3.valign = "top";
			cell4.valign = "top";
			cell5.valign = "top";
			cell6.valign = "top";
			cell7.valign = "top";
			cell8.valign = "top";
			
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			row.appendChild(cell7);
			row.appendChild(cell8);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);		
		}

		<?php } else {?>
		function Add_new_tbl_field(elem_field, tbl_id, field_name) {
			var new_element_txt = document.getElementById(elem_field).value;
			var add_new_field = document.getElementById("new_field_points").value;
			document.getElementById(elem_field).value = '';
			if (TRIM_str(new_element_txt) == '') {
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
			var cell8 = document.createElement("td");
			var input_hidden = document.createElement("input");
			input_hidden.type = "text";
			input_hidden.name = field_name;
			input_hidden.value = new_element_txt;
			
			var input_hidden_id = document.createElement("input");
			input_hidden_id.type = "hidden";
			input_hidden_id.setAttribute('name','jq_hid_fields_ids[]');
			
			input_hidden_id.value = "0";
			var input_check = createNamedElement("input","jq_checked[]");
			
			if(quest_type != 10)
			{
				input_check.type = "checkbox";
				input_check.setAttribute('name','jq_checked[]');
			
				input_check.checked = false;

			} else {
				var random_number = Math.floor(Math.random() * 1000000);
				var input_check_id = 'jq_checked_' + random_number;

				input_check.type = "hidden";
				input_check.setAttribute('name','jq_checked[]');
				input_check.value = 0;
				input_check.id = input_check_id;

				var br_elem = document.createElement("br");
				var span_radio = document.createElement("span");
				var label_radio_0 = document.createElement("label");
				label_radio_0.innerHTML = ' False';
				var input_radio_0 = createNamedElement("input","jq_radio_" + random_number);
				input_radio_0.setAttribute('name',"jq_radio_" + random_number);
				input_radio_0.setAttribute('checked', 1);
				input_radio_0.checked = 1;
				input_radio_0.type = "radio";
				input_radio_0.value = "0";
				input_radio_0.onclick = new Function('jq_SetHidden("' + input_check_id + '", 0)');
				span_radio.appendChild(input_radio_0);
				span_radio.appendChild(label_radio_0);
				span_radio.appendChild(br_elem);

				var label_radio_1 = document.createElement("label");
				label_radio_1.innerHTML = ' True';
				var input_radio_1 = createNamedElement("input","jq_radio_" + random_number);
				input_radio_1.setAttribute('name',"jq_radio_" + random_number);
				input_radio_1.type = "radio";
				input_radio_1.value = "1";
				input_radio_1.onclick = new Function('jq_SetHidden("' + input_check_id + '", 1)');
				span_radio.appendChild(input_radio_1);								
				span_radio.appendChild(label_radio_1);
				
				span_radio.appendChild(input_check);
			}
		
			var points = document.createElement("input");
			points.type = "text";
			points.name = "jq_a_points[]";
			points.value = parseFloat(add_new_field)?parseFloat(add_new_field):0;
			points.setAttribute('maxlength', 10);
			document.getElementById("new_field_points").value  = '';
			if(quest_type == 1)
			{
				var inc_text = 	document.createElement("textarea");
				inc_text.setAttribute('name','jq_incorrect_feed[]');
				
				inc_text.row = 5;
				inc_text.cols = 50;
				inc_text.value = document.getElementById('wr_mess').value;
				cell8.appendChild(inc_text);
				document.getElementById('wr_mess').value = '';
			}
			
			cell1.align = 'center';
			cell1.innerHTML = 0;
			if(quest_type == 10)
			{
				cell2.appendChild(span_radio);

			} else {
				input_check.onchange=input_check.onclick = new Function('jq_UnselectCheckbox(this)');
				cell2.appendChild(input_check);
			}
			
			cell3.appendChild(input_hidden);
			cell3.appendChild(input_hidden_id);
			cell4.innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/publish_x.png"  border="0" alt="Delete"></a>';
			cell5.innerHTML = '<'+'a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="<?php echo JURI::root()?>administrator/components/com_joomlaquiz/assets/images/uparrow.png"  border="0" alt="Move Up"></a>';
			cell6.innerHTML = '';
			cell7.appendChild(points);
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);row.appendChild(cell7);
			row.appendChild(cell8);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}
		<?php }?>
		
		function createNamedElement(type, name) {
		   var element = null;
		   // Try the IE way; this fails on standards-compliant browsers
		   try {
		      element = document.createElement('<'+type+' name="'+name+'">');
		   } catch (e) {
		   }
		   if (!element || element.nodeName != type.toUpperCase()) {
		      // Non-IE browser; use canonical method to create named element
		      element = document.createElement(type);
		      element.name = name;
		   }
		   return element;
		}
		
		function jq_UnselectCheckbox(che) {
			<?php if ($q_om_type == 1) { ?>
			f_name = che.form.name;
			ch_name = che.name;
						
			var a = che.checked;	
			start_index = 1;
			var tbl_elem = document.getElementById('qfld_tbl');
			if (tbl_elem.rows[start_index]) {
				var count = start_index;
				for (var j=start_index; j<tbl_elem.rows.length; j++) {
					if (tbl_elem.rows[j].cells[1].hasChildNodes()) {
						for (var i = 0; i < tbl_elem.rows[j].cells[1].childNodes.length; i++) {
							if (tbl_elem.rows[j].cells[1].childNodes[i].nodeName.toLowerCase() == 'input') {
								if (tbl_elem.rows[j].cells[1].childNodes[i].type.toLowerCase() == 'checkbox') {
									tbl_elem.rows[j].cells[1].childNodes[i].setAttribute('checked', false);
									tbl_elem.rows[j].cells[1].childNodes[i].checked = false;
								}
							}
						}
					}
					count++;
				}
			}
			if (a)
			che.checked = true;
		<?php } else { ?>
			return;
		<?php } ?>
		}
		function jq_UnselectCheckbox2(e) {
			if (!e) { e = window.event;}
			var cat2=e.target?e.target:e.srcElement;
			jq_UnselectCheckbox(cat2);
			
		}

		function jq_SetHidden(random_number, value) {
			document.getElementById(random_number).value = value;
		}
		
//-->
</script>