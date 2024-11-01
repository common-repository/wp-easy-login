<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main class of WP Easy Login
 *
 * @author 5um17
 */
class WP_Easy_Login {

    /**
     * Instance of this class
     * @var WP_Easy_login 
     */
    protected static $instance = null;
    
    /**
     * Plugin settings
     * @var Array 
     */
    public $options = null;

    /**
     * Browser and OS name 
     * @var array 
     */
    public $browser_info = false;
    
    /**
     * List of stored usernames
     * @var array
     */
    public $stored_usernames = false;
    
    /**
     * Check if this a login action
     * @var boolean 
     */
    public $is_login = false;

    /**
     * Get the instance
     * @since 1.0
     * @return WP_Easy_login
     */
    public static function instance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @since 1.0
     * Constructor of WP_Easy_Login
     */
    public function __construct() {
        //Set variables
        $this->browser_info = wp_el_get_browser();
        $this->stored_usernames = $this->get_stored_usernames();
        $this->is_login = empty($_REQUEST['action']) || $_REQUEST['action'] === 'login' ? true : false;
        
        //If it is admin initiate admin classes
        if (is_admin()) {
            new WP_Easy_Admin();
        }

        add_action('wp_login', array($this, 'set_cookie'));
        add_action('plugins_loaded', array($this, 'setup_options'), 99);
        add_action('login_form', array($this, 'display_user_list'));
        add_action('login_enqueue_scripts', array($this, 'enqueue_scripts_css'));
        add_action('wp_ajax_wp_el_remove_acc', array($this, 'remove_account'));
        add_action('wp_ajax_nopriv_wp_el_remove_acc', array($this, 'remove_account'));
    }

    /**
     * Load plugin textdomain and setup option variable
     * @since 1.0
     */
    public function setup_options() {
        $this->options = $this->get_options();
        
        // Load plugin text domain. We can enhance it later if needed.
        load_plugin_textdomain( 'wp-easy-login', false, dirname( plugin_basename( WP_EASY_LOGIN_DIR . 'WP_Easy_Login.php' ) ) . '/languages' );
    }
    
    /**
     * Handle remove account request
     * @since 1.0
     */
    public function remove_account() {
        $result = array(
            'deleted' => FALSE
        );
        
        if (!empty($_POST['username'])) {
            $cookie_value = $this->encode_cookie(sanitize_user($_POST['username']), $this->browser_info);
            
            foreach ($_COOKIE['wpel'] as $key => $value) {
                if ($value == $cookie_value) { //Check if cookie exist
                    setcookie("wpel[$key]", '', time() - 3600, SITECOOKIEPATH);
                    unset($_COOKIE['wpel'][$key]);
                    $result['deleted'] = true;
                    break;
                }
            }
        }
        
        wp_send_json($result);
    }

    /**
     * Default settings for WP Easy login
     * @since 1.0
     * @return array Default settings
     */
    public function get_default_options() {
        return array(
            'cookie_expire' => 365,
            'display_avatar' => true,
            'display_email' => true,
            'display_role' => true,
            'labels' => array(
                'add_new_txt' => __('Login with new account', 'wp-easy-login'),
                'header_hint' => __('Select an account for login', 'wp-easy-login')
            )
        );
    }
    
    /**
     * Get settings from DB
     * @since 1.0
     * @return array
     */
    public function get_options() {
        return wp_parse_args(get_option('wp_el_options'), $this->get_default_options());
    }

    /**
     * Encode string to url-safe base64 
     * @since 1.0
     * @link http://us3.php.net/manual/en/function.base64-encode.php#103849
     * @param mixed $data Data to encode
     * @return string encoded string
     */
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode url-safe base64 string
     * @since 1.0
     * @link http://us3.php.net/manual/en/function.base64-encode.php#103849
     * @param string $data Encoded string
     * @return mixed decoded value
     */
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Encode username with browser and os info
     * @since 1.0
     * @param string $user_login username
     * @param array $browser_info browser and OS name
     * @return string Encoded value to store as cookie
     */
    public function encode_cookie($user_login, $browser_info) {
        $data = array(
            'u' => $user_login,
            'b' => $browser_info['browser'],
            'o' => $browser_info['os']
        );

        return $this->base64url_encode(json_encode($data));
    }

    /**
     * Decode encoded cookie
     * @since 1.0
     * @param string $cookie_value Cookie value
     * @return array Decoded cookie value
     */
    public function decode_cookie($cookie_value) {
        $data = json_decode($this->base64url_decode($cookie_value), true);
        if (!empty($data['u'])) {
            return $data;
        }

        return array(
            'u' => '',
            'b' => '',
            'o' => ''
        );
    }

    /**
     * add/update cookie when user logged-in
     * @since 1.0
     * @param string $user_login username
     */
    public function set_cookie($user_login) {
        $current_cookie = !empty($_COOKIE['wpel']) ? $_COOKIE['wpel'] : array();
        $key = false;
        $cookie_value = false;

        //Find if user already exist
        if (is_array($current_cookie)) {
            foreach ($current_cookie as $index => $value) {
                if ($this->encode_cookie($user_login, $this->browser_info) == $value) {
                    $key = $index;
                    $cookie_value = $value;
                    break;
                }
            }
        }

        //No user found, create a new cookie
        if ($key == false) {
            $key = time();
            $cookie_value = $this->encode_cookie($user_login, $this->browser_info);
        }
        
        $expire = !empty($this->options['cookie_expire']) ? time()+DAY_IN_SECONDS*($this->options['cookie_expire']) : 0;
        setcookie("wpel[$key]", $cookie_value, $expire, SITECOOKIEPATH);
    }

