<?php

/**
 * This function is called when the block is being rendered on the front end of the site
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 */

require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_league_table_render_callback')) :
	function sd_league_table_render_callback($attributes, $content, $block_instance)
	{
		ob_start();
		$uid = esc_attr(uniqid());
		if (isset($attributes['teamkey'])) {
			$team = sd_get_team($attributes['teamkey'], array(
				'cachemode' => get_query_var('force_refresh') === 'true' ? CacheMode::serveronly : CacheMode::cacheonly
			));
		} else {
			$team = null;
		}
?>
		<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>>
			<script type="text/javascript">
				var leagueTableParams;
				(function($) {
					sdRegisterBlock({
						uid: "<?php echo $uid ?>",
						url: "<?php echo get_site_url(null, '/wp-json/sportsdata/v1') ?>",
						function: "league_table",
						data: {},
						hash: <?php echo (isset($team)) ? "'$team->hash'" : 'null'; ?>,
						teamkey: "<?php echo $attributes['teamkey'] ?>",
						isStale: <?php echo (($team === null || $team->isStale === true) && get_query_var('force_refresh') !== 'true') ? "true" : "false" ?>
					});
				})(jQuery);
			</script>
			<div id="sd_content_<? echo $uid; ?>">
				<?php echo sd_league_table_render_content_inner($uid, $team); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_league_table_render_content_inner')) :
	function sd_league_table_render_content_inner($uid, $team, $defaultcompetition = null): string
	{
		ob_start();
		if (isset($team)) {
			if (!isset($defaultcompetition)) {
				$defaultcompetition = $team->competitions[0];
			}
		?>
			<table class="sd-competition-table">
				<thead class="sd-table-title">
					<th colspan="14"><?php echo esc_html($defaultcompetition->displayname); ?></th>
				</thead>
				<thead class="sd-table-caption">
					<th class="sd-col-gt2">Pos</th>
					<th class="sd-col-lt2">#</th>
					<th class="sd-col-gt1">Team</th>
					<th class="sd-col-gt2">Pl</th>
					<th class="sd-col-gt3">W</th>
					<th class="sd-col-gt3">D</th>
					<th class="sd-col-gt3">L</th>
					<th class="sd-col-gt5">PF</th>
					<th class="sd-col-gt5">PA</th>
					<th class="sd-col-gt4">PD</th>
					<th class="sd-col-gt4">TBP</th>
					<th class="sd-col-gt4">LBP</th>
					<th class="sd-col-lt4 sd-col-gt3">BP</th>
					<th class="sd-col-gt2">Pts</th>
				</thead>
				<?php
				// uasort($team->competitions, array('TMCompetition', 'sort_by_sortkey_asc'));
				foreach ($team->competitions as $competition) {
				?>
					<tbody id="<?php echo 'sd_tbody_' . $uid . '_' . $competition->id; ?>" class="sd-competition-data" <?php if ($competition->id !== $defaultcompetition->id) {
																															echo 'hidden';
																														} ?>>
						<?php
						foreach ($competition->table as $tableentry) {
						?>
							<tr class="<?php echo ($tableentry->isCurrentTeam) ? 'sd-highlighted-row' : '' ?>">
								<td class="sd-col-gt1 sd-number"><?php echo esc_html($tableentry->position) ?></td>
								<td class="sd-col-gt1"><?php echo esc_html($tableentry->team) ?></td>
								<td class="sd-col-gt2 sd-number"><?php echo esc_html($tableentry->played) ?></td>
								<td class="sd-col-gt3 sd-number"><?php echo esc_html($tableentry->won) ?></td>
								<td class="sd-col-gt3 sd-number"><?php echo esc_html($tableentry->drew) ?></td>
								<td class="sd-col-gt3 sd-number"><?php echo esc_html($tableentry->lost) ?></td>
								<td class="sd-col-gt5 sd-number"><?php echo esc_html($tableentry->pointsFor) ?></td>
								<td class="sd-col-gt5 sd-number"><?php echo esc_html($tableentry->pointsAgainst) ?></td>
								<td class="sd-col-gt4 sd-number"><?php echo esc_html($tableentry->pointsFor - $tableentry->pointsAgainst) ?></td>
								<td class="sd-col-gt4 sd-number"><?php echo esc_html($tableentry->tryBonus) ?></td>
								<td class="sd-col-gt4 sd-number"><?php echo esc_html($tableentry->losingBonus) ?></td>
								<td class="sd-col-lt4 sd-col-gt3 sd-number"><?php echo esc_html($tableentry->losingBonus + $tableentry->tryBonus) ?></td>
								<td class="sd-col-gt2 sd-number"><?php echo esc_html($tableentry->leaguePoints) ?></td>
							</tr>
						<?php } ?>
					</tbody>
				<?php }  ?>
			</table>
			<div class="sd-competition-select">
				<?php if (sizeof($team->competitions) > 1) { ?>
					<select onchange="java_script_:sdSelectCompetition('<?php echo $uid; ?>', this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
						<?php
						foreach ($team->competitions as $competition) {
						?>
							<option value='<?php echo esc_attr($competition->id) ?>' <?php selected($competition->id, $defaultcompetition->id) ?>><?php echo esc_html($competition->displayname) ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
					<span><?php echo esc_html($team->competitions[0]->name) ?></span>
				<?php } ?>
			</div>
<?php
		}
		return ob_get_clean();
	}
endif;

?>