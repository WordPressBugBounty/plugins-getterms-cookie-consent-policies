<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
Plugin Name: GetTerms Cookie Consent & Policies
Description: Easy installation of your GetTerms Cookie Consent and Policies widget.
Version: 1.5
Author: General Labs.
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: getterms-cookie-consent-policies
*/

define('GETTERMS_PLUGIN_VERSION', '1.5');

add_action('admin_menu', 'getterms_menu');
function getterms_menu()
{
	$page_title = 'GetTerms Policy and Cookie Consent Management';
	$menu_title = 'GetTerms';
	$capability = 'manage_options';
	$menu_slug = 'getterms';
	$function = 'getterms_settings_page';
	$icon_url = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMzAiIHdpZHRoPSIzMCIgdmlld0JveD0iMCAwIDMwIDMwIj4KICA8cGF0aCBjbGFzcz0ibG9nb19fZXllIiBmaWxsPSIjMmIyYjJiIiBkPSJNMTUgMGM4LjMgMCAxNSA2LjcgMTUgMTVzLTYuNyAxNS0xNSAxNVMwIDIzLjMgMCAxNSA2LjcgMCAxNSAwem0wIDNDOC40IDMgMyA4LjQgMyAxNXM1LjQgMTIgMTIgMTIgMTItNS40IDEyLTEyUzIxLjYgMyAxNSAzeiIvPgogIDxwYXRoIGNsYXNzPSJsb2dvX19pcmlzIiBmaWxsPSIjMmIyYjJiIiBkPSJNMTUgOC4xaC42Yy0uNi42LS45IDEuNS0uOSAyLjQgMCAyIDEuNiAzLjYgMy42IDMuNiAxLjMgMCAyLjQtLjcgMy4xLTEuNy4zLjguNSAxLjcuNSAyLjYgMCAzLjgtMy4xIDYuOS02LjkgNi45UzguMSAxOC44IDguMSAxNXMzLjEtNi45IDYuOS02Ljl6Ii8+Cjwvc3ZnPg==';
	$position = 65;

	add_menu_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function,
		$icon_url,
		$position
	);
}

function getterms_settings_page()
{
	include 'gt-settings.php';
}

add_action('admin_init', 'getterms_settings');

// WP Consent API is an optional integration (only needed if the site wants
// Google Consent Mode signalling through it), so it is not nagged about
// site-wide; the GetTerms settings page carries the recommendation and an
// install shortcut instead.
function getterms_settings()
{
	// Must match the option name every read uses and the form field's name
	// attribute, otherwise the options.php form post writes a key nothing reads.
	register_setting('getterms-settings', 'getterms-token', [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => ''
	]);
}

function getterms_settings_link($links)
{
	$settings_link = '<a href="options-general.php?page=getterms">' . __('Settings', 'getterms-cookie-consent-policies') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'getterms_settings_link');

add_action('wp_ajax_getterms_clear_options', 'getterms_clear_options');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_clear_getterms_options', 'getterms_clear_options');
function getterms_clear_options()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}
	$options_to_clear = [
		'getterms-token',
		'getterms-widget-slug',
		'getterms-languages',
		'getterms-policies',
	];
	foreach ($options_to_clear as $option) {
		delete_option($option);
	}

	wp_send_json_success('Options cleared successfully.');
}

add_action('wp_ajax_getterms_set_options', 'getterms_set_options');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_set_getterms_options', 'getterms_set_options');
function getterms_set_options()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	if (isset($_POST['options_data']) && is_array($_POST['options_data'])) {
		$options_data = map_deep(wp_unslash($_POST['options_data']), 'sanitize_text_field');
	} else {
		$options_data = null;
	}
	if (!is_null($options_data)) {
		foreach ($options_data as $option_key => $option_value) {
			update_option($option_key, $option_value);
		}

 	wp_send_json_success('Options updated successfully.');
	} else {
		wp_send_json_error('No options data provided.');
	}
}

