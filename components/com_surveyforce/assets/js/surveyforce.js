function popupWindow(a,b,c,d,f){winprops="height="+d+",width="+c+",top="+(screen.height-d)/2+",left="+(screen.width-c)/2+",scrollbars="+f+",resizable";win=window.open(a,b,winprops);4<=parseInt(navigator.appVersion)&&win.window.focus()}

function sf_getObj(name) {
    if (document.getElementById) {
        return document.getElementById(name);
    }
    else if (document.all) {
        return document.all[name];
    }
    else if (document.layers) {
       return document.layers[name];
    }
}

// JavaScript Document
function Browser() {

    var ua, s, i;

    this.isIE = false;
    this.isNS = false;
    this.version = null;

    ua = navigator.userAgent;

    s = "MSIE";
    if ((i = ua.indexOf(s)) >= 0) {
        this.isIE = true;
        this.version = parseFloat(ua.substr(i + s.length));
        return;
    }

    s = "Netscape6/";
    if ((i = ua.indexOf(s)) >= 0) {
        this.isNS = true;
        this.version = parseFloat(ua.substr(i + s.length));
        return;
    }

    // Treat any other "Gecko" browser as NS 6.1.

    s = "Gecko";
    if ((i = ua.indexOf(s)) >= 0) {
        this.isNS = true;
        this.version = 6.1;
        return;
    }
}

// redirect to list view
function surveyWindowSaveClose() {
    window.location.reload();
}

function editWindowTitle(nt) {
    //jax.$('surveyEditorMessage').innerHTML = nt;
}

// Global object to hold drag information.
var load_method = (window.ie ? 'load' : 'domready');

// Must re-initialize window position
function mySurveyShowWindow(windowUrl) {

    Obj = document.getElementById("surveyWindow");
    if (!Obj) {
        Obj = document.createElement('div');

        var html = '';
        html += '<div id="surveyWindow" return false;" onmousedown="dragOBJ(this, event);" style="top: 0px;">';
        html += '	<!-- top section -->';
        html += '	<div id="sf_tl"></div>';
        html += '	<div id="sf_tm"></div>';
        html += '	<div id="sf_tr"></div>';
        html += '	<div style="clear: both;"></div>';
        html += '	<!-- middle section -->';
        html += '	<div id="sf_ml"></div>';
        html += '	<div id="surveyWindowContentOuter">';
        html += '		<div id="surveyWindowContentTop">';
        html += '			<a href="javascript:void(0);" onclick="mySurveyHideWindow();" id="sf_close_btn">Close</a>';
        html += '			<div id="sf_logo"></div>';
        html += '		</div>';
        html += '		<div id="surveyWindowContent">';
        html += '		</div>';
        html += '	</div>';
        html += '	<div id="sf_mr"></div>';
        html += '	<div style="clear: both;"></div>';
        html += '	<!-- bottom section -->';
        html += '	<div id="sf_bl"></div>';
        html += '	<div id="sf_bm"></div>';
        html += '	<div id="sf_br"></div>';
        html += '	<div style="clear: both;"></div>';
        html += '</div>';


        Obj.innerHTML = html;
        document.body.appendChild(Obj);
    }


    var myWidth = 0, myHeight = 0;
    if (typeof(window.innerWidth) == 'number') {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    }
    else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight - 20 + 'px';
    }
    else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }

    var yPos;
    if (window.innerHeight != null)
    {
        yPos = window.innerHeight;
    }
    else if (document.documentElement && document.documentElement.clientHeight)
    {
        yPos = document.documentElement.clientHeight;
    }
    else
    {
        yPos = document.body.clientHeight;
    }

    yPos = yPos - 60;
    var leftPos = (myWidth - 837) / 2;

    document.getElementById('surveyWindow').style.visibility = "visible";
    document.getElementById('surveyWindow').style.zIndex = myGetZIndexMax() + 1;
    document.getElementById('surveyWindowContent').innerHTML = '<iframe id="surveyContentFrame" src="' + windowUrl + '" frameborder="0" style="width: 770px; height: 530px;" scrolling="auto"></iframe>';

    // change the iframe source
    document.getElementById('surveyContentFrame').setAttribute("src", '');
    document.getElementById('surveyContentFrame').setAttribute("src", windowUrl);

    //if (browser.isIE) {
    //jQuery('#sf_tl, #sf_tm, #sf_tr, #sf_ml, #sf_mr, #sf_bl, #sf_bm, #sf_br, #sf_logo').pngfix();
    //}

    /*
     Set editor position, center it in screen regardless of the scroll position
     */
    // In ie 7, pageYOffset is null
    var iframe = document.getElementById("surveyWindow");
    if (window.pageYOffset)
        iframe.style.marginTop = (window.pageYOffset + 10) + 'px';
    else
        iframe.style.marginTop = (document.body.scrollTop + 10) + 'px';
    iframe.style.height = (yPos) + 'px';


    /*
     Set height and width for transparent window
     */

    var m_s = yPos + 'px';
    document.getElementById("surveyWindow").style.height = m_s
    document.getElementById('surveyWindow').style.left = leftPos + 'px';
    document.getElementById("surveyWindowContent").style.height = (yPos - 30) + 'px';
    document.getElementById("surveyContentFrame").style.height = (yPos - 30) + 'px';
    document.getElementById("surveyWindowContentOuter").style.height = m_s;
    document.getElementById("sf_ml").style.height = m_s;
    document.getElementById("sf_mr").style.height = m_s;


}