    /**
     * Display user list
     * @since 1.0
     * @return NULL
     */
    public function display_user_list() {

        if (empty($this->stored_usernames) || !$this->is_login || !did_action('login_enqueue_scripts')) {
            return; //Return if no username is stored or not a login request or not a default login page
        }
        
        if (!empty($this->options['labels']['header_hint'])) { ?>
            <p class="wp-el-hint"><?php echo $this->options['labels']['header_hint']; ?></p><?php
        } ?>
        
        <ul id="wp-el-users-list"><?php
            foreach ($this->stored_usernames as $username) {
                $user = get_user_by('login', $username);
                if ($user) { ?>
                    <li class="wp-el-user-block" data-username="<?php echo $username; ?>"><?php
                        
                        if (!empty($this->options['display_avatar'])) {
                            echo get_avatar($user->ID, 32);
                        } ?>
                        
                        <div class="wp-el-user-data"><?php 
                            echo $user->data->display_name;
                            if (!empty($this->options['display_email']) || !empty($this->options['display_role'])) { ?>
                                <div class="wp-el-user-email-role"><?php 
                                    if (!empty($this->options['display_email'])) {
                                        echo '<span class="wp-el-useremail">' . $user->data->user_email . '</span>';
                                        $is_user_email_set = true;
                                    }
                                    
                                    if (!empty($this->options['display_role'])) {
                                        $role_wrapper[0] = '<span class="wp-el-userrole">';
                                        $role_wrapper[1] = '</span>';
                                        
                                        if (!empty($is_user_email_set)) {
                                            $role_wrapper[0] .= ' (';
                                            $role_wrapper[1] = ')' . $role_wrapper[1];
                                        }
                                        
                                        echo !empty($user->roles['0']) ? $role_wrapper[0] . ucwords($user->roles['0']) . $role_wrapper[1] : '';
                                    } ?>
                                </div><?php 
                            } ?>
                        </div>
                        
                        <span data-name="<?php echo $user->data->display_name; ?>" title="<?php _e('Remove this account', 'wp-easy-login') ?>" class="wp-el-remove-account dashicons dashicons-trash"></span>
                    </li><?php
                }
            } ?>
        </ul>

        <input placeholder="<?php _e('Username or Email', 'wp-easy-login'); ?>" class="input" id="wp-el-log" type="hidden" value="" name="log" />
        <p class="wp-el-pwd-wrapper" style="display: none;">
	    <input placeholder="<?php _e('Password', 'wp-easy-login'); ?>" class="input" id="wp-el-pwd" type="password" value="" name="pwd" />
	    <button type="button" class="button button-secondary wp-el-toggle-pwd hide-if-no-js">
		<span class="dashicons dashicons-visibility"></span>
	    </button>
	</p>
        <p class="wp-el-button-wrapper">
            <span class="wp-el-hint"><?php _e('OR', 'wp-easy-login'); ?></span>
            <button class="button" type="button" id="wp-el-add-new"><?php echo esc_attr($this->options['labels']['add_new_txt']); ?></button>
        </p><?php
    }

    /**
     * Get stored usernames from cookie
     * @since 1.0
     * @return array list of usernames
     */
    public function get_stored_usernames() {
        $usernames = array();
        if (!empty($_COOKIE['wpel'])) {
            foreach ($_COOKIE['wpel'] as $value) {
                $decoded_cookie = $this->decode_cookie($value);
                if ($decoded_cookie['o'] == $this->browser_info['os'] && $decoded_cookie['b'] == $this->browser_info['browser']) {
                    //Check if browser and os info same as in cookie
                    $username = sanitize_user($decoded_cookie['u']);
                    if (WP_User::get_data_by( 'login', $username )) {
                        $usernames[] = $username;
                    }
                }
            }
        }

        return $usernames;
    }

    /**
     * Enqueue scripts and CSS
     * @since 1.0
     * @return NULL
     */
    public function enqueue_scripts_css() {
        if (empty($this->stored_usernames) || !$this->is_login) {
            return;
        }
        
        $js_variable = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'strings' => array(
                'msg_1' => __('Somthing went wrong. Account can not be removed!', 'wp-easy-login'),
                'msg_2' => __('Error', 'wp-easy-login'),
                'msg_3' => __('Do you want to remove %%user_name%%?', 'wp-easy-login'),
                'msg_4' => __('Remove Account', 'wp-easy-login'),
                'msg_5' => __('Yes', 'wp-easy-login'),
                'msg_6' => __('No', 'wp-easy-login'),
            )
        );
        
        //Incldue jQuery
        wp_enqueue_script('jquery');
        
        //Include our js and CSS
        wp_enqueue_script('modernAlert', WP_EASY_LOGIN_URL . 'assets/js/modernAlert' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js', array('jquery'));
        wp_enqueue_script('wp-el-js', WP_EASY_LOGIN_URL . 'assets/js/wp-el' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js', array('modernAlert'));
        wp_enqueue_style('wp-el-css', WP_EASY_LOGIN_URL . 'assets/css/wp-el-css' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.css');
        
        //Localize script
        wp_localize_script('wp-el-js', 'wp_el_js_data', $js_variable);
    }
}