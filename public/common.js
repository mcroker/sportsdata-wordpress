var sdBlock = {};

jQuery('document').ready(function () {
	for (block of Object.values(sdBlock)) {
		sdRefreshDiv(block);
	}
});

function sdRegisterBlock(blockparams) {
	sdBlock[blockparams.uid] = blockparams;
}

function sdSelectCompetition(uid, compid, comptext) {
	jQuery('#sd_content_' + uid + ' tbody.sd-competition-data')
		.not('#sd_tbody_' + uid + '_' + compid)
		.hide();
	jQuery('#sd_tbody_' + uid + '_' + compid)
		.show();
	jQuery('#sd_content_' + uid + ' thead.sd-table-title th')
		.html(comptext);
}

function sdRefreshDiv(params) {
	if (params.isStale) {
		console.info('SportsData ' + params.function + ': Updating using API');
		jQuery.ajax({
			type: "post",
			dataType: "html",
			data: {
				...params.data,
				uid: params.uid,
				hash: params.hash,
				team: params.teamkey,
				force: params.force
			},
			url: params.url + '/team/' + params.teamkey + '/' + params.function,
			success: function (data) {
				if (data !== undefined) {
					jQuery('#sd_content_' + params.uid)
						.fadeOut(500, function () {
							jQuery(this).html(data).fadeIn(500);
						});
					console.info('SportsData ' + params.function + ': Updated from API');
				}
			},
			error: function (err, msg, thrown) {
				console.error('SportsData ' + params.function + ': API returned error:', err, msg, thrown);
			},
			complete: function (resp) {
				if (resp.status === 304) {
					console.info('SportsData ' + params.function + ': API returned 304 - Content Not Modified');
				}
			}
		});
	} else {
		console.info('SportsData ' + params.function + ': No API update required');
	}
}

