<?php
/**
 * The template for displaying product content within loops.
 *
 * Override: replace WooCommerce's action-hook card composition with our
 * reusable product card partial — keeps grid markup in one canonical place
 * across archive, related, and search surfaces.
 *
 * @package mysite-child
 * @based-on woocommerce/templates/content-product.php @ 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}

get_template_part('template-parts/product/card', null, ['product' => $product]);
