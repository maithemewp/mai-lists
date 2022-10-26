<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

add_action( 'acf/init', 'mai_register_list_block' );
/**
 * Registers the list blocks.
 *
 * @since 1.0.0
 * @since TBD Converted to block.json via `register_block_type()`.
 *
 * @return void
 */
function mai_register_list_block() {
	if ( ! class_exists( 'acf_pro' ) ) {
		return;
	}

	$icon    = '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>';
	$version = acf_get_setting( 'version' );

	// If at least ACF 6.0.0, register via block.json.
	if ( $version && version_compare( $version, '6.0.0', '>=' ) ) {
		// Mai List.
		register_block_type( __DIR__ . '/block.json',
			[
				'icon' => $icon,
			]
		);
	}
	// ACF Pro less than 6.0.0, register the old ACF way.
	elseif ( function_exists( 'acf_register_block_type' ) ) {
		// Mai List.
		acf_register_block_type(
			[
				'name'              => 'mai-list',
				'title'             => __( 'Mai List', 'mai-lists' ),
				'description'       => __( 'Simple and beautiful icon or numbered lists and responsive icon feature grids.', 'mai-lists' ),
				'render_callback'   => 'mai_do_list_block',
				'category'          => 'widgets',
				'keywords'          => [ 'list', 'number' ],
				'icon'              => $icon,
				'api_version'       => 2,
				'acf_block_version' => 2,
				'supports'          => [
					'align'  => false,
					'anchor' => true,
					'mode'   => false,
					'jsx'    => true,
				],
			]
		);
	}
}

/**
 * Renders the list container.
 *
 * @since 0.1.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content The block content.
 * @param bool     $is_preview Whether or not the block is being rendered for editing preview.
 * @param int      $post_id The current post being edited or viewed.
 * @param WP_Block $wp_block The block instance (since WP 5.5).
 * @param array    $context The block context array.
 *
 * @return void
 */
function mai_do_list_block( $attributes, $content = '', $is_preview = false, $post_id = 0, $wp_block, $context ) {
	$allowed  = [ 'acf/mai-list-item' ];
	$template = [ [ 'acf/mai-list-item', [], [] ] ];
	$inner    = sprintf( '<InnerBlocks allowedBlocks="%s" template="%s" />', esc_attr( wp_json_encode( $allowed ) ), esc_attr( wp_json_encode( $template ) ) );
	$args     = [
		'preview'                => $is_preview,
		'content'                => $inner,
		'type'                   => get_field( 'type' ),
		'location'               => get_field( 'location' ),
		'style'                  => get_field( 'style' ),
		'icon'                   => get_field( 'icon' ),
		'icon_brand'             => get_field( 'icon_brand' ),
		'color_icon'             => get_field( 'color_icon' ),
		'color_background'       => get_field( 'color_background' ),
		'icon_size'              => get_field( 'icon_size' ),
		'icon_padding'           => get_field( 'icon_padding' ),
		'icon_border_radius'     => get_field( 'icon_border_radius' ),
		'icon_margin_top'        => get_field( 'icon_margin_top' ),
		'content_margin_top'     => get_field( 'content_margin_top' ),
		'icon_gap'               => get_field( 'icon_gap' ),
		'columns'                => get_field( 'columns' ),
		'columns_responsive'     => get_field( 'columns_responsive' ),
		'columns_md'             => get_field( 'columns_md' ),
		'columns_sm'             => get_field( 'columns_sm' ),
		'columns_xs'             => get_field( 'columns_xs' ),
		'align_columns'          => get_field( 'align_columns' ),
		'align_columns_vertical' => get_field( 'align_columns_vertical' ),
		'column_gap'             => get_field( 'column_gap' ),
		'row_gap'                => get_field( 'row_gap' ),
		'margin_top'             => get_field( 'margin_top' ),
		'margin_bottom'          => get_field( 'margin_bottom' ),
	];

	// Swap for brand.
	if ( 'brands' === $args['style'] ) {
		$args['icon'] = $args['icon_brand'];
	}

	// Remove brand.
	unset( $args['icon_brand'] );

	if ( isset( $attributes['anchor'] ) && ! empty( $attributes['anchor'] ) ) {
		$args['id'] = $attributes['anchor'];
	}

	if ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ) {
		$args['class'] = $attributes['className'];
	}

	$list = new Mai_List( $args );

	echo $list->get();
}
