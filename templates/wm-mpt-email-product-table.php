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
                                'id' => $data['id']
                            );
                        }
                    }
                endif;
            }
        }
    }
    ob_start();
    foreach ($array_id as $array => $poi) {
        ?>
        <div style="display: grid;grid-template-columns: 1fr 2fr;">

            <div style="">
                <?php
                // change the name in thankyoupage webmapp
                $poi_title = get_the_title( $poi['id'] );
                $poi_permalink = get_permalink($poi['id']);
                $terms = get_the_terms( $poi['id'] , 'webmapp_category' );

                $item_name_poi = $poi['type'] . ' - ' . $terms[0]->name . ' - ' . $poi_title;
                $product_get_image = get_the_post_thumbnail($poi['id']);
                // $product_get_image .= '<div class="cart-item-cat-title">'.$terms[0]->name . ' - ' . $poi_title.'</div>';
                
                echo $product_get_image;
                
                ?>
            </div>
            <div style="">
                <?php
                echo  sprintf( '<a href="%s">%s</a>', $poi_permalink, $item_name_poi ); 
                ?>
            </div>

        </div>
        <?php
    }

    return ob_get_clean();
}