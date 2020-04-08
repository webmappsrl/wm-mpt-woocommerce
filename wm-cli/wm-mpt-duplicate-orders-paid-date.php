<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Copies the _pai_date of orders in a new ACF order_paid_date
 *
 *
 * @when after_wp_load
 */
$wm_mpt_duplicate_date = function( $args, $assoc_args )
{
    $arg = array(
        'limit' => 1000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    foreach ($orders as $order ){
                $order_meta = get_post_meta($order->ID);
                $current_paid_date = $order_meta['_paid_date'][0];
                $current_paid_date = date("Y-m-d", strtotime($current_paid_date));
                if ($current_paid_date) {
                    update_field('order_paid_date', $current_paid_date, $order->ID);
                    WP_CLI::success( $count .' - order ID # ' . $order->ID . ' order paid date: '. $current_paid_date . ' copied ' );
                    $count ++;
                }
    }

};

WP_CLI::add_command( 'wm-mpt-duplicate-orders-paid-date', $wm_mpt_duplicate_date );
