<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Generates a Renewal custom WC email for all orders some days before expiry: Accepts numeric arguments as days before. 
 *
 *
 * @when after_wp_load
 */
$wm_mpt_renewal = function( $args, $assoc_args )
{
    // Define a constant to use with html emails
    define("HTML_EMAIL_HEADERS", array('Content-Type: text/html; charset=UTF-8'));
    $subject = 'Adozione in scadenza';
    $heading = 'Rinnova l\'adozione';

    $arg = array(
        'limit' => -1,
        // 'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    $days = intval($args[0]);
    if ($days && is_int($days)) {
        foreach ($orders as $order ){
            $renewal_type = montepisanotree_order_is_already_renewed($order->ID);
            if (in_array('already_expired', $renewal_type) || in_array('already_renewed', $renewal_type)) {
                WP_CLI::line( 'The order #'.$order->ID. 'has already been renewed');
            } else {
                $order_data = $order->get_data();
                $current_paid_date = get_field('order_paid_date',$order->ID);
                $notice_date = date("Y-m-d", strtotime("+1 years - $args[0] day", strtotime($current_paid_date)));
                $expiry_date = date("Y-m-d", strtotime("+1 years", strtotime($current_paid_date)));
                $current_paid_date = date("Y-m-d", strtotime($current_paid_date));
                $today = date('Y-m-d');
                $order_billing_email = $order_data['billing']['email'];
                $order_billing_name = $order_data['billing']['first_name'];
                $message = 'Ciao '.$order_billing_name.',<br>
                            Mancano '.$days.' giorni prima che l’adozione del tuo albero scada. Se vuoi rinnovarla alle stesse condizioni dell’anno precedente clicca qui: 
                            </p><p><a style="color: white;font-weight: normal;text-decoration: none;padding: 10px 20px; background-color:#afad35;font-size: 16px;" href="'.home_url().'/renewal/?order_id='.$order->ID.'&token='.montepisanotree_add_token($order->ID).'">Rinnova la tua adozione</a>
                            ';
                $message .= '<h2 style="color:#afad35;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">
                Dettaglio dell\'ordine</h2>';
                $message .= wmGetTreeDetail($order->ID);
                $message .= '<p>Se desideri invece cambiare tipo di adozione o creare una nuova targhetta ti invitiamo a far scadere l’adozione e crearne una nuova.</p>';
                $message .= '<p>Grazie ancora</p>';
                    
                    if ($today == $notice_date) {
                        WP_CLI::line( $count .' - order matched ID # ' .$order->ID . ' with order_paid_date: '. $current_paid_date . '.Sending renewal notice with '.$days. ' days to expiry. Expiry date: '. $expiry_date .' notice date:  '.$notice_date);
                        // Get woocommerce mailer from instance
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
                        $headingADmin = 'Remainder per '. $days .' gironi prima di scadenza mandato ';
                        $messageADmin = 'Scade tra '.$days.' giorni l\'adozione di un albero, per vedere l\'ordine clicca qui: 
                        <a href="'.esc_url( $order->get_edit_order_url() ).'">ordine numero '.$order->ID.'</a>
                        ';
                        $messageADmin .= wmGetTreeDetail($order->ID);

                        $subjectAdmin = 'È stato inviato un reminder per ordine # '.$order->ID;
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
    } else {
        WP_CLI::error( 'The parameter you entered: ' . $args[0] . ' is not a number or missing. '  );
    }

};

WP_CLI::add_command( 'wm-mpt-renewal', $wm_mpt_renewal );
