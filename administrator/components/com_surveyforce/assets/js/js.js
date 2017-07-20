	function makeRequest(url) {

		var http_request = false;
	
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType) {
				http_request.overrideMimeType('text/xml');
				// See note below about this line
			}
		} else if (window.ActiveXObject) { // IE
			try {
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					http_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
		}
	
		if (!http_request) {
			// alert('Giving up: Cannot create an XMLHTTP instance');
			return false;
		}
		if (url.indexOf('latestNews') == -1){
			http_request.onreadystatechange = function() { alertContents(http_request); }
		} else{
			http_request.onreadystatechange = function() { alertContentsNews(http_request); }
		}
		http_request.open('GET', url, true);
		http_request.send(null);
	}

    function alertContents(http_request) {

        if (http_request.readyState == 4) {
            if ((http_request.status == 200) && (http_request.responseText.length < 1025)) {
				document.getElementById('sf_LatestVersion').innerHTML = '&nbsp;'+http_request.responseText;
            } else {
                document.getElementById('sf_LatestVersion').innerHTML = 'There was a problem with the request.';
            }
        }

    }
	function alertContentsNews(http_request) {

        if (http_request.readyState == 4) {
            if ((http_request.status == 200) && (http_request.responseText.length < 1025)) {
				document.getElementById('sf_LatestNews').innerHTML = '&nbsp;'+http_request.responseText;
            } else {
                document.getElementById('sf_LatestNews').innerHTML = 'There was a problem with the request.';
            }
        }

    }

	function sf_CheckNews(){
		document.getElementById('sf_LatestNews').innerHTML = 'Checking latest news now...';
    	makeRequest('index.php?option=com_surveyforce&task=latestNews&tmpl=component');
    	return false;
	}

    function sf_CheckVersion() {
    	document.getElementById('sf_LatestVersion').innerHTML = 'Checking latest version now...';
    	makeRequest('index.php?option=com_surveyforce&task=latestVersion&tmpl=component');
    	return false;
    }
    function sf_InitAjax() {
    	makeRequest('index.php?option=com_surveyforce&task=latestVersion&tmpl=component');
    }

    function sf_dateAjaxRef()
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_surveyforce&task=datedb"
        });
        window.open("http://www.joomplace.com/support/product-improvement-survey?usf_source=SurveyForce&usf_medium=Survey&usf_campaign=Product%2BImprovement", "_blank");
    }

    function sf_dateAjaxIcon()
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_surveyforce&task=datedb"
        });
        jQuery('#notification').remove();
    }
