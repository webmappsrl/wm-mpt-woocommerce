<?php
/*
  Plugin Name: wm-mpt-woocommerce
  Description: Add extra functionality to Woocommerce related to Webmapp plugin
  Version: 0.0.1
  Author NAME: Pedram Katanchi
  Author URI: http://webmapp.it
 */
require ('include/wm-mpt-wc.php');
require ('templates/wm-mpt-email-product-table.php');
require ('acf-poi/custom-acf-poi.php');
if ( defined( 'WP_CLI' ) && WP_CLI ) {
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-cli-update-poi-paid-date.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-clean-paid-date.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-clean-paid-date-500.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-force-renewal.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-renewal.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-expired.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-duplicate-orders-meta-data.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-test-update-order-email.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-set-order-paid-date.php';
  require_once dirname( __FILE__ ) . '/wm-cli/wm-mpt-set-poi-paid-date.php';
}
