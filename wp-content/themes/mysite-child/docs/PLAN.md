# Plan — WordPress E-commerce (Furniture & Household)

## Context

Greenfield WordPress portfolio site selling home furniture and household essentials. Built locally on Local by Flywheel, deployed to Hostinger later. Astra parent theme is installed; we're building a hand-coded child theme on top of it. Public browsing is open; cart and checkout require login. The brief is portfolio-quality — clean architecture, no plugin bloat, modern CSS/JS without a build step, and durable AI-collaboration scaffolding (CLAUDE.md, `.claude/` skills) baked in from day one.

Scope confirmed with user:
- **Market**: United States, USD, English only.
- **Catalog**: Small (<100), mostly simple products, few variations.
- **Optional features included**: blog + product reviews. (Wishlist, comparison, multilingual, multi-currency excluded.)
- **Shipping & payment specifics**: deferred — portfolio scope. We'll wire sensible defaults (flat-rate-per-zone, Stripe + PayPal in test mode) with a clear path to swap later.

The order of work is: (1) approve this plan; (2) scaffold the child theme; (3) write the AI scaffolding (CLAUDE.md, `.claude/`); (4) build features in milestones; (5) deploy.

---

## 1. Tech Stack

**WordPress core**: latest stable. PHP 8.2+. **Parent**: Astra. **Child**: `mysite-child`.

**E-commerce engine**: WooCommerce. Free, dominant, well-supported, Astra-aware.

**Essential plugins (all free):**
- **WooCommerce** — core engine.
- **Advanced Custom Fields (ACF) Free** — extra product data (dimensions, materials, care). Free tier is enough; PHP-registered field groups for portability.
- **WooCommerce Stripe Gateway** + **WooCommerce PayPal Payments** — both first-party Woo plugins, free, test-mode now.
- **Rank Math (free)** — SEO. Lighter than Yoast, better Woo integration in free tier.
- **WP Super Cache** — page caching. Simple, reliable, works on Hostinger.
- **Wordfence (free)** — firewall, login attempt limiting, malware scan, optional 2FA.
- **WPS Hide Login** — moves `/wp-admin` to a custom path. Tiny, defense in depth.
- **All-in-One WP Migration** — for Local → Hostinger push. Free to 512 MB; if exceeded later, swap to Duplicator Lite.
- **FluentSMTP** — transactional email reliability. Defer install until deploy.

**Plugins explicitly avoided:** page builders (Elementor/Divi/WPBakery — bloat, CWV regressions, lock-in), Jetpack (heavy, mostly redundant), bundled "Woo extension packs", any second SEO/cache/security plugin (conflicts).

**Built with code instead of plugins:**
- Login gate at add-to-cart and checkout (~20 lines of Woo hooks).
- Category nav + "View All" (template part + small CSS).
- Disabling WP emoji script and other small bloat-removal tweaks.
- Lazy loading is native to WP — no plugin needed.

