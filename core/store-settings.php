<?php
namespace DOC\core;

class Store_Settings{

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
	    add_filter( 'dokan_save_settings_value', [ $this, 'modify_admin_option_value' ], 10, 2 );
        add_filter( 'dokan_settings_fields', [ $this, 'add_admin_fields' ], 10, 2 );
	    add_action( 'dokan_settings_form_bottom', [ $this, 'settings_form_custom_fields' ], 10, 2 );
	    add_action( 'dokan_store_profile_saved', [ $this, 'save_settings_fields' ], 15, 2 );
    }

	/**
	 * @param $option_value
	 * @param $option_name
	 *
	 * @return array
	 */
    public function modify_admin_option_value( $option_value, $option_name ) {
	    if ( $option_name == 'dokan_appearance' ) {
	        if ( is_array( $option_value ) && isset( $option_value['store_open_close'] ) && $option_value['store_open_close'] == 'on'
            && isset( $option_value['doc_multislot_time_enabled'] ) && $option_value['doc_multislot_time_enabled'] == 'on'
            ) {
	            $option_value['store_open_close'] = 'off';
            }
        }
        return $option_value;
    }

	/**
	 * @param $store_id
	 * @param $dokan_settings
	 */
    function save_settings_fields ( $store_id, $dokan_settings ) {
	    $dokan_settings = dokan_get_store_info($store_id);
		$dokan_days = [ 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];

		foreach ( $dokan_days as $k => $day ) {
		    for ( $i = 1; $i <= 2; $i++ ) {
		        $key = $i == 1 ? '' : '_'.$i;
			    $saved_opening_key = 'opening_time'.$key;
			    $saved_closing_key = 'closing_time'.$key;
			    $opening_key = $day.'_'.$saved_opening_key;
			    $closing_key = $day.'_'.$saved_closing_key;

			    if ( isset( $dokan_settings['dokan_store_time'][$day]['status'] ) ) {
				    if ( $dokan_settings['dokan_store_time'][$day]['status'] == 'open' ) {
					    if ( isset( $_POST[$opening_key] ) ) {
						    $dokan_settings['dokan_store_time'][$day][$saved_opening_key] = $_POST[$opening_key];
					    }
					    if ( isset( $_POST[$closing_key] ) ) {
						    $dokan_settings['dokan_store_time'][$day][$saved_closing_key] = $_POST[$closing_key];
					    }
				    }
			    }
            }
        }

		update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
	}

	/**
	 * @param $settings
	 *
	 * @return mixed
	 */
	function add_admin_fields( $settings_fields, $settings ) {
		//$show_store_open_close = dokan_get_option( 'store_open_close', 'dokan_appearance', 'on' );
		$settings_fields['dokan_appearance']['doc_multislot_time_enabled'] = [
			'name'    => 'doc_multislot_time_enabled',
			'label'   => __( 'Enable Multislot Store Opening Closing Time Widget', 'doc' ),
			'desc'    => __( 'Enable multislot store opening & closing time widget in the store sidebar provided by Dokan Open Closed Plugin.', 'doc' ),
			'type'    => 'checkbox',
			'default' => 'off',
		];
		return $settings_fields;
	}

	public function settings_form_custom_fields( $current_user, $profile_info ) {
		$show_store_open_close    = dokan_get_option( 'doc_multislot_time_enabled', 'dokan_appearance', 'on' );
		$dokan_days               = [ 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];
		$dokan_store_time_enabled = isset( $profile_info['dokan_store_time_enabled'] ) ? $profile_info['dokan_store_time_enabled'] : '';
		$all_times                = isset( $profile_info['dokan_store_time'] ) ? $profile_info['dokan_store_time'] : '';
		if ( $show_store_open_close == 'on' ) { ?>
			<div class="dokan-form-group store-open-close-time">
				<label class="dokan-w3 dokan-control-label" for="dokan-store-close">
					<?php esc_html_e( 'Store Opening Closing Time', 'doc' ); ?>
				</label>

				<div class="dokan-w5 dokan-text-left dokan_tock_check">
					<div class="checkbox">
						<label for="dokan-store-time-enable" class="control-label">
							<input type="checkbox" name="dokan_store_time_enabled" id="dokan-store-time-enable" value="yes"
								<?php echo $dokan_store_time_enabled == 'yes' ? 'checked' : ''; ?>>
							<?php esc_html_e( 'Show store opening closing time widget in store page', 'doc' ); ?>
						</label>
					</div>
				</div>
			</div>

			<div class="dokan-form-group store-open-close">
				<label class="dokan-w3 control-label"></label>
				<div class="dokan-w6" style="width: auto">
					<?php foreach ( $dokan_days as $key => $day ) { ?>
						<?php
						$status = isset( $all_times[$day]['status'] ) ? $all_times[$day]['status'] : '';
						$status = isset( $all_times[$day]['open'] ) ? $all_times[$day]['open'] : $status;
						?>
						<div class="dokan-form-group">
							<label class="day control-label" for="<?php echo esc_attr( $day ); ?>-opening-time">
								<?php echo esc_html( dokan_get_translated_days( $day ) ); ?>
							</label>
							<label for="">
								<select name="<?php echo esc_attr( $day ); ?>_on_off" class="dokan-on-off dokan-form-control">
									<option value="close" <?php ! empty( $status ) ? selected( $status, 'close' ) : ''; ?> >
										<?php esc_html_e( 'Close', 'doc' ); ?>
									</option>
									<option value="open" <?php ! empty( $status ) ? selected( $status, 'open' ) : ''; ?> >
										<?php esc_html_e( 'Open', 'doc' ); ?>
									</option>
								</select>
							</label>
							<div>
								<label for="opening-time" class="time" style="visibility: <?php echo isset( $status ) && $status == 'open' ? 'visible' : 'hidden'; ?>" >
									<input type="text" class="dokan-form-control" name="<?php echo esc_attr( strtolower( $day ) ); ?>_opening_time" id="<?php echo esc_attr( $day ); ?>-opening-time" placeholder="<?php echo esc_attr( date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ) ); ?>" value="<?php echo isset( $all_times[$day]['opening_time'] ) ? esc_attr( $all_times[$day]['opening_time'] ) : ''; ?>" >
								</label>
								<label for="closing-time" class="time" style="visibility: <?php echo isset( $status ) && $status == 'open' ? 'visible' : 'hidden'; ?>" >
									<input type="text" class="dokan-form-control" name="<?php echo esc_attr( $day ); ?>_closing_time" id="<?php echo esc_attr( $day ); ?>-closing-time" placeholder="<?php echo esc_attr( date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ) ); ?>" value="<?php echo isset( $all_times[$day]['closing_time'] ) ? esc_attr( $all_times[$day]['closing_time'] ) : ''; ?>">
								</label>
							</div>
							<!---->
							<div>
								<label for="opening-time-2" class="time" style="visibility: <?php echo isset( $status ) && $status == 'open' ? 'visible' : 'hidden'; ?>" >
									<input type="text" class="dokan-form-control" name="<?php echo esc_attr( strtolower( $day ) ); ?>_opening_time_2" id="<?php echo esc_attr( $day ); ?>-opening-time-2" placeholder="<?php echo esc_attr( date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ) ); ?>" value="<?php echo isset( $all_times[$day]['opening_time_2'] ) ? esc_attr( $all_times[$day]['opening_time_2'] ) : ''; ?>" >
								</label>
								<label for="closing-time-2" class="time" style="visibility: <?php echo isset( $status ) && $status == 'open' ? 'visible' : 'hidden'; ?>" >
									<input type="text" class="dokan-form-control" name="<?php echo esc_attr( $day ); ?>_closing_time_2" id="<?php echo esc_attr( $day ); ?>-closing-time-2" placeholder="<?php echo esc_attr( date_i18n( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) ) ); ?>" value="<?php echo isset( $all_times[$day]['closing_time_2'] ) ? esc_attr( $all_times[$day]['closing_time_2'] ) : ''; ?>">
								</label>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="dokan-form-group store-open-close">
				<label class="dokan-w3 dokan-control-label" for="dokan-store-time-notice">
					<?php esc_html_e( 'Store Open Notice', 'doc' ); ?>
				</label>
				<div class="dokan-w6">
					<input type="text" class="dokan-form-control input-md" name="dokan_store_open_notice" placeholder="<?php esc_attr_e( 'Store is open', 'doc' ); ?>" value="<?php echo esc_attr( $dokan_store_open_notice ); ?>">
				</div>
			</div>
			<div class="dokan-form-group store-open-close">
				<label class="dokan-w3 dokan-control-label" for="dokan-store-time-notice">
					<?php esc_html_e( 'Store Close Notice', 'doc' ); ?>
				</label>
				<div class="dokan-w6">
					<input type="text" class="dokan-form-control input-md" name="dokan_store_close_notice" placeholder="<?php esc_attr_e( 'Store is closed', 'doc' ); ?>" value="<?php echo esc_attr( $dokan_store_close_notice ); ?>">
				</div>
			</div>
		<?php }
	}
}