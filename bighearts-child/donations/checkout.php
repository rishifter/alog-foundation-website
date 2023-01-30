<?php
if ( is_admin() )
	return;

require_once( get_stylesheet_directory() . '/donations/utils.php' );

// Modifications to the checkout page

// Remove added to cart message on checkout
add_filter( 'wc_add_to_cart_message_html', '__return_false', 99 );

// Check if cart has donations
if ( cart_has_virtual_product() ) {
	// Remove extra checkout fields for donations in cart
	add_filter( 'woocommerce_checkout_fields', 'rt_simplify_checkout_virtual' );

	// Disable Notes field on checkout
	add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

	// Disable coupon field on checkout
	add_filter( 'woocommerce_coupons_enabled', '__return_false' );

	// Change order button text to Pay
	add_filter( 'woocommerce_order_button_text', 'rt_custom_button_text', 10 );

	//  Change YOUR ORDER and other text on checkout for donations
	add_filter( 'gettext', 'rt_wc_translations', 10 );


	// Add checkout styles
	add_action( 'woocommerce_before_checkout_form', 'rt_checkout_styles' );

	// Avoid virtual and physical products combination in WooCommerce cart
	add_filter( 'woocommerce_add_to_cart_validation', 'rt_add_to_cart_validation', 10, 3 );
	add_action( 'woocommerce_check_cart_items', 'rt_check_cart_items' );

}


// Remove extra checkout fields for donations in cart
function rt_simplify_checkout_virtual( $fields ) {
	unset( $fields['billing']['billing_company'] );
	unset( $fields['billing']['billing_address_1'] );
	unset( $fields['billing']['billing_address_2'] );
	unset( $fields['billing']['billing_city'] );
	unset( $fields['billing']['billing_postcode'] );
	unset( $fields['billing']['billing_country'] );
	unset( $fields['billing']['billing_state'] );
	//   unset($fields['billing']['billing_phone']);

	return $fields;
}

//  Change YOUR ORDER text on checkout for donations
function rt_wc_translations( $translated ) {
	$text = array(
		'Your order' => 'Your Donation',
		'Product' => 'Cause',
		'Billing Details' => 'Donor Details',
	);

	$translated = str_ireplace( array_keys( $text ), $text, $translated );

	return $translated;
}


// Change order button text to Pay
function rt_custom_button_text( $button_text ) {
	return 'Pay';
}

// Add checkout styles
function rt_checkout_styles() {
	echo "<style>";
	echo ".woocommerce-form-coupon-toggle,
			.elementor-widget-wgl-header-cart{
				display: none
			}";
	echo ".woocommerce-checkout-review-order-table thead tr th:first-of-type {
				color: transparent;
				position: relative;
			}
			.woocommerce-checkout-review-order-table thead tr th:first-of-type::after {
				content: 'Cause';
				color: black;
				position: absolute;
				left: 40px;
			}
			";
	echo "</style>";

}

// Avoid virtual and physical products combination in WooCommerce cart
function rt_add_to_cart_validation( $passed, $product_id, $quantity ) {
	$is_virtual = $is_physical = false;
	$product = wc_get_product( $product_id );

	if ( $product->is_virtual() ) {
		$is_virtual = true;
	} else {
		$is_physical = true;
	}

	// Loop though cart items
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		// Check for specific product categories
		if (
			( $cart_item['data']->is_virtual() && $is_physical )
			|| ( ! $cart_item['data']->is_virtual() && $is_virtual )
		) {
			wc_add_notice( __( "You can't have a donation and a shop item in the same cart. Please remove either to proceed.", "woocommerce" ), 'error' );
			return false;
		}
	}
	return $passed;
}
function rt_check_cart_items() {
	$has_virtual = $has_physical = false;

	// Loop though cart items
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( $cart_item['data']->is_virtual() ) {
			$has_virtual = true;
		} else {
			$has_physical = true;
		}
	}

	if ( $has_virtual && $has_physical ) {
		// Display an error notice (and avoid checkout)
		wc_add_notice( __( "You can't have a donation and a shop item in the same cart. Please remove either to proceed.", "woocommerce" ), 'error' );
	}
}
?>