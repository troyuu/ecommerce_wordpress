<?php
/**
 * Theme supports, menu locations, image sizes.
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    register_nav_menus([
        'primary' => __('Primary Menu', 'mysite-child'),
    ]);

    // Product imagery — square crop for grid cards, uncropped for hero.
    add_image_size('mysite-product-card', 600, 600, true);
    add_image_size('mysite-product-hero', 1200, 1200, false);
});
