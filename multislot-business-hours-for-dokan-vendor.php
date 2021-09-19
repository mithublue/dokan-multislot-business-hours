<?php
/*
	Plugin Name: Multislot Business Hours for Dokan Vendor - create multiple opening and closing time for vendor's store on each day
	Plugin URI:
	Description: Maintain Business hours for your WooCommerce Shop. Let your customers know about business schedules.
	Version: 1.0.1
	Text Domain: doc
	Author: CyberCraft
	Author URI: http://cybercraftit.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace DOC;

use DOC\core\Functions;
use DOC\core\Product_Actions;
use DOC\core\Store;
use DOC\core\Store_Settings;
use DOC\core\Widget_Actions;

defined( 'ABSPATH' ) || exit;
defined( 'DOKANOPENCLOSE_PLUGIN_URL' ) || define( 'DOKANOPENCLOSE_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'DOKANOPENCLOSE_PLUGIN_DIR' ) || define( 'DOKANOPENCLOSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'DOKANOPENCLOSE_PLUGIN_FILE' ) || define( 'DOKANOPENCLOSE_PLUGIN_FILE', plugin_basename( __FILE__ ) );

if ( !function_exists( 'pri' ) ) {
	function pri( $data ) {
		echo '<pre>';print_r($data);echo '</pre>';
	}
}

spl_autoload_register(function ($class_name) {
	$file = strtolower( str_replace( ['\\','_'], ['/','-'], $class_name ) ).'.php';;
	$filepath =  __DIR__  . '/' . str_replace( 'doc/', '', $file );
	if ( file_exists( $filepath ) ) {
		include_once $filepath;
	}
});

class DOC {

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return ${ClassName} An instance of the class.
     */
    public static function instance() {
    	Product_Actions::instance();
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    public function __construct() {
    	register_activation_hook( __FILE__, [ $this, 'on_active' ] );
	    add_filter( 'plugin_action_links_multislot-business-hours-for-dokan-vendor/multislot-business-hours-for-dokan-vendor.php', [ $this, 'plugin_links' ] );
    	include_once 'core/promo.php';
    	Store::instance();
    	Widget_Actions::instance();
    	Store_Settings::instance();
    }

	function plugin_links( $links ) {
    	//Rate Us
		$url = '<a style="color: #DD5E3B;font-weight: bold;" href="https://wordpress.org/support/plugin/multislot-business-hours-for-dokan-vendor/reviews/#new-post" target="_blank" class="help-link">'.__( 'Rate', 'doc' ).'</a>';
		array_push(
			$links,
			$url
		);
		//Feature Request
		$url = '<a style="color: #DD5E3B;font-weight: bold;" href="https://cybercraftit.com/contact" target="_blank" class="help-link">'.__( 'Request for feature', 'doc').'</a>';
		array_push(
			$links,
			$url
		);
		//report issue
		$url = '<a style="color: #DD5E3B;font-weight: bold;" href="https://github.com/mithublue/dokan-multislot-business-hours/issues/new" target="_blank" class="help-link">'.__( 'Report issue', 'doc' ).'</a>';
		array_push(
			$links,
			$url
		);
		//get pro
		if ( ! Functions::instance()->is_pro() ) {
			$url = '<a style="background: #DD5E3B; color: #ffffff; padding: 0 10px 1px 10px; font-weight: bold;" href="https://cybercraftit.com/product/dokan-multislot-business-hours-for-vendors/" target="_blank" class="help-link">'.__( 'GET PRO', 'doc' ).'</a>';
			array_push(
				$links,
				$url
			);
		}

		return $links;
	}

	public function on_active() {
		$dokan_appearance = get_option( 'dokan_appearance' );
		!is_array( $dokan_appearance ) ? $dokan_appearance = [] : '';
		$dokan_appearance['store_open_close'] = 'off';
		$dokan_appearance['doc_multislot_time_enabled'] = 'on';
		update_option( 'dokan_appearance', $dokan_appearance );
	}
}

DOC::instance();