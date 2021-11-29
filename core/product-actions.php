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
    	add_action( 'dokan_seller_listing_footer_content', [ $this, 'add_store_notice_badge_in_store_list'], 10, 2 );
	    add_action( 'pre_get_posts', [ $this, 'pre_get_posts']);
	    add_filter( 'posts_fields', [ $this, 'posts_fields' ], 10, 2 );
	    add_filter( 'posts_join', [ $this, 'posts_join' ], 10, 2 );
	    add_filter( 'posts_where', [ $this, 'posts_where' ], 10, 2 );
	    add_action( 'the_post', [ $this, 'reset_post_keys' ]);
	    add_action( 'woocommerce_after_shop_loop_item', [ $this, 'show_store_notice' ] );
    }

	function pre_get_posts( $q ) {
		if ( !is_archive() && !is_shop() ) return;
		//$q->suppress_filters = false;
	}

	function posts_fields( $fields, $wp_query ) {
		if ( !is_archive() && !is_shop() ) return $fields;
		global $wpdb;
		$fields .= ",usermeta.meta_value as dokan_profile_settings";
		return $fields;
	}

	function posts_join( $join, $wp_query ) {
		if ( !is_archive() && !is_shop() ) return $join;
		global $wpdb;
		$join .= " JOIN {$wpdb->prefix}usermeta as usermeta on usermeta.user_id = {$wpdb->posts}.post_author ";
		return $join;
	}

	function posts_where( $where, $wp_query ) {
		if ( !is_archive() && !is_shop() ) return $where;
		$where .= " AND usermeta.meta_key = 'dokan_profile_settings'";
		return $where;
	}

	function reset_post_keys() {
		if ( !is_archive() && !is_shop() ) return;
		global $doc_fetched_posts,$posts;
		if ( is_array( $doc_fetched_posts ) ) return;
		$doc_fetched_posts = [];
		foreach ( $posts as $k => $each ) {
			$doc_fetched_posts[$each->ID] = $each;
		}
	}

	function show_store_notice() {
		global $doc_fetched_posts,$product;
		if ( !is_array( $doc_fetched_posts ) ) return;
		if ( isset( $doc_fetched_posts[$product->get_id()] ) ) {
			$profile_settings = unserialize($doc_fetched_posts[$product->get_id()]->dokan_profile_settings);
			if ( is_array( $profile_settings ) ) {
				if ( Functions::instance()->is_store_open( $doc_fetched_posts[$product->get_id()]->post_author, $profile_settings ) ) {
					?>
					<div>
						<span class="doc-store-notice doc-store-notice-open"><?php echo esc_attr( $profile_settings['dokan_store_open_notice'] ); ?></span>
					</div>
					<?php
				} elseif ( ! Functions::instance()->is_store_open( $doc_fetched_posts[$product->get_id()]->post_author, $profile_settings ) ) {
					?>
					<div>
						<span class="doc-store-notice doc-store-notice-close"><?php echo esc_attr( $profile_settings['dokan_store_close_notice'] ); ?></span>
					</div>
					<?php
				}
			}
		}
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

    public function add_store_notice_badge_in_store_list( $seller, $store_info ) {
	    if ( Functions::instance()->is_store_open( $seller->ID ) ) {
		    ?>
		    <span class="doc-store-notice doc-store-notice-open"><?php echo esc_attr( $store_info['dokan_store_open_notice'] ); ?></span>
		    <?php
	    } elseif ( ! Functions::instance()->is_store_open( $seller->ID ) ) {
		    ?>
		    <span class="doc-store-notice doc-store-notice-close"><?php echo esc_attr( $store_info['dokan_store_close_notice'] ); ?></span>
		    <?php
	    }
    }
}