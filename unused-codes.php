<?php
// Empty cart when product is added to cart, so we can't have multiple products in cart
add_action( 'woocommerce_add_cart_item_data', function( $cart_item_data ) {
    wc_empty_cart();
    return $cart_item_data;
} );

// When add a product to cart, redirect to checkout
add_action( 'woocommerce_init', function() {
    if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
        add_filter( 'add_to_cart_redirect', function() {
            return wc_get_checkout_url();
        } );
    } else {
        add_filter( 'woocommerce_add_to_cart_redirect', function() {
            return wc_get_checkout_url();
        } );
    }
} );

// Remove added to cart message
add_filter( 'wc_add_to_cart_message_html', '__return_null' );

// If someone reaches the cart page, redirect to checkout permanently
add_action( 'template_redirect', function() {
    if ( ! is_cart() ) { return; }
    if ( WC()->cart->get_cart_contents_count() == 0 ) {
        wp_redirect( apply_filters( 'wcrcsbox_products_redirect', wc_get_page_permalink( 'shop' ) ) );
        exit;
    }

    // Redirect to checkout page
    wp_redirect( wc_get_checkout_url(), '301' );
    exit;
} );


// Change add to cart button text ( in loop )
add_filter( 'add_to_cart_text', function() {
    return __( 'Buy now', 'disable-cart-page-for-woocommerce' );
} );

// Change add to cart button text ( in product page )
add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
    return __( 'Buy now', 'disable-cart-page-for-woocommerce' );
} );

// Remove cart button from mini-cart
remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );

// Add language support to internationalize plugin
add_action( 'init', function() {
	load_plugin_textdomain( 'disable-cart-page-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/' );
} );