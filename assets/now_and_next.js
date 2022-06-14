function addTableRow(tbodyId, fixture) {
    var homeLogoHtml =
        (fixture.homeLogoUrl) ? '<img src="' + fixture.homeLogoUrl + '">' : '';
    var awayLogoHtml =
        (fixture.awayLogoUrl) ? '<img src="' + fixture.awayLogoUrl + '">' : '';
    var rowHtml =
        '<tr class="sd-event-row">'
        + '  <td>'
        + '    <div class="sd-team-logo sd-logo-home">' + homeLogoHtml + '</div>'
        + '    <div class="sd-team-logo sd-logo-away">' + awayLogoHtml + '</div>'
        + '    <time class="sd-event-date">' + fixture.eventDate + '</time>'
        + '    <time class="sd-event-time">' + fixture.eventTime + '</time>'
        + '    <h5 class="sd-event-results">' + fixture.homeScore + ' - ' + fixture.awayScore + '</h5>'
        + '    <h4 class="sd-event-title">' + fixture.homeTeam + ' Vs. ' + fixture.awayTeam + '</h4>'
        + '  </td>'
        + '</tr>';
    jQuery('#' + tbodyId).append(rowHtml);
}

function deleteTableRows(tbodyId) {
    jQuery('#' + tbodyId + ' tr').remove();
}

jQuery('document').ready(function () {
    deleteTableRows(sdparams.uid);
    for (fixture of sdparams.fixtures) {
        addTableRow(sdparams.uid, fixture);
    }
    jQuery.ajax({
        type: "post",
        dataType: "json",
        data: {
            maxfixtures: sdparams.maxrows,
            maxfuture: sdparams.maxfuture
        },
        url: "http://localhost:8000/wp-json/sportsdata/v1/team/twrfc-1",
        success: function (data) {
            console.log(data);
            deleteTableRows(sdparams.uid);
            for (fixture of data) {
                addTableRow(sdparams.uid, fixture);
            }
        }
    });
});