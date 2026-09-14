<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$languages = $languages ?? [];
$policies = $policies ?? [];
$default_language = $default_language ?? 'en-us';
$widget_slug = $widget_slug ?? null;
$widget_language = get_option('getterms-widget-language');
$auto_language_detection = get_option('getterms-auto-language-detection');

echo '<h3>' . esc_html__('Manual Installation.', 'getterms-cookie-consent-policies') . '</h3>';
echo '<p>' . esc_html__('To install manually, copy the entire code snippet for your selected language into the &lt;head&gt; section of your page.', 'getterms-cookie-consent-policies') . '</p>';
echo '<h4><strong>' . esc_html__('IMPORTANT:', 'getterms-cookie-consent-policies') . '</strong> ' . esc_html__('The code must be the first &lt;script&gt; tags on the page.', 'getterms-cookie-consent-policies') . '</h4>';
echo '<p>' . esc_html__('You can optionally select the "embed" option for a specific language. The widget will be automatically added to your site  &lt;head&gt;.', 'getterms-cookie-consent-policies') . '</p>';
echo '<p class="description"><strong>' . esc_html__('Content Security Policy (CSP):', 'getterms-cookie-consent-policies') . '</strong> ' . esc_html__('If your site enforces a strict CSP, add a nonce="YOUR_NONCE" attribute to each tag below, matching the nonce your CSP header issues for the request. Because that nonce is generated per request by your server or security stack, it cannot be added automatically by this plugin — use this manual installation (or inject the snippet via your header/theme) for nonce support.', 'getterms-cookie-consent-policies') . '</p>';
echo '<table class="code-table">';
echo '<thead>';
echo '<tr>';
echo '<th class="lang-column">' . esc_html__('Language', 'getterms-cookie-consent-policies') . '</th>';
echo '<th class="code-column">' . esc_html__('Code', 'getterms-cookie-consent-policies') . '</th>';
echo '<th class="copy-column">' . esc_html__('Copy', 'getterms-cookie-consent-policies') . '</th>';
echo '<th class="embed-column">' . esc_html__('Embed', 'getterms-cookie-consent-policies') . '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($languages as $lang_key => $lang_name) {
	// Create display-only code snippet template for users to copy.
	// This is shown as escaped text (via esc_html() below) and copied verbatim
	// by the admin bundle; it is never executed by WordPress.
	$query       = ($auto_language_detection === 'true') ? '?auto=true' : '';
	$blocker_url = 'https://gettermscmp.com/cookie-consent/blocker/' . $widget_slug . '/' . $lang_key . $query;
	$widget_url  = 'https://gettermscmp.com/cookie-consent/widget/' . $widget_slug . '/' . $lang_key . $query;

	// This snippet is display-only copy text (escaped via esc_html() below), not
	// a script this plugin enqueues or outputs, so the enqueue rule does not apply.
	// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	$code = "<!-- GetTerms CMP -->\n"
		. '<link rel="preload" href="' . $blocker_url . '" as="script">' . "\n"
		. '<script type="text/javascript" src="' . $blocker_url . '"></script>' . "\n"
		. '<script async type="text/javascript" src="' . $widget_url . '"></script>' . "\n"
		. '<!-- End GetTerms CMP -->';
	// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript

	$checked = ($lang_key === $widget_language) ? 'checked' : '';

	echo '<tr>';
	echo '<td>' . esc_html($lang_name) . '</td>';
	echo '<td>
        <div class="code-container">
            <button type="button" class="show-code-btn" data-lang-key="' . esc_attr($lang_key) . '">' . esc_html__('Show Code', 'getterms-cookie-consent-policies') . '</button>
            <code id="code-inner-widget-embed-' . esc_attr($lang_key) . '" class="code-snippet" style="display:none">' . esc_html($code) . '</code>
        </div>
    </td>';
	echo '<td>
        <button type="button" class="code-block__copy btn--border btn--icon btn--border-secondary" data-copy="#code-inner-widget-embed-' . esc_attr($lang_key) . '">
            <span class="inner">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" role="img" aria-label="' . esc_attr__('Clipboard', 'getterms-cookie-consent-policies') . '">
                    <title>' . esc_html__('Clipboard', 'getterms-cookie-consent-policies') . '</title>
                    <path fill="none" stroke="#065af9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M8.2 20.2h-6c-.8 0-1.5-.7-1.5-1.5v-15c0-.8.7-1.5 1.5-1.5h3m7.6 0h3c-.8 0 1.5.7 1.5 1.5v4.5"></path>
                    <path fill="none" stroke="#065af9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M8.2 17.2H3.8v-12h10.4v3"></path>
                    <path fill="none" stroke="#065af9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12.8 11.2h9c.8 0 1.5.7 1.5 1.5v9c0 .8-.7 1.5-1.5 1.5h-9c-.8 0-1.5-.7-1.5-1.5v-9c-.1-.8.6-1.5 1.5-1.5zM14.2 14.2h6M14.2 17.2h6M14.2 20.2h2.3M12.8 5.2H5.2v-3c0-.8.7-1.5 1.5-1.5h4.5c.8 0 1.5.7 1.5 1.5v3z"></path>
                </svg>
                ' . esc_html__('Copy', 'getterms-cookie-consent-policies') . '
            </span>
        </button>
    </td>';
	echo '<td>
        <label class="switch">
            <input type="checkbox" class="language-toggle-checkbox" id="toggle-' . esc_attr($lang_key) . '" data-lang="' . esc_attr($lang_key) . '" ' . esc_attr($checked) . '>
            <span class="slider round"></span>
        </label>
    </td>';
	echo '</tr>';
}

echo '</tbody>';
echo '</table>';