add_action('wp_ajax_getterms_get_options', 'getterms_get_options');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_get_getterms_options', 'getterms_get_options');
function getterms_get_options()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	$options_to_get = [
		'getterms-token',
		'getterms-widget-slug',
		'getterms-languages',
		'getterms-policies',
		'getterms-default-language',
	];

	$options = [];
	foreach ($options_to_get as $option) {
		$options[$option] = get_option($option);
	}

	wp_send_json_success($options);
}

add_action('wp_ajax_getterms_update_auto_widget', 'getterms_update_auto_widget');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_update_getterms_auto_widget', 'getterms_update_auto_widget');
function getterms_update_auto_widget()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	$auto_widget = isset($_POST['auto_widget']) ? sanitize_text_field(wp_unslash($_POST['auto_widget'])) : '0';
	if ($auto_widget) {
		update_option('getterms-manual-widget', 'false');
	}
	update_option('getterms-auto-widget', $auto_widget);
	update_option('getterms-show-widget', $auto_widget);

	wp_send_json_success();
}

add_action('wp_ajax_getterms_update_manual_widget', 'getterms_update_manual_widget');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_update_getterms_manual_widget', 'getterms_update_manual_widget');
function getterms_update_manual_widget()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	$manual_widget = isset($_POST['manual_widget']) ? sanitize_text_field(wp_unslash($_POST['manual_widget'])) : '0';

	if ($manual_widget) {
		update_option('getterms-show-widget', 'false');
		update_option('getterms-auto-widget', 'false');
	}

	update_option('getterms-manual-widget', $manual_widget);

	wp_send_json_success();
}

add_action('wp_ajax_getterms_update_auto_language_detection', 'getterms_update_auto_language_detection');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_update_getterms_auto_language_detection', 'getterms_update_auto_language_detection');
function getterms_update_auto_language_detection()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	$auto_language_detection = isset($_POST['auto_language_detection']) ? sanitize_text_field(wp_unslash($_POST['auto_language_detection'])) : '0';

	update_option('getterms-auto-language-detection', $auto_language_detection);

	wp_send_json_success();
}

// Resolve which Compliance Pack widget to embed and in which language.
// Returns null when nothing should be embedded, otherwise an array with the
// slug, language, and whether auto language detection is enabled.
function getterms_get_embed_context() {
	$widget_slug = get_option('getterms-widget-slug');
	if (empty($widget_slug)) {
		return null;
	}

	$widget_lang             = get_option('getterms-widget-language');
	$show_auto               = get_option('getterms-auto-widget');
	$show_manual             = get_option('getterms-manual-widget');
	$auto_language_detection = get_option('getterms-auto-language-detection');
	$default_language        = get_option('getterms-default-language');

	$lang = '';
	if (!empty($widget_lang) && $show_manual === 'true') {
		// Manual embed: use the explicitly selected language.
		$lang = $widget_lang;
	} elseif ($show_auto === 'true') {
		// Auto embed: use the configured default language.
		$lang = $default_language;
	}

	// The blocker/widget endpoints both require a language segment.
	if (empty($lang)) {
		return null;
	}

	return array(
		'slug' => $widget_slug,
		'lang' => $lang,
		'auto' => ($auto_language_detection === 'true'),
	);
}

// Build the GetTerms CMP embed markup for a resolved embed context: a preload
// hint plus the synchronous blocker and the async widget. The blocker is loaded
// synchronously so it can gate other scripts before the visitor consents; the
// widget UI is loaded asynchronously.
function getterms_build_embed_markup($context) {
	$base   = 'https://gettermscmp.com/cookie-consent/';
	$suffix = $context['slug'] . '/' . $context['lang'] . ($context['auto'] ? '?auto=true' : '');

	$blocker_url = esc_url($base . 'blocker/' . $suffix);
	$widget_url  = esc_url($base . 'widget/' . $suffix);

	// The consent blocker must be the first script in <head> so it can gate
	// other scripts before the visitor consents; wp_enqueue_script() cannot
	// guarantee that ordering (nor the preload hint / per-tag async), so the
	// markup is built and printed directly. URLs are escaped with esc_url().
	// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	return "<!-- GetTerms CMP -->\n"
		. '<link rel="preload" href="' . $blocker_url . '" as="script">' . "\n"
		. '<script type="text/javascript" src="' . $blocker_url . '"></script>' . "\n"
		. '<script async type="text/javascript" src="' . $widget_url . '"></script>' . "\n"
		. "<!-- End GetTerms CMP -->\n";
	// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
}

