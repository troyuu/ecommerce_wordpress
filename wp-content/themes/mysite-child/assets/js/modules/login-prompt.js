/**
 * Login prompt module.
 *
 * Intercepts Add-to-Cart clicks for logged-out users in the capture
 * phase — before WooCommerce's own Ajax handlers fire — and surfaces
 * the prompt rendered by template-parts/global/login-prompt.php.
 *
 * Server-side validation in inc/auth-gate.php is the source of truth;
 * this module is a UX polish layer.
 */

const PROMPT = '[data-mysite-login-prompt]';
const CLOSE = '[data-mysite-login-prompt-close]';
const TRIGGERS = '.add_to_cart_button, .single_add_to_cart_button';

export function initLoginPrompt() {
  const prompt = document.querySelector(PROMPT);
  if (!prompt) return;

  document.addEventListener(
    'click',
    (event) => {
      if (!event.target.closest(TRIGGERS)) return;
      event.preventDefault();
      event.stopPropagation();
      open(prompt);
    },
    true
  );

  document.addEventListener(
    'submit',
    (event) => {
      if (!event.target.closest('form.cart')) return;
      event.preventDefault();
      open(prompt);
    },
    true
  );

  prompt.addEventListener('click', (event) => {
    if (event.target === prompt || event.target.closest(CLOSE)) {
      close(prompt);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !prompt.hidden) close(prompt);
  });
}

function open(prompt) {
  const cta = prompt.querySelector('.login-prompt__cta');
  if (cta) {
    const url = new URL(cta.href, window.location.href);
    url.searchParams.set('redirect_to', window.location.href);
    cta.href = url.toString();
  }
  prompt.hidden = false;
  prompt.querySelector('.login-prompt__cta')?.focus();
}

function close(prompt) {
  prompt.hidden = true;
}
