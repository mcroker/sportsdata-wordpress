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
	<?php
	if (isset($attributes['teamkey'])) {
		$team = sd_get_team($attributes['teamkey']);
	} else {
		$team = null;
	}
	?>
	<script type="text/javascript">
		var sdparams;
		(function($) {
			"use strict";
			sdparams = {
				uid: "<?php echo $uid ?>",
				teamkey: "<?php echo $attributes['teamkey'] ?>",
				maxrows: "<?php echo $attributes['maxrows'] ?>",
				maxfuture: "<?php echo $attributes['maxfuture'] ?>",
				fixtures: <?php echo json_encode($team->fixtures_now_and_next($attributes['maxrows'], $attributes['maxfuture'])) ?>
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
		<tbody id="<?php echo $uid ?>">
			<tr class="sd-event-row">
				<td>
					<h4 class="sd-event-title"><?php echo __('No fixtures', 'sd') ?></h4>
				</td>
			</tr>
		</tbody>
	</table>
</div>