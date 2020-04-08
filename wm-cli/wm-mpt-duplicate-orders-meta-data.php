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
        'limit' => -1,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    foreach ($orders as $count => $order ){
        $check_order_paid_date = get_field('order_paid_date',$order->ID);
        if (!$check_order_paid_date) {
            $order_meta = get_post_meta($order->ID);
            $current_paid_date = $order_meta['_paid_date'][0];
            $current_paid_date = date("Y-m-d", strtotime($current_paid_date));
            if ($current_paid_date) {
                update_field('order_paid_date', $current_paid_date, $order->ID);
                WP_CLI::line( ($count+1) .' - order ID # ' . $order->ID . ' order paid date: '. $current_paid_date . ' copied ' );
            }
        }
        $check_order_json = get_field('order_json',$order->ID);
        if (!$check_order_json){
            $order_json = array();
            foreach ($order->get_items() as $product_id => $product) {
                $product_name_variation = $product->get_name();
                $explode_variation = explode(' - ', $product_name_variation); //substr($product_name_variation,0,3);
                $poi_name = $explode_variation[0];
                $modality = $explode_variation[1];
                $poi = get_page_by_title( $poi_name, OBJECT, 'poi' );
                $poi_id = $poi->ID;
    
                $item = array();
                $item['id'] = $poi_id;
                $item['title'] = $poi_name;
                $item['dedication'] = '';
                if ($modality == 'Friendship') {
                    $order_json['friendship'][] = $item;
                }
                if ($modality == 'Love') {
                    $order_json['Love'][] = $item;
                }
                if ($modality == 'Passion') {
                    $order_json['Passion'][] = $item;
                }
            }
            update_field('order_json', json_encode($order_json), $order->ID);
            WP_CLI::line( " order ID #  {$order->ID}  now has order_json " . json_encode($order_json));
        }
        WP_CLI::success( " order ID #  {$order->ID}  has processed");
    }

};

WP_CLI::add_command( 'wm-mpt-duplicate-orders-meta-data', $wm_mpt_duplicate_date );
