<?php

/**
 * This function is called when the block is being rendered on the front end of the site
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 */

require_once plugin_dir_path(__FILE__) . '../common/index.php';
require_once 'utils.php';

if (!function_exists('sd_now_and_next_render_callback')) :
	function sd_now_and_next_render_callback($attributes, $content, $block_instance)
	{
		ob_start();
		$uid = uniqid();
		$teams = [];
		if (isset($attributes['teamkey'])) {
			$teamkeys = explode(',', $attributes['teamkey']);
			foreach ($teamkeys as $teamkey) {
				$teams[] = SDTeam::createFromCache($teamkey);
			}
		}
?>
		<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>>
			<?php $uid = sd_register_block($teams, 'now_and_next', $attributes); ?>
			<div id="sd_content_<? echo $uid; ?>">
				<?php echo sd_now_and_next_render_content_inner($teams, $attributes); ?>
			</div>

		</div>
	<?php
		return ob_get_clean();
	}
endif;

if (!function_exists('sd_now_and_next_render_content_inner')) :
	function sd_now_and_next_render_content_inner($teams, $attributes): string
	{
		ob_start();
	?>
		<table class="sd-now-next-table">
			<thead class="sd-table-title">
				<th><?php if (isset($attributes["title"])) {
						echo esc_html($attributes["title"]);
					} ?></th>
			</thead>
			<tbody>
				<?php
				$fixtures = sd_fixtures_now_and_next($teams, $attributes);
				if (is_array($fixtures)) {
					foreach ($fixtures as $fixture) {
				?>
						<tr class="sd-event-row">
							<td>
								<div class="sd-team-logo sd-logo-home">
										<img src="<?php echo sd_team_logo_url($fixture->homeLogoUrl) ?>">
								</div>
								<div class="sd-team-logo sd-logo-away">
										<img src="<?php echo sd_team_logo_url($fixture->awayLogoUrl) ?>">
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
