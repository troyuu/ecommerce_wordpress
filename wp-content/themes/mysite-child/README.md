# mysite-child

Astra child theme for a WordPress e-commerce portfolio site (furniture & household). Hand-coded, plugin-light, vanilla CSS/JS, no build step.

## Quick start

1. Place this folder at `wp-content/themes/mysite-child/` in your WordPress install.
2. Ensure the **Astra** parent theme is installed.
3. Activate via WP Admin → Appearance → Themes.

## Conventions

- All PHP modules live in `inc/`. `functions.php` is a 2-line bootstrap.
- WooCommerce overrides live in `woocommerce/` and mirror Woo's `templates/` structure.
- Reusable partials live in `template-parts/<surface>/`.
- CSS is layered (`@layer base, components, pages`); page-specific stylesheets are enqueued conditionally.
- JS is a vanilla ES module; entry is `assets/js/main.js`.
- Hook/filter prefix: `mysite_`.

See `CLAUDE.md` for the AI-collaboration brief and `docs/PLAN.md` for the approved build plan.
