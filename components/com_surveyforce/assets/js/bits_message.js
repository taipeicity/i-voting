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
///////////////////////////////////////////
/*  ShowMessage(msg_obj,stat,mes)
 ** 
 */////////////////////////
var numSteps1 = 0;
var startingRed1 = 0;
var startingGreen1 = 0;
var startingBlue1 = 0;
var endingRed1 = 0;
var endingGreen1 = 0;
var endingBlue1 = 0;
var deltaRed1 = 0;
var deltaGreen1 = 0;
var deltaBlue1 = 0;
var currentRed1 = 0;
var currentGreen1 = 0;
var currentBlue1 = 0;
var currentStep1 = 0;
var timerID1 = 55;

function startFadeDec_mes(fade_obj, suffix, to_exec, ch_prop, startR, startG, startB, endR, endG, endB, nSteps) {
    currentRed1 = startingRed1 = parseInt(startR, 10);
    currentGreen1 = startingGreen1 = parseInt(startG, 10);
    currentBlue1 = startingBlue1 = parseInt(startB, 10);
    endingRed1 = parseInt(endR, 10);
    endingGreen1 = parseInt(endG, 10);
    endingBlue1 = parseInt(endB, 10);
    numSteps1 = parseInt(nSteps, 10);
    deltaRed1 = (endingRed1 - startingRed1) / numSteps1;
    deltaGreen1 = (endingGreen1 - startingGreen1) / numSteps1;
    deltaBlue1 = (endingBlue1 - startingBlue1) / numSteps1;
    currentStep1 = 0;
    fade_mes(fade_obj, suffix, to_exec, ch_prop);
}

function fade_mes(fade_obj, suffix, to_exec, ch_prop) {
    currentStep1++;
    if (currentStep1 <= numSteps1) {
        var hexRed = decToHex_mes(currentRed1);
        var hexGreen = decToHex_mes(currentGreen1);
        var hexBlue = decToHex_mes(currentBlue1);
        var color = "#" + hexRed + "" + hexGreen + "" + hexBlue + "";
        eval('sf_getObj(\'' + fade_obj + suffix + '\').style.' + ch_prop + ' = color');
        currentRed1 += deltaRed1;
        currentGreen1 += deltaGreen1;
        currentBlue1 += deltaBlue1;
        timerID = setTimeout("fade_mes('" + fade_obj + "', '" + suffix + "', '" + to_exec + "', '" + ch_prop + "')", 20); // sets timer
    }
    else {
        var hexRed = decToHex_mes(endingRed1);
        var hexGreen = decToHex_mes(endingGreen1);
        var hexBlue = decToHex_mes(endingBlue1);
        var color = "#" + hexRed + "" + hexGreen + "" + hexBlue + "";
        eval('sf_getObj(\'' + fade_obj + suffix + '\').style.' + ch_prop + ' = color');
        if (to_exec != '') {
            eval(to_exec);
        }
    }
}

function decToHex_mes(decNum) {
    decNum = Math.floor(decNum);
    var decString = "" + decNum;
    for (var i = 0; i < decString.length; i++) {
        if (decString.charAt(i) >= '0' && decString.charAt(i) <= '9') {
        }
        else {
            return decNum;
        }
    }
    var result = decNum;
    var remainder = "";
    var hexNum = "";
    var hexAlphabet = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");
    while (result > 0) {
        result = Math.floor(decNum / 16);
        remainder = decNum % 16;
        decNum = result;
        hexNum = "" + hexAlphabet[remainder] + "" + hexNum;
    }
    ;
    if (hexNum.length == 1)
        hexNum = "0" + hexNum;
    else if (hexNum.length == 0)
        hexNum = "00";
    return hexNum;
}

function ShowMessage(msg_obj, stat, mes) {
    var mes_span = sf_getObj(msg_obj);
   
    try {
        if (stat == 1) {
            mes_span.innerHTML = mes;
            mes_span.style.visibility = "visible";
            startFadeDec_mes(msg_obj, '', '', 'color', 250, 250, 250, 200, 0, 0, 30);
        }
        else {
            mes_span.style.visibility = "hidden";
        }
    } catch (e) {
    }
}