function mySurveyHideWindow() {
    document.getElementById('surveyWindowContent').innerHTML = "";
    document.getElementById('surveyWindow').style.visibility = "hidden";
}

function dragOBJ(d, e) {

    function drag(e) {
        if (!stop) {
            d.style.top = (tX = xy(e, 1) + oY - eY + 'px');
            d.style.left = (tY = xy(e) + oX - eX + 'px');
        }
    }

    function agent(v) {
        return(Math.max(navigator.userAgent.toLowerCase().indexOf(v), 0));
    }
    function xy(e, v) {
        return(v ? (agent('msie') ? event.clientY + document.body.scrollTop : e.pageY) : (agent('msie') ? event.clientX + document.body.scrollTop : e.pageX));
    }

    var oX = parseInt(d.style.left);
    var oY = parseInt(d.style.top);
    var eX = xy(e);
    var eY = xy(e, 1);
    var tX, tY, stop;

    document.onmousemove = drag;
    document.onmouseup = function() {
        stop = 1;
        document.onmousemove = '';
        document.onmouseup = '';
    };

}

function myGetZIndexMax() {
    var allElems = document.getElementsByTagName ?
            document.getElementsByTagName("*") :
            document.all; // or test for that too
    var maxZIndex = 0;

    for (var i = 0; i < allElems.length; i++) {
        var elem = allElems[i];
        var cStyle = null;
        if (elem.currentStyle) {
            cStyle = elem.currentStyle;
        }
        else if (document.defaultView && document.defaultView.getComputedStyle) {
            cStyle = document.defaultView.getComputedStyle(elem, "");
        }

        var sNum;
        if (cStyle) {
            sNum = Number(cStyle.zIndex);
        } else {
            sNum = Number(elem.style.zIndex);
        }
        if (!isNaN(sNum)) {
            maxZIndex = Math.max(maxZIndex, sNum);
        }
    }
    return maxZIndex;
}

function preload(arrayOfImages) {
    jQuery(arrayOfImages).each(function() {
        jQuery('<img/>')[0].src = this;
    });
}

function ScrollToElement(theElement) {

    var selectedPosX = 0;
    var selectedPosY = 0;

    while (theElement != null) {
        try {
            selectedPosX += theElement.offsetLeft;
            selectedPosY += theElement.offsetTop;
            theElement = theElement.offsetParent;
        } catch (e) {
        }
    }
    try {
        window.scrollTo(selectedPosX, selectedPosY);
    } catch (e) {
    }

}

function question_data(){
    // *** DRAG'and'DROP CODE *** //
    this.kol_drag_elems = 0;
    this.drag_array = new Array(this.kol_drag_elems);
    this.coord_left = new Array(this.kol_drag_elems);
    this.coord_top = new Array(this.kol_drag_elems);
    this.ids_in_cont = new Array(this.kol_drag_elems); // what div id's in containers
    this.cont_for_ids = new Array(this.kol_drag_elems); //in that container this id
    this.answ_ids = new Array(this.kol_drag_elems);
    this.cont_index = 0;
    // *** end of DRAG'and'DROP CODE *** //
    this.kol_main_elems = 0;
    this.main_ids_array = new Array(kol_main_elems);

    this.cur_quest_type = '';
    this.cur_quest_id = 0;
    this.cur_impscale_ex = 0;
    this.compulsory = 1;
    this.default_hided = 0;

    this.cur_quest_text = '';
    this.div_id = '';
    this.response = null;
}

