<?php /**
 * @author Bill Minozzi
 * @copyright 2017
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action( 'wp_loaded', 'realestate_get_locations' );
function realestate_get_locations()
{
    global $wpdb;
    $re_locations = array();  
    $args = array(
        'taxonomy'               => 'locations',
        'orderby'                => 'name',
        'order'                  => 'ASC',
        'hide_empty'             => false,
    );
    $the_query = new WP_Term_Query($args);
    foreach($the_query->get_terms() as $term){ 
       $re_locations[] = $term->name;
    }
 return $re_locations; 
}
function realestate_findglooglemap()
{
 global $wpdb;
        $argsfindfields = array(
            'post_status' => 'publish',
            'post_type' => 'realestatefields'
        );
        query_posts( $argsfindfields );
        $afields = array();
        $afieldsid = array();
        $Mapfield_name = '';
        while ( have_posts() ) : the_post();
            $post_id = esc_attr(get_the_ID());
            $Mapfield_name = get_the_title($post_id);
            $field_type = esc_attr(get_post_meta($post_id, 'field-typefield', true));
            if($field_type  == 'googlemap')
              {
                if (!empty ($Mapfield_name) )
                  return 'product-'.$Mapfield_name;
              }
           //   break;
        endwhile;
           return '';
}
function realestate_get_fields($type)
{
  global $wpdb;
   if(!function_exists('get_userdata()')) {
    include(ABSPATH . "/wp-includes/pluggable.php");
   }
    if ( $type == 'search')
    {
    $args = array(
            'post_status' => 'publish',
            'post_type' => 'realestatefields',
            'meta_key' => 'field-order',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
            array(
            'key' => 'field-searchbar',
            'value' => '1'
            )
        )
    );
    }
    elseif($type == 'all')
    {
    $args = array(
            'post_status' => 'publish',
            'post_type' => 'realestatefields',
            'meta_key' => 'field-order',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        );
    }
    elseif ( $type == 'widget')
    {
    $args = array(
            'post_status' => 'publish',
            'post_type' => 'realestatefields',
            'meta_key' => 'field-order',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
            array(
            'key' => 'field-searchwidget',
            'value' => '1'
            )
        )
    );
    }    
        query_posts( $args );
        $afields = array();
        $afieldsid = array();
        while ( have_posts() ) : the_post();
            $afieldsid[] = esc_attr(get_the_ID());
        endwhile;
        ob_start();
        wp_reset_query();
        ob_end_clean();       
         return $afieldsid;  
} // end Funcrions
function realestate_get_meta($post_id)
{
    $fields = array(
        'field-label',
        'field-typefield',
        'field-drop_options',
        'field-searchbar',
        'field-searchwidget',
        'field-rangemin',
        'field-rangemax',
        'field-rangestep',
        'field-slidemin',
        'field-slidemax',
        'field-slidestep',  
        'field-order',
        'field-name');
    $tot = count($fields);
    for ($i = 0; $i < $tot; $i++) {
        $field_value[$i] = esc_attr(get_post_meta($post_id, $fields[$i], true));
    }
    $field_value[$tot-1] = esc_attr(get_the_title($post_id));
    return $field_value;
}
function realestate_get_types()
{
    global $wpdb;
    $productmake = array();  
    $args = array(
        'taxonomy'               => 'agents',
        'orderby'                => 'name',
        'order'                  => 'ASC',
        'hide_empty'             => false,
    );
    $the_query = new WP_Term_Query($args);
    $productmake = array();  
    foreach($the_query->get_terms() as $term){ 
       $productmake[] = $term->name;
    }
 return $productmake; 
}
function realestate_get_max()
{
    global $wpdb;
    // $afilter = array();
    if(isset($_GET['meta_purpose']))  
      {
          $purpose = sanitize_text_field($_GET['meta_purpose']);          
          $purpose = __($purpose, 'realestate');
          if(!empty($purpose))
            $afilter[] = array('key' => 'product-purpose', 'value' => $purpose);
          else
           $afilter[] = array('key' => 'product-purpose', 'value' => __('Rent', 'realestate'));
      }
    else
          $afilter[] = array('key' => 'product-purpose', 'value' => __('Rent', 'realestate'));
    // }  
    $args = array(
        'numberposts' => 1,
        'post_type' => 'products',
        'meta_key' => 'product-price',
        'orderby' => 'meta_value_num',
        'meta_query' => $afilter,
        'order' => 'DESC');
    $posts = get_posts($args);
    foreach ($posts as $post) {
        $x = get_post_meta($post->ID, 'product-price', true);
        if (!empty($x)) {
            $x = (int)$x;
            if (is_int($x)) {
                $x = ($x) * 1.2;
                $x = round($x, 0, PHP_ROUND_HALF_EVEN);
                //return $x;
            }
        }
        if($x < 1)
          return '100000';
        else
          return $x;
    }
}
add_action( 'wp_loaded', 'realestate_get_types' );
function realestate_currency()
{
    if (get_option('RealEstatecurrency') == 'Dollar') {
        return "$";
    }
    if (get_option('RealEstatecurrency') == 'Pound') {
        return "&pound;";
    }
    if (get_option('RealEstatecurrency') == 'Yen') {
        return "&yen;";
    }
    if (get_option('RealEstatecurrency') == 'Euro') {
        return "&euro;";
    }
    if (get_option('RealEstatecurrency') == 'Universal') {
        return "&curren;";
    }
    if (get_option('RealEstatecurrency') == 'AUD') {
        return "AUD";
    }
    if (get_option('RealEstatecurrency') == 'Real') {
        return "$R";
    }
     if (get_option('RealEstatecurrency') == 'Krone') {
        return "kr";
    }    
    if (get_option('RealEstatecurrency') == 'Forint') {
        return "Ft"; /* Ft or HUF is also perfect for me. */ 
    }  
