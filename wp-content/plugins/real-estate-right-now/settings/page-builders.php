<?php 
/**
 * @author Bill Minozzi
 * @copyright 2017
 */
namespace realestate\WP\Settings;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Base class for optons page builders.
 */
class OptionPageBuilder {
    protected $page;
	protected $tabs;
	protected $scripts;
	protected $styles;
	public function __construct (  $page, $scripts = array(), $styles = array() ) 
	{
		// Initialize page and register page action
		$this->page = $page;
		add_action('admin_menu', array($this, 'register_page'));
		// Add user supplied scripts for this page
		$this->scripts = $scripts;
		// Add user supplied stylesheets
		$this->styles = $styles;
		global $pcs_settings_config;
		// Load PCS Settings stylesheet.
		$this->styles[] = array('handle' => 'pcs-admin-settings', 'src'=> $pcs_settings_config['base_uri'] . 'styles/admin-settings.css', 'enqueue' => TRUE);
		$this->styles[] = array('handle' => 'pcs-admin-settings', 'src'=> $pcs_settings_config['base_uri'] . 'styles/admin-settings.css', 'enqueue' => TRUE);



	//	add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
	}
	public function register_page()
	{
           // Global $menu;
       /*
       echo $this->page->parent_slug;
       echo '<br>';
       echo $this->page->slug;
       echo '<br>';
       die();
       */
		switch($this->page->type) {
			case 'menu':
				// TODO: Add icon url and postion configuration values
				add_menu_page( $this->page->title, $this->page->menu_title, $this->page->capability, $this->page->slug, array($this, 'render') );
				$this->page->set_hook('toplevel_page_');
				break;
			case 'submenu':
				add_submenu_page( 
                $this->page->parent_slug,
               // 'admin.php?page=multi-dealer-plugin',
                $this->page->title, 
                $this->page->menu_title, 
                'manage_options', 
                // $this->page->slug, 
                'md_settings1',
                array($this, 'render'
                ) );
				break;
     // add_submenu_page('real_estate_plugin', 'Do Stuff', 'Do Stuff', 'manage_options', 'myplugin-dostuff-page2', 'page_callback22');
        //http://autosellerplugin.com/wp-admin/admin.php?page=real_estate_plugin      
 			case 'submenu2':
               add_submenu_page( 
               // 'edit.php?post_type=products',
               //   'admin.php?page=real_estate_plugin',
              // 'real_estate_plugin', //
              // 'admin.php?page=real_estate_plugin',
              '', // 'NULL,
              // 'realestate/realestate.php',
                'Settings1', 
               'Settings2',  
               'manage_options', 
               'md_settings',
               // 'admin.php?page=real_estate_plugin',
               array($this, 'render')
               );
				break;               
			case 'settings':
				add_options_page( $this->page->title, $this->page->menu_title, $this->page->capability, $this->page->slug, array($this, 'render') );
				$this->page->set_hook('settings_page_');
				break;
			default:
				add_theme_page( $this->page->title, $this->page->menu_title, $this->page->capability, $this->page->slug, array($this, 'render') );
				$this->page->set_hook('appearance_page_');
				break;
		}
	}
	public function admin_enqueue_scripts($page_hook)
	{
		// Only load our scripts on our page
		if($this->page->hook == $page_hook) {
			// Process the Scripts
			foreach($this->scripts as $script) {
				$deps = (isset($script['deps'])) ? $script['deps'] : array();
				if(isset($script['enqueue']) && $script['enqueue'])  {
						if(isset($script['src']) && !wp_script_is( $script['handle'], 'registered' )) {
							wp_register_script( $script['handle'], $script['src'], $deps);
						}
						if(!wp_script_is( $script['handle'], 'enqueued')) {
							wp_enqueue_script($script['handle']);
						}	
				} else {
						if(isset($script['src']) && !wp_script_is( $script['handle'], 'registered' )) {
							wp_register_script( $script['handle'], $script['src'], $script['deps']);
						}
				}
			}
			// Process the Styles
			foreach($this->styles as $style) {
				$deps = (isset($style['deps'])) ? $style['deps'] : array();
				if(isset($style['enqueue']) && $style['enqueue'])  {
						if(isset($style['src']) && !wp_style_is( $style['handle'], 'registered' )) {
							wp_register_style( $style['handle'], $style['src'], $deps);
						}
						if(!wp_style_is( $style['handle'], 'enqueued')) {
							wp_enqueue_style($style['handle']);
						}	
				} else {
						if(isset($style['src']) && !wp_style_is( $style['handle'], 'registered' )) {
							wp_register_style( $style['handle'], $style['src'], $style['deps']);
						}
				}
			}
		}
	}
	public function render()
	{
	{
		do_action('pcs_render_option_page');
		echo '<form method="post" action="options.php">';
		// TODO: only output errors on custom pages
		// settings_errors();
		settings_fields( $this->page->slug );
		do_settings_sections( $this->page->slug );
		submit_button();
		echo '</form>';
		$this->render_reset_form();
		echo $this->page->markup_bottom;
	}
	}
	public function render_reset_form( $active_tab = NULL )
	{
		// echo reset form
		echo '<form method="post" action="' . str_replace( '&settings-updated=true', '', esc_url($_SERVER["REQUEST_URI"] )) . '" class="reset-form">';
		// Reset nonce
		wp_nonce_field( 'pcs_reset_options', 'pcs_reset_options_nonce' );
		echo '<input type="hidden" name="action" value="reset" />';
		if(!is_null($active_tab)) {
			echo '<button type="submit" class="button secondary reset-settings" title="Reset ' . $active_tab->title . '">Reset ' . $active_tab->title . '</button>';
		} else {
			echo '<button type="submit" class="button secondary reset-settings" title="Reset Options">Reset Options</button>';
		}
		echo '</form>';
	}
}
/**
 * Single options page builder
 */
