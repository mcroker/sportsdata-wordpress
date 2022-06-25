/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

export function sdSelectCompetition(uid, compid, comptext) {
	jQuery('#sd_content_' + uid + ' tbody.sd-competition-data')
		.not('#sd_tbody_' + uid + '_' + compid)
		.hide();
	jQuery('#sd_tbody_' + uid + '_' + compid)
		.show();
	jQuery('#sd_content_' + uid + ' TH.sd-table-title')
		.html(comptext);
}