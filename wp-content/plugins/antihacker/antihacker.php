<?php /*
Plugin Name: AntiHacker 
Plugin URI: http://antihackerplugin.com
Description: Improve security and prevent unauthorized access by restrict access to login to whitelisted IP and much more.
Version: 2.24
Text Domain: antihacker
Domain Path: /lang
Author: Bill Minozzi
Author URI: http://billminozzi.com
License:     GPL2
Copyright (c) 2015 Bill Minozzi
Antihacker is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
Antihacker is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with Antihacker. If not, see {License URI}.
Permission is hereby granted, free of charge subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/
// ob_start();


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
define('ANTIHACKERVERSION', '2.24' );
define('ANTIHACKERPATH', plugin_dir_path(__file__) );
define('ANTIHACKERURL', plugin_dir_url(__file__));
// Add settings link on plugin page
function antihacker_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=anti-hacker">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'antihacker_plugin_settings_link' );

/* Begin Language */
if(is_admin())
    {
        function ah_localization_init_fail()
        {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<br />';
            echo __('Anti Hacker Plugin: Could not load the localization file (Language file)','antihacker');
            echo '.<br />';
            echo __('Please, take a look in our site, FAQ page, item => How can i translate this plugin?', 'antihacker');
            echo '<br /><br /></div>';
        
        }

    
      if (isset($_GET['page'])) {
        $page = sanitize_text_field($_GET['page']);
        if ($page == 'anti-hacker') 
        {
                  $path = dirname(plugin_basename( __FILE__ )) . '/language/';
                  $loaded = load_plugin_textdomain( 'antihacker', false, $path);
                  if (!$loaded AND get_locale() <> 'en_US') { 
                    
                       if( function_exists('ah_localization_init_fail'))
                         add_action( 'admin_notices', 'ah_localization_init_fail' );
                  }
              }
        }
    } 
else
    {
         add_action( 'plugins_loaded', 'ah_localization_init' );
    }
function ah_localization_init() {
    $path = dirname(plugin_basename( __FILE__ )) . '/language/';
    $loaded = load_plugin_textdomain( 'antihacker', false, $path);
}
/* End language */


require_once (ANTIHACKERPATH . "settings/load-plugin.php");
require_once (ANTIHACKERPATH . "includes/functions/functions.php");
$my_whitelist = trim(get_site_option('my_whitelist',''));
$amy_whitelist = explode(PHP_EOL, $my_whitelist);
$antihackerip = trim(ahfindip());
$ah_admin_email = trim(get_option( 'my_email_to' )); 
$my_radio_all_logins =  get_site_option('my_radio_all_logins', 'No'); // Alert me All Logins
$my_checkbox_all_failed =  get_site_option('my_checkbox_all_failed', '0'); // Alert me all Failed Login Attempts
if(!empty($_POST["myemail"]))
  {$myemail = $_POST["myemail"];}
else
  {$myemail = '';}
require_once (ANTIHACKERPATH . "settings/options/plugin_options_tabbed.php");
$ah_admin_email = trim(get_option( 'my_email_to' ));
if( ! empty($ah_admin_email)) {
    if ( ! is_email($ah_admin_email)) {
        $ah_admin_email = '';
        update_option('my_email_to', '');
    }
}
if(empty($ah_admin_email))
     $ah_admin_email = get_option( 'admin_email' ); 
if (! ah_whitelisted($antihackerip, $amy_whitelist)) {
     add_action('login_form', 'ah_email_display');
     add_action('wp_authenticate_user', 'ah_validate_email_field', 10, 2);
    function ah_validate_email_field($user, $password)
    {
        global $myemail;
        if (!is_email($myemail))
            return new WP_Error('wrong_email', 'Please, fill out the email field!');
        else
           {
                // The Query
                $user_query = new WP_User_Query( array ( 'orderby' => 'registered', 'order' => 'ASC' ) );
                // User Loop
                if ( ! empty( $user_query->results ) ) {
                	foreach ( $user_query->results as $user ) {
                        if(strtolower(trim($user->user_email)) == $myemail )
                                 return $user;
                	}
                } else {
                	// echo 'No users found.';
                }
                    return new WP_Error( 'wrong_email', 'email not found!');
           } 
            return $user;
    }
} /* endif if (! ah_whitelisted($antihackerip, $my_whitelist)) */

add_action('wp_login', 'ah_successful_login');
add_action('wp_login_failed', 'ah_failed_login');
register_deactivation_hook(__FILE__, 'ah_my_deactivation');
register_activation_hook( __FILE__, 'ah_activated' );

if (get_site_option('antihacker_automatic_plugins', 'no') == 'yes') 
  add_filter( 'auto_update_plugin', '__return_true' ); 
if (get_site_option('antihacker_automatic_themes', 'no') == 'yes')
  add_filter( 'auto_update_theme', '__return_true' );
  
if (get_site_option('antihacker_replace_login_error_msg', 'no') == 'yes') 
add_filter( 'login_errors', function( $error ) {
     return '<strong>'.__('Wrong Username or Password', 'antihacker') .'</strong>';
} );

if (get_site_option('antihacker_disallow_file_edit', 'yes') == 'yes') 
  {
    if( ! defined('DISALLOW_FILE_EDIT'))
       define('DISALLOW_FILE_EDIT', true);
  }


if (WP_DEBUG and get_site_option('antihacker_debug_is_true', 'yes') == 'yes')
     add_action( 'admin_notices', 'ah_debug_enabled' );


function antihacker_load_feedback()
{
    if(is_admin())
    {
       // ob_start();
        require_once (ANTIHACKERPATH . "includes/feedback/feedback.php");

        if( get_option('bill_last_feedback', '') != '1')
           require_once (ANTIHACKERPATH . "includes/feedback/feedback-last.php");



    }  // ob_end_clean();
}
add_action( 'wp_loaded', 'antihacker_load_feedback' );

function antihackerplugin_load_activate()
{
    if (is_admin()) {
        require_once (ANTIHACKERPATH . 'includes/feedback/activated-manager.php');
    }
}
add_action('in_admin_footer', 'antihackerplugin_load_activate'); 




//$out = ob_get_clean(); 
      
?>