// Print the GetTerms CMP embed as early as possible in <head>. The blocker must
// run before any other scripts so it can gate them before the visitor has
// consented, so the markup is emitted directly here rather than enqueued (which
// would not guarantee ordering, a preload hint, or per-tag async).
add_action('wp_head', 'getterms_print_consent_embed', 1);
function getterms_print_consent_embed() {
	$context = getterms_get_embed_context();
	if (empty($context)) {
		return;
	}

	// The blocker must be the first script in <head> so it can gate other
	// scripts before the visitor consents; wp_enqueue_script() cannot guarantee
	// that ordering (nor the preload hint / per-tag async), so the markup is
	// printed directly. URLs are escaped with esc_url() inside
	// getterms_build_embed_markup() and the surrounding markup is static.
	echo getterms_build_embed_markup($context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.EnqueuedResources.NonEnqueuedScript
}

add_action('wp_enqueue_scripts', 'getterms_add_consent_scripts', 1);
function getterms_add_consent_scripts() {
	// The consent widget itself is printed directly in <head> by
	// getterms_print_consent_embed(). Here we only handle the document embed
	// runtime used by the policy shortcodes.

	// Check if any getterms shortcodes are present in the current post/page content
	global $post;
	if (is_object($post) && !empty($post->post_content)) {
		$languages = get_option('getterms-languages');
		$policies = get_option('getterms-policies');

		if (is_array($languages) && is_array($policies)) {
			$shortcode_found = false;
			foreach ($policies as $policy) {
				foreach ($languages as $lang_key => $lang_name) {
					$shortcode_tag = 'getterms_' . $policy . '_' . $lang_key;
					if (has_shortcode($post->post_content, $shortcode_tag)) {
						$shortcode_found = true;
						break 2;
					}
				}
			}

			if ($shortcode_found) {
				wp_enqueue_script('getterms-embed-js', 'https://gettermscdn.com/dist/js/embed.js', array(), GETTERMS_PLUGIN_VERSION, true);
			}
		}
	}
}

add_action('init', 'getterms_generate_shortcodes', 5);
function getterms_generate_shortcodes()
{
	$languages = get_option('getterms-languages');
	$policies = get_option('getterms-policies');
	$token = get_option('getterms-token');

	// Handle JSON decoding if needed
	if (is_string($languages)) {
		$languages = json_decode($languages, true);
	}
	if (is_string($policies)) {
		$policies = json_decode($policies, true);
	}

	if (is_array($languages) && !empty($languages) && is_array($policies) && !empty($policies)) {
		foreach ($policies as $originalPolicy) {
			foreach ($languages as $lang_key => $lang_name) {
				$shortcode_tag = 'getterms_' . $originalPolicy . '_' . $lang_key;

				add_shortcode($shortcode_tag, function () use ($originalPolicy, $lang_key, $lang_name, $token) {

					$transformedPolicy = $originalPolicy;
					switch ($originalPolicy) {
						case 'terms':
							$transformedPolicy = 'tos';
							break;
						case 'cookies':
							$transformedPolicy = 'cookie';
							break;
					}

					$lang_key = str_replace('_', '-', $lang_key);

					$output = '<div class="getterms-document-embed" data-getterms="' . esc_attr($token) . '" data-getterms-document="' . esc_attr($transformedPolicy) . '" data-getterms-lang="' . esc_attr($lang_key) . '" data-getterms-mode="direct" data-getterms-env="https://gettermscdn.com"></div>';
					return $output;
				});
			}
		}
	}
}

add_action('admin_enqueue_scripts', 'getterms_enqueue_styles');
function getterms_enqueue_styles($hook_suffix)
{
	// Only load on the GetTerms settings page to avoid affecting other admin pages.
	if ('toplevel_page_getterms' !== $hook_suffix) {
		return;
	}

	wp_enqueue_style(
		'getterms-style',
		plugins_url('css/getterms.css', __FILE__),
		[],
		GETTERMS_PLUGIN_VERSION
	);
}

add_action('admin_enqueue_scripts', 'getterms_admin_scripts');
function getterms_admin_scripts($hook_suffix)
{
	// Only load on the GetTerms settings page to avoid affecting other admin pages.
	if ('toplevel_page_getterms' !== $hook_suffix) {
		return;
	}

	wp_enqueue_script(
		'getterms-bundle',
		plugin_dir_url(__FILE__) . 'dist/getterms.bundle.js',
		[],
		GETTERMS_PLUGIN_VERSION,
		true
	);

	wp_localize_script(
		'getterms-bundle',
		'getTermsAjax',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('getterms_nonce_action')
		)
	);
}

