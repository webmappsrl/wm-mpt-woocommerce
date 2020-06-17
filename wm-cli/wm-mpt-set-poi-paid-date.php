<?php

# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * Gets first argument as POI ID and second argument as YYYY-MM-DD to update POI's paid-date
 *
 *
 * @when after_wp_load
 */
$wm_mpt_set_paid_date = function( $args, $assoc_args )
{
    $poi_id = intval($args[0]);
    $paid_date = $args[1];
    
    if ( $paid_date && is_int($poi_id)) {
        update_field('paid_date',$paid_date,$poi_id);
        WP_CLI::success( 'POI with ID #'.$poi_id. ' has a new paid_date: '.$paid_date);
    } else {
        WP_CLI::error( 'Either one argument is missing or the first argument is not numeric' );
    }


};

WP_CLI::add_command( 'wm-mpt-set-poi-paid-date', $wm_mpt_set_paid_date );
