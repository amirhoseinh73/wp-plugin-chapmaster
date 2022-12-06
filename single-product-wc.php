<?php

add_action( "woocommerce_share", "amhnj_add_contact_info" );
function amhnj_add_contact_info() {
    global $product;
    if ( ! isset( $product ) || empty( $product ) ) return;

    $product_price = $product->get_price();
    if ( isset( $product_price ) && ! empty( $product_price ) ) return;

    echo amhnj_get_html_contact_info();
}

function amhnj_get_html_contact_info() {
    return "<p class='alert alert-info'>
        برای سفارش تماس بگیرید:
        <a href='tel:09126011282'>09126011282</a>
        -
        <a href='tel:09364763076'>09364763076</a>
    </p>";
}
