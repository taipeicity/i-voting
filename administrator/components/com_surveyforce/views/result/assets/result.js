jQuery(document).ready(function () {

    var configTabs = jQuery('#configTabs');

    jQuery('#selectDate').on('change', function () {
        jQuery('#result-form').submit();
    });


    configTabs.find("li").on("click", function () {
        jQuery.ajax({
            url: "index.php?option=com_surveyforce&task=result.mark",
            type: "POST",
            data: {mark: jQuery(this).find("a").attr("href").substring(1)},
            success: function () {
                return true
            },
            error: function () {
                return false;
            }
        });
    });
});
