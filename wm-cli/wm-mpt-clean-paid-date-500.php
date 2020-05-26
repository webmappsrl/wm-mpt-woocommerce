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
    $arg = array( 'post_type' => 'poi', 'post_name__in' => array('403','401','398','315','361','354','355','370','379','380','384','455','435','427','415','414','458'), 'posts_per_page' => -1 ) ;
    
    if ( home_url() == 'https://test.montepisanotree.org' || home_url() == 'http://localhost/mpt' || home_url() == 'https://preprod.montepisanotree.org') {
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
