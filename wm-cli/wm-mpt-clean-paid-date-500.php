<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Clears the ACF paid_date of these POI '401','402','403','404','405'
 *
 *
 * @when after_wp_load
 */
$wm_mpt_clean_paid_date_500 = function( $args, $assoc_args )
{
    $arg = array( 'post_type' => 'poi', 'post_name__in' => array('574','573','572','571','570','569','568','567','566','565','564','563','562','520','521','522','515','516','517','518','519','500','501','502','503','504','505','506','507','508','509','510','511','512','513','514'), 'posts_per_page' => -1 ) ;
    
    if ( home_url() == 'https://test.montepisanotree.org' || home_url() == 'http://localhost/mpt') {
        $pois = get_posts($arg);
        $count = 1;
        foreach ($pois as $poi ){
            $pdate = get_field('paid_date',$poi->ID);
            if ($pdate) {
                delete_field('paid_date',$poi->ID);
                WP_CLI::success( $count .' - Updating peyment date of POI ID # ' . $poi->ID .' from date: '.$pdate.' to new value: ' );
            } else {
                WP_CLI::success( $count .' - The of POI ID # ' . $poi->ID . ' Paid date is already empty' );
            }
            $count ++;
        }
    } else {
        WP_CLI::error( 'Seriously! on this site? NO !' );
    }


};

WP_CLI::add_command( 'wm-mpt-clean-paid-date-500', $wm_mpt_clean_paid_date_500 );
