<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

add_action( 'acf/init', 'mai_register_list_item_block' );
/**
 * Registers the list blocks.
 *
 * @since 1.0.0
 * @since TBD Converted to block.json via `register_block_type()`.
 *
 * @return void
 */
function mai_register_list_item_block() {
	if ( ! class_exists( 'acf_pro' ) ) {
		return;
	}

	$icon    = '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M22,17.5c-0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l-0,-1Z" style="fill:#231f20;"/><path d="M16,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M16,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>';
	$version = acf_get_setting( 'version' );

	// If at least ACF 6.0.0, register via block.json.
	if ( $version && version_compare( $version, '6.0.0', '>=' ) ) {
		// Mai List Item.
		register_block_type( __DIR__ . '/block.json',
			[
				'icon' => $icon,
			]
		);
	}
	// ACF Pro less than 6.0.0, register the old ACF way.
	elseif ( function_exists( 'acf_register_block_type' ) ) {
		// Mai List Item.
		acf_register_block_type(
			[
				'name'              => 'mai-list-item',
				'title'             => __( 'Mai List Item', 'mai-lists' ),
				'description'       => __( 'A list item in for Mai List block.', 'mai-lists' ),
				'render_callback'   => 'mai_do_list_item_block',
				'category'          => 'widget',
				'icon'              => $icon,
				'parent'            => [ 'acf/mai-list' ],
				'api_version'       => 2,
				'acf_block_version' => 2,
				'supports'          => [
					'align' => false,
					'mode'  => false,
					'jsx'   => true,
				],
			]
		);
	}
}

/**
 * Renders each list item.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_do_list_item_block( $block, $content = '', $is_preview = false ) {
	$template = [ [ 'core/paragraph', [], [] ] ];
	$inner    = sprintf( '<InnerBlocks template="%s" />', esc_attr( wp_json_encode( $template ) ) );
	$args     = [
		'preview'          => $is_preview,
		'content'          => $inner,
		'style'            => get_field( 'style' ),
		'icon'             => get_field( 'icon' ),
		'icon_brand'       => get_field( 'icon_brand' ),
		'color_icon'       => get_field( 'color_icon' ),
		'color_background' => get_field( 'color_background' ),
	];

	// Swap for brand.
	if ( 'brands' === $args['style'] ) {
		$args['icon'] = $args['icon_brand'];
	}

	// Remove brand.
	unset( $args['icon_brand'] );

	if ( isset( $block['className'] ) && ! empty( $block['className'] ) ) {
		$args['class'] = $block['className'];
	}

	$item = new Mai_List_Item( $args );

	echo $item->get();
}
