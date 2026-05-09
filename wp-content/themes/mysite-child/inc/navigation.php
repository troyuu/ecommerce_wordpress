<?php
/**
 * Primary navigation rendering.
 *
 * Replaces wp_nav_menu output for the 'primary' theme location with
 * a category-driven nav: every top-level product_cat term, followed
 * by a "View All" link to the shop archive. The site's nav IS the
 * category list — there's no human-curated WP menu to maintain.
 */

defined('ABSPATH') || exit;

add_filter('pre_wp_nav_menu', function ($output, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $output;
    }
    if (!taxonomy_exists('product_cat')) {
        return $output;
    }

    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => false,
        'orderby'    => 'menu_order',
    ]);
    if (is_wp_error($categories)) {
        $categories = [];
    }

    $shop_id  = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
    $shop_url = $shop_id > 0 ? get_permalink($shop_id) : home_url('/');

    ob_start();
    get_template_part('template-parts/header/primary-nav', null, [
        'categories' => $categories,
        'shop_url'   => is_string($shop_url) ? $shop_url : home_url('/'),
    ]);
    return ob_get_clean();
}, 10, 2);
