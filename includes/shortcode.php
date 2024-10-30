<?php
/**
 * Shortcode
 */

if(!defined('ABSPATH')) {
	exit;
}

// Create nonce for logged in user
function lcf_create_nonce() {
	if(is_user_logged_in())	return wp_create_nonce('wp_rest');
	return '';
}

// Enqueue plugin script
function lcf_enqueue_scripts() {
	global $post;
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'contact_form')) {
		do_action('lcf_enqueue_scripts');
		if(file_exists(get_stylesheet_directory().'/lite-contact-form.css')) {
			wp_enqueue_style('lite-contact-form', get_stylesheet_directory_uri().'/lite-contact-form.css', array(), LCF_VERSION, 'all');
		} elseif(file_exists(get_template_directory().'/lite-contact-form.css')) {
			wp_enqueue_style('lite-contact-form', get_template_directory_uri().'/lite-contact-form.css', array(), LCF_VERSION, 'all');
		} else {
			wp_enqueue_style('lite-contact-form', LCF_DIR_URL.'css/style.min.css', array(), LCF_VERSION, 'all');
		}
		wp_enqueue_script('lite-contact-form', LCF_DIR_URL.'js/js.lite-contact-form.min.js', array(), LCF_VERSION, true);
		wp_localize_script('lite-contact-form', 'lcf', array(
			'nonce' => lcf_create_nonce(),
			'submit' => esc_url_raw(rest_url().'lite-contact-form/v1/submit'),
			'empty_field' => __('This field is required.', 'lite-contact-form'),
			'invalid' => __('One or more fields have an error. Please check and try again.', 'lite-contact-form'),
			'error' => __('An unexpected error occurred. Please try again.', 'lite-contact-form')
		));
	}
}
add_action('wp_enqueue_scripts', 'lcf_enqueue_scripts');

// Contact form shortcode
function lcf_shortcode($atts) {
	// Default attributes value
	$atts = shortcode_atts(
		array(
			'recipient' => get_bloginfo('name').' <'.get_option('admin_email').'>',
			'sender' => get_option('admin_email'),
			'style' => 'placeholders',
			'id' => get_post_field('post_name')
		), $atts, 'lite_contact_form');
	// Save attributes to transient
	set_transient('lcf_'.$atts['id'], $atts);
	// Output shortcode
	if($atts['style'] == 'labels') {
		$output = '<form class="lcf" method="post" onsubmit="return false">
			'.apply_filters('lcf_before_fields', '').'
			<p class="lcf-validate">
				<label for="lcf-name">'.__('Name', 'lite-contact-form').'</label>
				<input type="text" name="name" id="lcf-name" size="40">
				<span class="lcf-tip"></span>
			</p>
			<p class="lcf-validate">
				<label for="lcf-email">'.__('Email', 'lite-contact-form').'</label>
				<input type="email" name="email" id="lcf-email" size="40">
				<span class="lcf-tip"></span>
			</p>
			<p class="lcf-validate">
				<label for="lcf-subject">'.__('Subject', 'lite-contact-form').'</label>
				<input type="text" name="subject" id="lcf-subject" size="40">
				<span class="lcf-tip"></span>
			</p>
			'.apply_filters('lcf_before_message_field', '').'
			<p class="lcf-validate">
				<label for="lcf-message">'.__('Your message', 'lite-contact-form').'</label>
				<textarea name="message" id="lcf-message" cols="40" rows="15"></textarea>
				<span class="lcf-tip"></span>
			</p>
			'.apply_filters('lcf_after_fields', '').'
			<p>
				<input type="submit" value="'.__('Send', 'lite-contact-form').'">
				<input type="hidden" name="_lcf" value="'.$atts['id'].'">
				<span class="lcf-spinner" style="visibility: hidden;"></span>
			</p>
			<div id="lcf-response"></div>
		</form>';
	}
	else {
		$output = '<form class="lcf" method="post" onsubmit="return false">
			'.apply_filters('lcf_before_fields', '').'
			<p class="lcf-validate">
				<input type="text" name="name" size="40" placeholder="'.__('Name', 'lite-contact-form').'">
				<span class="lcf-tip"></span>
			</p>
			<p class="lcf-validate">
				<input type="email" name="email" size="40" placeholder="'.__('Email', 'lite-contact-form').'">
				<span class="lcf-tip"></span>
			</p>
			<p class="lcf-validate">
				<input type="text" name="subject" size="40" placeholder="'.__('Subject', 'lite-contact-form').'">
				<span class="lcf-tip"></span>
			</p>
			'.apply_filters('lcf_before_message_field', '').'
			<p class="lcf-validate">
				<textarea name="message" cols="40" rows="15" placeholder="'.__('Your message', 'lite-contact-form').'"></textarea>
				<span class="lcf-tip"></span>
			</p>
			'.apply_filters('lcf_after_fields', '').'
			<p>
				<input type="submit" value="'.__('Send', 'lite-contact-form').'">
				<input type="hidden" name="_lcf" value="'.$atts['id'].'">
				<span class="lcf-spinner" style="visibility: hidden;"></span>
			</p>
			<div id="lcf-response"></div>
		</form>';
	}

	return $output;
}
add_shortcode('contact_form', 'lcf_shortcode');