class OptionPageBuilderSingle extends OptionPageBuilder {
	public function __construct ( $page, $section_settings = array(), $scripts = array(), $styles = array() ) 
	{
		parent::__construct( $page, $scripts, $styles );
		new SectionFactory( $page, $section_settings );
	}
}
/**
 * Tabbed options page builder.
 */
class OptionPageBuilderTabbed extends OptionPageBuilder {
	protected $tabs;
	public function __construct ( $page, $options_settings = array(), $scripts = array(), $styles = array() ) 
	{
		parent::__construct( $page, $scripts, $styles );
		$this->tabs = array();
		$counter = 0;
		// Runs when posting to option.php
		// Only create the active tab so the other page sections
		// Do not get overwritten
		$action = sanitize_text_field((isset($_POST['action'])) ? $_POST['action'] : FALSE);
		$page_key = sanitize_text_field((isset($_POST['option_page'])) ? $_POST['option_page'] : FALSE);
		if($page_key == $page->slug && $action == 'update') {
			// Extract the tab id from the referer post
			$referrer = sanitize_text_field((isset($_POST['_wp_http_referer'])) ? $_POST['_wp_http_referer'] : '');
			$matches = array();
			preg_match('/tab=([^&]*)/', $referrer , $matches );
			// Build the Tab Sections for the submitted tab
			foreach( $options_settings as $title=>$section_settings ) {	
				$id = str_replace('-', '_', sanitize_title_with_dashes($title));
				if(isset($matches[1]) && $matches[1] == $id) {
					// Tab submitted was determined
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings, TRUE );
					break;
				}
				// Cache first id for use if no tab match is found
				if($counter == 0) {
					$first = array(
						'id' => $id,
						'title' => $title,
						'settings' => $section_settings
						);
				}
				$counter++;
			}
			// If no tab was created
			// create the default tab with the first id
			if(empty($this->tabs)) {
				$this->tabs[] = new Tab( $first['title'], $first['id'], $this->page, $first['settings'], TRUE );
			}
		} else {
			// Runs when displaying the options page
			// Show the first tab as active by default		
			foreach( $options_settings as $title=>$section_settings ) {	
				$id = str_replace('-', '_', sanitize_title_with_dashes($title));	
				// Each Key Is Tab
				// Set first one to active by default
				if($counter == 0) {
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings, TRUE );
				} else {
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings );
				}
				$counter++;
			}
		}
	}
	public function render()
	{
		$active_tab_id = (isset($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : $this->tabs[0]->id;
		do_action('pcs_render_option_page');
        
        echo $this->page->markup_top;
        echo '<div id="containerleft">'; 
		echo '<form method="post" action="options.php">';
		settings_errors();
		// Output all tab headings
		echo '<h2 class="nav-tab-wrapper">';
		foreach($this->tabs as $tab) {
			// Outbut Tabs
			if( $tab->active ) {
				echo $tab->get_anchor(true);
				// Cache active tab to reneder sections later
				$active_tab = $tab;
			} else {
				echo $tab->get_anchor();	
			}
		}
		echo '</h2>';
		settings_fields( $this->page->slug );
		do_settings_sections( $this->page->slug );
if ($active_tab_id <> 'startup_guide' and $active_tab_id <> 'go_premium' )
  	submit_button();
		echo '</form>';
	//  $this->render_reset_form( $active_tab ); 
   echo '</div>'; //containerleft
   
   
     $when_installed = get_option('bill_installed');
     
     
     $now = time();
     $delta = $now - $when_installed;
     //$delta = $now;
     

     
     
     if ($delta > (3600 * 24 * 7))
     {
        
    
       
       
        echo '<div id="containerright">';
        echo '<ul>';
        echo '<h2>Help & Support</h2>';
        echo '<li><a href="http://RealEstatePlugin.eu/help">OnLine Guide</a></li>';
        echo '<li><a href="http://billminozzi.com/dove/">Support</a></li>';
        echo '</ul>';
        echo '<ul>';
        echo '<h2>Like This Plugin?</h2>';
        _e( 'If you like this product, please write a few words about it. It will help other people find this useful plugin more quickly.<br><b>Thank you!</b>', 'cardealer' ); 
        ?>
        <br /><br />
        <a href="http://RealEstatePlugin.eu/share/" class="button button-medium button-primary"><?php _e( 'Rate or Share', 'cardealer' ); ?></a>
        <?php
        echo '</ul>';
        echo '<ul>';
        $x = rand(1,4);
       //        $banner_image = REALESTATEIMAGES.'/keys_from_left.png';
        if($x == 1){
            echo '<h2>Get Professional Version:<br />Pro Version + Top Features.</h2>';
            // Apple
            echo '<img src="'.REALESTATEIMAGES.'/apple.jpg" width="250" />';
        }
        if($x == 2){
            echo '<h2>Become Pro:<br />Pro Version + Top Features.</h2>';
            // Chave
            echo '<img src="'.REALESTATEIMAGES.'/keys.jpg" width="250" />';
        }
        if($x == 3){
            echo '<h2>Power for Your site: <br />Pro Version + Top Features.</h2>';
            // Leao
            echo '<img src="'.REALESTATEIMAGES.'/lion.jpg" width="250" />';
        }
        if($x == 4){
            echo '<h2>Get Premium Performance: <br />Pro Version + Top Features.</h2>';
            // Corrida
            echo '<img src="'.REALESTATEIMAGES.'/racing.jpg" width="250" />';
        }
        ?>
        <li>More Shortcodes to increase your control over the show room page</li>
        <li>Unlimited Colours Setup to match your site theme</li>
        <li>Dedicated Premium Support</li>
        <li>More...</li>
         <br />
        <a href="http://realestateplugin.eu/premium/" class="button button-medium button-primary" ><?php _e( 'Learn More', 'cardealer' ); ?></a>
        <?php
        echo '</ul>';  
        echo '</div>'; //containerright 
    
    } // if delta...

	echo $this->page->markup_bottom;
 	}       
}