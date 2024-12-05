<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

class Mai_List_Item_Block {
	/**
	 * Construct the class.
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function hooks() {
		add_action( 'acf/init', [ $this, 'register_block' ] );
		add_action( 'acf/init', [ $this, 'register_field_group' ], 12 ); // 12 to make sure clone fields are registered.
	}

	/**
	 * Registers block.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function register_block() {
		if ( ! class_exists( 'Mai_Engine' ) ) {
			return;
		}

		register_block_type( __DIR__ . '/block.json',
			[
				'icon'            => '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M22,17.5c-0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l-0,-1Z" style="fill:#231f20;"/><path d="M16,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M16,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>',
				'render_callback' => [ $this, 'render_block' ],
			]
		);
	}

	/**
	 * Callback function to render the block.
	 *
	 * @since TBD
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $content    The block content.
	 * @param bool     $is_preview Whether or not the block is being rendered for editing preview.
	 * @param int      $post_id    The current post being edited or viewed.
	 * @param WP_Block $block      The block instance (since WP 5.5).
	 *
	 * @return void
	 */
	function render_block( $attributes, $content, $is_preview, $post_id, $block ) {
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

		// Add class.
		if ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ) {
			$args['class'] = $attributes['className'];
		}

		// Set the list item.
		$item = new Mai_List_Item( $args );

		// Render.
		echo $item->get();
	}

	/**
	 * Registers field group.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function register_field_group() {
		if ( ! class_exists( 'Mai_Engine' ) ) {
			return;
		}

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		if ( ! class_exists( 'Mai_Icons_Plugin' ) ) {
			$link   = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=mai-theme' ), __( 'Mai Icons', 'mai-engine' ) );
			/* translators: %s is replaced with the linked plugin name. */
			$text   = sprintf( __( '%s plugin required.', 'mai-engine' ), $link );
			$fields = [
				[
					'key'       => 'mai_icon_missing',
					'label'     => '',
					'type'      => 'message',
					'message'   => $text,
					'new_lines' => 'wpautop',
				],
			];
		} else {
			acf_add_local_field_group(
				[
					'key'       => 'mai_list_item_field_group',
					'title'     => __( 'Mai List Item', 'mai-lists' ),
					'fields'    => [
						[
							'key'     => 'mai_list_item_icon_clone',
							'label'   => __( 'Icon', 'mai-lists' ),
							'name'    => 'icon_clone',
							'type'    => 'clone',
							'display' => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
							'clone'   => [ 'mai_icon_style', 'mai_icon_choices', 'mai_icon_brand_choices' ],
						],
						[
							'key'     => 'mai_list_item_icon_color_clone',
							'label'   => __( 'Icon Color', 'mai-engine' ),
							'name'    => 'icon_color_clone',
							'type'    => 'clone',
							'display' => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
							'clone'   => [ 'mai_icon_color', 'mai_icon_color_custom', 'mai_icon_background', 'mai_icon_background_custom' ],
						],
					],
					'location' => [
						[
							[
								'param'    => 'block',
								'operator' => '==',
								'value'    => 'acf/mai-list-item',
							],
						],
					],
				]
			);
		}
	}
}
