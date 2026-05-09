<?php
/**
 * Product card: image, title, price.
 *
 * Used by woocommerce/content-product.php (shop archive, category archives,
 * related products, search) and any other product-grid surface.
 *
 * Renders as <li> — assumes the calling context is a <ul>/<ol> grid, which
 * is how WooCommerce composes product loops.
 *
 * Expects:
 *   $args['product'] — WC_Product instance.
 */

defined('ABSPATH') || exit;

$product = $args['product'] ?? null;
if (!$product instanceof WC_Product) {
    return;
}

$id        = $product->get_id();
$permalink = get_permalink($id);
$title     = $product->get_name();
$price     = $product->get_price_html();

$image = has_post_thumbnail($id)
    ? get_the_post_thumbnail($id, 'mysite-product-card', [
        'class'    => 'product-card__image',
        'loading'  => 'lazy',
        'decoding' => 'async',
    ])
    : wc_placeholder_img('mysite-product-card', ['class' => 'product-card__image']);
?>
<li <?php wc_product_class('product-card', $product); ?>>
    <a class="product-card__image-link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
        <?php echo $image; // image HTML from trusted WP/Woo helpers ?>
    </a>
    <div class="product-card__body">
        <h3 class="product-card__title">
            <a class="product-card__title-link" href="<?php echo esc_url($permalink); ?>">
                <?php echo esc_html($title); ?>
            </a>
        </h3>
        <?php if ($price) : ?>
            <p class="product-card__price"><?php echo wp_kses_post($price); ?></p>
        <?php endif; ?>
    </div>
</li>
