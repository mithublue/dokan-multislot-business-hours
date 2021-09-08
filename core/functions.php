<?php
namespace DOC\core;

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

    public function __construct() {

    }

	function is_store_open( $user_id ) {
		$store_user = dokan()->vendor->get( $user_id );
		$store_info = $store_user->get_shop_info();
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
			foreach ( $schedule as $key => $time ) {
				if ( strpos( $key, 'opening_time' ) !== false ) {
					if ( $time ) $is_filled_data++;
					$time_key = (int) filter_var( $key, FILTER_SANITIZE_NUMBER_INT);
					$time_key = ( $time_key ? '_'.$time_key : '' );
					$opening_key = 'opening_time'. $time_key;
					$closing_key = 'closing_time' . $time_key;
					$open  = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule[$opening_key], new \DateTimeZone( dokan_wp_timezone_string() ) );
					$close = \DateTimeImmutable::createFromFormat( esc_attr( get_option( 'time_format' ) ), $schedule[$closing_key], new \DateTimeZone( dokan_wp_timezone_string() ) );

					if ( $open <= $current_time && $close >= $current_time ) {
						return true;
					}
				}
			}

			if ( ! $is_filled_data ) {
				return true;
			}
		}

		return false;
	}
}