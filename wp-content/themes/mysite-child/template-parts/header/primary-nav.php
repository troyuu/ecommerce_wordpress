<?php
/**
 * Primary navigation: top-level product categories + "View All" link.
 *
 * Rendered into wp_nav_menu's primary location by inc/navigation.php's
 * pre_wp_nav_menu filter. No queries here — data arrives via $args.
 *
 * Expects:
 *   $args['categories'] — array of WP_Term, top-level product_cat terms.
 *   $args['shop_url']   — string, link for the "View All" entry.
 */

defined('ABSPATH') || exit;

$categories = isset($args['categories']) && is_array($args['categories']) ? $args['categories'] : [];
$shop_url   = isset($args['shop_url']) && is_string($args['shop_url']) ? $args['shop_url'] : home_url('/');
?>
<nav class="primary-nav" aria-label="<?php esc_attr_e('Primary', 'mysite-child'); ?>">
    <ul class="primary-nav__list">
        <?php foreach ($categories as $term) :
            if (!$term instanceof WP_Term) {
                continue;
            }
            $url        = get_term_link($term);
            $href       = is_wp_error($url) ? '#' : $url;
            $is_current = is_tax('product_cat', $term->term_id);
        ?>
            <li class="primary-nav__item">
                <a class="primary-nav__link" href="<?php echo esc_url($href); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                    <?php echo esc_html($term->name); ?>
                </a>
            </li>
        <?php endforeach; ?>
        <li class="primary-nav__item primary-nav__item--all">
            <a class="primary-nav__link" href="<?php echo esc_url($shop_url); ?>"<?php echo (function_exists('is_shop') && is_shop()) ? ' aria-current="page"' : ''; ?>>
                <?php esc_html_e('View All', 'mysite-child'); ?>
            </a>
        </li>
    </ul>
</nav>