// R (for ZAR) our currency - Afric Sul
    if (get_option('RealEstatecurrency') == 'Zar') {
        return "R"; /* Ft or HUF is also perfect for me. */ 
    } 
    if (get_option('RealEstatecurrency') == 'Swiss') {
        return "CHF "; 
    }
}
function RealEstate_localization_init_fail()
{
    echo '<div class="error notice">
                     <br />
                     realestatePlugin: Could not load the localization file (Language file).
                     <br />
                     Please, take a look the online Guide item Plugin Setup => Language.
                     <br /><br />
                     </div>';
}
function RealEstate_Show_Notices1()
            {
                    echo '<div class="update-nag notice"><br />';
                    echo 'Warning: Upload directory not found (RealEstate Plugin). Enable debug for more info.';
                    echo '<br /><br /></div>';
            }
function RealEstate_plugin_was_activated()
{
                echo '<div class="updated"><p>';
                $bd_msg = '<img src="'.REALESTATEURL.'assets/images/infox350.png" />';
                $bd_msg .= '<h2>RealEstate Plugin was activated! </h2>';
                $bd_msg .= '<h3>For details and help, take a look at Real Estate Dashboard at your left menu <br />';
                $bd_url = '  <a class="button button-primary" href="admin.php?page=real_estate_plugin">or click here</a>';
                $bd_msg .=  $bd_url;
                echo $bd_msg;
                echo "</p></h3></div>";
     $Multidealerplugin_installed = trim(get_option( 'Multidealerplugin_installed',''));
     if(empty($Multidealerplugin_installed)){
        add_option( 'Multidealerplugin_installed', time() );
        update_option( 'Multidealerplugin_installed', time() );
     }
} 
if( is_admin())
{
   if(get_option('RealEstate_activated', '0') == '1')
   {
     add_action( 'admin_notices', 'RealEstate_plugin_was_activated' );
     $r =  update_option('RealEstate_activated', '0'); 
     if ( ! $r )
        add_option('RealEstate_activated', '0');
   }
} 
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
add_filter( 'plugin_row_meta', 'realestate_custom_plugin_row_meta', 10, 2 );
function realestate_custom_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'realestate.php' ) !== false ) {
		$new_links = array(
				'OnLine Guide' => '<a href="http://realestateplugin.eu/guide/" target="_blank">OnLine Guide</a>',
                                'Pro' => '<a href="http://realestateplugin.eu/premium/" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>'
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
function realestate_get_page()
{
  $page = 1;
  $url = esc_url($_SERVER['REQUEST_URI']);
  $pieces = explode("/", $url);
  for ($i=0; $i < count($pieces); $i++)
  {
    if ($pieces[$i] == 'page' and ($i+1) <  count($pieces))
      {
          $page = $pieces[$i+1];
          if(is_numeric($page))
             return $page;
      }
  }
  return $page;
}
function RealEstate_wrong_permalink()
{
    echo '<div class="notice notice-warning">
                     <br />
                     Real Estate Plugin: Wrong Permalink settings !
                     <br />
                     Please, fix it to avoid 404 error page.
                     <br />
                     To correct, just follow this steps:
                     <br />
                     Dashboard => Settings => Permalinks => Post Name (check)
                     <br />  
                     Click Save Changes
                     <br /><br />
                     </div>';
}
$realestateurl = esc_url($_SERVER['REQUEST_URI']);
if (strpos($realestateurl, '/options-permalink.php') === false)
{            
  $permalinkopt  = get_option('permalink_structure');
  if($permalinkopt != '/%postname%/')
    add_action( 'admin_notices', 'RealEstate_wrong_permalink' );
}
/////////////
function realestaterightnow_ask_for_upgrade()
 { 
    $x = rand(0,1);
    if ($x == 0)
       $banner_image = REALESTATEIMAGES.'/introductory.png';
    else
       $banner_image = REALESTATEIMAGES.'/keys_from_left.png';
    echo '<script type="text/javascript" src="' .REALESTATEURL .
            'assets/js/c_o_o_k_i_e.js' . '"></script>';
    ?>
	<script type="text/javascript">
        jQuery(document).ready(function() {
        	var hide_message = jQuery.cookie('bill_go_pro_hide');
/*   hide_message = false;  */
        	if (hide_message == "true") {
        		jQuery(".bill_go_pro_container").css("display", "none");
        	} else {
        		jQuery(".bill_go_pro_container").css("display", "block");
        	};
        	jQuery(".bill_go_pro_close_icon").click(function() {
        		jQuery(".bill_go_pro_message").css("display", "none");
        		jQuery.cookie("bill_go_pro_hide", "true", {
        			expires: 15
        		});
        		jQuery(".bill_go_pro_container").css("display", "none");
        	});
        	jQuery(".bill_go_pro_dismiss").click(function(event) {
        		jQuery(".bill_go_pro_message").css("display", "none");
        		jQuery.cookie("bill_go_pro_hide", "true", {
        			expires: 15
        		});
        		event.preventDefault()
        		jQuery(".bill_go_pro_container").css("display", "none");
        	});
        }); // end (jQuery);
	</script>
    <style type="text/css">
            .bill_go_pro_close_icon {
            width:31px;
            height:31px;
            border: 0px solid red;
            /* background: url("http://xxxxxx.com/wp-content/plugins/realestate/assets/images/close_banner.png") no-repeat center center; */
            box-shadow:none;
            float:right;
            margin:8px;
            margin:60px 40px 8px 8px;
            }
            .bill_hide_settings_notice:hover,.bill_hide_premium_options:hover {
            cursor:pointer;
            }
            .bill_hide_premium_options {
            position:relative;
            }
            .bill_go_pro_image {
            float:left;
            margin-right:20px;
            max-height:90px !important;
            }
            .bill_image_go_pro {
            max-width:200px;
            max-height:88px;
            }
            .bill_go_pro_text {
            font-size:18px;
            padding:10px;
            }
            .bill_go_pro_button_primary_container {
            float:left;
            margin-top: 0px;
            }
            .bill_go_pro_dismiss_container
            {
              margin-top: 0px;
            }
            .bill_go_pro_buttons {
              display: flex;
              max-height: 30px;
              margin-top: -10px;
            }        
            .bill_go_pro_container {
                border:1px solid darkgray;
                height:90px;
                padding: 0; 
                margin: 0; 
                background: white;
            }
            .bill_go_pro_dismiss {
              margin-left:15px !important;
            }
             .button {
                vertical-align: top;
            }           
            @media screen and (max-width:900px) {
                .bill_go_pro_text {
                  font-size:16px;
                  padding:5px;
                  margin-bottom: 10px;
                }
            }
            @media screen and (max-width:800px) {
                .bill_go_pro_container {
                  display:none !important;
                }
            }
	</style>
    <div class="notice notice-success bill_go_pro_container" style="display: none;">
    	<div class="bill_go_pro_message bill_banner_on_plugin_page bill_go_pro_banner">
    		<button class="bill_go_pro_close_icon close_icon notice-dismiss bill_hide_settings_notice" title="<?php _e('Close notice',
    		'real-estate-right-now'); ?>">
    		</button>
    		<div class="bill_go_pro_image">
    			<img class="bill_image_go_pro" title="" src="<?php echo $banner_image;?>" alt="" />
    		</div>
    		<div class="bill_go_pro_text">
    			<?php _e( 'It is time to upgrade your', 'real-estate-right-now'); ?>
    				<strong>
    					Real Estate Plugin
    				</strong>
    				<?php _e( 'to', 'real-estate-right-now'); ?>
    					<strong>
    						Pro
    					</strong>
    					<?php _e( 'version!', 'real-estate-right-now'); ?>
    						<br />
    						<span>
    							<?php _e( 'Extend standard plugin functionality with new great options.', 'real-estate-right-now'); ?>
    						</span>
    		</div>
            <div class="bill_go_pro_buttons">
        		<div class="bill_go_pro_button_primary_container">
        			<a class="button button-primary" target="_blank" href="http://realestateplugin.eu/premium/"><?php _e('Learn More',
        			'real-estate-right-now'); ?></a>
        		</div>
        		<div class="bill_go_pro_dismiss_container">
        			<a class="button button-secondary bill_go_pro_dismiss" target="_blank" href="http://realestateplugin.eu/premium/"><?php _e('Dismiss',
        			'real-estate-right-now'); ?></a>
        		</div>
            </div>
    	</div>
    </div>
<?php               
 } // end Bill ask for upgrade 
 $when_installed = get_option('bill_installed');
 $now = time();
 $delta = $now - $when_installed;
 if ($delta > (3600 * 24 * 8))
 {
    $realestateurl = esc_url($_SERVER['REQUEST_URI']);
    if (strpos($realestateurl, 'post_type=products') !== false or strpos($realestateurl, 'post_type=realestatefields') !== false )
       if (strpos($realestateurl, 'settings') === false)
          add_action( 'admin_notices', 'realestaterightnow_ask_for_upgrade' );
 }
function RealEstate_add_admin_files()
{
   wp_enqueue_style('pluginStyleAdmin', REALESTATEURL . 'settings/styles/admin-settings.css');    
}
add_action('admin_enqueue_scripts', 'RealEstate_add_admin_files');