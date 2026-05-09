<?php
/**
 * mysite-child theme bootstrap.
 *
 * Each subsystem lives in its own file under inc/. Add new modules
 * by creating inc/<name>.php and including it from this file.
 */

defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/setup.php';
require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/woocommerce.php';
require_once get_stylesheet_directory() . '/inc/customizations.php';
require_once get_stylesheet_directory() . '/inc/auth-gate.php';
require_once get_stylesheet_directory() . '/inc/navigation.php';
