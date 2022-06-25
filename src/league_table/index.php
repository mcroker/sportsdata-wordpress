<?php
require_once plugin_dir_path(__FILE__) . 'api.php';
require_once plugin_dir_path(__FILE__) . 'template.php';

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function sd_league_table_block_init()
{
	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'sd_league_table_render_callback',
		)
	);
}
add_action('init', 'sd_league_table_block_init');

/*
function sd_league_table_scripts()
{
	wp_enqueue_script('sd_league_table_script', plugin_dir_url(__DIR__) . '../assets/league_table.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'sd_league_table_scripts');
*/
