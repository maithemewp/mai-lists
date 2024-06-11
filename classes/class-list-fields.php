<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_List_Fields {
	/**
	 * Gets it started.
	 *
	 * @since 0.1.0
	 *
	 * @return void
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
		add_filter( 'acf/load_field/key=mai_list_icon_clone',      [ $this, 'load_list_icon' ] );
		add_filter( 'acf/load_field/key=mai_list_item_icon_clone', [ $this, 'load_list_item_icon' ] );
		add_filter( 'acf/load_field/key=mai_list_columns_clone',   [ $this, 'load_list_columns' ] );
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
		if ( ! is_admin() ) {
			return $field;
		}

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
		if ( ! is_admin() ) {
			return $field;
		}

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
		if ( ! is_admin() ) {
			return $field;
		}

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
}
