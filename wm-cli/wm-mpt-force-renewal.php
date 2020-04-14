<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Generates a Renewal custom WC email for a given order ID
 *
 *
 * @when after_wp_load
 */
$wm_mpt_force_renewal = function( $args, $assoc_args )
{
    
    $order = wc_get_order($args[0]);
    $count = 1;
    $order_data = $order->get_data();
    $current_paid_date = get_field('order_paid_date',$args[0]);
    $current_paid_date = date("", strtotime($current_paid_date));
    $order_billing_email = $order_data['billing']['email'];
    $subject = 'Rinnovo dell\'albero Montepisanotree ';
    $heading = 'Rinnovo Albero';
    $message = 'è scaduto adozione del tuo albero, per rinnovare clicca qui: 
                <a href="'.home_url().'/renewal/?order_id='.$args[0].'&token='.montepisanotree_add_token($args[0]).'">Rinnova il tuo adozione</a>
                ';
    $message .= wmGetTreeDetail($args[0]);
    
    // Define a constant to use with html emails
    define("HTML_EMAIL_HEADERS", array('Content-Type: text/html; charset=UTF-8'));
    
    
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
    $headingADmin = 'Remainder mandato';
    $messageADmin = 'è scaduto adozione di un albero, per vedere un ordine clicca qui: 
                <a href="'.esc_url( $order->get_edit_order_url() ).'">ordine numero '.$args[0].'</a>
                ';
    $messageADmin .= wmGetTreeDetail($args[0]);
    
    $subjectAdmin = 'è stato mandato un reminder per ordine # '.$args[0];
    $wrapped_messageAdmin = $mailer->wrap_message($headingADmin, $messageADmin);
    $html_messageAdmin = $wc_email->style_inline($wrapped_messageAdmin);
    
    // Get recipients from New Order email notification
    $new_order_recipient = WC()->mailer()->get_emails()['WC_Email_New_Order']->get_recipient();

    wp_mail( $new_order_recipient, $subjectAdmin, $html_messageAdmin, HTML_EMAIL_HEADERS );
    WP_CLI::success( $count .' - order matched ID # ' . $args[0] . '.Renewal sent with order paid date: '. $current_paid_date );

    $count ++;

};

WP_CLI::add_command( 'wm-mpt-force-renewal', $wm_mpt_force_renewal );
