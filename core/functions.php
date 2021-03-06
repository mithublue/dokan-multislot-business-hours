<?php
namespace DOC\core;

use DOCPRO\DOC_Pro;

class Functions{

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

	function is_store_open( $user_id, $store_info = null ) {
		if ( ! $store_info ) {
			$store_user = dokan()->vendor->get( $user_id );
			$store_info = $store_user->get_shop_info();
		}
		$open_days  = isset( $store_info['dokan_store_time'] ) ? $store_info['dokan_store_time'] : '';

		$current_time = dokan_current_datetime();
		$today        = strtolower( $current_time->format( 'l' ) );

		if ( ! isset( $open_days[ $today ] ) ) {
			return false;
		}

		$schedule = $open_days[ $today ];
		$status   = isset( $schedule['open'] ) ? $schedule['open'] : $schedule['status'];

		$is_filled_data = 0;

		if ( 'open' === $status ) {

			foreach ( $schedule['opening_time'] as $key => $time ) {
				if ( $time ) $is_filled_data++;
				$opening_key = $key;
				$closing_key = $key;
				$open  = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule['opening_time'][$opening_key], new \DateTimeZone( dokan_wp_timezone_string() ) );
				$close = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule['closing_time'][$closing_key], new \DateTimeZone( dokan_wp_timezone_string() ) );
				if ( $open <= $current_time && $close >= $current_time ) {
					return true;
				}
			}

			if ( ! $is_filled_data ) {
				return true;
			}
		}

		return false;
	}

	public function get_time_ranges_string( $schedule ) {
		$is_filled_data = 0;
		$str = '';
    	if ( 'open' === $schedule['status'] ) {
			foreach ( $schedule['opening_time'] as $key => $time ) {
				if ( $time ) $is_filled_data++;
				$opening_key = $key;
				$closing_key = $key;
				$open  = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule['opening_time'][$opening_key], new \DateTimeZone( dokan_wp_timezone_string() ) );
				$close = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule['closing_time'][$closing_key], new \DateTimeZone( dokan_wp_timezone_string() ) );
				$open = $open ? date( 'H:i:s', $open->getTimestamp() ) : '';
				$close = $close ? date('H:i:s', $close->getTimestamp() ) : '';
				$str .= $open . '-' . $close.'/';
			}
		}

		return $str = trim( $str, '/' );
	}

	public function is_pro() {
    	if ( class_exists( DOC_Pro::class  )) {
    		return true;
		}
		return false;
	}
}