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
					sdRegisterBlock({
						uid: '<?php echo $uid ?>',
						url: '<?php echo get_site_url(null, '/wp-json/sportsdata/v1') ?>',
						teamkey: '<?php echo $attributes['teamkey'] ?>',
						function: 'now_and_next',
						data: {
							title: '<?php echo $attributes['title'] ?>',
							maxfixtures: <?php echo $attributes['maxrows'] ?>,
							maxfuture: <?php echo $attributes['maxfuture'] ?>
						},
						isStale: <?php echo (($team === null || $team->isStale === true) && get_query_var('force_refresh') !== 'true') ? "true" : "false" ?>
					});
				})(jQuery);
			</script>
			<div id="sd_content_<? echo $uid; ?>">
				<?php echo sd_now_and_next_render_content_inner($team, $attributes['title'], $attributes['maxrows'], $attributes['maxfuture']); ?>
			</div>

		</div>
	<?php
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_now_and_next_render_content_inner')) :
	function sd_now_and_next_render_content_inner($team, $title, $maxfixtures = null, $maxfuture = null): string
	{
		ob_start();
	?>
		<table class="sd-now-next-table">
			<thead class="sd-table-title">
				<th><?php if (isset($title)) {
						echo esc_html($title);
					} ?></th>
			</thead>
			<tbody>
				<?php
				if (isset($team)) {
					$fixtures = $team->fixtures_now_and_next($maxfixtures, $maxfuture);
					foreach ($fixtures as $fixture) {
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
					}
				} else {
					?>
					<tr class="sd-event-row">
						<td>
							<h4 class="sd-event-title"><?php echo __('No fixtures', 'sd') ?></h4>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
<?php
		return ob_get_clean();
	}
endif;
