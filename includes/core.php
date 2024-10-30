<?php
/**
 * Core
 */

if(!defined('ABSPATH')) {
	exit;
}

// Route form submit
function lcf_route_form_submit() {
	register_rest_route('lite-contact-form/v1', '/submit', array(
		'methods' => 'POST',
		'callback' => 'lcf_form_submit',
		'args' => lcf_form_submit_arguments(),
		'permission_callback' => '__return_true'
	));
}
add_action('rest_api_init', 'lcf_route_form_submit');

// Form submit arguments
function lcf_form_submit_arguments() {
	$args = array();
	$args['_lcf'] = array(
		'type' => 'string',
		'required' => true,
		'sanitize_callback' => function($param, $request, $key) {
			return stripslashes_deep(sanitize_text_field($param));
		},
	);
	$args['name'] = array(
		'type' => 'string',
		'required' => true,
		'sanitize_callback' => function($param, $request, $key) {
			return stripslashes_deep(sanitize_text_field($param));
		},
	);
	$args['email'] = array(
		'type' => 'string',
		'required' => true,
		'sanitize_callback' => function($param, $request, $key) {
			return stripslashes_deep(sanitize_text_field($param));
		},
	);
	$args['subject'] = array(
		'type' => 'string',
		'required' => true,
		'sanitize_callback' => function($param, $request, $key) {
			return stripslashes_deep(sanitize_text_field($param));
		},
	);
	$args['message'] = array(
		'type' => 'string',
		'required' => true,
		'sanitize_callback' => function($param, $request, $key) {
			return stripslashes_deep(sanitize_text_field($param));
		},
	);
	return $args;
}

// Form submit callback
function lcf_form_submit($request) {
	// Create default result array
	$result = array(
		'status' => 'success'
		// 'message' => string
		// 'fields' => array('field' => string, 'message' => string)
	);
	// Retrieves merged parameters from the request
	$fields = $request->get_params();
	unset($fields['_lcf']);
	// Validates all parameters
	foreach($fields as $field => $value) {
		$result = apply_filters("lcf_validate_{$field}", $result, $field, $value);
	}
	$result = apply_filters('lcf_validate', $result, $fields);
	// Validation passed
	if($result['status'] == 'success') {
		// Get shortcode attributes from transient
		$transient = get_transient('lcf_'.$request['_lcf']);
		// Email data
		$subject = apply_filters('lcf_mail_subject', $fields['subject'], $fields);
		$message = apply_filters('lcf_mail_message', $fields['message'], $fields);
		$headers[] = 'From: '.$fields['name'].' <'.$transient['sender'].'>';
		$headers[] = 'Reply-To: '.$fields['name'].' <'.$fields['email'].'>';
		$headers[] = 'Content-Type: text/plain; charset=UTF-8';
		// Try send email
		if(wp_mail($transient['recipient'], $subject, $message, $headers)) {
			$result['message'] = __('Your message has been sent.', 'lite-contact-form');
		}
		// Problem with sending email
		else {
			$result['status'] = 'error';
			$result['message'] = __('An error occurred while trying to send the message. Please try again later.', 'lite-contact-form');
		}
	}
	else {
		$result['message'] = $result['message'] ?: __('One or more fields have an error. Please check and try again.', 'lite-contact-form');
	}
	return rest_ensure_response($result);
}

// Validates fields value
function lcf_validate_fields($result, $field, $value) {
	if(empty($value)) {
		$result['status'] = 'blocked';
		$result['fields'][] = array('field' => $field, 'message' => __('This field is required.', 'lite-contact-form'));
	}
	return $result;
}
add_filter('lcf_validate_name', 'lcf_validate_fields', 10, 3);
add_filter('lcf_validate_subject', 'lcf_validate_fields', 10, 3);
add_filter('lcf_validate_message', 'lcf_validate_fields', 10, 3);

// Validates email address
function lcf_validate_email($result, $field, $value) {
	// Validate value
	if(empty($value)) {
		$result['status'] = 'blocked';
		$result['fields'][] = array('field' => $field, 'message' => __('This field is required.', 'lite-contact-form'));
	}
	// Validate syntax
	else if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
		$result['status'] = 'blocked';
		$result['fields'][] = array('field' => $field, 'message' => __('Email address is invalid.', 'lite-contact-form'));
	}
	// Validate MX record
	else {
		// Explode email
		list($user, $domain) = explode('@', $value);
		// Check record
		$mx = dns_get_record($domain, DNS_MX);
		if($mx[0]['host'] != $domain || empty($mx[0]['target'])) {
			$result['status'] = 'blocked';
			$result['fields'][] = array('field' => $field, 'message' => __('Email address is invalid.', 'lite-contact-form'));
		}
	}
	return $result;
}
add_filter('lcf_validate_email', 'lcf_validate_email', 10, 3);

// Spam protection with Akismet
function lcf_validate_akismet($result, $fields) {
	if(is_callable(array('Akismet', 'http_post'))) {
		// Parameters
		// https://akismet.com/development/api/#comment-check
		$parameters = array();
		$parameters['blog'] = get_option('home');
		$parameters['blog_lang'] = get_locale();
		$parameters['blog_charset'] = get_option('blog_charset');
		$parameters['user_ip'] = $_SERVER['REMOTE_ADDR'];
		$parameters['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$parameters['referrer'] = $_SERVER['HTTP_REFERER'];
		$parameters['comment_type'] = 'contact-form';
		$parameters['comment_author'] = $fields['name'];
		$parameters['comment_author_email'] = $fields['email'];
		$parameters['comment_content'] = $fields['message'];
		// Call Akismet API
		$response = Akismet::http_post(build_query($parameters), 'comment-check');
		// Message marked as spam
		if('true' == $response[1]) {
			$result['status'] = 'blocked';
			$result['message'] = __('Your message has been marked as spam. Please try again.', 'lite-contact-form');
		}
	}
	return $result;
}
add_filter('lcf_validate', 'lcf_validate_akismet', 10, 2);
