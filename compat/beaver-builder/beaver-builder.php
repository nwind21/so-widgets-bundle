<?php

class SiteOrigin_Widgets_Bundle_Beaver_Builder {

	/**
	 * Get the singleton instance
	 *
	 * @return SiteOrigin_Widgets_Bundle_Beaver_Builder
	 */
	public static function single() {
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_active_widgets_scripts' ) );

		add_filter( 'siteorigin_widgets_get_field_name', array( $this, 'bb_get_field_name' ) );
	}

	function enqueue_active_widgets_scripts() {
		global $wp_widget_factory;

		// Beaver Builder does it's editing in the front end so enqueue required form scripts for active widgets.
		foreach ( $wp_widget_factory->widgets as $class => $widget_obj ) {
			if ( ! empty( $widget_obj ) && is_object( $widget_obj ) && is_subclass_of( $widget_obj, 'SiteOrigin_Widget' ) ) {
				ob_start();
				$widget_obj->form( array() );
				ob_clean();
			}
		}

		if ( ! wp_script_is( 'wp-color-picker' ) ) {
			// wp-color-picker hasn't been registered because we're in the front end, so enqueue with full args.
			wp_enqueue_script( 'iris', '/wp-admin/js/iris.min.js', array(
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			), '1.0.7', 1 );

			wp_enqueue_script( 'wp-color-picker', '/wp-admin/js/color-picker' . SOW_BUNDLE_JS_SUFFIX . '.js', array( 'iris' ), false, 1 );

			wp_enqueue_style( 'wp-color-picker' );

			// Localization args for when wp-color-picker script hasn't been registered.
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
				'clear'         => __( 'Clear', 'so-widgets-bundle' ),
				'defaultString' => __( 'Default', 'so-widgets-bundle' ),
				'pick'          => __( 'Select Color', 'so-widgets-bundle' ),
				'current'       => __( 'Current Color', 'so-widgets-bundle' ),
			) );
		}

		wp_enqueue_style( 'sow-icons-for-beaver', plugin_dir_url( __FILE__ ) . 'styles.css' );
	}

	function bb_get_field_name( $name ) {
		return preg_replace( '/\[[^\]]*\]/', '[]', $name, 1 );
	}
}

SiteOrigin_Widgets_Bundle_Beaver_Builder::single();
