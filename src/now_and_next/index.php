<?php
require_once plugin_dir_path(__FILE__) . 'api.php';
require_once plugin_dir_path(__FILE__) . 'template.php';

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function sd_now_and_next_block_init()
{
	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'sd_now_and_next_render_callback',
		)
	);
}
add_action('init', 'sd_now_and_next_block_init');