<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Admin setting class of WP Easy login
 *
 * @author 5um17
 */
class WP_Easy_Admin {
    
    /**
     * Default Constructor
     * @since 1.0
     */
    public function __construct(){
        
        add_action('admin_menu', array($this, 'add_setting_page'));
        add_action('admin_init', array($this, 'admin_init'));
	
	add_filter( 'plugin_row_meta', array($this, 'plugin_links'), 10, 2 );
	add_filter( 'plugin_action_links_' . WP_EL_Filename, array($this, 'plugin_action_links'));
    }

    /**
     * Add Admin page
     * @since 1.0
     */
    public function add_setting_page(){
        add_options_page('WP Easy Login Settings', 'WP Easy Login', 'manage_options', 'wpel', array($this, 'setting_page_content'));
    }

    /**
     * Print admin page content
     * @since 1.0
     */
    public function setting_page_content(){ ?>
        <div class="wrap">
            
            <h2>WP Easy Login <?php _e('Settings', 'wp-easy-login'); ?></h2>

            <form method="post" action="options.php"><?php
                settings_fields('wp_el_option_group');	
                do_settings_sections('wpel');
                submit_button(__('Save Changes'), 'primary', 'submit', false); ?>
            </form>
            
        </div><?php
    }

    /**
     * Add Section settings and settings fields
     * @since 1.0
     */
    public function admin_init(){

        /* Register Settings */
        register_setting('wp_el_option_group', 'wp_el_options', array($this, 'validate_options'));

        /* Add Section */
        add_settings_section( 'wp_el_section_main', NULL, NULL, 'wpel' );	
        add_settings_section( 'wp_el_section_labels', __('Customize Labels', 'wp-easy-login'), NULL, 'wpel' );	

        /* Add fields */
        add_settings_field( 'wp_el_cookie_expire', __('Remember usernames for', 'wp-easy-login'), array($this, 'cookie_expire'), 'wpel', 'wp_el_section_main', array('label_for' => 'wp_el_cookie_expire') );
        add_settings_field( 'wp_el_display_avatar', __('Customize users list', 'wp-easy-login'), array($this, 'users_list_options'), 'wpel', 'wp_el_section_main' );
        add_settings_field( 'wp_el_header_hint', __('Text before users list', 'wp-easy-login'), array($this, 'header_hint'), 'wpel', 'wp_el_section_labels', array('label_for' => 'wp_el_header_hint') );
        add_settings_field( 'wp_el_add_new_txt', __('Add new account button label', 'wp-easy-login'), array($this, 'add_new_btn_label'), 'wpel', 'wp_el_section_labels', array('label_for' => 'wp_el_add_new_txt') );
    }
    
    /**
     * Cookie expire time field
     * @since 1.0
     */
    public function cookie_expire() { ?>
        <input id="wp_el_cookie_expire" class="regular-text" type="number" value="<?php echo WP_Easy_Login()->options['cookie_expire']; ?>" name="wp_el_options[cookie_expire]" />&nbsp;<?php _e('days', 'wp-easy-login'); ?>
        <p class="description"><?php _e('Set 0 to store usernames for current session only', 'wp-easy-login'); ?></p><?php
    }
    
    public function users_list_options() {
        $settings = WP_Easy_Login()->options; ?>
        <input type="hidden" name="wp_el_options[display_avatar]" value="0" />
        <input <?php checked($settings['display_avatar']); ?> type="checkbox" id="wp_el_display_avatar" name="wp_el_options[display_avatar]" value="1" />&nbsp;
        <label for="wp_el_display_avatar"><?php _e('Display Avatar', 'wp-easy-login'); ?></label>
        <br />
        <input type="hidden" name="wp_el_options[display_email]" value="0" />
        <input <?php checked($settings['display_email']); ?> type="checkbox" id="wp_el_display_email" name="wp_el_options[display_email]" value="1" />&nbsp;
        <label for="wp_el_display_email"><?php _e('Display Email', 'wp-easy-login'); ?></label>
        <br />
        <input type="hidden" name="wp_el_options[display_role]" value="0" />
        <input <?php checked($settings['display_role']); ?> type="checkbox" id="wp_el_display_role" name="wp_el_options[display_role]" value="1" />&nbsp;
        <label for="wp_el_display_role"><?php _e('Display Role', 'wp-easy-login'); ?></label><?php
    }
    
    /**
     * Header hint text
     * @since 1.0
     */
    public function header_hint() { ?>
        <input id="wp_el_header_hint" class="regular-text" type="text" value="<?php echo esc_attr(WP_Easy_Login()->options['labels']['header_hint']); ?>" name="wp_el_options[labels][header_hint]" /><?php
    }
    
    /**
     * Add new account button label
     * @since 1.0
     */
    public function add_new_btn_label() { ?>
        <input id="wp_el_add_new_txt" class="regular-text" type="text" value="<?php echo esc_attr(WP_Easy_Login()->options['labels']['add_new_txt']); ?>" name="wp_el_options[labels][add_new_txt]" /><?php
    }

    /**
     * Validate settings
     * @since 1.0
     * @param array $input User submitted settings
     * @return array Filtered settings
     */
    public function validate_options($input){
        $settings = WP_Easy_Login()->options;
        
        if (empty($input['labels']['add_new_txt'])) {
            add_settings_error('wp_el_error', 'wp_el_error_empty_btn_txt', __('Button label can not be empty!', 'wp-easy-login'), 'notice-warning');
            return $settings;
        }

        $input['cookie_expire'] = isset($input['cookie_expire']) ? intval($input['cookie_expire']) : $settings['cookie_expire'];
        
        return $input;
    }
    
    /**
     * Add docs and other links to plugin row meta
     * @since 1.0.1
     * @param array $links The array having default links for the plugin
     * @param string $file The name of the plugin file
     * @return array $links array with newly added links
     */
    public function plugin_links($links, $file) {
	if ( $file !== WP_EL_Filename ) {
            return $links;
        }
        
        if (is_array($links)) {
            $links[] = '<a href="https://www.secretsofgeeks.com/2019/10/wordpress-login-remember-recent-usernames.html" target="_blank">'
                    . __('Docs', 'wp-easy-login')
                    . '</a>';
            $links[] = '<a href="https://wordpress.org/plugins/search/5um17/" target="_blank">'
                    . __('More Plugins', 'wp-easy-login')
                    . '</a>';
        }
	return $links;
    }
    
    /**
     * Add setting link to plugin action list.
     * @since 1.0.1
     * @param array $links action links
     * @return array $links new action links
     */
    public function plugin_action_links( $links ) {
	if ( is_array( $links ) ) {
            $links[] = '<a href="' . admin_url( 'options-general.php?page=wpel' ) . '">'
                    . __( 'Settings', 'wp-easy-login' )
                    . '</a>';
        }
	
	return $links;
    }
}