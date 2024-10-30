=== Lite Contact Form ===
Contributors: Beherit
Tags: contact, contact form, email, feedback, feedback form
Requires at least: 4.6
Tested up to: 5.9
Stable tag: 1.1.6
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Lightweight and simple contact form with no additional user-unfriendly options. Can be additionally protected against spam by using Akismet and Google reCAPTCHA.

== Description ==

Lightweight and simple contact form with no additional user-unfriendly options. You can add the contact form to any page with a shortcode `[contact_form]`. Plugin is integrated with plugin [Akismet](https://wordpress.org/plugins/akismet/) and [GreCAPTCHA](https://wordpress.org/plugins/grecaptcha/) which protect against spam.

== Frequently Asked Questions ==

= How to change sender and recipient email address? =

By default, the plugin uses the administrator's email address which is set in the WordPress general settings. It can be changed by adding attributes to the shortcode.

`[contact_form sender="noreply@domain.tld" recipient="Blog Admin <admin@domain.tld>"]`

= How to protect contact form against spam? =

The plugin has no built-in spam protection features, but is integrated with other plugins which do that job in the best way. You can use for it  plugin [Akismet](https://wordpress.org/plugins/akismet/) or/and [GreCAPTCHA](https://wordpress.org/plugins/grecaptcha/).

= Will this plugin work with cache plugins? =

Yes. The plugin was designed to work with cache plugins. It uses lightweight Vanilla JS script and WordPress REST API to process requests.

= How to change the style of the contact form? =

By default, the fields description is displayed in placeholders, but you can change it to labels.

`[contact_form style="labels"]`

If you want only to change CSS style just add selectors to file style.css in active theme or create new file lite-contact-form.css in active theme.

= How to add custom fields? =

The plugin is simple and there are no settings to add custom fields, you need to add PHP code e.g. in functions.php in the active theme.

Use the add_filter function to add a custom field, there are three filters that you can use.

`function custom_fields_before() {
	return '<p class="lcf-validate">
		<input type="text" name="test" placeholder="Field with JS validation">
		<span class="lcf-tip"></span>
	</p>';
}
add_filter('lcf_before_fields', 'custom_fields_before');`

`function custom_fields_before_message() {
	return '<p>
		<input type="text" name="test" placeholder="Field without JS validation">
		<span class="lcf-tip"></span>
	</p>';
}
add_filter('lcf_before_message_field', 'custom_fields_before_message');`

`function custom_fields_after() {
	return 'some html code';
}
add_filter('lcf_after_fields', 'custom_fields_after');`

You can validates the entire request or only your field.

`function custom_field_validate($result, $field, $value) {
	if(empty($value)) {
		$result['status'] = 'blocked';
		$result['fields'][] = array('field' => $field, 'message' => 'This field is required.');
	}
	return $result;
}
add_filter('lcf_validate_field_name', 'custom_field_validate', 10, 3);`

`function custom_validate_request($result, $fields) {
	if(...) {
		$result['status'] = 'error';
		$result['message'] = 'There was an error trying to send your message.';
	}
	return $result;
}
add_filter('lcf_validate', 'custom_validate_request', 10, 2);`

There are two more filters that you can use to change the email subject and the message body.

`function custom_mail_subject($subject, $fields) {
	return $subject;
}
add_filter('lcf_mail_subject', 'custom_mail_subject', 10, 2);`

`function custom_mail_message($message, $fields) {
	return $message;
}
add_filter('lcf_mail_message', 'custom_mail_message', 10, 2);`

== Installation ==

In most cases you can install automatically from plugins page in admin panel.

However, if you want to install it manually, follow these steps:

1. Download the plugin and unzip the archive.
2. Upload the entire `lite-contact-form` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress.

== Changelog ==
= 1.1.6 (2022-02-16) =
* Minor improvements.
= 1.1.4 (2020-08-18) =
* Fixed Akismet API call.
= 1.1.2 (2020-08-05) =
* Minor improvements.
= 1.1 (2020-05-19) =
* Added a style attribute for shortcode.
* Enqueue CSS stylesheet from active theme.
= 1.0 (2020-05-18) =
* Initial release.
