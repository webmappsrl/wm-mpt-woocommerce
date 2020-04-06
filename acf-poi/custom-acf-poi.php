<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5e7a260757ead',
	'title' => 'Payment date',
	'fields' => array(
		array(
			'key' => 'wm-mpt-poi-paid-date',
			'label' => 'Paid date',
			'name' => 'paid_date',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'd/m/Y',
			'return_format' => 'd/m/Y',
            'first_day' => 1,
            'readonly' => 1
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'poi',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;


// Added ACF field for orders
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5caf0f4d42775',
		'title' => 'Aggiungi data di acquisto',
		'fields' => array(
			// array(
			// 	'key' => 'wm_order_parid_date',
			// 	'label' => 'Data di acquisto',
			// 	'name' => '_paid_date',
			// 	'type' => 'date_picker',
			// 	'instructions' => '',
			// 	'required' => 0,
			// 	'conditional_logic' => 0,
			// 	'wrapper' => array(
			// 		'width' => '',
			// 		'class' => '',
			// 		'id' => '',
			// 	),
			// 	'display_format' => 'd/m/Y',
			// 	'return_format' => 'd/m/Y',
			// 	'first_day' => 1,
			// 	'readonly' => 1
			// ),
			array(
				'key' => 'wm_mpt_order_paid_date',
				'label' => 'Data di acquisto',
				'name' => 'order_paid_date',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'd/m/Y',
				'return_format' => 'd/m/Y',
				'first_day' => 1,
				'readonly' => 1
			),
			array(
				'key' => 'wm_order_json',
				'label' => 'Order Json',
				'name' => 'order_json',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'new_lines' => '',
				'readonly' => 1
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'shop_order',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));
	
	endif;