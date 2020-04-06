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
        'limit' => 10000,
        'status' => array('completed','processing'),
    );
    $orders = wc_get_orders($arg);
    $count = 1;
    $days = intval($args[0]);
    if ($days && is_int($days)) {
        foreach ($orders as $order ){
            $order_data = $order->get_data();
            $current_paid_date = get_field('order_paid_date',$order->ID);
            $notice_date = date("d-m-Y", strtotime("+1 years - $args[0] day", strtotime($current_paid_date)));
            $expiry_date = date("d-m-Y", strtotime("+1 years", strtotime($current_paid_date)));
            $current_paid_date = date("d-m-Y", strtotime($current_paid_date));
            $today = date('d-m-Y');
            $order_billing_email = $order_data['billing']['email'];
            $message = 'Mancano '.$days.' giorni alla scadenza dell\'adozione del tuo albero, per rinnovare clicca qui: 
                        <a href="'.home_url().'/renewal/?order_id='.$order->ID.'">Rinnova il tuo adozione</a>
                        ';
                
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
                    $subjectAdmin = 'è stato mandato un reminder per ordine # '.$order->ID;
                    $wrapped_messageAdmin = $mailer->wrap_message($headingADmin, $messageADmin);
                    $html_messageAdmin = $wc_email->style_inline($wrapped_messageAdmin);
                    
                    // wp_mail( 'pedramkatanchi@webmapp.it', $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
                    wp_mail( 'alessiopiccioli@webmapp.it', $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
                    WP_CLI::success( 'Email sent to: '. $order_billing_email);
                    $count ++;
                }
            }
    } else {
        WP_CLI::error( 'The parameter you entered: ' . $args[0] . ' is not a number or missing. '  );
    }

};

WP_CLI::add_command( 'wm-mpt-renewal', $wm_mpt_renewal );
