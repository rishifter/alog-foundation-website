<?php

function wgl_child_scripts() {
	wp_enqueue_style( 'wgl-parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'wgl_child_scripts' );

/**
 * Your code here.
 *
 */

add_action( 'init', 'donations_init' );
function donations_init() {
	require_once( get_stylesheet_directory() . '/donations/utils.php' );
	require_once( get_stylesheet_directory() . '/donations/checkout.php' );
	require_once( get_stylesheet_directory() . '/donations/archive_and_single.php' );
	require_once( get_stylesheet_directory() . '/donations/emails_and_misc.php' );
}

// PDF 80g size
// add_filter( 'wpo_wcpdf_paper_format', 'wcpdf_a5_packing_slips', 1, 2 );
// function wcpdf_a5_packing_slips( $paper_format, $template_type ) {
// 	$paper_format = 'a5';
// 	return $paper_format;
// }
// // add_filter( 'wpo_wcpdf_paper_orientation', 'wcpdf_landscape', 1, 2 );
// function wcpdf_landscape( $paper_orientation, $template_type ) {
// 	// use $template type ( 'invoice' or 'packing-slip') to set paper oriention for only one document type.
// 	$paper_orientation = 'landscape';
// 	return $paper_orientation;
// }


add_filter( 'wpo_wcpdf_paper_format', 'wcpdf_custom_inch_page_size', 10, 2 );
function wcpdf_custom_inch_page_size( $paper_format, $template_type ) {
	// change the values below
	$width = 8; //inches!
	$height = 4.5; //inches!

	//convert inches to points
	$paper_format = array( 0, 0, $width * 72, $height * 72 );

	return $paper_format;
}