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
        'limit' => -1,
        'status' => array('completed'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    
        foreach ($orders as $order ){
            $renewal_type = montepisanotree_order_is_already_renewed($order);
            if (in_array('already_expired', $renewal_type)) {
                WP_CLI::line( 'The order #'.$order->ID. 'is already expired');
            } else {
                $order_data = $order->get_data();
                $current_paid_date = get_field('order_paid_date',$order->ID);
                $expiry_date = date("Y-m-d", strtotime("+1 years +1 day", strtotime($current_paid_date)));
                $current_paid_date = date("Y-m-d", strtotime($current_paid_date));
                $today = date('Y-m-d');
                $order_billing_email = $order_data['billing']['email'];
                $order_billing_name = $order_data['billing']['first_name'];
                
                if ($today == $expiry_date) {
                    
                    WP_CLI::line( $count .' - order matched ID # ' .$order->ID . ' with order_paid_date: '. $current_paid_date . '. Sending expiry notice. Expired date: '. $expiry_date .' notice date:  '.$today);
                    
                    $message = 'Ciao '.$order_billing_name.',<br>
                    Ti informiamo che l’adozione è scaduta. Da oggi il tuo albero/i è accessibile a tutti. Se lo desideri puoi sempre adottarlo cliccando qui: <br>'; 
                    $message .= wmGetTreeDetail($order->ID);
                    
                    montepisanotree_add_already_expired_to_oldorder($order->ID);
                    WP_CLI::line( 'Adding already_expired to the order #'.$order->ID);
                    montepisanotree_delete_token( $order->ID );
                    WP_CLI::line( 'Removing renewal token of order #'.$order->ID);
                    $current_json = get_field('order_json',$order->ID);
                    if ($current_json && !empty($current_json)){
                        $current_json = json_decode($current_json);
                        foreach ($current_json as $modality => $items) {
                            if (is_array($items)) :
                            foreach ( $items as $item) {
                                $message .= '<a href="'.esc_url(get_permalink($item->id)).'">'.$item->title .' - '. $modality .'</a><br>';
                                WP_CLI::line( 'Resetting paid date of poi with name ' . $item->title .' and ID # '. $item->id);
                                delete_field('paid_date',$item->id);
                            }
                            endif;
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
                    $subjectAdmin = 'È stato inviato  un reminder per ordine scaduto # '.$order->ID;
                    $messageADmin = 'È Scaduto il periodo di adozione di quest\'ordine: 
                    <a href="'.esc_url( $order->get_edit_order_url() ).'">ordine numero '.$order->ID.'</a>
                    ';
                    $messageADmin .= wmGetTreeDetail($order->ID);
                    $wrapped_messageAdmin = $mailer->wrap_message($headingADmin, $messageADmin);
                    $html_messageAdmin = $wc_email->style_inline($wrapped_messageAdmin);
                    
                    // Get recipients from New Order email notification
                    $new_order_recipient = WC()->mailer()->get_emails()['WC_Email_New_Order']->get_recipient();

                    wp_mail( $new_order_recipient, $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
                    WP_CLI::success( 'Email sent to: '. $order_billing_email);
                    $count ++;
                    }
                }
            }

};

WP_CLI::add_command( 'wm-mpt-expired', $wm_mpt_expired );
