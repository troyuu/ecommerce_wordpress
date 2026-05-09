<?php
/**
 * WooCommerce theme integration.
 *
 * Declares HPOS (High-Performance Order Storage) compatibility so the
 * theme doesn't trip the Woo "incompatibility" admin notice, and registers
 * Woo's gallery theme supports (zoom, lightbox, slider) for the
 * single-product page UX.
 */

defined('ABSPATH') || exit;

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
});

add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});
