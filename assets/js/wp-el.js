/**
 * Login page JS
 */
jQuery(document).ready(function (){
    /* Remove labels and hide submit button */
    if (jQuery('.user-pass-wrap').length > 0) { /* If WP version >= 5.3 */
        jQuery('#user_login').parent('p').remove();
        jQuery('.user-pass-wrap').remove();
    } else {
        jQuery('#user_login, #user_pass').parent('label').parent('p').remove();
    }
    
    jQuery('.forgetmenot, .submit').hide()
    
    /* Handle click even on user lists */
    jQuery('#wp-el-users-list > li').click(function (){
        var login_input = jQuery('#wp-el-log');
        var username = jQuery(this).data('username');
        
        jQuery('#wp-el-users-list > li.wp-el-selected').removeClass('wp-el-selected');
        jQuery(this).addClass('wp-el-selected');
        
        if (login_input.prop('type') === 'hidden') {
            jQuery(login_input).val(username);
            wp_el_slide('.wp-el-pwd-wrapper, .forgetmenot, .submit');
        } else {
            wp_el_slide(login_input, 'up', 'fast', function (){
                jQuery(login_input).prop('type', 'hidden').val(username);
            });
        }
        
        jQuery('#wp-el-add-new').parent('p').show(); //Show new user login button incase it is hidden
        jQuery('#wp-el-pwd').focus();
    });
    
    /* Handle click even on add new login button */
    jQuery('#wp-el-add-new').click(function (e){
        e.preventDefault();
        var login_input = jQuery('#wp-el-log');
        
        if (login_input.prop('type') === 'text') {
            login_input.focus();
            return; //Return if already open
        }
        
        login_input
                .hide()
                .prop({
                    type: 'text',
                    value: ''
                })
                .slideDown('fast');
        wp_el_slide('.wp-el-pwd-wrapper, .forgetmenot, .submit');
        jQuery('#wp-el-users-list > li.wp-el-selected').removeClass('wp-el-selected');
        jQuery(this).parent('p').hide();
        login_input.focus();
    });
    
    /* Handle remove account event */
    jQuery('.wp-el-remove-account').click(function (e){
        e.stopPropagation(); //Prevent firing of user list click event
        var confirm_msg = wp_el_js_data.strings.msg_3.replace('%%user_name%%', jQuery(this).data('name'));
        modernAlert.confirm(confirm_msg, wp_el_js_data.strings.msg_4, wp_el_remove_account, jQuery(this).parent('li').data('username'), {ok : wp_el_js_data.strings.msg_5, cancel : wp_el_js_data.strings.msg_6}); 
    });
    
    /* Toggle Password */
    jQuery('.wp-el-toggle-pwd').click(function (){
        if (jQuery('#wp-el-pwd').prop('type') === 'password') {
            jQuery('#wp-el-pwd').prop('type', 'text');
            jQuery(this).children('.dashicons').toggleClass('dashicons-hidden dashicons-visibility');
        } else {
            jQuery('#wp-el-pwd').prop('type', 'password');
            jQuery(this).children('.dashicons').toggleClass('dashicons-hidden dashicons-visibility');
        }
    });
    
    /**
     * Fire remove account Ajax
     * @param {boolean} rm_acc - true or false based on user input
     * @param {string} username - Username to remove account
     * @returns {undefined}
     */
    function wp_el_remove_account(rm_acc, username) {
        if (rm_acc) {
            jQuery('body').addClass('wp-el-doing-ajax');

            jQuery.ajax({
                method: 'POST',
                url: wp_el_js_data.ajax_url,
                data: {action: 'wp_el_remove_acc', username: username},
                success: function (result) {
                    if (result.hasOwnProperty('deleted') && result.deleted === true) {
                        window.location.reload();
                    } else {
                        modernAlert.alert(wp_el_js_data.strings.msg_1, wp_el_js_data.strings.msg_2);
                    }
                }
            }).done(function () {
                jQuery('body').removeClass('wp-el-doing-ajax');
            });
        }
    }

    /**
     * Animate elements
     * @param {object} elements - jQuery object of element
     * @param {string} [type=down] - Animation type up or down
     * @param {string} [speed=fast] - Animation speed
     * @param {funtion} [callback=null] - Callback function
     * @returns {undefined}
     */
    function wp_el_slide(elements, type, speed, callback) {
        type = typeof type !== 'undefined' ? type : 'down';
        speed = typeof speed !== 'undefined' ? speed : 'fast';
        callback = typeof callback !== 'undefined' ? callback : null;

        if (type === 'up') {
            jQuery(elements).slideUp(speed, callback);
        } else {
            jQuery(elements).slideDown(speed, callback);
        }
    }
});

/**
 * Call modernAlert constructor
 */
modernAlert({
    color: '#444',
    borderColor: '#0085ba',
    titleBackgroundColor: '#0085ba',
    overrideNative: false
});