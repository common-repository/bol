function doBolSearch(limit) {
    var srch = jQuery("#bolsearch").val();
                
    jQuery.ajax({
        url: "/wp-content/plugins/bol/bol-search.php",
        type: 'post',
        data: {'text': srch, 'category': 0, 'limit': limit},
        success: function(response) {
            jQuery("#bolSearchDiv").html(response);
        }
    });

    return false;
}