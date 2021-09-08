<?php
namespace DOC\core;

class Store{

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
    	add_action( 'dokan_store_header_info_fields', [ $this, 'add_header_info' ] );
    }

    public function add_header_info ( $store_id ) {
	    $store_user               = dokan()->vendor->get( get_query_var( 'author' ) );
	    $store_info               = $store_user->get_shop_info();
	    $show_store_open_close    = dokan_get_option( 'doc_multislot_time_enabled', 'dokan_appearance', 'on' );
	    $dokan_store_time_enabled = isset( $store_info['dokan_store_time_enabled'] ) ? $store_info['dokan_store_time_enabled'] : '';
	    $store_open_notice        = isset( $store_info['dokan_store_open_notice'] ) && ! empty( $store_info['dokan_store_open_notice'] ) ? $store_info['dokan_store_open_notice'] : __( 'Store Open', 'dokan-lite' );
	    $store_closed_notice      = isset( $store_info['dokan_store_close_notice'] ) && ! empty( $store_info['dokan_store_close_notice'] ) ? $store_info['dokan_store_close_notice'] : __( 'Store Closed', 'dokan-lite' );
	    ?>
	    <?php if ( $show_store_open_close == 'on' && $dokan_store_time_enabled == 'yes') : ?>
		    <li class="dokan-store-open-close">
			    <i class="fa fa-shopping-cart"></i>
			    <?php
			    if ( Functions::instance()->is_store_open( $store_id ) ) {
				    echo esc_attr( $store_open_notice );
			    } else {
				    echo esc_attr( $store_closed_notice );
			    } ?>
		    </li>
	    <?php endif ?>
<?php
    }
}