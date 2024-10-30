<?php
/**
 * Plugin Name: Lite Contact Form
 * Plugin URI: https://beherit.pl/en/wordpress/lite-contact-form/
 * Description: Lightweight and simple contact form with no additional user-unfriendly options. Can be additionally protected against spam by using Akismet and Google reCAPTCHA.
 * Version: 1.1.6
 * Requires at least: 4.6
 * Requires PHP: 7.0
 * Author: Krzysztof Grochocki
 * Author URI: https://beherit.pl/
 * Text Domain: lite-contact-form
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */

if(!defined('ABSPATH')) {
	exit;
}

// Define variables
define('LCF_VERSION', '1.1.6');
//define('LCF_BASENAME', plugin_basename(__FILE__));
define('LCF_DIR', plugin_dir_path(__FILE__));
define('LCF_DIR_URL', plugin_dir_url(__FILE__));

// Load necessary files
require_once LCF_DIR.'includes/core.php';
//require_once LCF_DIR.'includes/gutenberg.php';
require_once LCF_DIR.'includes/shortcode.php';
