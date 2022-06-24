function sdLeaguetableSelectCompetition(uid, compid, comptext) {
    jQuery('#sd_content_' + uid + ' tbody.sd-league-data')
        .not('#sd_league_data_' + uid + '_' + compid)
        .hide();
    jQuery('#sd_league_data_' + uid + '_' + compid)
        .show();
    jQuery('#sd_content_' + uid + ' TH.sd-league-title')
        .html(comptext);
}

function sdRefreshLeagueTable(params) {
    console.info('SportsData LeagueTable: Updating using API');
    jQuery.ajax({
        type: "post",
        dataType: "html",
        data: {
            uid: params.uid,
            team: params.teamkey
        },
        url: params.url + '/team/' + params.teamkey + '/league_table',
        success: function (data) {
            if (data !== undefined) {
                jQuery('#sd_content_' + params.uid)
                    .fadeOut(500, function () {
                        jQuery(this).html(data).fadeIn(500);
                    });
                console.info('SportsData LeagueTable: Updated from API');
            }
        },
        error: function (err, msg, thrown) {
            console.error('SportsData LeagueTable: API returned error:', err, msg, thrown);
        },
        complete: function (resp) {
            if (resp.status === 304) {
                console.info('SportsData LeagueTable: API returned 304 - Content Not Modified');
            }
        }
    });
}

jQuery('document').ready(function () {
    let isLeagueTable = false;
    try {
        isLeagueTable = undefined !== leagueTableParams;
    } catch {
        // Throw this away - clearly not our page
    }
    if (isLeagueTable && leagueTableParams.isStale !== false) {
        sdRefreshLeagueTable(leagueTableParams);
    } else {
        console.info('SportsData LeagueTable: No API update required');
    }
});