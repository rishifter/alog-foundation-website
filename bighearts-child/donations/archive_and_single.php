<?php
if ( is_admin() )
	return;

require_once( get_stylesheet_directory() . '/donations/utils.php' );

// Modifications to the Donations archive and single product page

// Redirect to checkout page on adding to cart
add_filter( 'woocommerce_add_to_cart_redirect', 'rt_add_to_cart_redirect' );
function rt_add_to_cart_redirect( $url ) {
	$url = null;

	if ( cart_has_virtual_product() )
		$url = wc_get_checkout_url();

	return $url;
}


// Exclude products from a particular category on the shop page
add_action( 'woocommerce_product_query', 'rt_custom_product_query' );
function rt_custom_product_query( $q ) {
	if ( is_shop() ) {
		$tax_query = (array) $q->get( 'tax_query' );

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => array( 'donations' ),
			'operator' => 'NOT IN'
		);


		$q->set( 'tax_query', $tax_query );

	}

}
add_filter( 'get_terms', 'rt_get_subcategory_terms', 10, 3 );
function rt_get_subcategory_terms( $terms, $taxonomies, $args ) {
	$new_terms = array();
	// if it is a product category and on the shop page
	if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() && is_shop() ) {
		foreach ( $terms as $key => $term ) {
			if ( ! in_array( $term->slug, array( 'donations' ) ) ) { //pass the slug name here
				$new_terms[] = $term;
			}
		}
		$terms = $new_terms;
	}
	return $terms;
}



// Shop and archive page goal cards
add_action( 'woocommerce_after_shop_loop_item_title', 'custom_loop_product_title', 2 );
function custom_loop_product_title() {
	global $product;
	$pid = $product->get_id();

	if ( is_product_donation_category( $pid ) ) {
		$raised = get_product_net_revenue( $pid );
		$goal = get_field( 'donation_goal', $pid );
		$diff = $goal - $raised;
		$percent = ( $raised / $goal ) * 100 . '%';

		echo '<div class="card__progress">
                <div class="progress__bar">
                    <div class="bar__container" style="width: ' . $percent . '">
                        <span class="bar__label">' . $percent . '</span>
                    </div>
                </div>
                <div class="progress__stats">
                    <div class="stats__goal">
                        <div class="stats__info">
                            <span class="stats__label">Goal:</span>
                            <span class="stats__value">₹' . number_format( $goal ) . '</span>
                        </div>
                    </div>
                    <div class="stats__raised">
                        <div class="stats__info">
                            <div class="stats__info--aligned">
                                <span class="stats__label">Raised:</span>
                                <span class="stats__value">₹' . number_format( $raised ) . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="stats__lack">
                        <div class="stats__info">
                            <span class="stats__label">To Go:</span>
                            <span class="stats__value">₹' . number_format( $diff ) . '</span>
                        </div>
                    </div>
                </div>
            </div>';
	}
}

// change the "Add to Cart" button text for products in a specific category 
add_filter( 'woocommerce_product_single_add_to_cart_text', 'rt_add_to_cart_button_text', 10, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'rt_add_to_cart_button_text', 10, 2 );
function rt_add_to_cart_button_text( $button_text, $product ) {
	if ( is_product_donation_category( $product->get_id() ) ) {
		$button_text = 'Donate';
	}

	return $button_text;
}

// Add Donation single page styles
// add_action( 'woocommerce_before_single_product', 'rt_donation_single_styles' );
// function rt_donation_single_styles() {
// 	global $product;
// 	$pid = $product->get_id();

// 	if ( is_product_donation_category( $pid ) ) {
// 		echo "<style>";
// 		echo ".elementor-widget-wgl-header-cart{
//                     display: none
//                 }";
// 		echo "</style>";
// 	}

// }


?>