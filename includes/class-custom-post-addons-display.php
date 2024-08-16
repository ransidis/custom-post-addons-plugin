<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Post_Addons_Display {

	private static $instance;

	private function __construct() {
		add_filter( 'the_content', array( $this, 'modify_post_content' ) );
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function modify_post_content( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		$options       = get_option( 'custom_post_addons_options' );
		$position      = isset( $options['position'] ) ? $options['position'] : 'below';
		$text_content  = isset( $options['text_content'] ) ? $options['text_content'] : '';
		$button_text   = isset( $options['button_text'] ) ? $options['button_text'] : '';
		$button_url    = isset( $options['button_url'] ) ? $options['button_url'] : '';
		$button_color  = isset( $options['button_color'] ) ? $options['button_color'] : '#000000';
		$button_radius = isset( $options['button_radius'] ) ? intval( $options['button_radius'] ) : 0;
		$button_font_size = isset( $options['button_font_size'] ) ? intval( $options['button_font_size'] ) : 16;
		$button_padding = isset( $options['button_padding'] ) ? esc_attr( $options['button_padding'] ) : '10px';
		$button_text_color = isset( $options['button_text_color'] ) ? $options['button_text_color'] : '#ffffff';

		$custom_content = '<div class="custom-post-addons-content">';
		$custom_content .= wpautop( $text_content );
		if ( $button_text && $button_url ) {
			$custom_content .= '<a href="' . esc_url( $button_url ) . '" class="custom-post-addons-button" style="background-color:' . esc_attr( $button_color ) . '; border-radius:' . intval( $button_radius ) . 'px; font-size:' . intval( $button_font_size ) . 'px; padding:' . esc_attr( $button_padding ) . '; color:' . esc_attr( $button_text_color ) . ';">';
			$custom_content .= esc_html( $button_text ) . '</a>';
		}
		$custom_content .= '</div>';

		if ( 'above' === $position ) {
			return $custom_content . $content;
		} else {
			return $content . $custom_content;
		}
	}
}
