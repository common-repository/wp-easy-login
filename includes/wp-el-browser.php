<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Get the OS and Browser name
 * @since 1.0
 * @link http://www.php.net/manual/en/function.get-browser.php#101125
 * @return array Name of browser and OS
 */
function wp_el_get_browser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'] ?? false;
    $browser_os_data = array(
        'browser'   => 'Unknown',
        'os'        => 'Unknown'
    );
    
    if (empty($u_agent)) {
        return $browser_os_data;
    }

    // First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $browser_os_data['os'] = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $browser_os_data['os'] = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $browser_os_data['os'] = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $browser_os_data['browser'] = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $browser_os_data['browser'] = 'Mozilla Firefox';
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $browser_os_data['browser'] = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $browser_os_data['browser'] = 'Apple Safari';
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $browser_os_data['browser'] = 'Opera';
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $browser_os_data['browser'] = 'Netscape';
    }

    return $browser_os_data;
}