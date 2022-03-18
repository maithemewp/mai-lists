<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_List_Item {
	protected $args;
	protected $icon;
	protected $style;

	/**
	 * Gets it started.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function __construct( $args ) {
		$args        = wp_parse_args( $args, $this->get_defaults() );
		$this->args  = $this->get_sanitized_args( $args );
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
			'preview'          => false,
			'style'            => '',
			'icon'             => '',
			'color_icon'       => '',
			'color_background' => '',
			'content'          => '',
			'class'            => '',
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
		$args['preview']          = mai_sanitize_bool( $args['preview'] );
		$args['style']            = esc_html( $args['style'] );
		$args['icon']             = esc_html( $args['icon'] );
		$args['color_icon']       = esc_html( $args['color_icon'] );
		$args['color_background'] = esc_html( $args['color_background'] );
		$args['content']          = $args['content'];
		$args['class']            = esc_attr( $args['class'] );

		return $args;
	}

	/**
	 * Gets the list item.
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
			'class' => 'mai-list-item is-column',
			'style' => '',
		];

		if ( $this->args['class'] ) {
			$atts['class'] = mai_add_classes( $this->args['class'], $summary_atts['class'] );
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

		return genesis_markup(
			[
				'open'    => '<li %s>',
				'close'   => '</li>',
				'context' => 'mai-list-item',
				'content' => sprintf( '<span class="mai-list-icon-wrap" role="presentation"><span class="mai-list-icon"></span></span><div class="mai-list-content">%s</div>', $this->args['content'] ),
				'echo'    => false,
				'atts'    => $atts,
			]
		);
	}
}