function sf_GetQuestionData(question, n) {
    questions[n].cur_quest_text = question.getElementsByTagName('quest_text')[0].firstChild.data;
    questions[n].cur_quest_type = question.getElementsByTagName('quest_type')[0].firstChild.data;
    questions[n].cur_quest_id = question.getElementsByTagName('quest_id')[0].firstChild.data;
    questions[n].compulsory = question.getElementsByTagName('compulsory')[0].firstChild.data;
    questions[n].default_hided = question.getElementsByTagName('default_hided')[0].firstChild.data;
    questions[n].div_id = 'quest_div' + questions[n].cur_quest_id;
    questions[n].response = question;

    if ( questions[n].cur_quest_type == 1 )
    {
        questions[n].kol_main_elems = question.getElementsByTagName('main_fields_count')[0].firstChild.data;
        questions[n].main_ids_array = new Array(questions[n].kol_main_elems);

        for (j = 0; j < questions[n].kol_main_elems; j++) {
            questions[n].main_ids_array[j] = SF_getElement(question, 'mfield_id', j);
        }
    }

    if ( questions[n].cur_quest_type == 6 )
    {
        questions[n].kol_drag_elems = question.getElementsByTagName('alt_fields_count')[0].firstChild.data; // !!!
        questions[n].drag_array = new Array(questions[n].kol_drag_elems);
        questions[n].coord_left = new Array(questions[n].kol_drag_elems);
        questions[n].coord_top = new Array(questions[n].kol_drag_elems);
        questions[n].ids_in_cont = new Array(questions[n].kol_drag_elems);
        questions[n].cont_for_ids = new Array(questions[n].kol_drag_elems);
        questions[n].answ_ids = new Array(questions[n].kol_drag_elems);

        questions[n].kol_main_elems = question.getElementsByTagName('main_fields_count')[0].firstChild.data;
        questions[n].main_ids_array = new Array(questions[n].kol_main_elems);

        for (j = 0; j < questions[n].kol_main_elems; j++) {
            questions[n].main_ids_array[j] = SF_getElement(question, 'mfield_id', j);
            questions[n].answ_ids[j] = SF_getElement(question, 'afield_id', j);
        }
    }
}

function sf_CreateQuestions() {
    var html_data;
    var i = 0;
    var index = '0';

    sf_getObj('survey_container').innerHTML = '';
    for (i = 0; i < quest_count; i++) {
        index = ''+response.getElementsByTagName('question_data')[i].getElementsByTagName('quest_id')[0].firstChild.data;
        questions[index] = new question_data();

        sf_GetQuestionData(response.getElementsByTagName('question_data')[i], index);
        
        html_data = sf_GetQuestionHtml(questions[index].cur_quest_type, index);

        if(html_data.match(/iscale_table/)){
            questions[index].cur_impscale_ex = 1;
        }

        var hided = (questions[index].default_hided == '1' ? ' style="display:none;" ' : '');

        var div_inside = document.createElement("div");
        div_inside.innerHTML = html_data + (quest_count > 1 ? '<div id="dl_' + questions[index].div_id + '" ' + hided + '>' + getQuestionDelimeter() + '</div>' : '');
        sf_getObj('survey_container').appendChild(div_inside);
        if (questions[index].cur_quest_type == 5 || questions[index].cur_quest_type == 9) {
            if (window.getChoosenSelect)
                getChoosenSelect();
        }
    }    
    
}

function sf_in_array(needle, haystack, argStrict) {
    var key = '', strict = !!argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}

function getQNumberByID(qid) {
    for (n = 0; n < quest_count; n++) {
        if (qid == questions[n].cur_quest_id) {
            return n;
        }
    }
    return NULL;
}

