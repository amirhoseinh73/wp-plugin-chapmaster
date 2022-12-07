<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Function to execute on plugin activation
register_activation_hook( __FILE__, function() {
	if ( ! current_user_can( 'activate_plugins' ) ) return;

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : null;
    check_admin_referer( 'activate-plugin_' . $plugin );

    /* Code here */
} );

// Function to execute on plugin deactivation
register_deactivation_hook( __FILE__, function() {
	if ( ! current_user_can( 'activate_plugins' ) ) return;

    $plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : null;
    check_admin_referer( 'deactivate-plugin_' . $plugin );

    /* Code here */
} );

// Add link to configuration page into plugin
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	return array_merge( array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wcrcsbox_products' ) . '">' . __( 'Settings' ) . '</a>'
	), $links );
} );

function wcrcsbox_products_fields() {
    $fields = array(
        'section_title-enable' => array(
            'name'     => __( 'حذف سبد خرید از محصولات' ),
            'type'     => 'title',
            'desc'     => __( 'با فعالسازی این افزونه، مرحله سبد خرید از محصولات با دسته بندی جعبه و طرح جعبه حذف میشود و کاربر تنها قادر به خرید یک محصول از این دسته ها می باشد.' )
        ),
        'wcrcsbox_products_enable' => array(
            'name' => __( 'فعالسازی' ),
            'type' => 'checkbox',
            'desc' => __( 'حذف سبد خرید و رفتن مستقیم به صفحه ثبت اطلاعات' ),
            'id'   => 'wcrcsbox_products_enable'
        ),
        'section_end-enable' => array(
             'type' => 'sectionend'
        )
    );

    return apply_filters( 'wcrcsbox_products_fields', $fields );
}

// Add settings tab to WooCommerce options
add_filter( 'woocommerce_settings_tabs_array', function( $tabs ) {
    $tabs['wcrcsbox_products'] = __( 'حذف مرحله سبد خرید' );
    
    return $tabs;
}, 50 );

// Add settings to the new tab
add_action( 'woocommerce_settings_tabs_wcrcsbox_products', function() {
    woocommerce_admin_fields( wcrcsbox_products_fields() );
} );

// Save settings
add_action( 'woocommerce_update_options_wcrcsbox_products', function() {
    woocommerce_update_options( wcrcsbox_products_fields() );
} );

/*** IF PLUGIN IS ENABLED ***/
if ( get_option( 'wcrcsbox_products_enable' ) == 'yes' ) {

    // Add checks and notices
    add_action( 'admin_notices', function() {
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            ?><div class="notice notice-error"><p><?php _e( 'Warning! To use Disable cart page for WooCommerce it need WooCommerce is installed and active.' ); ?></p></div><?php
        }
    } );

    // Force WooCommerce to redirect after product added to cart
    add_filter( 'pre_option_woocommerce_cart_redirect_after_add', function( $pre_option ) {
        return 'yes';
    } );

    add_filter( 'woocommerce_product_settings', function( $fields ) {
        foreach ( $fields as $key => $field ) {
            if ( $field['id'] === 'woocommerce_cart_redirect_after_add' ) {
                $fields[$key]['custom_attributes'] = array(
                    'disabled' => true
                );
            }
        }
        return $fields;
    }, 10, 1 );

    // Avoid add to cart others product categories when "beerservice" is in cart
    add_filter( 'woocommerce_add_to_cart_validation', 'specific_category_avoid_add_to_cart_others', 20, 3 );
    function specific_category_avoid_add_to_cart_others( $passed, $product_id, $quantity) {
        if( WC()->cart->is_empty() || has_parent_term( $product_id ) ) {
            return $passed;
        }

        $error_txt = 'سبد خرید شما دارای یک محصول از دسته طرح جعبه است، محصول دیگری نمیتوانید همزمان با این نوع محصول خریداری کنید!';
        foreach( WC()->cart->get_cart() as $cart_item ){
            if( has_parent_term( $cart_item['product_id'] ) ) {
                wc_add_notice( __( $error_txt ), 'error' ); // Display a custom error notice
                echo "<div class='alert alert-error alert-fixed'>$error_txt</div>";
                return false; // Avoid add to cart
            }
        }
        return $passed;
    }

    // Remove other items when our specific product is added to cart
    add_action( 'woocommerce_add_to_cart', 'conditionally_remove_other_products', 20, 4 );
    function conditionally_remove_other_products ( $cart_item_key, $product_id, $quantity, $variation_id ){
        if( has_parent_term( $product_id ) ) {
            foreach( WC()->cart->get_cart() as $item_key => $cart_item ){
                if( ! has_parent_term( $cart_item['product_id'] ) ) {
                    WC()->cart->remove_cart_item( $item_key );
                }
            }
            wc_add_notice( __('سایر محصولات شما از سبد خرید حذف شد، در صورت انتخاب طرح جعبه، فقط طرح جعبه میتوانید خریداری کنید!' ), 'error' ); // Display a custom error notice

            return wp_redirect( wc_get_checkout_url(), 301 );
        }
    }

    // Clear cart if there are errors
    add_action( 'woocommerce_cart_has_errors', function() {
        wc_empty_cart();
    } );

}

// Custom conditional function that checks for parent product categories
function has_parent_term( $product_id ) {
    // HERE set your targeted product category SLUG
    $category_slug = 'طرح-جعبه'; //

    // Convert category term slug to term id
    $category_id   = get_term_by('slug', $category_slug, 'product_cat')->term_id;
    $parent_term_ids = array(); // Initializing

    // Loop through the current product category terms to get only parent main category term
    foreach( get_the_terms( $product_id, 'product_cat' ) as $term ){
        if( $term->parent > 0 ){
            $parent_term_ids[] = $term->parent; // Set the parent product category
        } else {
            $parent_term_ids[] = $term->term_id;
        }
    }
    return in_array( $category_id, $parent_term_ids );
}