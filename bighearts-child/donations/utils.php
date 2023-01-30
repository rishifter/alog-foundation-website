<?php
// Does the order contain a product from the category passed?
function is_donation( $order_id, $donations_cat = 'donations' ) {
	if ( is_admin() || empty( WC()->cart->get_cart() ) ) {
		return;
	}
	$order = wc_get_order( $order_id );
	$is_donation = false;
	// Check if the order contains a product from the "Donations" category
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( has_term( $donations_cat, 'product_cat', $product->get_id() ) ) {
			$is_donation = true;
			break;
		}
	}
	return $is_donation;
}


// Does the cart contain a virtual product
function cart_has_virtual_product() {
	if ( is_admin() || is_null( WC()->cart ) ) {
		return;
	}
	$has_virtual = false;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		// Check if there are non-virtual products
		if ( $cart_item['data']->is_virtual() ) {
			$has_virtual = true;
			break;
		}
	}

	return $has_virtual;
}

// Does the product id belong to donations category?
function is_product_donation_category( $pid, $donations_cat = 'donations' ) {
	return has_term( $donations_cat, 'product_cat', $pid );
}

// Get net revenue for product for donation goals calculation
function get_product_net_revenue( $pid ) {
	global $wpdb;

	return (float) $wpdb->get_var( $wpdb->prepare( "
        SELECT SUM(o.product_net_revenue) 
        FROM {$wpdb->prefix}wc_order_product_lookup o 
        INNER JOIN {$wpdb->prefix}posts p
            ON o.order_id = p.ID
        WHERE ( p.post_status = 'wc-processing' OR p.post_status = 'wc-completed' )
            AND o.product_id = %d
    ", $pid ) );
}
?>