<?php
/*
Plugin Name: Collections Plugin
Plugin URI: https://github.com/webbouwer
Description: Collections plugin for Wordpress

Suggestion to use with
https://wordpress.org/plugins/taxonomy-terms-order
https://wordpress.org/plugins/admin-taxonomy-filter/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path(__FILE__) . 'func/taxonomy_types.php');
require_once(plugin_dir_path(__FILE__) . 'func/taxonomy_collection.php');
require_once(plugin_dir_path(__FILE__) . 'func/posttype_artifact.php');
