function refreshTable(params) {
    console.info('SportsData Now & Next: Updating fixtures using API');
    jQuery.ajax({
        type: "post",
        dataType: "html",
        data: {
            maxfixtures: params.maxfixtures,
            maxfuture: params.maxfuture
        },
        url: params.url + '/team/' + params.teamkey + '/now_and_next',
        success: function (data) {
            if (data !== undefined) {
                jQuery('#tbody_' + params.uid)
                    .fadeOut(500, function () {
                        jQuery(this).html(data).fadeIn(500);
                    });
                console.info('SportsData Now & Next: Fixtures updated from API');
            }
        },
        error: function (err, msg, thrown) {
            console.error('SportsData Now & Next: API returned error:', err, msg, thrown);
        },
        complete: function (resp) {
            if (resp.status === 304) {
                console.info('SportsData Now & Next: API returned 304 - Content Not Modified');
            }
        }
    });
}

jQuery('document').ready(function () {
    if (sdparams.isStale !== false) {
        refreshTable(sdparams);
    } else {
        console.info('SportsData Now & Next: No API update required');
    }
});