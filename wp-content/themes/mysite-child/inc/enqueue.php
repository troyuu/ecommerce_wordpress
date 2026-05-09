<?php
/**
 * Front-end stylesheet and script enqueueing.
 *
 * Priority 20 places us after Astra's default priority-10 styles, so
 * our overrides land last. Cache-busting via filemtime() so edits
 * propagate without manual versioning.
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    wp_enqueue_style(
        'mysite-main',
        $theme_uri . '/assets/css/main.css',
        [],
        filemtime($theme_dir . '/assets/css/main.css')
    );

    wp_enqueue_script(
        'mysite-main',
        $theme_uri . '/assets/js/main.js',
        [],
        filemtime($theme_dir . '/assets/js/main.js'),
        true
    );
}, 20);

add_filter('script_loader_tag', function ($tag, $handle) {
    if ($handle === 'mysite-main') {
        return str_replace('<script ', '<script type="module" ', $tag);
    }
    return $tag;
}, 10, 2);

/**
 * Page-specific stylesheets — enqueued only on the matching template.
 * Depend on `mysite-main` so they cascade after the global components.
 */
add_action('wp_enqueue_scripts', function () {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    if (function_exists('is_cart') && is_cart()) {
        $rel = '/assets/css/pages/cart.css';
        wp_enqueue_style(
            'mysite-cart',
            $theme_uri . $rel,
            ['mysite-main'],
            filemtime($theme_dir . $rel)
        );
    }
}, 21);
