=== GetTerms Cookie Consent & Policies ===
Contributors: getterms
Tags: privacy, terms of service, cookie consent, GDPR, compliance
Requires at least: 4.7
Tested up to: 7.0
Stable tag: 1.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The GetTerms plugin allows users to manage their GetTerms compliance packs, embed auto-updating policies, and display their Cookie Consent Widget.

== Description ==

The GetTerms plugin helps you implement cookie consent management and embed legal policies directly into your WordPress site. It connects with your GetTerms account to provide compliant, auto-updating documents and a fully configurable cookie consent banner.

== Features ==

- Connect to your GetTerms Compliance Pack using a unique token.
- Embed the Cookie Consent Widget site-wide or via manual integration.
- Shortcodes to display live-updated legal documents (Privacy Policy, Terms, AUP, etc.).
- Support for multiple languages for both widget and policies.

== Installation ==

1. Go to your WordPress admin dashboard.
2. Navigate to `Plugins > Add New`.
3. Search for `GetTerms Cookie Consent & Policies`.
4. Click `Install Now` and then `Activate`.

== Usage ==

=== Setting Up ===
1. Navigate to `GetTerms` in the WordPress admin menu.
2. Enter your GetTerms token and save your settings.

=== Embedding the Cookie Consent Widget ===
- Enable the auto-embed option in plugin settings to insert the consent widget in the `&lt;head&gt;` tag.
- You can also embed manually with the provided script tag for advanced use cases or multilingual configurations.
- To ensure proper consent enforcement, embed the script as early as possible in the head section.
- If you have manually included other scripts in your `&lt;head&gt;` the recommendation is to manually embed the GetTerms script above all other scripts, and not use auto-embed

=== Using Shortcodes ===
Use the following shortcodes to display your policy documents:

- `[getterms_privacy_en]`, `[getterms_terms_en]`, `[getterms_aup_en]`
- `[getterms_privacy_fr]`, `[getterms_terms_fr]`, `[getterms_aup_fr]`
- Additional languages and policies are listed in the plugin settings.

=== How wp_head and wp_enqueue_script work ===

WordPress loads styles and scripts in two steps:

1) Registration + enqueue (what you do in PHP)
- You register a script/style and enqueue it using WordPress functions.
- Common hooks:
  - `wp_enqueue_scripts` (front end): enqueue scripts/styles for the theme and plugins.
  - `admin_enqueue_scripts` (wp-admin): enqueue for specific admin screens.
  - `login_enqueue_scripts` (login screen): enqueue for wp-login.php.

2) Output (what WordPress prints into the page)
- WordPress prints the enqueued styles and scripts into the HTML at the proper places, using theme hooks:
  - `wp_head`: prints styles and any head scripts (e.g., those with `$in_footer = false`).
  - `wp_footer`: prints scripts that were enqueued for the footer (those with `$in_footer = true`).

In practice, you should enqueue on the correct action, and let WordPress output them on `wp_head`/`wp_footer`. Example:

- Register and enqueue a script

  function myplugin_enqueue_assets() {
      wp_register_script(
          'myplugin-frontend',
          plugins_url('dist/myplugin.js', __FILE__),
          array('jquery'),
          '1.0.0',
          true // load in footer
      );
      wp_enqueue_script('myplugin-frontend');
  }
  add_action('wp_enqueue_scripts', 'myplugin_enqueue_assets');

- What gets printed where
  - Because `$in_footer = true`, the script tag is printed near the end of the page when the theme calls `wp_footer()`.
  - If `$in_footer = false` (default), it will be printed in the `<head>` when the theme calls `wp_head()`.

How this plugin uses these hooks
- For the Cookie Consent Widget, you can choose between:
  - Auto-embed: The plugin hooks into `wp_head` very early to print the consent script as high as possible in the `<head>` for best consent enforcement. This is intentional because consent tools must run before other scripts.
  - Manual embed: You paste the provided `<script>` tag directly into your theme (ideally above other scripts in the head) if you need strict ordering or have custom setups.
- For admin pages and settings, the plugin uses `admin_enqueue_scripts` to load its admin bundle only on our settings page to keep the rest of wp-admin lightweight.

Troubleshooting and best practices
- Always load third-party tracking scripts after consent scripts have run. If you must hard-code tags, put the GetTerms script above them in the head.
- Never echo `<script>` tags directly in templates unless you must. Prefer `wp_enqueue_script` to let WP handle dependencies, versions, and placement.
- Make sure your theme calls `wp_head()` before `</head>` and `wp_footer()` before `</body>`. Most themes already do; if not, scripts will not appear.

== Data Collection and Privacy ==

The plugin communicates with the GetTerms content network (`https://app.getterms.io`, `https://gettermscmp.com`, `https://gettermscdn.com`) to deliver policy content and manage cookie consent. The following data is transmitted:

- **Authentication Token**: Used to authenticate and fetch Compliance Pack data.
- **Domain Name**: Validates the plugin’s authorization and retrieves domain-specific settings.
- **User Consent Logs**: Records of accepted/rejected cookie categories (anonymized).
- **Policy Requests**: Dynamic retrieval of policy content for embedding.
- **Error Logs**: Technical error messages (non-personal) may be sent to assist with debugging.

All data is anonymized where applicable and stored in compliance with GDPR data sovereignty standards.

