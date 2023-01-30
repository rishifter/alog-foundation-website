<?php if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly ?>

<table class="header">
	<tr>
		<td>
			<div class=" logo">
				<?php
				if ( $this->has_header_logo() ) {
					do_action( 'wpo_wcpdf_before_shop_logo', $this->get_type(), $this->order );
					$this->header_logo();
					do_action( 'wpo_wcpdf_after_shop_logo', $this->get_type(), $this->order );
				} else {
					$this->title();
				}
				?>
			</div>
		</td>
		<td>
			<h1 class="title">
				<?php $this->shop_name(); ?>
			</h1>
			<div class="subtitle">
				<?php $this->shop_address(); ?>
			</div>
		</td>
		<td class="cin">
			<?php print_r( $this->get_extra_1() ); ?>
		</td>
	</tr>
</table>

<table class="meta">
	<tr>
		<td></td>
		<td>
			<h3 class="label">Payment Receipt</h3>
		</td>
		<td>
			<div class="details">
				<div class="underline">No: <span>
						<?php $this->order_number(); ?>
					</span></div>
				<div class="underline">Date: <span>
						<?php $this->invoice_date(); ?>
					</span></div>
			</div>
		</td>
	</tr>
</table>

<table class="donation">
	<tr>
		<td>
			<h4 class="donation underline">
				Received with thanks Rupees <span>
					<?php
					echo $this->order->get_total() . "/-";
					?>
				</span> through <span>
					<?php echo $this->get_payment_method() ? $this->get_payment_method() : "Online"; ?>
				</span> payment dated <span>
					<?php $this->invoice_date(); ?>
				</span> from Mr./Mrs./Ms.<span>
					<?php $this->custom_field( 'billing_first_name' ); ?>&nbsp; <?php $this->custom_field( 'billing_last_name' ); ?>
				</span> as donation for cause <?php foreach ( $this->get_order_items() as $item_id => $item ) : ?><span>
						<?php echo $item['name']; ?>
					</span>
				<?php endforeach; ?>.
			</h4>
		</td>
	</tr>
</table>



<table class="footer">
	<tr>
		<td>
			<?php echo $this->get_footer() ? $this->get_footer() : ""; ?>
		</td>

		<td>
			<div class="logo">
				<?php
				if ( $this->get_extra_3() ) {
					echo $this->get_extra_3();
				}
				?>
				<?php
				if ( $this->get_extra_3() )
					print_r( $this->get_extra_2() ); ?>
			</div>
		</td>
	</tr>
</table>