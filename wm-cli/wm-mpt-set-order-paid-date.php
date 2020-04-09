<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Sets the last 3 orders order_paid_date to X days from now if e specific order_id is not given as second parameter. Accepts one numeric arguments as days and the second as order ID. 
 *
 *
 * @when after_wp_load
 */
$wm_mpt_set_order_paid_date = function( $args, $assoc_args )
{
    $days = intval($args[0]);
    $custom_order = intval($args[1]);
    if ($days && is_int($days)) {
        if ($custom_order) {
            $order = wc_get_order($custom_order);
            $today = date('Y-m-d');
            $current_paid_date = get_field('order_paid_date',$order->ID);
            $new_date = date("Y-m-d", strtotime("-1 years + $args[0] day", strtotime($today)));
            update_field('order_paid_date', $new_date, $order->ID);
            
            WP_CLI::success( "{$count} - changed order ID #  {$order->ID} order_paid_date from {$current_paid_date} to new date: {$new_date}");
        } else {
            $arg = array(
                'limit' => 3,
                // 'status' => array('completed','processing'),
            );
            $today = date('Y-m-d');
            $orders = wc_get_orders($arg);
            foreach ($orders as $count => $order ){
                $current_paid_date = get_field('order_paid_date',$order->ID);
                $new_date = date("Y-m-d", strtotime("-1 years + $args[0] day", strtotime($today)));
                update_field('order_paid_date', $new_date, $order->ID);
                
                WP_CLI::success( "{$count} - changed order ID #  {$order->ID} order_paid_date from {$current_paid_date} to new date: {$new_date}");
            }
        }
    } else {
        WP_CLI::error( 'The parameter you entered: ' . $args[0] . ' is not a number or missing. '  );
    }

};

WP_CLI::add_command( 'wm-mpt-set-order-paid-date', $wm_mpt_set_order_paid_date );