function SF_do_redirect(redirect_url) {
    if (!redirect_url)
        return false;
    redirect_url = redirect_url + '';
    if (redirect_url.indexOf('javascript:') === -1) {
        window.location.href = redirect_url;
    } else {
        redirect_url = redirect_url.replace("javascript:", "");
        eval(redirect_url);
    }
    return true;
}

function sf_AddEvent(obj, evType, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(evType, fn, true);
        return true;
    } else if (obj.attachEvent) {
        var r = obj.attachEvent("on" + evType, fn);
        return r;
    } else {
        return false;
    }
}

function sf_SetTimer() {
    timerID = setTimeout("sf_InitAjax()", 300);
}

function sf_releaseBlock() {
    survey_blocked = 0;
}

function sf_InitFunc() {
    sf_getObj('survey_container').removeChild(sf_getObj('wait_div'));
    var div_inside1 = document.createElement("div");
    div_inside1.setAttribute("style", "padding:40px ");
    div_inside1.innerHTML = "<br\/>Load complete.";
    sf_getObj('survey_container').appendChild(div_inside1);
}

function sf_StartSurveyOn() {
    if (!survey_blocked) {
        ShowMessage('error_messagebox', 1, mes_loading);
        timerID = setTimeout("sf_StartSurvey()", 300);
    } else {
        ShowMessage('error_messagebox', 1, mes_please_wait);
    }
}

function setDrnDnAnswers(n) {
    var ans_count = questions[n].response.getElementsByTagName('ans_count')[0].firstChild.data;
    var i = 0;
    var j = 0;
    if (ans_count > 0) {
        var mfield_id = 0;
        var ans_id = 0;
        var div_n = 0;
        for (i = 1; i <= questions[n].kol_drag_elems; i++) {
            mfield_id = questions[n].response.getElementsByTagName('mfield_id')[i - 1].firstChild.data;
            ans_id = 0;
            for (j = 0; j < ans_count; j++) {
                if (questions[n].response.getElementsByTagName('a_quest_id')[j].firstChild.data == mfield_id)
                    ans_id = questions[n].response.getElementsByTagName('ans_id')[j].firstChild.data;
            }

            div_n = 0;
            for (j = 0; j < ans_count; j++) {
                if (ans_id == questions[n].response.getElementsByTagName('afield_id')[j].firstChild.data)
                    div_n = j + 1;
            }
            if (div_n > 0) {
                an_div = sf_getObj('cdiv' + questions[n].cur_quest_id + '_' + i);
                targ = sf_getObj('ddiv' + questions[n].cur_quest_id + '_' + div_n);
                targ.style.left = parseInt((targ.offsetLeft - an_div.offsetLeft) / -2) + 'px';
                targ.style.top = parseInt((an_div.offsetLeft - targ.offsetLeft) + 10) + 'px';
                last_drag_id = 'ddiv' + questions[n].cur_quest_id + '_' + div_n;
                last_drag_quest_n = n;
                questions[n].cont_index = i;
                stopDrag();
            }
        }
    }
}

function removeSameRank(e, n) {
    var targ = e;
    if (targ.id.substring(0, 12) != 'quest_select') {
        return;
    }
    var cur = targ.value;
    var mcount = questions[n].response.getElementsByTagName('main_fields_count')[0].firstChild.data;
    var sel = null;
    for (i = 0; i < mcount; i++) {
        sel = sf_getObj("quest_select_" + questions[n].cur_quest_id + "_" + questions[n].response.getElementsByTagName('mfield_id')[i].firstChild.data);
        if (sel.id != targ.id && sel.value == cur)
            sel.value = 0;
    }
    check_answer(n);
}

function sf_SurveyPrevOn() {
    window.scroll(0, 0);
    if (!survey_blocked) {
        ShowMessage('error_messagebox', 1, mes_loading);
        timerID = setTimeout("sf_SurveyPrev()", 300);
    } else {
        ShowMessage('error_messagebox', 1, mes_please_wait);
    }
}

function sf_SurveyPrev() { //send 'TASK = prev'
    var request_str = '';
    
    for (var i in questions) {
        request_str = request_str + '&quest_id[]=' + questions[i].cur_quest_id;
    }
    ShowMessage('error_messagebox2', 1, '');
    sf_UpdateTaskDiv('null');
    sf_MakeRequest('action=prev&survey=' +survey_id+ invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + request_str);
}

