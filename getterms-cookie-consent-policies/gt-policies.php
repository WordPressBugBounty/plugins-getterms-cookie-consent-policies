<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$languages = isset($languages) ? $languages : [];
$policies = isset($policies) ? $policies : [];
$default_language = isset($default_language) ? $default_language : 'en-us';

// Language keys use underscores (en_au) but the stored default language uses
// hyphens (en-au), so normalise before comparing — a plain == would never
// pre-select the English/Hindi variants.
$selected_lang_key = '';
foreach ($languages as $lang_key => $lang_name) {
	if (str_replace('_', '-', $lang_key) === $default_language) {
		$selected_lang_key = $lang_key;
		break;
	}
}

// Non-selected language columns are hidden inline at render time. The admin
// bundle re-applies the same filtering on load and on selector changes, but it
// runs from the footer — without the inline styles every language column is
// visible for a moment before the script kicks in.
$getterms_lang_col_style = function ($lang_key) use ($selected_lang_key) {
	return ($selected_lang_key !== '' && $lang_key !== $selected_lang_key) ? ' style="display:none"' : '';
};

echo '<hr style="margin-top:1rem">';
echo '<h2>' . esc_html__('Embed your policies', 'getterms-cookie-consent-policies') . '</h2>';
echo '<h4>' . esc_html__('Use these shortcodes on your pages to display your policy content', 'getterms-cookie-consent-policies') . '</h4>';

echo '<select id="language-selector">';
echo '<option value="">' . esc_html__('Select a Language', 'getterms-cookie-consent-policies') . '</option>';
foreach ($languages as $lang_key => $lang_name) {
 $selected = ($lang_key === $selected_lang_key) ? ' selected' : '';
	echo '<option value="' . esc_attr($lang_key) . '"' . esc_attr($selected) . '>' . esc_html($lang_name) . '</option>';
}
echo '</select>';

echo '<table class="getterms-table">';
echo '<thead>';
echo '<tr>';
echo '<th>' . esc_html__('Policy', 'getterms-cookie-consent-policies') . '</th>';
foreach ($languages as $lang_key => $lang_name) {
 echo '<th data-lang="' . esc_attr($lang_key) . '"' . $getterms_lang_col_style($lang_key) . '>' . esc_html($lang_name) . '</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($policies as $policy) {
 echo '<tr>';
	echo '<td>' . esc_html(ucfirst($policy)) . '</td>';
 foreach ($languages as $lang_key => $lang_name) {
  $shortcode_tag = 'getterms_' . $policy . '_' . $lang_key;
  echo '<td data-lang="' . esc_attr($lang_key) . '"' . $getterms_lang_col_style($lang_key) . '><button class="copy-button" data-shortcode="[' . esc_attr($shortcode_tag) . ']">[' . esc_html($shortcode_tag) . ']</button></td>';
 }
 echo '</tr>';
}
echo '</tbody>';
echo '</table>';
