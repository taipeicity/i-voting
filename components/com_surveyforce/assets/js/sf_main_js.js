function getObj(name) {
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
function SF_preloadImages() {
    var d = document;
    if (d.images) {
        if (!d.MM_p)
            d.MM_p = new Array();
        var i, j = d.MM_p.length, a = SF_preloadImages.arguments;
        for (i = 0; i < a.length; i++)
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
    }
}
function SF_WStatus(ws_txt) {
    window.status = '';//ws_txt;
}
function SF_ShowTBToolTip_quiz(tt) {
    var q_elem = getObj('SF_toolbar_tooltip');
    q_elem.innerHTML = tt;
}