function startDrag(e, n){
    last_drag_quest_n = n;
    // determine event object
    if(!e){var e=window.event};
    // determine target element
    var targ=e.target?e.target:e.srcElement;
    //break if not draggable element
    if (targ.id.substring(0, 4) != 'ddiv') {return;}
    if (last_drag_id_drag != '') {
        //break if target not last draggable div(div on mouse)
        if (last_drag_id_drag != targ.id) {return;}
    }
    //Bring draggable div to top, other div's to back
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div	= sf_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
        an_div.style.zIndex = 500;
    }
    targ.style.zIndex = 1000;
    //set some config options
    targ.style.position = 'relative';
    last_drag_id = targ.id;
    last_drag_id_drag = targ.id;
    // calculate event X,Y coordinates
    offsetX=e.clientX;
    offsetY=e.clientY;
    // assign default values for top and left properties
    if(!targ.style.left){targ.style.left='0px'};
    if(!targ.style.top){targ.style.top='0px'};
    // calculate integer values for top and left properties
    coordX=parseInt(targ.style.left);
    coordY=parseInt(targ.style.top);
    drag=true;
    questions[n].cont_index = 0;
    // move div element
    document.onmousemove=dragDiv;
}
// continue dragging
function dragDiv(e){
    var n = last_drag_quest_n;
    if(!drag){return};
    if(!e){var e=window.event};
    var targ		= e.target?e.target:e.srcElement;
    //set old coordinates to other div's (because it's position is relative and they 'prygayut'
    if (last_drag_id_drag != '') {
        if (last_drag_id_drag != targ.id) {
            var ddd = sf_getObj(last_drag_id_drag);
            ddd.style.left	= coordX+e.clientX-offsetX+'px';
            ddd.style.top	= coordY+e.clientY-offsetY+'px';
            return;
        }
    }
    if (targ.id.substring(0, 4) != 'ddiv') {return;}
    // move div element
    targ.style.left	= coordX+e.clientX-offsetX+'px';
    targ.style.top	= coordY+e.clientY-offsetY+'px';
    var is_on_cont = false;
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div			= sf_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);

        FDIV_RightX		= an_div.offsetLeft + an_div.offsetWidth;
        SDIV_LeftX		= targ.offsetLeft;//+coordX+e.clientX-offsetX;
        FDIV_TopY		= an_div.offsetTop;
        FDIV_DownY		= an_div.offsetTop + an_div.offsetHeight;
        SDIV_MiddleY	= targ.offsetTop + parseInt(targ.offsetHeight/2);
        if ( ((parseInt(FDIV_RightX) + 10) > (parseInt(SDIV_LeftX))) &&
            ((parseInt(FDIV_DownY) + 10) > (parseInt(SDIV_MiddleY))) &&
            ((parseInt(FDIV_TopY) + 10) < (parseInt(SDIV_MiddleY))) ) {
            an_div.style.backgroundColor = color_highlight;
            an_div.className = 'jb_survey_dragdrop_left js_dragdrop_highlight';
            questions[n].cont_index = i;
            is_on_cont = true;
        }
        else {
            an_div.style.backgroundColor = color_cont;
            an_div.className = 'jb_survey_dragdrop_left';
        }
    }
    var dr_number = parseInt(last_drag_id.substring(last_drag_id.lastIndexOf("_")+1));
    //! id of the div is 'ddiv_xxx' - five simbols! plus number
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        if (i != dr_number) {
            an_div			= sf_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
            if ( (questions[n].coord_left[i]) && (questions[n].coord_left[i] != '') ) {
                an_div.style.left = questions[n].coord_left[i];
            }
            if ( (questions[n].coord_top[i]) && (questions[n].coord_top[i] != '') ) {
                an_div.style.top = questions[n].coord_top[i];
            }
        }
    }
    if (!is_on_cont) { questions[n].cont_index = 0; }
    return false;
}
// stop dragging
function stopDrag(){
    var n = last_drag_quest_n;
    var dr_obj = sf_getObj(last_drag_id);
    if (n < 0) { return; }
    if (dr_obj) {
        var dr_number = parseInt(last_drag_id.substring(last_drag_id.indexOf('_')+1));
        //! id of the div is 'ddivxx_xxx' - ddiv plus quest_id plus '_' plus number
        if (questions[n].cont_index) {
            dr_obj.style.position = 'relative';
            dr_obj.style.left	= '-57px';
            dr_obj.style.top	= parseInt((questions[n].cont_index - 1)*56 - (56*(dr_number - 1)) + 7) + 'px';
            questions[n].ids_in_cont[questions[n].cont_index - 1] = dr_number;
        }

        questions[n].cont_for_ids[dr_number - 1] = questions[n].cont_index;

        questions[n].coord_left[dr_number] = dr_obj.style.left;
        questions[n].coord_top[dr_number] = dr_obj.style.top;
        dr_obj.style.zIndex = 499;
    }
    last_drag_id_drag = '';
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div	= sf_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);
        an_div.style.backgroundColor = color_cont;
        an_div.className = 'jb_survey_dragdrop_left';
    }
    last_drag_quest_n = -1;
    drag=false;
    check_answer(n);
}

