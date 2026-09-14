<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$token = esc_attr(get_option('getterms-token'));
$widget_slug = esc_attr(get_option('getterms-widget-slug'));

$auto_widget = esc_attr(get_option('getterms-auto-widget'));
$auto_widget = $auto_widget !== false ? $auto_widget : '0';

$manual_widget = esc_attr(get_option('getterms-manual-widget'));
$manual_widget = $manual_widget !== false ? $manual_widget : '0';

$auto_language_detection = esc_attr(get_option('getterms-auto-language-detection'));
$auto_language_detection = $auto_language_detection !== false ? $auto_language_detection : '0';

$languages = get_option('getterms-languages');
$policies = get_option('getterms-policies');
$default_language = get_option('getterms-default-language');

$default_language_name = __('Unknown Language', 'getterms-cookie-consent-policies');
switch ($default_language) {
	case 'hi-in':
		$default_language_name = __('Hindi', 'getterms-cookie-consent-policies');
		break;
	case 'en-us':
		$default_language_name = __('English (US)', 'getterms-cookie-consent-policies');
		break;
	case 'en-au':
		$default_language_name = __('English (UK)', 'getterms-cookie-consent-policies');
		break;
	case 'es':
		$default_language_name = __('Spanish', 'getterms-cookie-consent-policies');
		break;
	case 'de':
		$default_language_name = __('German', 'getterms-cookie-consent-policies');
		break;
	case 'fr':
		$default_language_name = __('French', 'getterms-cookie-consent-policies');
		break;
	case 'it':
		$default_language_name = __('Italian', 'getterms-cookie-consent-policies');
		break;
}

?>
<div class="wrap">
    <h1><?php esc_html_e('GetTerms Settings', 'getterms-cookie-consent-policies'); ?></h1>
    <?php
    // --- WP Consent API recommendation / installer (optional integration) ---
    // Load plugin.php only when needed in admin context
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if ( ! is_plugin_active( 'wp-consent-api/wp-consent-api.php' ) ) :
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php esc_html_e('Optional:', 'getterms-cookie-consent-policies'); ?></strong> <?php
                /* translators: %s: Plugin name with link to WordPress.org plugin page */
                printf(esc_html__('If you want to integrate with Google Consent Mode via the WordPress consent framework, we recommend installing the %s plugin. GetTerms works fine without it.', 'getterms-cookie-consent-policies'), '<a href="https://wordpress.org/plugins/wp-consent-api/" target="_blank">WP Consent API</a>'); ?>
            </p>
            <a class="button button-primary" href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-consent-api' ) ); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e( 'Install WP Consent API', 'getterms-cookie-consent-policies' ); ?>
            </a>
        </div>
    <?php endif; ?>
    <form method="post" action="options.php" id="getterms-form">
		<?php settings_fields('getterms-settings'); ?>
		<?php do_settings_sections('getterms-settings'); ?>
        <div class="form-row" id="getterms-token-input">
            <div class="form-label">
                <label for="getterms-token"><?php esc_html_e('GetTerms Token:', 'getterms-cookie-consent-policies'); ?></label>
                <input type="text" id="getterms-token" name="getterms-token" value="<?php echo esc_attr($token); ?>" />
            </div>
            <div class="button-container">
				<?php submit_button('Update', 'primary', 'submit', false); ?>
            </div>
        </div>
        <div id="getterms-error-message" style="display:none"></div>
    </form>
    <div id='getterms-content' style='<?php echo !empty($token) ? 'display: block' : 'display: none'; ?>'>
		<?php
		if (is_string($languages)) {
			$languages = json_decode($languages, true);
		}
		if (is_string($policies)) {
			$policies = json_decode($policies, true);
		}

		if (!empty($languages) && !empty($policies)) {
			include('gt-policies.php');
		} else {
			echo '<p>' . esc_html__('No policies have been set up yet.', 'getterms-cookie-consent-policies') . '</p>';
		}
		?>
		<?php if (!empty($widget_slug)) : ?>
            <hr style='margin-top:1rem'>
            <div id="getterms-widget-content">
                <div style="padding-bottom: 10px;">
                    <h2><?php esc_html_e('Cookie Consent Widget Management', 'getterms-cookie-consent-policies'); ?></h2>
                    <div class="toggle-group">
                        <label class="switch">
                            <input type="checkbox"
                                   id="getterms-auto-enable-widget-toggle"
                                   name="getterms_auto_enable" <?php checked($auto_widget, 'true'); ?> />
                            <span class="slider round"></span>
                        </label>
                        <p>
                            <?php 
                            /* translators: %s: Default language name (e.g., English (US), Spanish, etc.) */
                            printf(esc_html__('Automatically Embed in %s', 'getterms-cookie-consent-policies'), esc_html($default_language_name)); ?>
                            <span class="description" style="display: block; font-style: italic; color: #666; margin-top: 5px;">
                                <?php esc_html_e('If you have manually edited your site files to place other scripts at the top of the &lt;head&gt;, auto-blocking of cookies may work inconsistently and manual implementation of our script is recommended .', 'getterms-cookie-consent-policies'); ?>
                            </span>
                        </p>
                    </div>
                    <div class="toggle-group">
                        <label class="switch">
                            <input type="checkbox"
                                   id="getterms-manual-enable-widget-toggle"
                                   name="getterms_manual_enable" <?php checked($manual_widget, 'true'); ?> />
                            <span class="slider round"></span>
                        </label>
                        <p>
                            <?php esc_html_e('Embed Widget Manually (Supports multilingual options)', 'getterms-cookie-consent-policies'); ?>
                        </p>
                    </div>
                    <div class="toggle-group">
                        <label class="switch">
                            <input type="checkbox"
                                   id="getterms-auto-language-detection-toggle"
                                   name="getterms_auto_language_detection" <?php checked($auto_language_detection, 'true'); ?> />
                            <span class="slider round"></span>
                        </label>
                        <p>
                            <?php esc_html_e('Enable auto language detection', 'getterms-cookie-consent-policies'); ?>
                            <span class="description" style="display: block; font-style: italic; color: #666; margin-top: 5px;">
                                <?php esc_html_e('This implementation will attempt to match the visitor\'s Accept-Language settings in their OS/Browser to one of our available languages. The selected embed language will be used as the default fallback.', 'getterms-cookie-consent-policies'); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div id="getterms-widget-settings" style="display: <?php echo esc_attr($manual_widget === 'true' ? 'block' : 'none'); ?>">
					<?php include('gt-widgets.php')?>
                </div>
                <div>
                    <a id="getterms-cookie-link"
                       target="_blank"
                       rel="noopener noreferrer"
                       href="<?php echo esc_url('https://app.getterms.io/cookie-consent/' . get_option('getterms-widget-slug') . '/dashboard/appearance'); ?>">
                        <?php esc_html_e('Update your Cookie Consent Widget style, layout, language, and content', 'getterms-cookie-consent-policies'); ?>
                    </a>
                </div>
            </div>
		<?php endif; ?>
    </div>
</div>
