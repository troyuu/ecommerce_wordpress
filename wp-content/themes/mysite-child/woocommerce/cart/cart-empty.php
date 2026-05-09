<?php
/**
 * Empty cart page.
 *
 * Override: friendlier copy + a small list of top-level categories so a
 * user with an empty cart has somewhere to go that isn't just "shop".
 *
 * @package mysite-child
 * @based-on woocommerce/templates/cart/cart-empty.php @ 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

$shop_id  = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
$shop_url = $shop_id > 0 ? get_permalink($shop_id) : home_url('/');

$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'parent'     => 0,
    'hide_empty' => false,
    'number'     => 6,
]);
if (is_wp_error($categories)) {
    $categories = [];
}
?>
<div class="cart-empty-state">
    <h2 class="cart-empty-state__title"><?php esc_html_e('Your cart is empty', 'mysite-child'); ?></h2>
    <p class="cart-empty-state__body"><?php esc_html_e('Browse our collections and find something for your space.', 'mysite-child'); ?></p>

    <?php if (!empty($categories)) : ?>
        <ul class="cart-empty-state__categories">
            <?php foreach ($categories as $term) :
                if (!$term instanceof WP_Term) {
                    continue;
                }
                $url  = get_term_link($term);
                $href = is_wp_error($url) ? $shop_url : $url;
            ?>
                <li>
                    <a class="cart-empty-state__category-link" href="<?php echo esc_url($href); ?>">
                        <?php echo esc_html($term->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a class="cart-empty-state__cta" href="<?php echo esc_url($shop_url); ?>">
        <?php esc_html_e('View all products', 'mysite-child'); ?>
    </a>
</div>
