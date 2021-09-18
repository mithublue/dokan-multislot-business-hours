<?php

namespace DOC\core;

class Product_Actions{

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
    	add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'add_store_notice_badge'] );
    }

    public function add_store_notice_badge() {
    	global $product;
    	$vendor = dokan_get_vendor_by_product( $product );
    	$vendor_info = dokan_get_store_info( $vendor->id );
    	if ( Functions::instance()->is_store_open( $vendor->id ) ) {
    		?>
		    <span class="doc-store-notice doc-store-notice-open"><?php echo esc_attr( $vendor_info['dokan_store_open_notice'] ); ?></span>
<?php
	    } elseif ( ! Functions::instance()->is_store_open( $vendor->id ) ) {
		    ?>
		    <span class="doc-store-notice doc-store-notice-close"><?php echo esc_attr( $vendor_info['dokan_store_close_notice'] ); ?></span>
		    <?php
	    }
    }
}