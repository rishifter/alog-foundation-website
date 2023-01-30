<?php
/**
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// CUSTOM EMAIL FOR DONATIONS ONLY
$donation_cat = 'donations';
$is_donation = false;
foreach ($order->get_items() as $order_item) {
	$variation_id = $order_item->get_variation_id();
	if (has_term($donation_cat, 'product_cat', $order_item["product_id"])) {
		$is_donation = true;
	} else {
		$is_donation = false;
	}
}

/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email); ?>

<?php /* translators: %s: Customer first name */?>
<p>
	<?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); ?>
</p>
<?php
if ($is_donation) { ?>
	<p>
		<?php
		/* translators: %s Order date */
		printf(esc_html__('Here are the details of your donation made on %s:', 'woocommerce'), esc_html(wc_format_datetime($order->get_date_created())));
		?>
	</p>
	<h2>
		<?php
		/* translators: %s: Order ID. */
		echo wp_kses_post(sprintf(__('[Donation #%s]', 'woocommerce') . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format('c'), wc_format_datetime($order->get_date_created())));
		?>
	</h2>
	<div style="margin-bottom: 40px;">
		<!-- <pre>
											<?php print_r($order); ?>
									</pre> -->
		<table class="td" cellspacing="0" cellpadding="6"
			style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
			<thead>
				<tr>
					<th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;">
						<?php esc_html_e('Cause', 'woocommerce'); ?>
					</th>
					<th class="td" scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;">
						<?php esc_html_e('Amount', 'woocommerce'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$text_align = is_rtl() ? 'right' : 'left';
				$margin_side = is_rtl() ? 'left' : 'right';
				foreach ($order->get_items() as $item_id => $item):
					$product = $item->get_product();
					$image = '';

					if (is_object($product)) {
						$image = $product->get_image($image_size);
					}

					?>
					<tr
						class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
						<td class="td"
							style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
							<?php

							// Show title/image etc.
							if ($show_image) {
								echo wp_kses_post(apply_filters('woocommerce_order_item_thumbnail', $image, $item));
							}

							// Product name.
							echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false));
							?>
						</td>
						<td class="td"
							style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
							<?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
						</td>
					</tr>
					<?php endforeach; ?>
			</tbody>
			<tfoot>
				<?php
				$item_totals = $order->get_order_item_totals();
				?>
				<tr>
					<th class="td" scope="row"
						style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo 'border-top-width: 4px;'; ?>">
						<?php echo wp_kses_post($item_totals['payment_method']['label']); ?>
					</th>
					<td class="td"
						style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo 'border-top-width: 4px;'; ?>">
						<?php echo wp_kses_post($item_totals['payment_method']['value']); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
<?php } else {
	if ($order->needs_payment()) { ?>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %1$s Site title, %2$s Order pay link */
					__('An order has been created for you on %1$s. Your invoice is below, with a link to make payment when you’re ready: %2$s', 'woocommerce'),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_html(get_bloginfo('name', 'display')),
				'<a href="' . esc_url($order->get_checkout_payment_url()) . '">' . esc_html__('Pay for this order', 'woocommerce') . '</a>'
			);
			?>
		</p>

	<?php } else { ?>
		<p>
			<?php
			/* translators: %s Order date */
			printf(esc_html__('Here are the details of your order placed on %s:', 'woocommerce'), esc_html(wc_format_datetime($order->get_date_created())));
			?>
		</p>
	<?php
	}
}
?>


<?php
/**
 * Hook for the woocommerce_email_order_details.
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */

if (!$is_donation) {
	do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);
}

/**
 * Hook for the woocommerce_email_order_meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/**
 * Hook for woocommerce_email_customer_details.
 *
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);