function sf_Check_selectRadio(rad_name, form_name) {
    var selItem = eval('document.' + form_name + '.' + rad_name);
    if (selItem) {
        if (selItem.length) {
            var i;
            for (i = 0; i < selItem.length; i++) {
                if (selItem[i].checked) {
                    if (selItem[i].value > 0) {
                        return selItem[i].value;
                    }
                }
            }
        }
        else if (selItem.checked) {
            return selItem.value;
        }
    }
    return false;
}

function sf_Check_selectCheckbox(n) {
    var acount = questions[n].response.getElementsByTagName('alt_fields_count')[0].firstChild.data;
    var other_id = 0;
    if (acount > 0) {
        other_id = questions[n].response.getElementsByTagName('afield_id')[0].firstChild.data;
    }
    var selItem = eval('document.quest_form' + questions[n].cur_quest_id + '.quest_check' + questions[n].cur_quest_id);
    var rrr = '';
    if (selItem) {
        if (selItem.length) {
            var i;
            for (i = 0; i < selItem.length; i++) {
                if (selItem[i].checked) {
                    if (selItem[i].value > 0) {
                        if (selItem[i].value == other_id) {
                            if (sf_getObj('other_op_' + questions[n].cur_quest_id).value != '') {
                                rrr = rrr + selItem[i].value + '!!--!!' + sf_escape(sf_getObj('other_op_' + questions[n].cur_quest_id).value) + '!!,!! ';
                            }
                        }
                        else {
                            rrr = rrr + selItem[i].value + '!!,!! ';
                        }
                    }
                }
            }
            rrr = rrr.substring(0, rrr.length - 6)
        } else if (selItem.checked) {
            if (selItem.value == other_id) {
                if (sf_getObj('other_op_' + questions[n].cur_quest_id).value != '') {
                    rrr = rrr + selItem.value + '!!--!!' + sf_getObj('other_op_' + questions[n].cur_quest_id).value;
                }
            }
            else {
                rrr = rrr + selItem.value;
            }
        }
    }
    return rrr;
}

function sf_SurveyFinishOn() {
    try {
        ScrollToElement(sf_getObj('surveyforce_top'));
    } catch (e) {
    }
    var request_str = '';
    var tmp;
    var i;
    if (!survey_blocked) {
        var no_error = true;
        for (var i in questions) {
            //if (sf_getObj('quest_div' + questions[i].cur_quest_id).style.display != 'none') {
                tmp = sf_SurveyNextData(i);
                if (tmp != false)
                    request_str = request_str + tmp;
                else
                    no_error = false;
            //}
        }
        if (!no_error)
            return false;
        sf_UpdateTaskDiv('null');
        sf_MakeRequest('action=next&survey='+survey_id + invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + '&finish=1' + request_str);
    } else {
        ShowMessage('error_messagebox', 1, mes_please_wait);
        ShowMessage('error_messagebox2', 1, '');
    }
}

