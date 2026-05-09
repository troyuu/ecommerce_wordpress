<?php
/**
 * Security and performance hardening (theme-level).
 *
 * Defensive defaults that don't require a plugin and don't change
 * behavior in surprising ways. Heavy lifting (page caching, firewall,
 * malware scanning) lives in dedicated plugins per CLAUDE.md tech stack.
 */

defined('ABSPATH') || exit;

// Disable XML-RPC entirely — well-known brute-force vector, never used here.
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Block ?author=N user enumeration on the front end. Bots probe this to
// discover usernames for credential-stuffing attacks.
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }
    if (!empty($_GET['author']) && is_numeric($_GET['author'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

// Drop the dashicons stylesheet for logged-out visitors — used only by
// the admin bar, which they don't see.
add_action('wp_enqueue_scripts', function () {
    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}, 100);

// Baseline security response headers. Conservative values that won't
// break embeds, fonts, or third-party payment iframes.
add_action('send_headers', function () {
    if (headers_sent()) {
        return;
    }
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Content-Type-Options: nosniff');
});