For more information, refer to our:
- [Privacy Policy](https://getterms.io/our-privacy-policy)
- [Terms of Service](https://getterms.io/our-terms-of-service)

== External Services ==

This plugin relies on external services operated by GetTerms to function. It reaches out to these services to display the Cookie Consent Widget, record consent choices, and embed automatically updating legal documents. Below we explain what each service is used for, what data is sent, and when.

Services used (all operated by GetTerms Pty Ltd):
- https://app.getterms.io — Application/API used for account authentication, compliance pack configuration, and policy retrieval.
- https://gettermscmp.com — Cookie Consent Widget service that renders the banner and manages user consent interactions.
- https://gettermscdn.com — Content Delivery Network for static assets and the document/embed runtime used to display policies.

What data is sent and when
- When you save settings in the WordPress admin (Settings → GetTerms):
  - Authentication token (provided by you) is sent to app.getterms.io to verify your account and fetch your Compliance Pack configuration.
  - Your site’s domain is sent to app.getterms.io to validate authorization and deliver domain‑specific settings.
- When a page loads with the Cookie Consent Widget enabled (auto‑embed or manual embed):
  - The page will request widget resources from gettermscmp.com and gettermscdn.com.
  - Anonymous consent state and user choices (e.g., accepted/rejected categories) are transmitted to gettermscmp.com when visitors interact with the banner, for the purpose of storing and enforcing consent. No personally identifiable information is required for this.
- When a page renders a policy via a shortcode (e.g., Privacy Policy, Terms):
  - The embed runtime from gettermscdn.com requests the latest policy content from app.getterms.io and/or the CDN, along with the configured language.
- When technical errors occur:
  - Non‑personal error details may be sent to the above services to help diagnose and resolve issues.

Terms of Service and Privacy Policy
- All of the above domains are operated by GetTerms and governed by the same legal documents:
  - Terms of Service: https://getterms.io/our-terms-of-service
  - Privacy Policy: https://getterms.io/our-privacy-policy

We disclose these external connections for transparency and to help you assess compliance obligations for your site.

== Development ==

=== Source for Compiled Assets (Required by WordPress.org) ===

The full human‑readable, non‑minified source code for the compiled JavaScript/CSS is publicly available in our GitHub repository. This satisfies the WordPress.org guideline requiring publicly accessible sources for generated assets.

- Public source repository (first‑party):
  - https://github.com/GetTerms/getterms-cookie-consent-and-policies
  - Key sources: `src/getterms.js`, `vite.config.mjs`, `package.json`

- Compiled files shipped with the plugin (in this ZIP):
  - JavaScript bundle: `dist/getterms.bundle.js`

- Third‑party libraries used in the bundle (public sources):
  - Tippy.js (tooltip library): https://github.com/atomiks/tippyjs
  - Popper Core (positioning engine): https://github.com/popperjs/popper-core

Note: The distributed plugin package does not include the `src/` directory. For review or contributions, please use the public repository linked above.

=== Build Tools ===

This plugin uses modern build tooling for asset management:

- **Vite** compiles source JavaScript files from `src/` to `dist/`.
- **NPM** manages dependencies and scripts.
- Output is compiled as an IIFE for safe WordPress execution.

Build config: `vite.config.mjs`

Build steps to reproduce the distributed assets:

1. Install Node.js (use `.nvmrc` for the recommended version).
2. Clone the public repository and from the plugin root directory, run:
   - `npm install`
   - `npm run build`
3. The compiled output will be written to `dist/`.

No private repositories are required to build; all source files needed are available in the public repository.

== Changelog ==

= 1.5 =
* Fixed the settings page clearing a working configuration. The settings page cleared the stored token, widget slug, languages and policies before it had retrieved and validated their replacements, so any problem with the retrieval left the site with no configuration at all and no error shown. The plugin now retrieves and validates the new configuration first and writes over the existing options only on success.
* Fixed the plugin failing to read the API response when it arrives double-encoded, which made every retrieved value empty.
* Fixed errors returned by the plugin's own AJAX handlers being treated as successes, which hid failures behind a silent page reload. Failures are now shown on the settings page.
* Fixed the settings form saving the token under an option name the plugin never read, so the standard form post could not populate the token.
* Fixed error reporting never running from the settings page's main code paths because of a scoping error.
* WP Consent API is no longer a required dependency. It was only ever needed for the optional Google Consent Mode integration, so WordPress no longer blocks activation without it; the settings page now shows an optional recommendation with an install shortcut instead, and the site-wide admin notice is gone.
* Fixed the "Embed your policies" table flashing every language column before collapsing to the selected language; the non-selected columns are now hidden at render time.
* Fixed the language selector failing to pre-select English and Hindi variants (the stored default language uses hyphens while language keys use underscores, so those variants never matched).

= 1.4 =
* Fixed a malformed link on the settings page: the "Update your Cookie Consent Widget" link had its widget slug escaped with esc_url() mid-URL, which injected an extra "http://" and broke the link. The full URL is now escaped correctly.

= 1.3 =
* Fixed the plugin's admin CSS and JavaScript loading on every WordPress admin page; they now load only on the GetTerms settings page to avoid affecting other plugins' admin screens.

= 1.2 =
* Updated the Cookie Consent Widget embed to the new method: a preloaded, synchronous blocker plus an asynchronous widget, for more reliable consent enforcement.
* Manual installation now provides the matching code snippet, with guidance on adding a Content Security Policy (CSP) nonce.
* Fixed the manual installation snippet so the copied code contains valid &lt;script&gt; tags.
* Tested up to WordPress 7.0.

= 1.1 =
* Fix for bug when automatically embedding in default language

= 1.0 =
* Functionality updates to comply with WordPress plugin directory requirements

= 0.8 =
* Integration with WP Consent API.
* Require WP Consent API v1.0 or later.

= 0.7 =
* Added `readme.txt`.
* Moved changelog into `readme.txt`.

= 0.6 =
* Improved authentication method.
* Enhanced error logging.

= 0.5 =
* Initial beta release.

== Support ==

For help, email [support@getterms.io](mailto:support@getterms.io)

== License ==

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
