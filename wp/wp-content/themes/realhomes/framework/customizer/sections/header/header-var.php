<?php
/**
 * Section:	`Responsive Header`
 * Panel: 	`Header`
 *
 * @since 3.4.1
 */

if ( ! function_exists( 'inspiry_responsive_header_customizer' ) ) :

	/**
	 * inspiry_header_search_form_customizer.
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 * @since  3.4.1
	 */

	function inspiry_responsive_header_customizer( WP_Customize_Manager $wp_customize ) {

		/**
		 * Header Search Form
		 */


		/* Search Form Appearance in Header */
		if ( 'modern' === INSPIRY_DESIGN_VARIATION ) {

			$wp_customize->add_section( 'inspiry_header_mod_variations_section', array(
				'title' => esc_html__( 'Header Variations', 'framework' ),
				'panel' => 'inspiry_header_panel',
			) );

			$wp_customize->add_setting(
				'inspiry_header_mod_variation_option', array(
					'type'    => 'option',
					'default' => 'one',
					'sanitize_callback' => 'inspiry_sanitize_radio',
				)
			);

			$wp_customize->add_control(
				new Inspiry_Custom_Radio_Image_Control(
					$wp_customize,
					'inspiry_header_mod_variation_option',
					array(
						'section'     => 'inspiry_header_mod_variations_section',
						'label'       => esc_html__( 'Header Layout', 'framework' ),
						'description' => esc_html__( 'Choose your desired layout.', 'framework' ),
						'settings'    => 'inspiry_header_mod_variation_option',
						'choices'     => array(
							'one'   => get_template_directory_uri() . '/assets/modern/images/header-one.png',
							'two'   => get_template_directory_uri() . '/assets/modern/images/header-two.png',
							'three' => get_template_directory_uri() . '/assets/modern/images/header-three.png',
						)
					)
				)
			);
		}
	}

	add_action( 'customize_register', 'inspiry_responsive_header_customizer' );
endif;
