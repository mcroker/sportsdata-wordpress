<?php

/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 *
 * @package gutenberg-examples
 */

require_once plugin_dir_path(__FILE__) . '../common/index.php';
$uid = uniqid();

?>
<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>>
	<script type="text/javascript">
		var sdparams;
		(function($) {
			"use strict";
			sdparams = {
				uid: "<?php echo $uid ?>", 
				teamkey: "<?php echo $attributes['teamkey'] ?>" 
			};
		})(jQuery);
	</script>
	<?php
	if (isset($attributes['teamkey'])) {
		$team = sd_get_team($attributes['teamkey']);
	} else {
		$team = null;
	}
	?>
	<table class="sd-data-table">
		<thead class="sd-table-header">
			<th><?php
				if (isset($attributes['title'])) {
					echo $attributes['title'];
				} ?></th>
		</thead>
		<tbody id="<?php echo $uid ?>">
			<?php
			if (isset($team)) {
				$fixtures = $team->fixtures_now_and_next($attributes['maxrows'], $attributes['maxfuture']);
				foreach ($fixtures as $fixture) { ?>
					<tr class="sd-event-row">
						<td>
							<div class="sd-team-logo sd-logo-home">
								<? if (isset($fixture->homeLogoUrl)) { ?>
									<img src="<?php echo esc_attr($fixture->homeLogoUrl) ?>">
								<?php } ?>
							</div>
							<div class="sd-team-logo sd-logo-away">
								<? if (isset($fixture->awayLogoUrl)) { ?>
									<img src="<?php echo esc_attr($fixture->awayLogoUrl) ?>">
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
				<?php } ?>
			<?php } else { ?>
				<tr class="sd-event-row">
					<td>
						<h4 class="sd-event-title"><?php echo __('No fixtures', 'sd') ?></h4>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>