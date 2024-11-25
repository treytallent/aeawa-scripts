<?php

/**
 * Plugin Name:       AEAWA Scripts
 * Description:       Handles custom scripts for AEAWA.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Author:            MESH
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       aeawa-scripts
 *
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {

	global $wp_version;
	if ($wp_version !== '4.7.1') {
		return $data;
	}

	$filetype = wp_check_filetype($filename, $mimes);

	return [
		'ext'             => $filetype['ext'],
		'type'            => $filetype['type'],
		'proper_filename' => $data['proper_filename']
	];
}, 10, 4);

function cc_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function fix_svg()
{
	echo '<style type="text/css">
		  .attachment-266x266, .thumbnail img {
			   width: 100% !important;
			   height: auto !important;
		  }
		  </style>';
}
add_action('admin_head', 'fix_svg');

function aeawa_script_assets()
{
	$style_asset = include plugin_dir_path(__FILE__) . 'build/css/layouts.asset.php';

	wp_enqueue_style(
		'aeawa-script2',
		plugin_dir_url(__FILE__) . 'build/css/layouts.css',
		$style_asset['dependencies'],
		$style_asset['version']
	);
}
add_action('wp_enqueue_scripts', 'aeawa_script_assets');

function aeawa_script_editor_assets()
{
	$script_asset = include plugin_dir_path(__FILE__) . 'build/js/variations.asset.php';
	$style_asset = include plugin_dir_path(__FILE__) . 'build/css/editorstyles.asset.php';

	wp_enqueue_script(
		'aeawa-editor',
		plugin_dir_url(__FILE__) . 'build/js/variations.js',
		$script_asset['dependencies'],
		$script_asset['version'],
		true
	);

	wp_enqueue_style(
		'aeawa-editor',
		plugin_dir_url(__FILE__) . 'build/css/editorstyles.css',
		$style_asset['dependencies'],
		$style_asset['version']
	);
}
add_action('enqueue_block_editor_assets', 'aeawa_script_editor_assets');

function render_element_intersection($block_content, $block)
{
	if (isset($block['attrs']['className']) && $block['attrs']['className'] === 'intersect-element') {

		wp_enqueue_script_module(
			'@my-plugin/entry',
			plugin_dir_url(__FILE__) . '/src/js/gutenberg-store.js',
		);

		$p = new WP_HTML_Tag_Processor($block_content);
		$p->next_tag();
		$p->set_attribute('data-wp-interactive', 'gutenberg-store');
		$p->set_attribute('data-wp-run', 'callbacks.intersect');
		return $p->get_updated_html();
	}
	if (isset($block['attrs']['className']) && $block['attrs']['className'] === 'intersect-footer') {
		wp_enqueue_script_module(
			'@my-plugin/entry',
			plugin_dir_url(__FILE__) . '/src/js/gutenberg-store.js',
		);

		$p = new WP_HTML_Tag_Processor($block_content);
		$p->next_tag();
		$p->set_attribute('data-wp-interactive', 'gutenberg-store');
		$p->set_attribute('data-wp-run', 'callbacks.intersectFooter');
		return $p->get_updated_html();
	}
	return $block_content;
}
add_filter('render_block', 'render_element_intersection', 10, 2);