add_action('wp_ajax_getterms_set_widget_lang', 'getterms_set_widget_lang');
// Backward compatibility with older admin bundle action names
add_action('wp_ajax_set_widget_lang', 'getterms_set_widget_lang');

function getterms_set_widget_lang()
{
	check_ajax_referer('getterms_nonce_action', 'nonce');

	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions.');
		return;
	}

	if (isset($_POST['lang'])) {
		update_option('getterms-widget-language', sanitize_text_field(wp_unslash($_POST['lang'])));
		wp_send_json_success('Language updated successfully.');
	} else {
		wp_send_json_error('No language provided.');
	}
}

/*
 * Custom Menu option for Consent Widget
 */
function getterms_custom_menu_item($item_id, $item, $depth, $args)
{
	?>
    <div class="field-custom description-wide">
        <label for="edit-menu-item-custom-<?php echo esc_attr($item_id); ?>">
 		<?php esc_html_e('Custom Menu Item Field', 'getterms-cookie-consent-policies'); ?><br/>
            <input type="text"
                   id="edit-menu-item-custom-<?php echo esc_attr($item_id); ?>"
                   class="widefat edit-menu-item-custom"
                   name="menu-item-custom[<?php echo esc_attr($item_id); ?>]"
                   value="<?php echo esc_attr(get_post_meta($item_id, '_menu_item_custom', true)); ?>"
            />
        </label>
    </div>
	<?php
}

add_action('wp_nav_menu_item_custom_fields', 'getterms_custom_menu_item', 10, 4);

function getterms_save_custom_menu_item($menu_id, $menu_item_db_id)
{
	if (!isset($_POST['update-nav-menu-nonce']) ||
		!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['update-nav-menu-nonce'])), 'update-nav_menu')) {
		return;
	}

	if (!current_user_can('edit_theme_options')) {
		return;
	}

	if (isset($_POST['menu-item-custom'][$menu_item_db_id])) {
		$custom_value = sanitize_text_field(wp_unslash($_POST['menu-item-custom'][$menu_item_db_id]));
		update_post_meta($menu_item_db_id, '_menu_item_custom', $custom_value);
	} else {
		delete_post_meta($menu_item_db_id, '_menu_item_custom');
	}
}

add_action('wp_update_nav_menu_item', 'getterms_save_custom_menu_item', 10, 2);

function getterms_custom_menu_item_output($items, $args)
{
	foreach ($items as &$item) {
		$custom_value = get_post_meta($item->ID, '_menu_item_custom', true);
		if (!empty($custom_value)) {
			$item->title .= ' - ' . esc_html($custom_value);
		}
	}
	return $items;
}

add_filter('wp_nav_menu_objects', 'getterms_custom_menu_item_output', 10, 2);


add_action('wp_enqueue_scripts', function () {
	if (function_exists('wp_register_consent_script')) {
		wp_register_consent_script();
	}
});
