<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Generates an Expiry notice custom WC email for all orders the day after adoption expires. 
 *
 *
 * @when after_wp_load
 */
$wm_mpt_expired = function( $args, $assoc_args )
{
    // Define a constant to use with html emails
    define("HTML_EMAIL_HEADERS", array('Content-Type: text/html; charset=UTF-8'));
    $subject = 'Adozione scaduta';
    $heading = 'Adozione scaduta';

    $arg = array(
        'limit' => 10000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    
        foreach ($orders as $order ){
            $order_data = $order->get_data();
            $current_paid_date = get_field('order_paid_date',$order->ID);
            $expiry_date = date("d-m-Y", strtotime("+1 years +1 day", strtotime($current_paid_date)));
            $current_paid_date = date("d-m-Y", strtotime($current_paid_date));
            $today = date('d-m-Y');
            $order_billing_email = $order_data['billing']['email'];
            
            if ($today == $expiry_date) {
                
                WP_CLI::line( $count .' - order matched ID # ' .$order->ID . ' with order_paid_date: '. $current_paid_date . '. Sending expiry notice. Expired date: '. $expiry_date .' notice date:  '.$today);
                
                $message = 'È Scaduto il periodo della tua adozione, da oggi il tuo albero/i è accessibile a tutti. Potresti sempre adottarlo clicca qui: <br>'; 
                
                $current_json = get_field('order_json',$order->ID);
                if ($current_json && !empty($current_json)){
                    $current_json = json_decode($current_json);
                    foreach ($current_json as $modality => $items) {
                        if (is_array($items)) :
                        foreach ( $items as $item) {
                            $message .= '<a href="'.esc_url(get_permalink($item->id)).'">'.$item->title .' - '. $modality .'</a><br>';
                            WP_CLI::line( ' Resetting paid date of poi with name ' . $item->title .' and ID # '. $item->id);
                            delete_field('paid_date',$item->id);
                        }
                        endif;
                    }
                } else {
                    foreach ($order->get_items() as $item_id => $item) {
                        $product_name_variation = $item->get_name();
                        $poi_name = preg_replace('/[^0-9]/', '', $product_name_variation); //substr($product_name_variation,0,3);
                        $poi = get_page_by_title( $poi_name, OBJECT, 'poi' );
                        $poi_id = $poi->ID;
                        $message .= '<a href="'.esc_url(get_permalink($poi->ID)).'">'.$product_name_variation.'</a><br>';
                        WP_CLI::line( ' Resetting paid date of poi with name ' . $poi_name .' and ID # '. $poi_id);
                        delete_field('paid_date',$poi->ID);
                    }
                }
                
                //Get woocommerce mailer from instance
                $mailer = WC()->mailer();
                
                // Wrap message using woocommerce html email template
                $wrapped_message = $mailer->wrap_message($heading, $message);
                
                // Create new WC_Email instance
                $wc_email = new WC_Email;
                
                // Style the wrapped message with woocommerce inline styles
                $html_message = $wc_email->style_inline($wrapped_message);
                
                // Send the email using wordpress mail function
                wp_mail( $order_billing_email, $subject, $html_message, HTML_EMAIL_HEADERS );
                
                // Admin email
                $headingADmin = 'Remainder per ordine scaduto';
                $messageADmin = 'È Scaduto il periodo di adozione di quest\'ordine: 
                <a href="'.esc_url( $order->get_edit_order_url() ).'">ordine numero '.$order->ID.'</a>
                ';
                $subjectAdmin = 'è stato mandato un reminder per ordine scaduto # '.$order->ID;
                $wrapped_messageAdmin = $mailer->wrap_message($headingADmin, $messageADmin);
                $html_messageAdmin = $wc_email->style_inline($wrapped_messageAdmin);
                
                // wp_mail( 'pedramkatanchi@webmapp.it', $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
                wp_mail( 'alessiopiccioli@webmapp.it', $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
                WP_CLI::success( 'Email sent to: '. $order_billing_email);
                $count ++;
                }
            }

};

WP_CLI::add_command( 'wm-mpt-expired', $wm_mpt_expired );
