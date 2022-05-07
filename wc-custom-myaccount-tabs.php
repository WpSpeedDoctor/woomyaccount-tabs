<?php
namespace wpsd_customtabs;
/*
* Plugin Name: WC Custom tab on "My Account" page
* Plugin URI: https://wpspeeddoctor.com/
* Description: Adds additional tabs to "My account" page. Make sure to save Permalinks after plugin activation or changing tabs data.
* Version: 1.0.0
* Author: Jaro Kurimsky
* License: GPLv2 or later
*/  

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists('Woocommerce') ) return;

/**
 * I don't recommend saving tab data into WP options, it's unnecessary bloath and speed decrease.
 * That's why I use define, you set it once and it's like that for loooong time. No need saving in the database.
 * 
 **/
const CUSTOM_TABS = array( 

	'coupons' => 'Coupons',

	'points' => 'Points'
);

//has to be in global oherwise you can't sucessfully rewrite permalinks
add_action( 'init', 'wpsd_customtabs\add_endpoints_to_wc' );

if ( !is_admin() ) {

	//has to be called from WP hook as is_page() is not working yet
	add_action ('wp','wpsd_customtabs\body_custom_myaccount_tabs');
}

function add_tab_endpoint_content(){

	foreach ( CUSTOM_TABS as $key => $value) {

		$endpoint_hook = 'woocommerce_account_'.$key.'_endpoint';

		$endpoint_callable ='wpsd_customtabs\\'.$key.'_endpoint_content';

		if ( is_callable($endpoint_callable) ) add_action( $endpoint_hook, $endpoint_callable );

	}
}


function body_custom_myaccount_tabs(){

	//execute only on the account page
	if ( !is_account_page() ) return;   

	add_filter( 'woocommerce_account_menu_items', 'wpsd_customtabs\add_tab_to_myaccount_page' );

	add_tab_endpoint_content();

}

function coupons_endpoint_content(){

	?>
	Coupons
	<?php
}

function points_endpoint_content(){

	?>
	Points
	<?php
}


function add_endpoints_to_wc() {
	
	foreach ( CUSTOM_TABS as $key => $value) {

		add_rewrite_endpoint( $key, EP_PAGES );
	}
}

function add_tab_to_myaccount_page( $items ) {

	$last_key= array_key_last($items);

	$last_value = array_pop( $items );

	$items = array_merge( $items,CUSTOM_TABS );

	$items[$last_key] = $last_value;

	return $items;
}