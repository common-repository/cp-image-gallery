<?php
/*
Plugin Name: CP Image Gallery
Plugin URI: https://wordpress.org/plugins/cp-image-gallery
Description: Easy way to add gallery with images using run time resizer feature as well as with SEO friendly gallery url and display all galleries at one place. 
Version: 1.0.1
Author: Commerce Pundit
Author URI: http://www.commercepundit.com/
Text Domain: cp-image-gallery
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/
// Create a helper function for easy SDK access.
function cig_fs() {
    global $cig_fs;

    if ( ! isset( $cig_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $cig_fs = fs_dynamic_init( array(
            'id'                => '466',
            'slug'              => 'cp-image-gallery',
            'type'              => 'plugin',
            'public_key'        => 'pk_0c44b918c344ee5077308d01fb51f',
            'is_premium'        => false,
            'has_addons'        => false,
            'has_paid_plans'    => false,
            'menu'              => array(
                'slug'       => 'cp-image-gallery',
                'account'    => false,
                'contact'    => false,
                'support'    => false,
            ),
        ) );
    }

    return $cig_fs;
}

// Init Freemius.
cig_fs();
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CP_Image_Gallery' ) ) {

	/**
	 * PHP5 constructor method.
	 *
	 * @since 1.0
	*/
	class CP_Image_Gallery {

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'plugins_loaded', array( $this, 'constants' ));
			add_action( 'plugins_loaded', array( $this, 'includes' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cp_gallery_action_links' );
            register_activation_hook( __FILE__,array( $this, 'cp_gallery_activate' ));    
		}


		/**
		 * Internationalization
		 *
		 * @since 1.0
		*/
		public function load_textdomain() {
			load_plugin_textdomain( 'cp-image-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Constants
		 *
		 * @since 1.0
		*/
		public function constants() {

			if ( !defined( 'CP_GALLERY_PLUGIN_DIR' ) )
				define( 'CP_GALLERY_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			if ( !defined( 'CP_GALLERY_PLUGIN_URL' ) )
			    define( 'CP_GALLERY_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			if ( ! defined( 'CP_GALLERY_PLUGIN_VER' ) )
			    define( 'CP_GALLERY_PLUGIN_VER', '1.0.0' );

			if ( ! defined( 'CP_GALLERY_INC_PATH' ) )
			    define( 'CP_GALLERY_INC_PATH', CP_GALLERY_PLUGIN_DIR . trailingslashit( 'inc' ) );

		}

		/**
		* Loads the initial files needed by the plugin.
		*
		* @since 1.0
		*/
		public function includes() {

			require_once( CP_GALLERY_INC_PATH . 'template-functions.php' );
			require_once( CP_GALLERY_INC_PATH . 'scripts.php' );
			require_once( CP_GALLERY_INC_PATH . 'metabox.php' );
			require_once( CP_GALLERY_INC_PATH . 'admin-page.php' );
            require_once( CP_GALLERY_INC_PATH . 'cp-gallery.php' );
		}
        
        public function cp_gallery_activate() {
            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/cpgallery-cache';
            if (! is_dir($upload_dir)) {
                mkdir( $upload_dir, 0700 );
                if ((!is_dir($upload_dir)) || (!is_writable($upload_dir))) {
                    echo '<div class="updated notice is-dismissible" id="message">
                    <p>Pls Check your Upload directory File Permissions.</p>
                    <button class="notice-dismiss" type="button">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                    </button></div>';
                    return;
                }
            }
    }

	}
        

}

$cp_image_gallery = new CP_Image_Gallery();
