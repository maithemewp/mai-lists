<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'mai_lists_register_styles' );
/**
 * Registers the plugin scripts and styles.
 *
 * @access private
 *
 * @since TBD
 *
 * @return void
 */
function mai_lists_register_styles() {
	$suffix = mai_lists_get_suffix();

	wp_register_style( 'mai-lists', MAI_LISTS_PLUGIN_URL . sprintf( 'assets/mai-lists%s.css', $suffix ), [], MAI_LISTS_VERSION . '.' . date( 'njYHi', filemtime( MAI_LISTS_PLUGIN_DIR . sprintf( 'assets/mai-lists%s.css', $suffix ) ) ) );
}

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
