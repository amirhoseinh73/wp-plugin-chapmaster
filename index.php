<?php
/*
 Plugin Name: AMHNJ Remove cart step from specific products 
 Plugin URI: 
 Description: remove cart step from box products and add some styles to theme
 Author: amirhosein hasani
 Author URI: https://instagram.com/amirhoseinh73
 Version: 1.2.9
 Requires PHP: 7.2
 */

define( "VERSION", "1.2.9" );

define( "AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_FILE"       , __FILE__);
define( "AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH"   , plugin_dir_path( __FILE__ ) );
define( "AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_URL"    , plugin_dir_url(__FILE__) );

define( 'AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH_ADMIN' , AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH . 'admin/');
define( 'AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_URL_ADMIN'  , AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_URL . 'admin/');

if ( is_admin() ) {
	require_once AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH_ADMIN . 'admin.php';
}

require_once AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH . "scripts.php";
require_once AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH . "single-product-wc.php";

require_once AMHNJ_FUNCTIONS_PLUGIN_CHAPMASTER_DIR_PATH . "remove-cart.php";