<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Custom_Post_Addons_Admin {

	private static $instance;

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_admin_menu() {
		add_menu_page(
			__( 'Custom Post Addons', CUSTOM_POST_ADDONS_TEXT_DOMAIN ),
			__( 'CP Addons', CUSTOM_POST_ADDONS_TEXT_DOMAIN ),
			'manage_options',
			'custom-post-addons',
			array( $this, 'create_admin_page' ),
			'dashicons-insert-before'
		);
	}

	public function register_settings() {
		register_setting( 'custom_post_addons_settings', 'custom_post_addons_options', array( $this, 'sanitize' ) );

		add_settings_section(
			'custom_post_addons_section',
			__( 'Add Custom Text and a Button to All Posts', CUSTOM_POST_ADDONS_TEXT_DOMAIN ),
			null,
			'custom-post-addons'
		);

		$this->add_settings_field( 'position', __( 'Position of Content', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'position_callback' );
		$this->add_settings_field( 'text_content', __( 'Text Content', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'text_content_callback' );
		$this->add_settings_field( 'button_text', __( 'Button Text', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_text_callback' );
		$this->add_settings_field( 'button_url', __( 'Button URL', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_url_callback' );
		$this->add_settings_field( 'button_color', __( 'Button Color', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_color_callback' );
		$this->add_settings_field( 'button_radius', __( 'Button Radius', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_radius_callback' );
		$this->add_settings_field( 'button_font_size', __( 'Button Font Size', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_font_size_callback' );
		$this->add_settings_field( 'button_padding', __( 'Button Padding', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_padding_callback' );
		$this->add_settings_field( 'button_text_color', __( 'Button Text Color', CUSTOM_POST_ADDONS_TEXT_DOMAIN ), 'button_text_color_callback' );
	}

	private function add_settings_field( $id, $title, $callback ) {
		add_settings_field(
			$id,
			$title,
			array( $this, $callback ),
			'custom-post-addons',
			'custom_post_addons_section'
		);
	}

	public function sanitize( $input ) {
		$sanitized_input = array();
		$sanitized_input['position']        = sanitize_text_field( $input['position'] );
		$sanitized_input['text_content']    = wp_kses_post( $input['text_content'] );
		$sanitized_input['button_text']     = sanitize_text_field( $input['button_text'] );
		$sanitized_input['button_url']      = esc_url_raw( $input['button_url'] );
		$sanitized_input['button_color']    = sanitize_hex_color( $input['button_color'] );
		$sanitized_input['button_radius']   = intval( $input['button_radius'] );
		$sanitized_input['button_font_size'] = intval( $input['button_font_size'] );
		$sanitized_input['button_padding']  = sanitize_text_field( $input['button_padding'] );
		$sanitized_input['button_text_color'] = sanitize_hex_color( $input['button_text_color'] );

		return $sanitized_input;
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Custom Post Addons', CUSTOM_POST_ADDONS_TEXT_DOMAIN ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'custom_post_addons_settings' );
				do_settings_sections( 'custom-post-addons' );
				submit_button( __( 'Save', CUSTOM_POST_ADDONS_TEXT_DOMAIN ) );
				?>
			</form>
		</div>
		<?php
	}

	public function position_callback() {
		$this->render_select( 'position', array(
			'above' => __( 'Above the post', CUSTOM_POST_ADDONS_TEXT_DOMAIN ),
			'below' => __( 'Below the post', CUSTOM_POST_ADDONS_TEXT_DOMAIN )
		) );
	}

	private function render_select( $name, $options ) {
		$selected_option = $this->get_option_value( $name );
		echo '<select name="custom_post_addons_options[' . esc_attr( $name ) . ']">';
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected_option, $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	public function text_content_callback() {
		$value = esc_attr( $this->get_option_value( 'text_content' ) );
		wp_editor( $value, 'custom_post_addons_text_content', array(
			'textarea_name' => 'custom_post_addons_options[text_content]',
			'media_buttons' => true,
			'textarea_rows' => 10,
			'tinymce'       => true,
			'quicktags'     => true
		) );
	}

	public function button_text_callback() {
		$this->render_input( 'button_text', 'text', array( 'placeholder' => 'Click Me' ) );
	}

	public function button_url_callback() {
		$this->render_input( 'button_url', 'url', array( 'placeholder' => 'https://wordpress.org/' ) );
	}

	public function button_color_callback() {
		$this->render_input( 'button_color', 'text', array( 'class' => 'color-picker' ) );
	}

	public function button_radius_callback() {
		$this->render_input( 'button_radius', 'number', array( 'min' => 0, 'placeholder' => '0' ) );
	}

	public function button_font_size_callback() {
		$this->render_input( 'button_font_size', 'number', array( 'min' => 0, 'placeholder' => '0' ) );
	}

	public function button_padding_callback() {
		$this->render_input( 'button_padding', 'text', array( 'placeholder' => '0px' ) );
	}

	public function button_text_color_callback() {
		$this->render_input( 'button_text_color', 'text', array( 'class' => 'color-picker' ) );
	}

	private function render_input( $name, $type, $attributes = array() ) {
		$value = esc_attr( $this->get_option_value( $name ) );
		$attrs = '';
		foreach ( $attributes as $attr_name => $attr_value ) {
			$attrs .= esc_attr( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
		}
		echo '<input type="' . esc_attr( $type ) . '" name="custom_post_addons_options[' . esc_attr( $name ) . ']" value="' . $value . '" ' . $attrs . '/>';
	}

	private function get_option_value( $name ) {
		$options = get_option( 'custom_post_addons_options' );
		return isset( $options[ $name ] ) ? $options[ $name ] : '';
	}
}
