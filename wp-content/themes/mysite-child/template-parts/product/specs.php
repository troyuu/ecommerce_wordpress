<?php
/**
 * Product specifications block.
 *
 * Surfaces the ACF fields registered in inc/acf.php. Renders a definition
 * list. Skips rendering entirely when every field is empty so we never
 * output an empty <dl>.
 *
 * Expects:
 *   $args['product_id'] — int, the WC_Product post ID.
 */

defined('ABSPATH') || exit;

$product_id = isset($args['product_id']) ? (int) $args['product_id'] : 0;
if ($product_id <= 0 || !function_exists('get_field')) {
    return;
}

$dimensions = get_field('dimensions', $product_id);
$material   = get_field('material', $product_id);
$care       = get_field('care_instructions', $product_id);
$assembly   = (bool) get_field('assembly_required', $product_id);

if (!$dimensions && !$material && !$care && !$assembly) {
    return;
}
?>
<dl class="product-specs">
    <?php if ($dimensions) : ?>
        <div class="product-specs__row">
            <dt class="product-specs__label"><?php esc_html_e('Dimensions', 'mysite-child'); ?></dt>
            <dd class="product-specs__value"><?php echo esc_html($dimensions); ?></dd>
        </div>
    <?php endif; ?>
    <?php if ($material) : ?>
        <div class="product-specs__row">
            <dt class="product-specs__label"><?php esc_html_e('Material', 'mysite-child'); ?></dt>
            <dd class="product-specs__value"><?php echo esc_html($material); ?></dd>
        </div>
    <?php endif; ?>
    <?php if ($care) : ?>
        <div class="product-specs__row">
            <dt class="product-specs__label"><?php esc_html_e('Care', 'mysite-child'); ?></dt>
            <dd class="product-specs__value"><?php echo nl2br(esc_html($care)); ?></dd>
        </div>
    <?php endif; ?>
    <?php if ($assembly) : ?>
        <div class="product-specs__row">
            <dt class="product-specs__label"><?php esc_html_e('Assembly', 'mysite-child'); ?></dt>
            <dd class="product-specs__value"><?php esc_html_e('Some assembly required.', 'mysite-child'); ?></dd>
        </div>
    <?php endif; ?>
</dl>
