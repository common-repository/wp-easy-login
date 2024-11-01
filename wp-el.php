<?php
/**
 * Plugin Name: WP Easy Login
 * Plugin URI: https://www.secretsofgeeks.com/2019/10/wordpress-login-remember-recent-usernames.html
 * Description: WP Easy Login stores the recent logins and makes it easy for you to login by selecting an account.
 * Version: 1.0.2
 * Requires at least: 4.0
 * Requires PHP: 7.0
 * Author: 5um17
 * Author URI: https://www.secretsofgeeks.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-easy-login
 */

if (!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly.
}

/* Define plugin constants */
if (!defined('WP_EASY_LOGIN_DIR')) {
    //Plugin path
    define('WP_EASY_LOGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('WP_EASY_LOGIN_URL')) {
    //Plugin url
    define('WP_EASY_LOGIN_URL', plugin_dir_url(__FILE__));
}

if ( ! defined( 'WP_EL_Filename' ) ) {
    //Plugin Filename
    define( 'WP_EL_Filename', plugin_basename( __FILE__ ) );
}

/* Includes library files */
require_once WP_EASY_LOGIN_DIR . 'includes/wp-el-browser.php';
require_once WP_EASY_LOGIN_DIR . 'includes/WP_Easy_Login.php';

/* Includes admin files */
if (is_admin()) {
    require_once WP_EASY_LOGIN_DIR . 'admin/WP_Easy_Admin.php';
}

/**
 * Get the instance of WP_Easy_Login
 * @since 1.0
 * @return WP_Easy_Login Instance of WP_Easy_Login
 */
function WP_Easy_Login() {
    return WP_Easy_Login::instance();
}

// Let's start the show
WP_Easy_Login();
