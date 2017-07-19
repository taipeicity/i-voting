jQuery.noConflict();
function pagination_go(limit, id) {
    var site = GetSiteRoot();
    console.log(site);
    var mes_loading = "<img src='" + site + "/components/com_surveyforce/assets/images/loading.gif' border='0' alt='' title='' />";
    jQuery("#survey_container").html('<center>' + mes_loading + '</center>');
    jQuery.ajax({
        type: "POST",
        url: "index.php?no_html=1&tmpl=component&option=com_surveyforce&task=ajax_action",
        data: "limit=" + limit + "&count=20&survey=" + id + "&action=result&user_id=" + user_unique_id + "&start_id=" + start_id + "&pagination=1",
        success: function(msg) {
            jQuery("#survey_container").html(msg);
        }
    });
}
function GetSiteRoot()
{
    var rootPath = window.location.protocol + "//" + window.location.host + "/";
    if (window.location.hostname == "localhost")
    {
        var path = window.location.pathname;
        if (path.indexOf("/") == 0)
        {
            path = path.substring(1);
        }
        path = path.split("/", 1);
        if (path != "")
        {
            rootPath = rootPath + path + "/";
        }
    }
    return rootPath;
}