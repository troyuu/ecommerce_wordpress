<?php
/**
 * Login gate for the purchase flow.
 *
 * Logged-out visitors browse and view products freely; Add-to-Cart and
 * Checkout require an account. We gate at submit, not at render — the
 * Add-to-Cart button stays visible for SEO and discovery, and the server
 * is the source of truth (the JS module is a UX polish layer).
 */

defined('ABSPATH') || exit;

/**
 * Return the URL we should send logged-out users to. Falls back to the
 * core WP login when the WC My Account page isn't configured.
 */
function mysite_login_url(string $return_to = ''): string
{
    $myaccount = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '';
    $base      = $myaccount ?: wp_login_url($return_to);

    return $return_to
        ? add_query_arg('redirect_to', urlencode($return_to), $base)
        : $base;
}

/**
 * Block Add-to-Cart for logged-out users. Returning false aborts the
 * cart-add; the notice carries a login link so the user can act.
 */
add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id) {
    if (is_user_logged_in()) {
        return $passed;
    }

    $login_url = mysite_login_url(get_permalink((int) $product_id) ?: home_url('/'));

    wc_add_notice(
        sprintf(
            /* translators: %s: anchor wrapping the words "log in". */
            __('Please %s to add items to your cart.', 'mysite-child'),
            sprintf('<a href="%s">%s</a>', esc_url($login_url), esc_html__('log in', 'mysite-child'))
        ),
        'error'
    );

    return false;
}, 10, 2);

/**
 * Bounce non-logged-in users away from the checkout page early in
 * template_redirect, before any output starts.
 */
add_action('template_redirect', function () {
    if (is_user_logged_in()) {
        return;
    }
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }

    wp_safe_redirect(mysite_login_url(wc_get_checkout_url()));
    exit;
});
