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

wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.min.js', array('jquery'), '', true);

require_once plugin_dir_path(__FILE__) . 'build/api.php';
require_once plugin_dir_path(__FILE__) . 'build/settings.php';
require_once plugin_dir_path(__FILE__) . 'build/common/index.php';
require_once plugin_dir_path(__FILE__) . 'build/now_and_next/index.php';
