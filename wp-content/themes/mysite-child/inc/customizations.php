<?php
/**
 * Front-end optimizations: remove WP features we don't use.
 *
 * Each removal is small but shaves a request, inline script, or meta tag.
 * Group anything similarly tiny and unrelated-to-business-logic here.
 */

defined('ABSPATH') || exit;

// Disable the WordPress emoji script — saves a JS request and inline detection.
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
});

// Disable oEmbed discovery — we don't embed external content.
add_action('init', function () {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
});

// Hide the WordPress version from the generator meta tag (small fingerprinting win).
add_filter('the_generator', '__return_empty_string');