function sf_SurveyNextOn() {
    try {
        ScrollToElement(sf_getObj('surveyforce_top'));
    } catch (e) {
    }
    var request_str = '';
    var tmp;
    
    if (!survey_blocked) {
        ShowMessage('error_messagebox', 1, mes_loading);
        ShowMessage('error_messagebox2', 1, '');
        var no_error = true;
        for (var key in questions) {
            
           // if (sf_getObj('quest_div' + questions[key].cur_quest_id).style.display != 'none') {
                tmp = sf_SurveyNextData(key);
                if (tmp != false)
                    request_str = request_str + tmp;
                else
                    no_error = false;
            //}
        }
        if (!no_error)
            return false;
        sf_UpdateTaskDiv('null');
        sf_MakeRequest('action=next&survey='+ survey_id + invited_url + '&start_id=' + start_id + '&user_id=' + user_unique_id + request_str);
    } else {
        ShowMessage('error_messagebox', 1, mes_please_wait);
        ShowMessage('error_messagebox2', 1, '');
    }
}

function SF_getElement(data, name, i) {

    try {
        return data.getElementsByTagName(name)[i].firstChild.data;
    } catch (e) {
        if (debug_mode) {
            alert(name + ' ' + i);
        }
    }
}

function getImpScale(n) {
    var data = questions[n].response;
    cur_impscale_ex = 0;
    var iscount = SF_getElement(data, 'impscale_fields_count', 0);
    var return_str = '';
    if (iscount > 0) {
        questions[n].cur_impscale_ex = 1;
        var ans_imp_count = SF_getElement(data, 'ans_imp_count', 0);
        var iscale_name = SF_getElement(data, 'impscale_name', 0);
        return_str = '<div align="left" class="importance_div">' +
                '<form name="iscale_form' + questions[n].cur_quest_id + '">' +
                '<br/>' +
                '<br/>' +
                '<table class="importance_table" id="iscale_table" cellpadding="0" cellspacing="0">';
        return_str = return_str + '<tr class="sectiontableentry2"><td class="i_quest" colspan="' + iscount + '" >&nbsp;&nbsp;' + iscale_name + '<\/td><\/tr>';
        return_str = return_str + '<tr class="sectiontableentry1">';
        var j;
        for (j = 0; j < iscount; j++) {
            return_str = return_str + '<td class="i_text_cell" onclick="javascript: sf_getObj(\'iscale_radio' + questions[n].cur_quest_id + '_' + j + '\').checked=\'checked\';">' +
                    '<label for="iscale_radio' + questions[n].cur_quest_id + '_' + j + '" style="cursor: pointer;">' +
                    SF_getElement(data, 'isfield_text', j) +
                    '</label>' +
                    '</td>';
        }
        return_str = return_str + '</tr>';
        var i;
        return_str = return_str + '<tr class="sectiontableentry2">';
        var selected = '';
        for (j = 0; j < iscount; j++) {
            selected = '';
            if (ans_imp_count > 0) {
                if (SF_getElement(data, 'isfield_id', j) == SF_getElement(data, 'ans_imp_id', 0)) {
                    selected = " checked='checked' ";
                }
            }
            return_str = return_str + '<td class="i_ans_cell" onclick="javascript: sf_getObj(\'iscale_radio' + questions[n].cur_quest_id + '_' + j + '\').checked=\'checked\';">' +
                    '<input class="i_radio" type="radio" name="iscale_radio' + questions[n].cur_quest_id + '" id="iscale_radio' + questions[n].cur_quest_id + '_' + j + '" value="' + SF_getElement(data, 'isfield_id', j) + '" ' + selected + '/>' +
                    '</td>';
        }
        return_str = return_str + "<\/tr>";
        return_str = return_str + "<\/table><\/form><\/div>";
    }
    return return_str;
}

function getQuestionDelimeter() {
    return '<br/><hr/><br/>';
}

function getButton(type, label, onclick) {
    var return_str = '<input type="button" id="sf_'+type+'_button" class="button" value="'+label+'" onclick="'+onclick+'" />';
    return return_str;
}
/*
function getButton(type, label, onclick) {

    var return_str = '<span class="' + type + '_bt_container" id="><a class="' + type + '_link" onfocus="javascript: this.blur();"  href="javascript: void(0);" onclick="javascript: ' + onclick + '">' + label + '</a></div>';

    
    return return_str;
}
    */
    
function jq_in_array (needle, haystack, argStrict) {
    var key = '', strict = !!argStrict; 
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {                return true;
            }
        }
    }
     return false;
}

