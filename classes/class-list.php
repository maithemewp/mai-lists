<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_List {
	protected $defaults;
	protected $args;

	/**
	 * Gets it started.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function __construct( $args ) {
		$this->defaults = $this->get_defaults();
		$args           = wp_parse_args( $args, $this->get_defaults() );
		$this->args     = $this->get_sanitized_args( $args );

		// Force defaults.
		// We always need these properties added to the markup.
		// This is mostly for nested lists/blocks but also fixes weirdness when first adding a block.
		$this->args['location']            = $this->args['location'] ?: $this->defaults['location'];
		$this->args['style']               = $this->args['style'] ?: $this->defaults['style'];
		$this->args['icon']                = $this->args['icon'] ?: $this->defaults['icon'];
		$this->args['color_icon']          = $this->args['color_icon'] ?: $this->defaults['color_icon'];
		$this->args['icon_size']           = $this->args['icon_size'] ?: $this->defaults['icon_size'];
		$this->args['icon_margin_top']     = '' !== $this->args['icon_margin_top'] ? $this->args['icon_margin_top'] : $this->defaults['icon_margin_top'];
		$this->args['content_margin_top']  = '' !== $this->args['content_margin_top'] ? $this->args['content_margin_top'] : $this->defaults['content_margin_top'];
	}

	/**
	 * Gets defaults.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get_defaults() {
		return [
			'preview'                => false,
			'content'                => '', // Required.

			// General.
			'type'                   => 'ul',
			'location'               => 'start',
			'style'                  => 'light',
			'icon'                   => 'check',
			'color_icon'             => 'currentColor',
			'color_background'       => '',
			'icon_border_radius'     => '',
			'icon_size'              => '20',
			'icon_padding'           => '0',
			'icon_margin_top'        => '2',
			'content_margin_top'     => '0',
			'icon_gap'               => 'md',

			// Layout.
			'columns'                => 1,
			'columns_responsive'     => '',
			'columns_md'             => '',
			'columns_sm'             => '',
			'columns_xs'             => '',
			'align_columns'          => '',
			'align_columns_vertical' => '',
			'column_gap'             => 'md',
			'row_gap'                => 'lg',
			'margin_top'             => '',
			'margin_bottom'          => '',

			// Core.
			'id'                     => '',
			'class'                  => '',
		];
	}

	/**
	 * Gets sanitized args.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get_sanitized_args( $args ) {
		$args['preview']            = mai_sanitize_bool( $args['preview'] );
		$args['content']            = $args['content'];

		// General.
		$args['type']               = esc_html( $args['type'] );
		$args['location']           = esc_html( $args['location'] );
		$args['style']              = esc_html( $args['style'] );
		$args['icon']               = esc_html( $args['icon'] );
		$args['color_icon']         = esc_html( $args['color_icon'] );
		$args['color_background']   = esc_html( $args['color_background'] );
		$args['icon_size']          = esc_html( $args['icon_size'] );
		$args['icon_padding']       = esc_html( $args['icon_padding'] );
		$args['icon_border_radius'] = esc_html( $args['icon_border_radius'] );
		$args['icon_margin_top']    = esc_html( $args['icon_margin_top'] );
		$args['content_margin_top'] = esc_html( $args['content_margin_top'] );
		$args['icon_gap']           = esc_html( $args['icon_gap'] );

		// Layout.
		$args                       = mai_get_columns_sanitized( $args );

		// Core.
		$args['id']                 = sanitize_html_class( $args['id'] );
		$args['class']              = esc_attr( $args['class'] );

		return $args;
	}

	/**
	 * Gets the list.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get() {
		if ( ! class_exists( 'Mai_Engine' ) ) {
			return;
		}

		$atts = [
			'class' => 'mai-list',
		];

		if ( $this->args['id'] ) {
			$atts['id'] = $this->args['id'];
		}

		$atts = mai_get_columns_atts( $atts, $this->args, true );

		if ( $this->args['margin_top'] ) {
			$atts['class'] = mai_add_classes( sprintf( 'has-%s-margin-top', $this->args['margin_top'] ), $atts['class'] );
		}

		if ( $this->args['margin_bottom'] ) {
			$atts['class'] = mai_add_classes( sprintf( 'has-%s-margin-bottom', $this->args['margin_bottom'] ), $atts['class'] );
		}

		if ( $this->args['type'] && 'ol' === $this->args['type'] ) {
			$type          = 'ol';
			$atts['class'] = mai_add_classes( 'mai-list-numbers', $atts['class'] );
		} else {
			$type          = 'ul';
			$atts['class'] = mai_add_classes( 'mai-list-icons', $atts['class'] );
		}

		if ( $this->args['location'] ) {
			if ( mai_has_string( 'top', $this->args['location'] ) ) {
				$atts['class']  = mai_add_classes( 'mai-list-top', $atts['class'] );
				$atts['style'] .= sprintf( '--icon-align:%s;', str_replace( 'top-', '', $this->args['location'] ) );
			}

			$atts['class'] = mai_add_classes( sprintf( 'mai-list-%s', $this->args['location'] ), $atts['class'] );
		}

		if ( $this->args['class'] ) {
			$atts['class'] = mai_add_classes( $this->args['class'], $atts['class'] );
		}

		if ( $this->args['icon'] && $this->args['style'] ) {
			$icon_url = mai_get_svg_icon_url( $this->args['icon'], $this->args['style'] );

			if ( $icon_url ) {
				$atts['style'] .= sprintf( "--icon:url('%s');", $icon_url );
			}
		}

		if ( $this->args['color_icon'] ) {
			$atts['style'] .= sprintf( '--icon-color:%s;', mai_get_color_css( $this->args['color_icon'] ) );
		}

		if ( $this->args['color_background'] ) {
			$atts['style'] .= sprintf( '--icon-background:%s;', mai_get_color_css( $this->args['color_background'] ) );
		}

		if ( '' !== $this->args['icon_size'] ) {
			$atts['style'] .= sprintf( '--icon-size:%s;', mai_get_unit_value( $this->args['icon_size'] ) );
		}

		if ( '' !== $this->args['icon_padding'] ) {
			$atts['style'] .= sprintf( '--icon-padding:%s;', mai_get_unit_value( $this->args['icon_padding'] ) );
		}

		if ( '' !== $this->args['icon_border_radius'] ) {
			$atts['style'] .= sprintf( '--icon-border-radius:%s;', mai_get_unit_value( $this->args['icon_border_radius'] ) );
		}

		if ( $this->args['icon_gap'] ) {
			$atts['style'] .= sprintf( '--icon-gap:var(--spacing-%s);', $this->args['icon_gap'] );
		}

		if ( 'start' === $this->args['location'] ) {
			if ( '' !== $this->args['icon_margin_top'] ) {
				$atts['style'] .= sprintf( '--icon-margin-top:%s;', mai_get_unit_value( $this->args['icon_margin_top'] ) );
			}

			if ( '' !== $this->args['content_margin_top'] ) {
				$atts['style'] .= sprintf( '--content-margin-top:%s;', mai_get_unit_value( $this->args['content_margin_top'] ) );
			}
		}

		$html = genesis_markup(
			[
				'open'    => "<{$type} %s>",
				'close'   => "</{$type}>",
				'context' => 'mai-list',
				'content' => $this->args['content'],
				'echo'    => false,
				'atts'    => $atts,
			]
		);

		return $html;
	}
}
