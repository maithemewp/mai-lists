<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gets an list.
 *
 * @since 0.1.0
 *
 * @param array $args The list args.
 *
 * @return string
 */
function mai_get_list( $args ) {
	$list = new Mai_List( $args );
	return $list->get();
}

/**
 * Gets an list item.
 *
 * @since 0.1.0
 *
 * @param array  $args  The list args.
 * @param string $icon  The icon name.
 * @param string $style The icon style.
 *
 * @return string
 */
function mai_get_list_item( $args, $icon = 'check', $style = 'light' ) {
	$list = new Mai_List_Item( $args, $icon = 'check', $style = 'light' );
	return $list->get();
}

/**
 * Gets the script/style `.min` suffix for minified files.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_lists_get_suffix() {
	static $suffix = null;

	if ( ! is_null( $suffix ) ) {
		return $suffix;
	}

	$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$suffix = $debug ? '' : '.min';

	return $suffix;
}
