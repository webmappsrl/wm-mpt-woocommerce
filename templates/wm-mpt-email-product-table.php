<?php 
function wmGetTreeDetail ($order_id) {
    $json = get_field('order_json', $order_id);
    $array_id = array();
    if ($json) {
        $jsonPhp = json_decode($json, true);
        if (is_array($jsonPhp)) {
            foreach ($jsonPhp as $type => $arr) {
                if (is_array($arr)) :
                    foreach ($arr as $data) {
                        if (isset($data['id'])) {
                            $array_id[] = array( 'type' => $type,
                                'id' => $data['id'],
                                'dedication' => $data['dedication']
                            );
                        }
                    }
                endif;
            }
        }
    }
    ob_start();
    $totla_price = 0;
    foreach ($array_id as $array => $poi) {
        $product_name = get_page_by_title( $poi['type'], OBJECT, 'product' );
        $product = wc_get_product($product_name->ID);
        $product_price = $product->get_regular_price();
        $totla_price = $totla_price + $product_price;
        $poi_title = get_the_title( $poi['id'] );
        $poi_permalink = get_permalink($poi['id']);
        $terms = get_the_terms( $poi['id'] , 'webmapp_category' );

        $item_name_poi = $poi['type'] . ' - ' . $terms[0]->name . ' - ' . $poi_title;
        $product_get_image = get_the_post_thumbnail_url($poi['id']);
        ?>
        <div style="display: flex;align-items: center;height: 75px;">

            <div style="border: 1px solid;width: 80px;height: 75px;background: url(<?php echo $product_get_image;?>) center / cover no-repeat;"> </div>
            <div style="border: 1px solid;padding: 15px 20px 0;height: 60px;width:100%;">
                <?php
                echo  sprintf( '<a href="%s">%s</a>', $poi_permalink, $item_name_poi ).'<br>'; 
                if ($poi['dedication']){
                    echo 'Dedica: '.$poi['dedication'];
                }
                ?>
            </div>
            <div style="border: 1px solid;padding: 0 20px;height: 100%;line-height: 69px;width: 50px;">
                <?php
                echo  '€'.$product_price; 
                ?>
            </div>

        </div>
        <?php
    }
    ?>
    <p style="padding-top:30px;margin: 0;"><strong>Totale:</strong> <?php echo  '<strong>€'.$totla_price.'</strong>'; ?></p> 
    <h2 style="color:#afad35;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left"><strong>Indirizzo di fatturazione</strong></h2> 
    <?php
    $order = wc_get_order($order_id);
    $order_meta = get_post_meta($order_id);
    $order_data = $order->get_data();
    echo '<div style="padding:12px;color:#636363;border:1px solid #e5e5e5">';
    echo $order_data['billing']['first_name'].'<br>';
    echo $order_data['billing']['last_name'].'<br>';
    echo $order_data['billing']['address_1'].'<br>';
    echo $order_data['billing']['city'].'<br>';
    echo $order_data['billing']['state'].'<br>';
    echo $order_data['billing']['postcode'].'<br>';
    echo $order_data['billing']['country'].'<br>';
    echo $order_data['billing']['email'].'<br>';
    echo $order_data['billing']['phone'].'<br>';
    echo $order_meta['billing_codice_fiscale'][0];
    echo '</div>';
    return ob_get_clean();
}