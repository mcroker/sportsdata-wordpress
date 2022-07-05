<?php

/**
 * This function is called when the block is being rendered on the front end of the site
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 */

require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_fixtures_list_render_callback')) :
	function sd_fixtures_list_render_callback($attributes, $content, $block_instance)
	{
		ob_start();
		$uid = uniqid();
		if (isset($attributes['teamkey'])) {
			$team = sd_get_team($attributes['teamkey'], array(
				'cachemode' => CacheMode::fetchexpired
			));
		} else {
			$team = null;
		}
?>
		<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>>
			<script type="text/javascript">
				(function($) {
					sdRegisterBlock({
						uid: '<?php echo $uid ?>',
						url: '<?php echo get_site_url(null, '/wp-json/sportsdata/v1') ?>',
						function: 'fixtures_list',
						data: {},
						hash: <?php echo (isset($team)) ? "'$team->hash'" : 'null'; ?>,
						teamkey: '<?php echo $attributes['teamkey'] ?>',
						force: <?php echo get_query_var('force_refresh') === 'true' ? "true" : "false" ?>,
						isStale: <?php echo ($team === null || $team->isStale === true || get_query_var('force_refresh') === 'true') ? "true" : "false" ?>
					});
				})(jQuery);
			</script>
			<div id="sd_content_<? echo $uid; ?>">
				<?php echo sd_fixture_list_render_content_inner($uid, $team); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_fixture_list_render_content_inner')) :
	function sd_fixture_list_render_content_inner($uid, $team, $defaultcompetition = null): string
	{
		ob_start();
		if (isset($team)) {
			$competitions = $team->competitions;
			usort($competitions, array('SDCompetition', 'sort_by_season_desc'));
			if (!isset($defaultcompetition)) {
				$defaultcompetition = $competitions[0];
			}
		?>
			<table class="sd-competition-table">
				<thead class="sd-table-title">
					<th colspan=5><?php echo esc_html($defaultcompetition->displayname) ?></th>
				</thead>
				<thead class="sd-table-caption">
					<th>Date</th>
					<th>Opposition</th>
					<th>H/A</th>
					<th>Score For</th>
					<th>Score Against</th>
				</thead>
				<?php
				foreach ($competitions as $competition) {
				?>
					<tbody id="<?php echo 'sd_tbody_' . $uid . '_' . $competition->id ?>" class="sd-competition-data" <?php if ($competition->id !== $defaultcompetition->id) {
																															echo 'hidden';
																														} ?>>
						<?php if (sizeof($competition->fixtures) > 0) { ?>
							<?php foreach ($competition->fixtures as $fixture) { ?>
								<tr>
									<td> <?php echo $fixture->dateTime->format('F d, Y') ?> </td>
									<td> <?php echo esc_html($fixture->isHome ? $fixture->awayTeam :  $fixture->homeTeam) ?> </td>
									<td> <?php echo $fixture->isHome ? 'H' : 'A' ?> </td>
									<td class="sd-number"> <?php echo esc_html($fixture->isHome ? $fixture->homeScore :  $fixture->awayScore) ?> </td>
									<td class="sd-number"> <?php echo esc_html($fixture->isHome ? $fixture->awayScore :  $fixture->homeScore) ?> </td>
									</td>
								</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td>
									<h4><?php echo __('No fixtures', 'sd') ?></h4>
								</td>
							</tr>
					</tbody>
				<?php } ?>
			<?php } ?>
			</table>
			<div class="sd-competition-select">
				<?php if (sizeof($competitions) > 1) { ?>
					<select onchange="java_script_:sdSelectCompetition('<?php echo $uid; ?>', this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
						<?php foreach ($competitions as $competition) { ?>
							<option value='<?php echo esc_attr($competition->id) ?>' <?php selected($competition->id, $defaultcompetition->id) ?>><?php echo esc_html($competition->displayname) ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
					<span><?php echo esc_html($competitions[0]->name) ?></span>
				<?php } ?>
			</div>

<?php
		}
		return ob_get_clean();
	}
endif;

?>