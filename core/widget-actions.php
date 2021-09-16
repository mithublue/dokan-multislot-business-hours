<?php

namespace DOC\core;

class Widget_Actions {

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
	    add_filter( 'dokan_widgets', [ $this, 'register_widgets' ], 10 );
    }
	public function register_widgets( $widgets ) {
		include_once 'StoreOpenClose.php';
		$widgets['doc_store_open_close'] = 'DOC\core\StoreOpenClose';
		return $widgets;
	}
}
