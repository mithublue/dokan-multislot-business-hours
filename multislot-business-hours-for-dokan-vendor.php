<?php
/*
	Plugin Name: Multislot Business Hours for Dokan Vendor
	Plugin URI:
	Description: Maintain Business hours for your WooCommerce Shop. Let your customers know about business schedules.
	Version: 1.0
	Text Domain: doc
	Author: CyberCraft
	Author URI: http://cybercraftit.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace DOC;

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

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    public function __construct() {
    	Store::instance();
    	Widget_Actions::instance();
    	Store_Settings::instance();
    }
}

DOC::instance();