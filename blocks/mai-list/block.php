<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

class Mai_List_Block {
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
		add_action( 'acf/init',                  [ $this, 'register_block' ] );
		add_action( 'acf/init',                  [ $this, 'register_field_group' ], 12 ); // 12 to make sure clone fields are registered.
		add_filter( 'render_block_acf/mai-list', [ $this, 'add_css' ], 50, 2 );
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
				'icon'            => '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>',
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

		// Add anchor.
		if ( isset( $attributes['anchor'] ) && ! empty( $attributes['anchor'] ) ) {
			$args['id'] = $attributes['anchor'];
		}

		// Add class.
		if ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ) {
			$args['class'] = $attributes['className'];
		}

		// Set the list.
		$list = new Mai_List( $args );

		// Render.
		echo $list->get();
	}

	/**
	 * Adds CSS on demand.
	 * This runs after the standard `render_block` filters
	 * to fix instances where `render_block` hides/removes the block.
	 *
	 * @since TBD
	 *
	 * @param string $block_content The block content.
	 * @param array  $block         The block data.
	 *
	 * @return string
	 */
	function add_css( $block_content, $block ) {
		if ( is_admin() || ! $block_content || ! did_action( 'wp_print_styles' ) ) {
			return $block_content;
		}

		static $loaded = false;

		if ( ! $loaded ) {
			$loaded        = true;
			$suffix        = mai_lists_get_suffix();
			$src           = MAI_LISTS_PLUGIN_URL . sprintf( 'assets/mai-lists%s.css', $suffix ) . '?ver=' . MAI_LISTS_VERSION . '.' . date( 'njYHi', filemtime( MAI_LISTS_PLUGIN_DIR . sprintf( 'assets/mai-lists%s.css', $suffix ) ) );
			$html          = sprintf( '<link rel="stylesheet" href="%s">', esc_url( $src ) );
			$block_content = $this->str_replace_first( '<li', $html . '<li', $block_content );
		}

		return $block_content;
	}

	/**
	 * Replace first instance of a string.
	 *
	 * @since TBD
	 *
	 * @param string $from    The string to find.
	 * @param string $replace The string to replace.
	 * @param string $string  The string to search.
	 *
	 * @return string
	 */
	function str_replace_first( $find, $replace, $string ) {
		$find = '/' . preg_quote( $find, '/' ) . '/';
		return preg_replace( $find, $replace, $string, 1 );
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
					'key'    => 'mai_list_field_group',
					'title'  => __( 'Mai List', 'mai-lists' ),
					'fields' => [

						/***********
						 * General *
						 ***********/

						[
							'key'   => 'mai_list_display_section',
							'label' => __( 'Display', 'mai-lists' ),
							'type'  => 'tab',
						],
						[
							'key'           => 'mai_list_type',
							'label'         => __( 'List Type', 'mai-lists' ),
							'name'          => 'type',
							'type'          => 'button_group',
							'default_value' => 'ul',
							'choices'       => [
								'ul' => __( 'Icons', 'mai-lists' ),
								'ol' => __( 'Numbers', 'mai-lists' ),
							],
							'wrapper' => [
								'class' => 'mai-acf-button-group mai-acf-button-group-small',
							],
						],
						[
							'key'           => 'mai_list_location',
							'label'         => __( 'Icon Location', 'mai-lists' ),
							'name'          => 'location',
							'type'          => 'button_group',
							'default_value' => 'start',
							'choices'       => [
								'start'      => __( 'Default', 'mai-lists' ),
								'top-start'  => __( 'Start', 'mai-lists' ),
								'top-center' => __( 'Center', 'mai-lists' ),
								'top-end'    => __( 'End', 'mai-lists' ),
							],
							'wrapper' => [
								'class' => 'mai-acf-button-group mai-acf-button-group-small',
							],
						],
						[
							'key'               => 'mai_list_icon_clone',
							'label'             => __( 'Icon', 'mai-lists' ),
							'name'              => 'icon_clone',
							'type'              => 'clone',
							'display'           => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
							'clone'             => [ 'mai_icon_style', 'mai_icon_choices', 'mai_icon_brand_choices' ],
							'conditional_logic' => [
								[
									[
										'field'          => 'mai_list_type',
										'operator'       => '==',
										'value'          => 'ul',
									],
								],
							],
						],
						[
							'key'     => 'mai_list_icon_color_clone',
							'label'   => __( 'Icon Color', 'mai-engine' ),
							'name'    => 'icon_color_clone',
							'type'    => 'clone',
							'display' => 'group',                                                                                              // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
							'clone'   => [ 'mai_icon_color', 'mai_icon_color_custom', 'mai_icon_background', 'mai_icon_background_custom' ],
						],
						[
							'key'         => 'mai_list_icon_size',
							'label'       => __( 'Icon Size', 'mai-lists' ),
							'name'        => 'icon_size',
							'type'        => 'number',
							'placeholder' => '20',
							'append'      => 'px',
						],
						[
							'key'         => 'mai_list_icon_padding',
							'label'       => __( 'Icon Padding', 'mai-lists' ),
							'name'        => 'icon_padding',
							'type'        => 'number',
							'placeholder' => '0',
							'append'      => 'px',
						],
						[
							'key'         => 'mai_list_icon_border_radius',
							'label'       => __( 'Icon Border Radius', 'mai-lists' ),
							'name'        => 'icon_border_radius',
							'type'        => 'number',
							'placeholder' => '0',
							'append'      => 'px',
						],
						[
							'key'           => 'mai_list_icon_gap',
							'label'         => __( 'Icon Gap', 'mai-lists' ),
							'name'          => 'icon_gap',
							'type'          => 'select',
							'default_value' => 'md',
							'choices'       => [
								'xxs' => __( 'XXS', 'mai-lists' ),
								'xs'  => __( 'XS', 'mai-lists' ),
								'sm'  => __( 'S', 'mai-lists' ),
								'md'  => __( 'M', 'mai-lists' ),
								'lg'  => __( 'L', 'mai-lists' ),
								'xl'  => __( 'XL', 'mai-lists' ),
								'xxl' => __( '2XL', 'mai-lists' ),
							],
						],
						[
							'key'               => 'mai_list_icon_margin_top',
							'label'             => __( 'Icon Margin Top', 'mai-lists' ),
							'name'              => 'icon_margin_top',
							'type'              => 'number',
							'placeholder'       => '0',
							'append'            => 'px',
							'conditional_logic' => [
								[
									[
										'field'    => 'mai_list_location',
										'operator' => '==',
										'value'    => 'start',
									],
								],
							],
						],
						[
							'key'               => 'mai_list_content_margin_top',
							'label'             => __( 'Content Margin Top', 'mai-lists' ),
							'name'              => 'content_margin_top',
							'type'              => 'number',
							'placeholder'       => '0',
							'append'            => 'px',
							'conditional_logic' => [
								[
									[
										'field'    => 'mai_list_location',
										'operator' => '==',
										'value'    => 'start',
									],
								],
							],
						],

						/**********
						 * Layout *
						 **********/

						[
							'key'   => 'mai_list_layout_section',
							'label' => __( 'Layout', 'mai-lists' ),
							'type'  => 'tab',
						],
						[
							'key'     => 'mai_list_columns_clone',
							'label'   => __( 'Columns', 'mai-lists' ),
							'name'    => 'columns_clone',
							'type'    => 'clone',
							'display' => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
							'clone'   => [
								'mai_columns',
								'mai_columns_responsive',
								'mai_columns_md',
								'mai_columns_sm',
								'mai_columns_xs',
								'mai_align_columns',
								'mai_align_columns_vertical',
								'mai_column_gap',
								'mai_row_gap',
								'mai_margin_top',
								'mai_margin_bottom',
							],
						],
					],
					'location' => [
						[
							[
								'param'    => 'block',
								'operator' => '==',
								'value'    => 'acf/mai-list',
							],
						],
					],
				]
			);
		}
	}
}
