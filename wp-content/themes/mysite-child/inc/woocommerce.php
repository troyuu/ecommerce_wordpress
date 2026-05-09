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

/**
 * Add a "Specifications" tab on the single-product page when at least one
 * spec ACF field has content. Renders template-parts/product/specs.php.
 */
add_filter('woocommerce_product_tabs', function ($tabs) {
    global $product;
    if (!$product instanceof WC_Product || !function_exists('get_field')) {
        return $tabs;
    }

    $product_id = $product->get_id();
    $has_specs  = get_field('dimensions', $product_id)
        || get_field('material', $product_id)
        || get_field('care_instructions', $product_id)
        || (bool) get_field('assembly_required', $product_id);

    if (!$has_specs) {
        return $tabs;
    }

    $tabs['mysite_specs'] = [
        'title'    => __('Specifications', 'mysite-child'),
        'priority' => 25,
        'callback' => function () use ($product_id) {
            get_template_part('template-parts/product/specs', null, ['product_id' => $product_id]);
        },
    ];

    return $tabs;
});