**Deferred decisions** (documented in CLAUDE.md so they're not lost):
- Real shipping model (flat-rate-per-zone for now).
- Real tax handling (manual single-state for now; TaxJar/Avalara if ever going live).
- Image optimization plugin (only if Lighthouse demands it after deploy).
- Object cache (only if Hostinger plan supports Redis/Memcached).

---

## 2. Child Theme Architecture

```
mysite-child/
├── style.css                       # Theme header + minimal globals only
├── functions.php                   # Bootstrap — requires inc/ files, nothing else
├── screenshot.png                  # Theme screenshot for WP admin
├── README.md                       # Quick-start for human developers
├── CLAUDE.md                       # AI agent context
├── docs/
│   └── PLAN.md                     # Approved plan, version-controlled
├── .claude/
│   ├── README.md
│   ├── skills/
│   │   ├── new-woo-template-override/SKILL.md
│   │   ├── new-acf-field-group/SKILL.md
│   │   └── new-template-part/SKILL.md
│   └── agents/                     # Empty, with rationale in .claude/README.md
├── inc/                            # PHP modules — single responsibility each
│   ├── enqueue.php                 # Stylesheet + script enqueueing
│   ├── setup.php                   # Theme supports, image sizes, menus
│   ├── woocommerce.php             # HPOS declaration, Woo-specific hooks
│   ├── auth-gate.php               # Login-required-to-purchase logic
│   ├── acf.php                     # ACF field group registration
│   └── customizations.php          # Misc tweaks (emoji removal, etc.)
├── template-parts/
│   ├── header/
│   │   ├── site-branding.php
│   │   └── primary-nav.php         # Category nav + "View All"
│   ├── product/
│   │   ├── card.php
│   │   ├── meta.php
│   │   └── gallery.php
│   └── global/
│       ├── login-prompt.php
│       └── breadcrumbs.php
├── woocommerce/                    # Mirrors Woo's templates/ — only what we override
│   ├── archive-product.php
│   ├── single-product.php
│   ├── cart/
│   ├── checkout/
│   └── myaccount/
├── assets/
│   ├── css/
│   │   ├── base/                   # reset, variables, typography
│   │   ├── components/             # product-card, login-prompt, primary-nav, ...
│   │   ├── pages/                  # shop, single-product, checkout, ...
│   │   └── main.css                # Layered shell (@layer base, components, pages)
│   ├── js/
│   │   ├── modules/                # login-prompt.js, nav.js, ...
│   │   └── main.js                 # ES module entry point
│   └── images/
└── languages/                      # i18n .pot
```

**Rationale.** `style.css` is a header file, not a stylesheet — real CSS lives in `assets/css/`. `functions.php` is a 10-line bootstrap; all real PHP lives in `inc/` so each file has one job. `woocommerce/` mirrors WooCommerce's `templates/` exactly because that's how Woo finds overrides — and we only put files there that we actually customize. `template-parts/` groups partials by surface so finding things is intuitive. ITCSS-lite layering in CSS keeps specificity predictable without a build step. ES modules in JS keep things modern without a bundler.

---

## 3. Custom Code Plan

**Login gate at add-to-cart and checkout** (`inc/auth-gate.php`):
- Hook `woocommerce_add_to_cart_validation` — short-circuit when `!is_user_logged_in()`, push a Woo notice ("Please log in to continue"), redirect to `wc_get_page_permalink('myaccount')` with a `redirect_to` querystring pointing back to the product (or to the cart-add intent).
- Hook `template_redirect` — guard `is_checkout()` and bounce non-logged-in users to login with `redirect_to` set to checkout.
- Add a small JS module (`assets/js/modules/login-prompt.js`) for an immediate client-side affordance: intercept Add-to-Cart clicks, show a brief modal/toast before the server redirect, so the UX feels instant. Server-side validation is the source of truth; JS is a polish layer.
- Keep the Add-to-Cart button visible to logged-out users (good for SEO and discovery) — gate at submit, not at render.
- Post-login: redirect back to the product page, optionally re-trigger the add-to-cart action if the URL carried that intent.

**Category navigation + "View All"** (`template-parts/header/primary-nav.php`):
- Use `get_terms('product_cat', ['parent' => 0, 'hide_empty' => false])` to fetch top-level categories.
- Append a "View All" link to the shop archive — `get_permalink(wc_get_page_id('shop'))`.
- No CPT, no custom taxonomy. Six categories live as `product_cat` terms managed in WP admin.

**Custom post types / taxonomies beyond Woo**: none. Blog uses native `post`/`category`. Curated landing pages can use native `page` + ACF flexible content if/when needed.

**ACF strategy**: register field groups in PHP in `inc/acf.php` (free-tier-friendly, fully portable across local→Hostinger, no JSON sync needed). Initial product fields:
- Dimensions (W × D × H — text)
- Material (text)
- Care instructions (textarea)
- Assembly required (true/false)

Add more only when content demands them.

**Hook/filter naming**: prefix all custom hooks and filters with `mysite_` to avoid collisions. Documented in CLAUDE.md.

---

## 4. Frontend Approach

**CSS — vanilla, no build step.** Modern CSS gives us nesting, custom properties, `@layer`, calc — preprocessor wins (variables, nesting) are now native. Skipping the build means no Node/npm in the theme, trivial portability to Hostinger, and zero ramp-up for anyone editing the theme later. Architecture is ITCSS-lite via `@layer`:

- `@layer base` — reset, custom properties (color tokens, spacing scale, font stack), typography.
- `@layer components` — product card, primary nav, buttons, login prompt, etc.
- `@layer pages` — page-specific overrides, conditionally enqueued.

Class naming: BEM-lite (`product-card__title`, `product-card--featured`). One stylesheet per component file.

If SCSS is ever needed, migration is mechanical.

**JS — vanilla ES modules.** A single `main.js` entry imports modules from `assets/js/modules/`. No framework. Used only for genuinely interactive needs (login-prompt modal, mobile nav). WooCommerce's own scripts (cart fragments, quantity inputs) are not replaced.

**Asset enqueuing** (`inc/enqueue.php`):
- Parent stylesheet via `wp_enqueue_style` with parent's handle as dependency.
- Child `main.css` depends on parent.
- Page-specific stylesheets enqueued only on the matching template (`is_shop()`, `is_product()`, `is_checkout()`, etc.).
- JS deferred where safe; ES modules are deferred by default.
- Cache-busting via `filemtime()` so edits propagate immediately.

**Performance baseline:**
- Native lazy loading on images.
- Explicit `add_image_size()` declarations in `inc/setup.php` for product cards (e.g., 600×600) so we never serve oversized files.
- WP-managed `srcset` is automatic.
- No render-blocking JS in `<head>`.
- System font stack, or one self-hosted font max.
- Disable WP emoji script + embed script in `inc/customizations.php`.
- Core Web Vitals targets: LCP < 2.5s, CLS < 0.1, INP < 200ms. Verified with Lighthouse post-deploy.

---

## 5. Data Model

- **Products** — Woo `product` CPT. Mostly simple products. Variable products only where genuine variation exists (e.g., a chair offered in three fabrics).
- **Categories** — `product_cat` taxonomy: Living Room, Bedroom, Kitchen, Bathroom, Outdoor, Decor. Sub-categories optional later.
- **Tags** — `product_tag` for cross-cutting attributes (style, theme).
- **Custom fields** — ACF on product (dimensions, material, care, assembly).
- **Orders** — declare HPOS (High-Performance Order Storage) support in `inc/woocommerce.php`. Modern Woo default; non-negotiable.
- **Users** — standard WP users; Woo adds the `customer` role automatically.
- **Blog** — native `post` / `category`.

---

## 6. Security & Performance Baseline

**Day-one security:**
- Strong admin password + 2FA (Wordfence free tier supports it).
- WPS Hide Login → custom login path.
- `define('DISALLOW_FILE_EDIT', true)` in `wp-config.php` — disables admin-side file editor.
- HTTPS enforced (Hostinger free SSL via Let's Encrypt post-deploy).
- Auto-updates for minor releases + plugins; manual approval for majors.
- Hide WP version (small, free).
- Limit login attempts (Wordfence).
- File permissions on Hostinger: 644 files / 755 dirs.
- Backups: weekly Hostinger-managed (preferred over a plugin).

**Day-one performance:**
- WP Super Cache page caching enabled.
- Object cache only if Hostinger plan supports Redis/Memcached.
- Explicit image sizes registered.
- Defer non-critical JS.
- Disable emoji and embed scripts.
- Permalinks: `/%postname%/`.
- Discourage-search-engines: OFF (verify on launch).

---

## 7. Deployment Strategy

**Local → Hostinger** with All-in-One WP Migration:
1. Build full site locally.
2. Install AIOWPM on both ends; export from Local; import on Hostinger.
3. If site exceeds 512 MB, switch to Duplicator Lite or Hostinger's hPanel migration assistant.
4. Post-import: regenerate permalinks, regenerate Woo product image thumbnails (Woo → Status → Tools), test all major flows, point DNS, force HTTPS.

**Pre-launch checklist:**
- SSL active and forced.
- SMTP configured and a test email sent.
- Test order placed end-to-end in Stripe test mode → switch to live.
- Rank Math connected to Google Search Console; sitemap submitted.
- Backups scheduled.
- 404 page styled.
- Privacy policy + terms pages (Woo can generate stubs).

**Staging**: Hostinger Business plan and above include staging environments — use one before pushing to live. If on a lower plan, push during low-traffic windows.

**Version control**: Git the child theme directory only. WP core, plugins, and uploads are managed by WP itself, not by Git. `.gitignore` excludes everything except theme files. Recommend committing after each milestone.

---

## 8. Build Order

Each milestone ends in a working, committable state.

1. **Foundation** — child theme scaffolded, `CLAUDE.md`, `.claude/`, `docs/PLAN.md`, initial commit. User activates the theme manually in WP Admin.
2. **Core setup** — install WooCommerce + chosen plugins, run Woo setup wizard, configure permalinks, register image sizes, declare HPOS, create the six product categories.
3. **Sample data** — 6–10 sample products (one per category) with images, descriptions, prices, ACF fields filled.
4. **Authentication gate** — `inc/auth-gate.php`, `template-parts/global/login-prompt.php` + styles. Test logged-out → Add to Cart → prompt → login → return.
5. **Category navigation + View All** — primary-nav template part + styles. Test all six categories + View All link.
6. **Product archive (shop) page** — override `archive-product.php` if needed; build product card template part + styles.
7. **Single product page** — override `single-product.php` only if needed; render ACF fields cleanly.
8. **Cart & checkout polish** — override Woo cart/checkout templates only where styling demands it. Test full happy path in Stripe test mode.
9. **My Account & order history** — override myaccount templates as needed.
10. **Blog setup** — single + archive templates inheriting Astra; minimal overrides; a couple of posts.
11. **Performance + security baseline** — install caching + security plugins, run Lighthouse, fix obvious wins.
12. **Pre-deploy hardening** — `DISALLOW_FILE_EDIT`, custom login URL, debug off.
13. **Deploy to Hostinger** — migration, domain, SSL, post-deploy checklist.

---

## 9. CLAUDE.md & `.claude/` Outline

(Built in Phase 4 after this plan is approved.)

**`mysite-child/CLAUDE.md`** sections:
- Project summary (one paragraph).
- Environment (versions, Local, target Hostinger).
- Tech stack (canonical list, links to plugin docs).
- Folder structure (mirror of section 2 above).
- Conventions: file responsibility, hook prefix `mysite_`, BEM-lite class naming, ES module pattern, ACF field group registration in PHP.
- "Never do this" list: never edit parent theme or core; never page builders; never inline styles in templates; never bypass `wc_get_template`; never commit `.env`, DB dumps, or `wp-config.php`; never disable nonces; never trust unsanitized request data.
- Current build status (which milestone we're on).
- Pointer to `docs/PLAN.md`.

**`.claude/skills/`** — three starter skills, each `name` + `description` (when to trigger) + numbered steps:
- `new-woo-template-override/SKILL.md` — copy from `wp-content/plugins/woocommerce/templates/<path>` to `mysite-child/woocommerce/<path>`, edit, document why we overrode (one-line comment at top of the override).
- `new-acf-field-group/SKILL.md` — register in `inc/acf.php` via PHP (`acf_add_local_field_group`), document the fields, surface in the appropriate template part, never JSON-export.
- `new-template-part/SKILL.md` — file in `template-parts/<surface>/`, called via `get_template_part()`, paired CSS in `assets/css/components/`, no business logic in templates (logic lives in `inc/`).

**`.claude/agents/`** — empty, with rationale in `.claude/README.md`. Claude Code's built-in Explore, Plan, code-review, and security-review flows already cover this project's recurring needs. Pre-creating subagents adds maintenance overhead without payoff at this scope. We'll add one only when a real, recurring, specialized workflow emerges that the built-ins handle poorly.

**`.claude/README.md`** — explains the folder, points to the skills, captures the agents-deferred decision.

---

## 10. Critical Files (to be created in Phase 4)

These don't exist yet — this is the scaffolding work:

- `mysite-child/style.css` — theme header
- `mysite-child/functions.php` — bootstrap
- `mysite-child/CLAUDE.md`
- `mysite-child/docs/PLAN.md` (copy of this plan)
- `mysite-child/.claude/README.md`
- `mysite-child/.claude/skills/new-woo-template-override/SKILL.md`
- `mysite-child/.claude/skills/new-acf-field-group/SKILL.md`
- `mysite-child/.claude/skills/new-template-part/SKILL.md`
- `mysite-child/inc/enqueue.php`
- `mysite-child/inc/setup.php`
- (other inc/ files created as their milestones land)
- `.gitignore` at the install root limiting Git to the child theme

---

## 11. Verification

- **Theme activation**: child theme appears in WP Admin → Appearance → Themes; activates without PHP errors.
- **Login gate**: in an incognito window, click Add to Cart on any product → login redirect happens; after login, return to product.
- **Category nav**: every top-level `product_cat` term is in the primary nav; "View All" goes to `/shop/`.
- **Product archive & single**: shop page renders product cards correctly; single product page shows ACF fields where filled.
- **Checkout (Stripe test mode)**: place a test order end-to-end; order appears in WP Admin → WooCommerce → Orders.
- **Performance**: Lighthouse mobile score ≥ 90 for performance, accessibility, best practices, SEO.
- **No PHP notices**: `WP_DEBUG_LOG` clean during normal browsing.
- **CLAUDE.md**: open a fresh Claude Code session in `mysite-child/`; ask "what is this project?" — it answers correctly without needing additional context.

---

## 12. Outstanding logistics (resolved before Phase 4 begins)

Where does the WordPress install live? `C:\Users\Troy\Desktop\ecommerce_wordpress` is empty and `C:\Users\Troy\Local Sites` doesn't exist. Either Local by Flywheel hasn't been spun up yet, or the site lives elsewhere. Confirm the absolute path to the install (the directory containing `wp-content/`) before scaffolding so the child theme lands in the right `wp-content/themes/` folder.
