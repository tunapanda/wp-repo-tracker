<?php
/**
 * This file demonstrates how to use 'taxonomy' field
 */
add_filter( 'rwmb_meta_boxes', 'your_prefix_taxonomy_demo' );
function your_prefix_taxonomy_demo( $meta_boxes ) {
	$meta_boxes[] = array(
		'title' => __( 'Taxonomy Demo', 'your-prefix' ),
		'fields' => array(
			array(
				'name'    => __( 'Taxonomy', 'your-prefix' ),
				'id'      => 'prefix_taxonomy',
				'type'    => 'taxonomy',

				// Taxonomy name
				'taxonomy' => 'category',

				// How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
				'field_type'     => 'select_tree',

				// Additional arguments for get_terms() function. Optional
				'query_args'     => array(),
			),
		),
	);
	return $meta_boxes;
}
