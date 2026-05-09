<?php
/**
 * Login prompt: surfaced by JS when a logged-out user attempts a
 * gated action (Add-to-Cart, Checkout). Hidden by default via the
 * HTML5 `hidden` attribute; the JS module toggles it.
 *
 * Server-side validation in inc/auth-gate.php is the source of truth;
 * this partial is a UX polish layer.
 *
 * Expects:
 *   $args['login_url'] — string, base login URL (My Account page or wp-login).
 */

defined('ABSPATH') || exit;

$login_url = isset($args['login_url']) && is_string($args['login_url'])
    ? $args['login_url']
    : wp_login_url();
?>
<div
    class="login-prompt"
    data-mysite-login-prompt
    role="dialog"
    aria-modal="true"
    aria-labelledby="login-prompt-title"
    hidden
>
    <div class="login-prompt__panel">
        <h2 id="login-prompt-title" class="login-prompt__title">
            <?php esc_html_e('Log in to continue', 'mysite-child'); ?>
        </h2>
        <p class="login-prompt__body">
            <?php esc_html_e('You need an account to add items to your cart and check out.', 'mysite-child'); ?>
        </p>
        <div class="login-prompt__actions">
            <a class="login-prompt__cta" href="<?php echo esc_url($login_url); ?>">
                <?php esc_html_e('Log in or register', 'mysite-child'); ?>
            </a>
            <button class="login-prompt__close" type="button" data-mysite-login-prompt-close>
                <?php esc_html_e('Keep browsing', 'mysite-child'); ?>
            </button>
        </div>
    </div>
</div>
