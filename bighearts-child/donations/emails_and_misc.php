<?php
if ( is_admin() )
	return;

require_once( get_stylesheet_directory() . '/donations/utils.php' );

// send an email to the customer's billing email address when the order is marked as "completed" and the order contains a product from the "Donations" category. 

// add_action( 'woocommerce_order_status_completed', 'send_custom_order_email' );
function send_custom_order_email( $order_id ) {
	$order = wc_get_order( $order_id );
	$has_donation = false;
	// Check if the order contains a product from the "Donations" category
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( has_term( 'donations', 'product_cat', $product->get_id() ) ) {
			$has_donation = true;
			break;
		}
	}
	// Send the email if the order contains a product from the "Donations" category
	if ( $has_donation ) {
		// $to = $order->get_billing_email();
		// $subject = 'Thank you for your donation';
		// $message = 'Thank you for your generous donation. Your support is greatly appreciated.';
		// $headers = array('Content-Type: text/html; charset=UTF-8');
		// wp_mail($to, $subject, $message, $headers);
		WC()->mailer()->emails['WC_Email_Customer_Invoice']->trigger( $order_id );
	}
}

// Change the order status to "Completed" if the order contains a product from the "Donations" category
// add_action( 'woocommerce_checkout_order_created', 'complete_orders_with_donations_2' );
// add_action('woocommerce_thankyou', 'complete_orders_with_donations');
add_action( 'woocommerce_payment_complete', 'complete_orders_with_donations' );
// add_action('mer', 'complete_orders_with_donations');
function complete_orders_with_donations( $order_id ) {
	$order = wc_get_order( $order_id );
	$has_donation = false;
	// Check if the order contains a product from the "Donations" category
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( has_term( 'donations', 'product_cat', $product->get_id() ) ) {
			$has_donation = true;
			break;
		}
	}
	if ( $has_donation ) {
		// Remove the default completed order email first
		add_filter( 'woocommerce_email_recipient_customer_new_order', 'rt_no_recipient', 9999, 3 );
		add_filter( 'woocommerce_email_recipient_customer_processing_order', 'rt_no_recipient', 9999, 3 );
		add_filter( 'woocommerce_email_recipient_customer_completed_order', 'rt_no_recipient', 9999, 3 );

		// // New order emails
		// remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

		// // Processing order emails
		// remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		// remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

		// // Completed order emails
		// remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

		$order->update_status( 'completed' );
		WC()->mailer()->emails['WC_Email_Customer_Invoice']->trigger( $order_id );
	}
}

function complete_orders_with_donations_2( $order_id ) {
	$order = wc_get_order( $order_id );
	$has_donation = false;
	// Check if the order contains a product from the "Donations" category
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( has_term( 'donations', 'product_cat', $product->get_id() ) ) {
			$has_donation = true;
			break;
		}
	}
	if ( $has_donation ) {
		// Remove the default completed order email first
		add_filter( 'woocommerce_email_recipient_customer_new_order', 'rt_no_recipient', 10, 3 );
		add_filter( 'woocommerce_email_recipient_customer_processing_order', 'rt_no_recipient', 10, 3 );
		add_filter( 'woocommerce_email_recipient_customer_completed_order', 'rt_no_recipient', 10, 3 );

		// New order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

		// Processing order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

		// Completed order emails
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

		// $order->update_status( 'completed' );
		// WC()->mailer()->emails['WC_Email_Customer_Invoice']->trigger( $order_id );
	}
}
function rt_no_recipient( $recipient, $order, $email ) {
	$recipient = '';
	return $recipient;
}



add_filter( 'wpo_wcpdf_document_is_allowed', 'rt_disallow_document_on_all_but_donations', 10, 2 );
function rt_disallow_document_on_all_but_donations( $allowed, $document ) {
	$allowed = false;
	$order = $document->order;

	if ( ! empty( $order ) && $document->get_type() == 'invoice' ) { // replace 'custom-template' with your custom template name or default names like 'invoice', 'packing-slip', etc.

		$has_donation = false;
		// Check if the order contains a product from the "Donations" category
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( has_term( 'donations', 'product_cat', $product->get_id() ) ) {
				$has_donation = true;
				break;
			}
		}

		if ( $has_donation ) {
			$allowed = true;
		}

	}
	return $allowed;
}