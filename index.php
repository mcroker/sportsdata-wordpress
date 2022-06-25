<?php

/**
 * Plugin Name: SportsData 
 * Plugin URI: https://github.com/
 * Description: This is a plugin to display data from sportsdata API
 * Version: 1.1.0
 * Author: Martin Croker
 *
 * @package sportsdata
 */

defined('ABSPATH') || exit;

error_reporting(-1); // reports all errors
ini_set("display_errors", "1"); // shows all errors
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");


// Adding a new category.
add_filter('block_categories_all', function ($categories) {
	$categories[] = array(
		'slug'  => 'sportsdata',
		'title' => 'SportsData'
	);

	return $categories;
});

function sd_enqueue_scripts()
{
	wp_enqueue_script("jquery");
	wp_enqueue_script('sd_common_script', plugin_dir_url(__FILE__) . 'assets/common.js', array(), '1.0.0', false);
	wp_enqueue_style('sd_common_style', plugin_dir_url(__FILE__) . 'build/common/style-index.css');
}
add_action('wp_enqueue_scripts', 'sd_enqueue_scripts');

require_once plugin_dir_path(__FILE__) . 'build/common/index.php';
require_once plugin_dir_path(__FILE__) . 'build/now_and_next/index.php';
require_once plugin_dir_path(__FILE__) . 'build/league_table/index.php';
require_once plugin_dir_path(__FILE__) . 'build/fixtures_list/index.php';
