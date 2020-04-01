<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Returns all post meta of post id provided
 *
 *
 * @when after_wp_load
 */
$wm_update_poi_paid_date = function( $args, $assoc_args )
{
    //$order_results = new WP_Query( array( 'post_type' => 'shop_order', 'posts_per_page' => -1, 'status' => array('completed','processing')) );
    $arg = array(
        'limit' => 1000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    foreach ($orders as $order ){
        foreach( $order->get_items() as $item_id => $item ){
                    $order_ids = array();
                    $current_order_id = $item['order_id'];
                    $order_ids[] = $current_order_id;
                    // array_push($order_ids,$current_order_id);
                    $order_pd = get_field('_paid_date', $current_order_id);
                    if ($order_pd) {
                        $product_name_variation = $item->get_name();
                        $poi_name = preg_replace('/[^0-9]/', '', $product_name_variation); 
                        $poi = get_page_by_path( $poi_name,OBJECT,'poi' ); 
                        $order_pd = get_field('_paid_date', $current_order_id);
                        $order_pd = str_replace('/', '-', $order_pd );
                        $new_order_pd_format = date('Ymd',strtotime($order_pd));
                        update_field('paid_date',$new_order_pd_format,$poi->ID);
                        // update_field('order_ids',$order_ids,$poi->ID);
                        WP_CLI::success( $count .' - Updating peyment of POI ID # ' . $poi->ID .' from date: '.$order_pd.' to new value: ' .$new_order_pd_format );
                        $count ++;
                    }
                }
            }



};

WP_CLI::add_command( 'wm-update-poi-paid-date', $wm_update_poi_paid_date );
