<?php
if ( \DOC\core\Functions::instance()->is_pro() ) return;

add_filter( 'dokan_settings_fields', function ( $settings_fields, $settings ) {
	$settings_fields['dokan_appearance']['demo-1'] = [
		'name'    => 'demo-1',
		'label'   => __( 'Customer Cannot Purchase the product on Store Close (PRO)', 'doc' ),
		'desc'    => __( 'Check this if you want customer to not being able to purchase product from a closed store.', 'doc' ),
		'type'    => 'checkbox',
		'default' => 'off',
	];
	$settings_fields['dokan_appearance']['demo-2'] = [
		'name'        => 'demo-2',
		'label'       => __( 'Show Notice When User Visit Closed Store (PRO)', 'dokan-lite' ),
		'desc'        => __( 'This notice will be shown, when an user visit a closed store', 'docpro' ),
		'type'        => 'select',
		'placeholder' => __( 'Select page', 'docpro' ),
		'options'     => [
			'none' => __( 'None', 'docpro'),
			'banner' => __( 'Banner', 'docpro'),
			'popup' => __( 'Popup', 'docpro'),
		],
	];
	$settings_fields['dokan_appearance']['demo-3'] = [
		'name'        => 'demo-3',
		'label'       => __( 'Show Notice When User Visit Closed Store (PRO)', 'dokan-lite' ),
		'desc'        => __( 'This notice will be shown, when an user visit a closed store', 'docpro' ),
		'type'        => 'select',
		'placeholder' => __( 'Select Notice Type', 'docpro' ),
		'options'     => [
			'none' => __( 'None', 'docpro'),
			'banner' => __( 'Banner', 'docpro'),
			'popup' => __( 'Popup', 'docpro'),
		],
	];
	$settings_fields['dokan_appearance']['demo-4'] = [
		'name'    => 'demo-4',
		'label'   => __( 'Closed Notice Text (PRO)', 'dokan-lite' ),
		'type'    => 'textarea',
		'rows'    => 5,
		'desc' => __( 'Store is closed for now', 'docpro' ),
		'default' => __( 'This text will be shown as notice for closed store, if notice is chosen to show', 'docpro' ),
	];
	$settings_fields['dokan_appearance']['demo-5'] = [
		'name'    => 'demo-5',
		'label'   => __( 'Allow Vendor to Control Visibility of Notice (PRO)', 'doc' ),
		'desc'    => __( 'Check this if you want vendors to have the control to turn off/on notice in their store.', 'doc' ),
		'type'    => 'checkbox',
		'default' => 'off',
	];
	$settings_fields['dokan_appearance']['demo-6'] = [
		'name'    => 'demo-6',
		'label'   => __( 'Remove Product from Cart on Store Closes (PRO)', 'doc' ),
		'desc'    => __( 'Check this if you want products to be removed from customer\'s cart if the store of that product gets closed.', 'doc' ),
		'type'    => 'checkbox',
		'disabled' => true,
		'default' => 'off',
	];

	return $settings_fields;

}, 10, 2 );