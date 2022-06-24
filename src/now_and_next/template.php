<?php

/**
 * This function is called when the block is being rendered on the front end of the site
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 */

require_once plugin_dir_path(__FILE__) . '../common/index.php';

if (!function_exists('sd_now_and_next_render_callback')) :
	function sd_now_and_next_render_callback($attributes, $content, $block_instance)
	{
		ob_start();
		$uid = uniqid();
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
				var nowAndNextParams;
				(function($) {
					"use strict";
					nowAndNextParams = {
						uid: "<?php echo $uid ?>",
						url: "<?php echo get_site_url(null, '/wp-json/sportsdata/v1') ?>",
						teamkey: "<?php echo $attributes['teamkey'] ?>",
						maxfixtures: "<?php echo $attributes['maxrows'] ?>",
						maxfuture: "<?php echo $attributes['maxfuture'] ?>",
						isStale: <?php echo (($team === null || $team->isStale === true) && get_query_var('force_refresh') !== 'true') ? "true" : "false" ?>
					};
				})(jQuery);
			</script>
			<table class="sd-data-table">
				<thead class="sd-table-header">
					<th><?php
						if (isset($attributes['title'])) {
							echo $attributes['title'];
						} ?></th>
				</thead>
				<tbody id="<?php echo "tbody_$uid" ?>">
					<?php echo sd_now_and_next_render_tbody_inner($team, $attributes['maxrows'], $attributes['maxfuture']) ?>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_now_and_next_render_tbody_inner')) :
	function sd_now_and_next_render_tbody_inner($team, $maxfixtures = null, $maxfuture = null): string
	{
		ob_start();
		if (isset($team)) {
			$fixtures = $team->fixtures_now_and_next($maxfixtures, $maxfuture);
			foreach ($fixtures as $fixture) {
				echo sd_now_and_next_render_fixture($fixture);
			}
		} else {
		?>
			<tr class="sd-event-row">
				<td>
					<h4 class="sd-event-title"><?php echo __('No fixtures', 'sd') ?></h4>
				</td>
			</tr>
		<?php
		}
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_now_and_next_render_fixture')) :
	function sd_now_and_next_render_fixture($fixture): string
	{
		ob_start();
		?>
		<tr class="sd-event-row">
			<td>
				<div class="sd-team-logo sd-logo-home">
					<? if (isset($fixture->homeLogoUrl)) { ?>
						<img src="<?php
									echo esc_attr(
										sd_refresh_cached_team_logo(
											$fixture->homeTeam,
											$fixture->homeLogoUrl
										)
									) ?>">
					<?php } ?>
				</div>
				<div class="sd-team-logo sd-logo-away">
					<? if (isset($fixture->awayLogoUrl)) { ?>
						<img src="<?php echo esc_attr(
										sd_refresh_cached_team_logo(
											$fixture->awayTeam,
											$fixture->awayLogoUrl
										)
									) ?>">
					<?php } ?>
				</div>
				<?php
				?>
				<time class="sd-event-date"><?php echo $fixture->dateTime->format('F d, Y') ?></time>
				<time class="sd-event-time"><?php echo $fixture->dateTime->format('H:i') ?></time>
				<?php if (!empty($fixture->homeScore) || !empty($fixture->awayScore)) { ?>
					<h5 class="sd-event-results"><?php echo esc_html($fixture->homeScore) . __(' - ', 'sd') . esc_html($fixture->awayScore) ?></h5>
				<?php } ?>
				<h4 class="sd-event-title"><?php echo esc_html($fixture->homeTeam) . __(' Vs. ', 'sd') . esc_html($fixture->awayTeam) ?></h4>
			</td>
		</tr>
<?php
		return ob_get_clean();
	}
endif;
?>