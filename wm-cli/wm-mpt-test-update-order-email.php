<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Updates in all orders the client email address with test@test.it 
 *
 *
 * @when after_wp_load
 */
$wm_mpt_test_update_email = function( $args, $assoc_args )
{
    $arg = array(
        'limit' => -1,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);

    if ( home_url() == 'https://test.montepisanotree.org' || home_url() == 'http://localhost/mpt') {
        $count = 1;
        foreach ($orders as $order ){
            $test_email = 'test@test.it';
            $order_data = $order->get_data();
            $order_billing_email = $order_data['billing']['email'];
            update_post_meta( $order->ID, '_billing_email', $test_email );
            WP_CLI::success( $count .' - Order ID # ' . $order->ID . ' with billing email ' .$order_billing_email .' is changed to ' . $test_email);
            
            $count ++;
        }
    }


};

WP_CLI::add_command( 'wm-mpt-test-update-order-email', $wm_mpt_test_update_email );
