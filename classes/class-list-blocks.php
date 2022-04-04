<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_List_Blocks {
	/**
	 * Gets it started.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function __construct() {
		add_action( 'acf/init',                                    [ $this, 'register_blocks' ], 10, 3 );
		add_action( 'acf/init',                                    [ $this, 'register_field_group' ], 10, 3 );
		add_filter( 'acf/load_field/key=mai_list_icon_clone',      [ $this, 'load_list_icon' ] );
		add_filter( 'acf/load_field/key=mai_list_item_icon_clone', [ $this, 'load_list_item_icon' ] );
		add_filter( 'acf/load_field/key=mai_list_columns_clone',   [ $this, 'load_list_columns' ] );
	}

	/**
	 * Registers blocks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function register_blocks() {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		acf_register_block_type(
			[
				'name'            => 'mai-list',
				'title'           => __( 'Mai List', 'mai-lists' ),
				'description'     => __( 'Simple and beautiful icon or numbered lists and responsive icon feature grids.', 'mai-lists' ),
				'render_callback' => [ $this, 'do_list' ],
				'category'        => 'widget',
				'keywords'        => [ 'list', 'number' ],
				'icon'            => $this->get_list_svg_icon(),
				'enqueue_assets'  => function() {
					mai_enqueue_list_styles();
				},
				'supports'        => [
					'align'  => false,
					'anchor' => true,
					'mode'   => false,
					'jsx'    => true,
				],
			]
		);

		acf_register_block_type(
			[
				'name'            => 'mai-list-item',
				'title'           => __( 'Mai List Item', 'mai-lists' ),
				'description'     => __( 'A list item in for Mai List block.', 'mai-lists' ),
				'render_callback' => [ $this, 'do_list_item' ],
				'category'        => 'widget',
				'icon'            => $this->get_list_item_svg_icon(),
				'parent'          => [ 'acf/mai-list' ],
				'supports'        => [
					'align' => false,
					'mode'  => false,
					'jsx'   => true,
				],
			]
		);
	}

	/**
	 * Renders the list container.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function do_list( $block, $content = '', $is_preview = false ) {
		$args = [
			'preview'                => $is_preview,
			'content'                => $this->get_list_inner_blocks(),
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

		if ( isset( $block['anchor'] ) && ! empty( $block['anchor'] ) ) {
			$args['id'] = $block['anchor'];
		}

		if ( isset( $block['className'] ) && ! empty( $block['className'] ) ) {
			$args['class'] = $block['className'];
		}

		$list = new Mai_List( $args );

		echo $list->get();
	}

	/**
	 * Renders each list item.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function do_list_item( $block, $content = '', $is_preview = false ) {
		$args = [
			'preview'          => $is_preview,
			'content'          => $this->get_list_item_inner_blocks(),
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

	/**
	 * Gets list inner blocks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get_list_inner_blocks() {
		$allowed    = [ 'acf/mai-list-item' ];
		$template   = [
			[ 'acf/mai-list-item', [], [] ],
			// [ 'acf/mai-list-item', [], [] ],
			// [ 'acf/mai-list-item', [], [] ],
		];

		return sprintf( '<InnerBlocks allowedBlocks="%s" template="%s" />', esc_attr( wp_json_encode( $allowed ) ), esc_attr( wp_json_encode( $template ) ) );
	}

	/**
	 * Gets list item inner blocks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get_list_item_inner_blocks() {
		$template = [
			[ 'core/paragraph', [], [] ],
		];

		return sprintf( '<InnerBlocks template="%s" />', esc_attr( wp_json_encode( $template ) ) );
	}

	/**
	 * Registers field groups.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function register_field_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

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
							'top-end'    => __( 'Right', 'mai-lists' ),
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
						'key'               => 'mai_list_icon_color_clone',
						'label'             => __( 'Icon Color', 'mai-engine' ),
						'name'              => 'icon_color_clone',
						'type'              => 'clone',
						'display'           => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
						'clone'             => [ 'mai_icon_color', 'mai_icon_color_custom', 'mai_icon_background', 'mai_icon_background_custom' ],
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
						'key'               => 'mai_list_icon_border_radius',
						'label'             => __( 'Icon Border Radius', 'mai-lists' ),
						'name'              => 'icon_border_radius',
						'type'              => 'number',
						'placeholder'       => '0',
						'append'            => 'px',
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
									'field'          => 'mai_list_location',
									'operator'       => '==',
									'value'          => 'start',
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
									'field'          => 'mai_list_location',
									'operator'       => '==',
									'value'          => 'start',
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
						'key'          => 'mai_list_columns_clone',
						'label'        => __( 'Columns', 'mai-lists' ),
						'name'         => 'columns_clone',
						'type'         => 'clone',
						'display'      => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
						'clone'        => [
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
				'active' => true,
			]
		);

		acf_add_local_field_group(
			[
				'key'       => 'mai_list_item_field_group',
				'title'     => __( 'Mai List Item', 'mai-lists' ),
				'fields'    => [
					[
						'key'               => 'mai_list_item_icon_clone',
						'label'             => __( 'Icon', 'mai-lists' ),
						'name'              => 'icon_clone',
						'type'              => 'clone',
						'display'           => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
						'clone'             => [ 'mai_icon_style', 'mai_icon_choices', 'mai_icon_brand_choices' ],
					],
					[
						'key'               => 'mai_list_item_icon_color_clone',
						'label'             => __( 'Icon Color', 'mai-engine' ),
						'name'              => 'icon_color_clone',
						'type'              => 'clone',
						'display'           => 'group', // 'group' or 'seamless'. 'group' allows direct return of actual field names via get_field( 'style' ).
						'clone'             => [ 'mai_icon_color', 'mai_icon_color_custom', 'mai_icon_background', 'mai_icon_background_custom' ],
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
				'active'  => true,
			]
		);
	}

	/**
	 * Sets default icon.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function load_list_icon( $field ) {
		if ( ! ( isset( $field['sub_fields'] ) && $field['sub_fields'] ) ) {
			return $fields;
		}

		foreach ( $field['sub_fields'] as $index => $sub_field ) {
			if ( ! isset( $sub_field['key'] ) || 'mai_icon_choices' !== $sub_field['key'] ) {
				continue;
			}

			$field['sub_fields'][ $index ]['default_value'] = 'check';
		}

		return $field;
	}

	/**
	 * Remove default icon.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function load_list_item_icon( $field ) {
		if ( ! ( isset( $field['sub_fields'] ) && $field['sub_fields'] ) ) {
			return $fields;
		}

		foreach ( $field['sub_fields'] as $index => $sub_field ) {
			if ( ! isset( $sub_field['key'] ) ) {
				continue;
			}

			// Can't add an empty default because we can't pass it from the list to the list item.
			// if ( 'mai_icon_style' === $sub_field['key'] ) {
			// 	$field['sub_fields'][ $index ]['default_value'] = '';
			// 	$field['sub_fields'][ $index ]['choices']       = array_merge( [ '' => __( 'Default', 'mai-lists' ) ], $field['sub_fields'][ $index ]['choices'] );
			// }

			if ( 'mai_icon_choices' === $sub_field['key'] ) {
				$field['sub_fields'][ $index ]['default_value'] = null;
			}
		}

		return $field;
	}

	/**
	 * Set default columns values.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function load_list_columns( $field ) {
		if ( ! ( isset( $field['sub_fields'] ) && $field['sub_fields'] ) ) {
			return $fields;
		}

		foreach ( $field['sub_fields'] as $index => $sub_field ) {
			if ( ! isset( $sub_field['key'] ) || empty( $sub_field['key'] ) ) {
				continue;
			}

			switch ( $sub_field['key'] ) {
				case 'mai_columns':
					$field['sub_fields'][ $index ]['default_value'] = 1;
					break;
				case 'mai_column_gap':
					$field['sub_fields'][ $index ] = $this->get_gap_values( $field['sub_fields'][ $index ] );
				break;
				case 'mai_row_gap':
					$field['sub_fields'][ $index ] = $this->get_gap_values( $field['sub_fields'][ $index ] );
				break;
			}
		}

		return $field;
	}

	/**
	 * Gets gap values.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function get_gap_values( $field ) {
		$field['type']          = 'select';
		$field['allow_null']    = 0;
		$field['multiple']      = 0;
		$field['ui']            = 0;
		$field['ajax']          = 0;
		$field['default_value'] = 'md';
		$field['choices']       = [
			''     => __( 'None', 'mai-engine' ),
			'xxxs' => __( '3XS', 'mai-engine' ),
			'xxs'  => __( '2XS', 'mai-engine' ),
			'xs'   => __( 'XS', 'mai-engine' ),
			'sm'   => __( 'S', 'mai-engine' ),
			'md'   => __( 'M', 'mai-engine' ),
			'lg'   => __( 'L', 'mai-engine' ),
			'xl'   => __( 'XL', 'mai-engine' ),
			'xxl'  => __( '2XL', 'mai-engine' ),
			'xxxl' => __( '3XL', 'mai-engine' ),
		];

		return $field;
	}

	/**
	 * Gets list block svg icon.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_list_svg_icon() {
		return '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>';
	}

	/**
	 * Gets list item block svg icon.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_list_item_svg_icon() {
		return '<svg role="img" aria-hidden="true" focusable="false" style="display:block;" width="24" height="24" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><path d="M22,17.5c-0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l-0,-1Z" style="fill:#231f20;"/><path d="M16,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,17.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M4,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M22,5.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-15,0c-0.276,0 -0.5,0.224 -0.5,0.5l-0,1c-0,0.276 0.224,0.5 0.5,0.5l15,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g><g><path d="M22,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M16,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M10.5,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-3.5,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l3.5,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/><path d="M4,11.5c0,-0.276 -0.224,-0.5 -0.5,-0.5l-1,0c-0.276,0 -0.5,0.224 -0.5,0.5l0,1c0,0.276 0.224,0.5 0.5,0.5l1,0c0.276,0 0.5,-0.224 0.5,-0.5l0,-1Z" style="fill:#231f20;"/></g></svg>';
	}
}
