/**
 * mysite-child entry point.
 *
 * Pattern: feature modules live in ./modules/. Import each module here
 * and gate its bootstrap on a relevant DOM hook (selector or event).
 * Loaded as an ES module — see inc/enqueue.php script_loader_tag filter.
 */

import { initLoginPrompt } from './modules/login-prompt.js';

initLoginPrompt();
