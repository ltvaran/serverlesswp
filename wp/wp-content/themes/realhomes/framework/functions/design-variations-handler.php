<?php
/**
 * Design variation related functions
 *
 * This file contains all the functions related to design
 * variations of this theme.
 *
 * @package realhomes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rubik_google_font_url() {

	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not
	 * supported by Rubik, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Rubik font: on or off', 'framework' ) ) {
		$fonts[] = 'Rubik:400,400i,500,500i,700,700i';
	}

	/*
	 * This font is used on dashboard membership order completion page and will be included only on dashboard template.
	 *
	 * Translators: If there are characters in your language that are not
	 * supported by Damion, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Damion font: on or off', 'framework' ) && is_page_template('templates/dashboard.php') ) {
		$fonts[] = 'Damion';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg(
			array(
				'family'  => urlencode( implode( '|', $fonts ) ),
				'subset'  => urlencode( $subsets ),
				'display' => urlencode( 'fallback' ),
			),
			'https://fonts.googleapis.com/css'
		);
	}

	return esc_url_raw( $fonts_url );
}

if ( ! function_exists( 'inspiry_enqueue_theme_styles' ) ) {
	/**
	 * Load Required CSS Styles
	 */
	function inspiry_enqueue_theme_styles() {
		if ( ! is_page_template( 'templates/dashboard.php' ) ) {
			if ( 'classic' === INSPIRY_DESIGN_VARIATION ) {
				inspiry_enqueue_classic_styles();
			} elseif ( 'modern' === INSPIRY_DESIGN_VARIATION ) {
				inspiry_enqueue_modern_styles();
			}

			inspiry_enqueue_common_styles();
		}
	}

	add_action( 'wp_enqueue_scripts', 'inspiry_enqueue_theme_styles' );
}


if ( ! function_exists( 'inspiry_enqueue_theme_scripts' ) ) {
	/**
	 * Enqueue JavaScripts required for this theme
	 */
	function inspiry_enqueue_theme_scripts() {
		if ( ! is_page_template( 'templates/dashboard.php' ) ) {
			if ( 'classic' === INSPIRY_DESIGN_VARIATION ) {
				inspiry_enqueue_classic_scripts();
			} elseif ( 'modern' === INSPIRY_DESIGN_VARIATION ) {
				inspiry_enqueue_modern_scripts();
			}

			inspiry_enqueue_common_scripts();
		}
	}

	add_action( 'wp_enqueue_scripts', 'inspiry_enqueue_theme_scripts' );
}


if ( ! function_exists( 'inspiry_enqueue_classic_styles' ) ) {
	/**
	 * Function to load classic styles.
	 *
	 * @since 2.7.0
	 */
	function inspiry_enqueue_classic_styles() {
		if ( ! is_admin() ) {

			$js_dir_path  = '/assets/' . INSPIRY_DESIGN_VARIATION . '/scripts/';
			$css_dir_path = '/assets/' . INSPIRY_DESIGN_VARIATION . '/styles/';

			/**
			 * Settings for CSS optimisation
			 */
			$inspiry_optimise_css = get_option( 'inspiry_optimise_css' );

			/*
			 * Google Fonts
			 */
			wp_enqueue_style(
				'inspiry-google-fonts',
				inspiry_google_fonts(),
				array(),
				INSPIRY_THEME_VERSION
			);

			/*
			 * Register Default and Custom Styles
			 */
			wp_register_style(
				'parent-default',
				get_stylesheet_uri(),
				array(),
				INSPIRY_THEME_VERSION,
				'all'
			);

			// Flex Slider
			wp_dequeue_style( 'flexslider' );       // dequeue flexslider if it registered by a plugin.
			wp_deregister_style( 'flexslider' );    // deregister flexslider if it registered by a plugin.

			/*
			 * Main CSS
			 */
			if ( 'true' === $inspiry_optimise_css ) {
				wp_enqueue_style(
					'main-css',
					get_theme_file_uri( $css_dir_path . 'css/main.min.css' ),
					array(),
					INSPIRY_THEME_VERSION,
					'all'
				);
			} else {
				wp_enqueue_style(
					'main-css',
					get_theme_file_uri( $css_dir_path . 'css/main.css' ),
					array(),
					INSPIRY_THEME_VERSION,
					'all'
				);
			}

			/*
			 * RTL Styles
			 */
			if ( is_rtl() ) {
				if ( 'true' === $inspiry_optimise_css ) {
					wp_enqueue_style(
						'rtl-main-css',
						get_theme_file_uri( $css_dir_path . 'css/rtl-main.min.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				} else {
					wp_enqueue_style(
						'rtl-main-css',
						get_theme_file_uri( $css_dir_path . 'css/rtl-main.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				}
			}

			wp_add_inline_style( 'main-css', apply_filters( 'realhomes_classic_custom_css', '' ) );

			/*
			 * IF Visual Composer Plugins installed and activated
			 */
			if ( class_exists( 'Vc_Manager' ) ) {
				if ( 'true' === $inspiry_optimise_css ) {
					wp_enqueue_style(
						'vc-css',
						get_theme_file_uri( $css_dir_path . 'css/visual-composer.min.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				} else {
					wp_enqueue_style(
						'vc-css',
						get_theme_file_uri( $css_dir_path . 'css/visual-composer.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				}
			}

			// default css.
			wp_enqueue_style( 'parent-default' );
		}
	}
}


if ( ! function_exists( 'inspiry_enqueue_classic_scripts' ) ) {
	/**
	 * Function to load classic scripts.
	 *
	 * @since 2.7.0
	 */
	function inspiry_enqueue_classic_scripts() {
		if ( ! is_admin() ) {

			$js_dir_path = '/assets/' . INSPIRY_DESIGN_VARIATION . '/scripts/';

			/**
			 * Registering of Scripts
			 */

			// flexslider
			wp_dequeue_script( 'flexslider' );      // dequeue flexslider if it is enqueue by some plugin.

			// jQuery Easing.
			wp_register_script(
				'jquery-easing',
				get_theme_file_uri( $js_dir_path . 'vendors/jquery.easing.min.js' ),
				array( 'jquery' ),
				'1.4.1',
				true
			);

			// elasti slide.
			wp_register_script(
				'elastislide',
				get_theme_file_uri( $js_dir_path . 'vendors/elastislide/jquery.elastislide.js' ),
				array( 'jquery' ),
				null,
				true
			);


			// jcarousel.
			wp_register_script(
				'jcarousel',
				get_theme_file_uri( $js_dir_path . 'vendors/jquery.jcarousel.min.js' ),
				array( 'jquery' ),
				'0.2.9',
				true
			);

			// jQuery Transit.
			wp_register_script(
				'jqtransit',
				get_theme_file_uri( $js_dir_path . 'vendors/jquery.transit.min.js' ),
				array( 'jquery' ),
				'0.9.9',
				true
			);

			// Bootstrap.
//			wp_register_script(
//				'bootstrap',
//				get_theme_file_uri( $js_dir_path . 'vendors/bootstrap.min.js' ),
//				array( 'jquery' ),
//				null,
//				true
//			);


			// Search form script.
			wp_register_script(
				'inspiry-search',
				get_theme_file_uri( $js_dir_path . 'js/inspiry-search-form.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);

			// Theme's main script.
			wp_register_script(
				'custom',
				get_theme_file_uri( $js_dir_path . 'js/custom.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);

			/**
			 * Enqueue Scripts that are needed on all the pages
			 */
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-easing' );
			wp_enqueue_script( 'elastislide' );
			wp_enqueue_script( 'jcarousel' );
			wp_enqueue_script( 'jquery-form' );
			wp_enqueue_script( 'jqtransit' );

			if ( ! class_exists( 'iHomefinderAdmin' ) ) {
				wp_enqueue_script( 'bootstrap' );
			}

			/**
			 * Maps Script
			 */
			$map_type = inspiry_get_maps_type();
			if ( 'google-maps' == $map_type ) {
				inspiry_enqueue_google_maps();
			} else {
				inspiry_enqueue_open_street_map();
			}

			/**
			 * Property Submit and Edit page
			 */
			if ( is_page_template( 'templates/submit-property.php' ) ) {

				// For image upload.
				wp_enqueue_script( 'plupload' );

				// For sortable additional details.
				wp_enqueue_script( 'jquery-ui-sortable' );

				// Property Submit Script.
				wp_register_script(
					'property-submit',
					get_theme_file_uri( $js_dir_path . 'js/property-submit.js' ),
					array( 'jquery', 'wp-util', 'jquery-ui-sortable', 'plupload' ),
					INSPIRY_THEME_VERSION,
					true
				);

				// Data to print in JavaScript format above property submit script tag in HTML.
				$property_submit_data = array(
					'ajaxURL'       => admin_url( 'admin-ajax.php' ),
					'uploadNonce'   => wp_create_nonce( 'inspiry_allow_upload' ),
					'fileTypeTitle' => esc_html__( 'Valid file formats', 'framework' ),
				);

				wp_localize_script( 'property-submit', 'propertySubmit', $property_submit_data );
				wp_enqueue_script( 'property-submit' );

			}

			/**
			 * Edit profile template
			 */
			if ( is_page_template( 'templates/edit-profile.php' ) ) {

				// For image upload.
				wp_enqueue_script( 'plupload' );

				wp_register_script(
					'edit-profile',
					get_theme_file_uri( $js_dir_path . 'js/edit-profile.js' ),
					array( 'jquery', 'plupload' ),
					INSPIRY_THEME_VERSION,
					true
				);

				// Data to print in JavaScript format above edit profile script tag in HTML.
				$edit_profile_data = array(
					'ajaxURL'       => admin_url( 'admin-ajax.php' ),
					'uploadNonce'   => wp_create_nonce( 'inspiry_allow_upload' ),
					'fileTypeTitle' => esc_html__( 'Valid file formats', 'framework' ),
				);

				wp_localize_script( 'edit-profile', 'editProfile', $edit_profile_data );
				wp_enqueue_script( 'edit-profile' );
			}

			/**
			 * Script for comments reply
			 */
			if ( is_singular( 'post' ) || is_page() || is_singular( 'property' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}


			/* Print select status for rent to switch prices in properties search form */
			$rent_slug               = get_option( 'theme_status_for_rent' );
			$localized_search_params = array();
			if ( ! empty( $rent_slug ) ) {
				$localized_search_params['rent_slug'] = $rent_slug;
			}

			/* localize search parameters */
			wp_localize_script( 'inspiry-search', 'localizedSearchParams', $localized_search_params );


			/* Inspiry search form script */
			wp_enqueue_script( 'inspiry-search' );

			/*
			 * Google reCaptcha
			 */
			if ( class_exists( 'Easy_Real_Estate' ) ) {
				if ( ere_is_reCAPTCHA_configured() ) {

					$inspiry_contact_form_shortcode = get_option( 'inspiry_contact_form_shortcode' );
					$reCPATCHA_type                 = get_option( 'inspiry_reCAPTCHA_type', 'v2' );

					if ( 'v3' === $reCPATCHA_type ) {
						$render = get_option( 'theme_recaptcha_public_key' );
					} else {
						$render = 'explicit';
					}

					$recaptcha_src = esc_url_raw( add_query_arg( array(
						'render' => $render,
						'onload' => 'loadInspiryReCAPTCHA',
					), '//www.google.com/recaptcha/api.js' ) );

					if ( ! is_page_template( 'templates/contact.php' ) ) {
						// Enqueue google reCAPTCHA API.
						wp_enqueue_script(
							'rh-google-recaptcha',
							$recaptcha_src,
							array(),
							INSPIRY_THEME_VERSION,
							true
						);
					} elseif ( empty( $inspiry_contact_form_shortcode ) ) {
						// Enqueue google reCAPTCHA API.
						wp_enqueue_script(
							'rh-google-recaptcha',
							$recaptcha_src,
							array(),
							INSPIRY_THEME_VERSION,
							true
						);
					}
				}
			}

			/* custom js localization */
			$localized_array = array(
				'nav_title'          => esc_html__( 'Go to...', 'framework' ),
				'more_search_fields' => esc_html__( 'More fields', 'framework' ),
				'less_search_fields' => esc_html__( 'Less fields', 'framework' )
			);
			wp_localize_script( 'custom', 'localized', $localized_array );

			// Data to print in JavaScript format above custom js script tag in HTML.
			$custom_data = array(
				'video_width'  => get_option( 'inpsiry_property_video_popup_width', 1778 ),
				'video_height' => get_option( 'inspiry_property_video_popup_height', 1000 ),
			);
			wp_localize_script( 'custom', 'customData', $custom_data );

			$select_string = array(
				'select_noResult' => get_option( 'inspiry_select2_no_result_string', esc_html__( 'No Results Found!', 'framework' ), true )
			);
			wp_localize_script( 'custom', 'localizeSelect', $select_string );

			if ( is_page_template( 'templates/home.php' ) ) {
				$inspiry_cfos_success_redirect_page = get_post_meta( get_the_ID(), 'inspiry_cfos_success_redirect_page', true );
				if ( ! empty( $inspiry_cfos_success_redirect_page ) ) {
					$CFOSData = array( 'redirectPageUrl' => get_the_permalink( $inspiry_cfos_success_redirect_page ) );
					wp_localize_script( 'custom', 'CFOSData', $CFOSData );
				}
			}

			if ( is_page_template( 'templates/contact.php' ) ) {
				$inspiry_contact_form_success_redirect_page = get_post_meta( get_the_ID(), 'inspiry_contact_form_success_redirect_page', true );
				if ( ! empty( $inspiry_contact_form_success_redirect_page ) ) {
					$contactFromData = array( 'redirectPageUrl' => get_the_permalink( $inspiry_contact_form_success_redirect_page ) );
					wp_localize_script( 'custom', 'contactFromData', $contactFromData );
				}
			}

			/* Finally enqueue theme's main script */
			wp_enqueue_script( 'custom' );

		}
	}

}


if ( ! function_exists( 'inspiry_enqueue_modern_styles' ) ) {
	/**
	 * Function to load modern styles.
	 *
	 * @since 3.0.0
	 */
	function inspiry_enqueue_modern_styles() {

		if ( ! is_admin() ) {

			$js_dir_path  = '/assets/' . INSPIRY_DESIGN_VARIATION . '/scripts/';
			$css_dir_path = '/assets/' . INSPIRY_DESIGN_VARIATION . '/styles/';

			/**
			 * Settings for CSS optimisation
			 */
			$inspiry_optimise_css = get_option( 'inspiry_optimise_css' );

			/**
			 * Google Fonts
			 */
			wp_enqueue_style(
				'inspiry-google-fonts',
				inspiry_google_fonts(),
				array(),
				INSPIRY_THEME_VERSION
			);

			/**
			 * Rubik Google Font
			 */
			wp_enqueue_style(
				'realhomes-rubik-font',
				rubik_google_font_url(),
				array(),
				INSPIRY_THEME_VERSION
			);

			/**
			 * Register Default and Custom Styles
			 */
			wp_register_style(
				'parent-default',
				get_stylesheet_uri(),
				array(),
				INSPIRY_THEME_VERSION,
				'all'
			);

			// Flex Slider
			wp_dequeue_style( 'flexslider' );       // dequeue flexslider if it registered by a plugin.
			wp_deregister_style( 'flexslider' );    // deregister flexslider if it registered by a plugin.

			if ( is_singular( 'property' ) ) {

				// entypo fonts.
				wp_enqueue_style(
					'entypo-fonts',
					get_theme_file_uri( $css_dir_path . 'css/entypo.min.css' ),
					array(),
					INSPIRY_THEME_VERSION,
					'all'
				);
			}

			/**
			 * Main CSS
			 */
			if ( 'true' === $inspiry_optimise_css ) {
				wp_enqueue_style(
					'main-css',
					get_theme_file_uri( $css_dir_path . 'css/main.min.css' ),
					array(),
					INSPIRY_THEME_VERSION,
					'all'
				);
			} else {
				wp_enqueue_style(
					'main-css',
					get_theme_file_uri( $css_dir_path . 'css/main.css' ),
					array(),
					INSPIRY_THEME_VERSION,
					'all'
				);
			}

			wp_add_inline_style( 'main-css', apply_filters( 'realhomes_modern_custom_css', '' ) );

			/**
			 * RTL Styles
			 */
			if ( is_rtl() ) {
				if ( 'true' === $inspiry_optimise_css ) {
					wp_enqueue_style(
						'rtl-main-css',
						get_theme_file_uri( $css_dir_path . 'css/rtl-main.min.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				} else {
					wp_enqueue_style(
						'rtl-main-css',
						get_theme_file_uri( $css_dir_path . 'css/rtl-main.css' ),
						array(),
						INSPIRY_THEME_VERSION,
						'all'
					);
				}
			}

			// default css.
			wp_enqueue_style( 'parent-default' );

		}
	}
}


if ( ! function_exists( 'inspiry_enqueue_modern_scripts' ) ) {
	/**
	 * Function to load modern scripts.
	 *
	 * @since 3.0.0
	 */
	function inspiry_enqueue_modern_scripts() {

		if ( ! is_admin() ) {

			$js_dir_path = '/assets/' . INSPIRY_DESIGN_VARIATION . '/scripts/';

			// Flexslider.
			wp_dequeue_script( 'flexslider' );      // dequeue flexslider if it is enqueue by some plugin.

			// Progress bar.
			wp_register_script(
				'progress-bar',
				get_theme_file_uri( $js_dir_path . 'vendors/progressbar/dist/progressbar.min.js' ),
				array( 'jquery' ),
				'1.0.1',
				true
			);


			/**
			 * Edit profile template
			 */
			wp_enqueue_script( 'progress-bar' );
			wp_enqueue_script( 'jquery-form' );

			/**
			 * Maps Script
			 */
			$map_type = inspiry_get_maps_type();
			if ( 'google-maps' == $map_type ) {
				inspiry_enqueue_google_maps();
			} else {
				inspiry_enqueue_open_street_map();
			}


			// Search form script.
			wp_register_script(
				'inspiry-search',
				get_theme_file_uri( $js_dir_path . 'js/inspiry-search-form.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);

			// Theme's main script.
			wp_register_script(
				'custom',
				get_theme_file_uri( $js_dir_path . 'js/custom.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);

			/**
			 * Edit profile template
			 */
			if ( is_page_template( 'templates/edit-profile.php' ) ) {

				// For image upload.
				wp_enqueue_script( 'plupload' );

				wp_register_script(
					'edit-profile',
					get_theme_file_uri( $js_dir_path . 'js/edit-profile.js' ),
					array( 'jquery', 'plupload' ),
					INSPIRY_THEME_VERSION,
					true
				);

				// Data to print in JavaScript format above edit profile script tag in HTML.
				$edit_profile_data = array(
					'ajaxURL'       => admin_url( 'admin-ajax.php' ),
					'uploadNonce'   => wp_create_nonce( 'inspiry_allow_upload' ),
					'fileTypeTitle' => esc_html__( 'Valid file formats', 'framework' ),
				);

				wp_localize_script( 'edit-profile', 'editProfile', $edit_profile_data );
				wp_enqueue_script( 'edit-profile' );
			}

			/**
			 * Memberships template
			 */
			if ( is_page_template( 'templates/membership-plans.php' ) ) {

				wp_register_script(
					'rh-memberships',
					get_theme_file_uri( $js_dir_path . 'js/rh-memberships.js' ),
					array( 'jquery' ),
					INSPIRY_THEME_VERSION,
					true
				);

				// Localize the 'rh-memberships' script with needed data.
				$translation_array = array(
					'cancel_membership' => esc_html__( 'Cancel Membership', 'framework' ),
				);
				wp_localize_script( 'rh-memberships', 'rh_memberships', $translation_array );

				wp_enqueue_script( 'rh-memberships' );
			}

			/**
			 * My Properties Template
			 */
			if ( is_page_template( 'templates/my-properties.php' ) ) {
				wp_register_script(
					'my-properties',
					get_theme_file_uri( $js_dir_path . 'js/my-properties.js' ),
					array( 'jquery' ),
					INSPIRY_THEME_VERSION,
					true
				);
				wp_enqueue_script( 'my-properties' );
			}

			/**
			 * Property Submit and Edit page
			 */
			if ( is_page_template( 'templates/submit-property.php' ) ) {

				// For image upload.
				wp_enqueue_script( 'plupload' );

				// For sortable additional details.
				wp_enqueue_script( 'jquery-ui-sortable' );

				// Property Submit Script.
				wp_register_script(
					'property-submit',
					get_theme_file_uri( $js_dir_path . 'js/property-submit.js' ),
					array( 'jquery', 'wp-util', 'jquery-ui-sortable', 'plupload' ),
					INSPIRY_THEME_VERSION,
					true
				);

				// Data to print in JavaScript format above property submit script tag in HTML.
				$property_submit_data = array(
					'ajaxURL'       => admin_url( 'admin-ajax.php' ),
					'uploadNonce'   => wp_create_nonce( 'inspiry_allow_upload' ),
					'fileTypeTitle' => esc_html__( 'Valid file formats', 'framework' ),
				);

				wp_localize_script( 'property-submit', 'propertySubmit', $property_submit_data );
				wp_enqueue_script( 'property-submit' );
			}

			if ( is_singular( 'property' ) ) {

				wp_enqueue_script(
					'share-js',
					get_theme_file_uri( $js_dir_path . 'vendors/share.min.js' ),
					array( 'jquery' ),
					INSPIRY_THEME_VERSION,
					true
				);

				wp_enqueue_script(
					'property-share',
					get_theme_file_uri( $js_dir_path . 'js/property-share.js' ),
					array( 'jquery' ),
					INSPIRY_THEME_VERSION,
					true
				);

				if ( 'true' === get_option( 'realhomes_line_social_share', 'false' ) ) {
					$realhomes_line_social_share = "
					(function($) { 
					    'use strict';
						$(document).ready(function () {
							$(window).on('load', function () {
							    var shareThisDiv = $('.share-this');
							    shareThisDiv.addClass('realhomes-line-social-share-enabled');
								shareThisDiv.find('ul').append('<li class=\"entypo-line\" id=\"realhomes-line-social-share\"><i class=\"fab fa-line\"></i></li>');
							});
							$(document).on('click', '#realhomes-line-social-share', function () {
								window.open(
									'https://social-plugins.line.me/lineit/share?url=' + encodeURIComponent(window.location.href),
									'_blank',
									'location=yes,height=570,width=520,scrollbars=yes,status=yes'
								);
							});
						});
					})(jQuery);";
					wp_add_inline_script( 'property-share', $realhomes_line_social_share );
				}
			}

			/**
			 * Script for comments reply
			 */
			if ( is_singular( 'post' ) || is_page() || is_singular( 'property' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			/* Print select status for rent to switch prices in properties search form */
			$rent_slug               = get_option( 'theme_status_for_rent' );
			$localized_search_params = array();
			if ( ! empty( $rent_slug ) ) {
				$localized_search_params['rent_slug'] = $rent_slug;
			}

			/* localize search parameters */
			wp_localize_script( 'inspiry-search', 'localizedSearchParams', $localized_search_params );

			wp_localize_script( 'inspiry-search', 'frontEndAjaxUrl', array( 'sfoiajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			/* Inspiry search form script */
			wp_enqueue_script( 'inspiry-search' );

			/**
			 * Google reCaptcha
			 */
			if ( class_exists( 'Easy_Real_Estate' ) ) {
				if ( ere_is_reCAPTCHA_configured() ) {

					$inspiry_contact_form_shortcode = get_option( 'inspiry_contact_form_shortcode' );
					$reCPATCHA_type                 = get_option( 'inspiry_reCAPTCHA_type', 'v2' );

					if ( 'v3' === $reCPATCHA_type ) {
						$render = get_option( 'theme_recaptcha_public_key' );
					} else {
						$render = 'explicit';
					}

					$modern_recaptcha_src = esc_url_raw( add_query_arg( array(
						'render' => $render,
						'onload' => 'loadInspiryReCAPTCHA',
					), '//www.google.com/recaptcha/api.js' ) );

					if ( ! is_page_template( 'templates/contact.php' ) ) {
						// Enqueue google reCAPTCHA API.
						wp_enqueue_script(
							'inspiry-google-recaptcha',
							$modern_recaptcha_src,
							array(),
							INSPIRY_THEME_VERSION,
							true
						);
					} elseif ( empty( $inspiry_contact_form_shortcode ) ) {
						// Enqueue google reCAPTCHA API.
						wp_enqueue_script(
							'inspiry-google-recaptcha',
							$modern_recaptcha_src,
							array(),
							INSPIRY_THEME_VERSION,
							true
						);
					} else {
						remove_action( 'wp_footer', 'ere_recaptcha_callback_generator' );
					}
				}
			}

			// Data to print in JavaScript format above custom js script tag in HTML.
			$custom_data = array(
				'video_width'  => get_option( 'inpsiry_property_video_popup_width', 1778 ),
				'video_height' => get_option( 'inspiry_property_video_popup_height', 1000 ),
			);
			wp_localize_script( 'custom', 'customData', $custom_data );

			$select_string = array(
				'select_noResult' => get_option( 'inspiry_select2_no_result_string', esc_html__( 'No Results Found!', 'framework' ), true ),
			);
			wp_localize_script( 'custom', 'localizeSelect', $select_string );

			if ( is_page_template( 'templates/home.php' ) ) {
				$inspiry_cfos_success_redirect_page = get_post_meta( get_the_ID(), 'inspiry_cfos_success_redirect_page', true );
				if ( ! empty( $inspiry_cfos_success_redirect_page ) ) {
					$CFOSData = array( 'redirectPageUrl' => get_the_permalink( $inspiry_cfos_success_redirect_page ) );
					wp_localize_script( 'custom', 'CFOSData', $CFOSData );
				}
			}

			if ( is_page_template( 'templates/contact.php' ) ) {
				$inspiry_contact_form_success_redirect_page = get_post_meta( get_the_ID(), 'inspiry_contact_form_success_redirect_page', true );
				if ( ! empty( $inspiry_contact_form_success_redirect_page ) ) {
					$contactFromData = array( 'redirectPageUrl' => get_the_permalink( $inspiry_contact_form_success_redirect_page ) );
					wp_localize_script( 'custom', 'contactFromData', $contactFromData );
				}
			}

			wp_enqueue_script( 'custom' );

		}
	}
}


if ( ! function_exists( 'inspiry_enqueue_common_styles' ) ) {

	/**
	 * Function to load common styles.
	 *
	 * @since  3.0.2
	 */
	function inspiry_enqueue_common_styles() {

		$common_dir_path = '/common/';

		// FontAwesome 5 Stylesheet
		wp_enqueue_style( 'font-awesome-5-all',
			get_theme_file_uri( $common_dir_path . 'font-awesome/css/all.min.css' ),
			array(),
			'5.13.1',
			'all' );

		if ( is_singular( 'property' ) ) {
			wp_enqueue_style(
				'rh-font-awesome-stars',
				get_theme_file_uri( $common_dir_path . 'font-awesome/css/fontawesome-stars.css' ),
				array(),
				'1.0.0',
				'all'
			);
		}

		// VenoBox - Just another responsive jQuery lightbox plugin.
		wp_enqueue_style(
			'vendors-css',
			get_theme_file_uri( 'common/optamize/vendors.css' ),
			array(),
			INSPIRY_THEME_VERSION,
			'all'
		);

		// parent theme custom css
		wp_enqueue_style(
			'parent-custom',
			get_theme_file_uri( 'assets/' . INSPIRY_DESIGN_VARIATION . '/styles/css/custom.css' ),
			array(),
			INSPIRY_THEME_VERSION,
			'all'
		);

		wp_add_inline_style( 'parent-custom', apply_filters( 'realhomes_common_custom_css', '' ) );
	}
}


if ( ! function_exists( 'inspiry_enqueue_common_scripts' ) ) {

	/**
	 * Function to load common scripts.
	 *
	 * @since  3.0.2
	 */
	function inspiry_enqueue_common_scripts() {

		$common_js_dir_path = '/common/js/';


		// BarRating JS.
		$property_ratings = get_option( 'inspiry_property_ratings', 'false' );
		if ( 'true' === $property_ratings && is_singular( 'property' ) ) {
			// jQuery Bar Rating.
			wp_enqueue_script(
				'rh-jquery-bar-rating',
				get_theme_file_uri( $common_js_dir_path . 'jquery.barrating.min.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);
		}


		/**
		 * Login Script
		 */
		if ( ! is_user_logged_in() ) {
			wp_enqueue_script(
				'inspiry-login',
				get_theme_file_uri( 'common/js/inspiry-login.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);
		}

		if ( inspiry_is_rvr_enabled() ) {
			// Availability Calendar
			wp_enqueue_script(
				'availability-calendar',
				get_theme_file_uri( $common_js_dir_path . 'availability-calendar.min.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);
		}


		if ( ( 'enable' === get_option( 'theme_compare_properties_module' ) && get_option( 'inspiry_compare_page' ) ) ) {
			wp_enqueue_script(
				'compare-js',
				get_theme_file_uri( $common_js_dir_path . 'compare-properties.js' ),
				array( 'jquery' ),
				INSPIRY_THEME_VERSION,
				true
			);
		}

		wp_enqueue_script(
			'vendors-js',
			get_theme_file_uri( 'common/optamize/vendors.js' ),
			array( 'jquery' ),
			INSPIRY_THEME_VERSION,
			true
		);

		// Common Custom.
		wp_register_script(
			'common-custom',
			get_theme_file_uri( $common_js_dir_path . 'common-custom.js' ),
			array( 'jquery' ),
			INSPIRY_THEME_VERSION,
			true
		);
		wp_register_script(
			'inspiry-cfos-js',
			get_theme_file_uri( $common_js_dir_path . 'cfos.js' ),
			array( 'jquery', 'vendors-js' ),
			INSPIRY_THEME_VERSION,
			true
		);

		$utils_path = array(
			'stylesheet_directory' => get_theme_file_uri( 'common/js/utils.js' ),
		);
		wp_localize_script( 'inspiry-cfos-js', 'inspiryUtilsPath', $utils_path );

		$select_string = array(
			'select_noResult'  => get_option( 'inspiry_select2_no_result_string', esc_html__( 'No Results Found!', 'framework' ), true ),
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'page_template'    => get_page_template_slug(),
			'searching_string' => esc_html__( 'Searching...', 'framework' ),
			'loadingMore'      => esc_html__( 'Loading more results...', 'framework' ),
		);
		wp_localize_script( 'common-custom', 'localizeSelect', $select_string );

		if ( is_singular( 'property' ) ) {

			$inspiry_agent_form_success_redirect_page = get_option( 'inspiry_agent_form_success_redirect_page' );
			if ( ! empty( $inspiry_agent_form_success_redirect_page ) ) {
				$agentData = array( 'redirectPageUrl' => get_the_permalink( $inspiry_agent_form_success_redirect_page ) );
				wp_localize_script( 'common-custom', 'agentData', $agentData );
			}

			if ( 'enable' === get_option( 'inspiry_similar_properties_frontend_filters', 'disable' ) ) {

				$properties_per_page = get_option( 'theme_number_of_similar_properties' );
				if ( is_page_template( 'templates/property-full-width-layout.php' ) ) {
					$properties_per_page = 3;
				}

				$similarPropertiesData = array(
					'design' => INSPIRY_DESIGN_VARIATION,
					'propertyId' => get_the_ID(),
					'propertiesPerPage' => $properties_per_page,

				);

				wp_localize_script( 'common-custom', 'similarPropertiesData', $similarPropertiesData );
			}
		}

		wp_enqueue_script( 'common-custom' );
		wp_enqueue_script( 'inspiry-cfos-js' );
	}
}

/**
 * Dynamic CSS
 */
if ( file_exists( INSPIRY_THEME_DIR . '/styles/css/dynamic-css.php' ) ) {
	include_once( INSPIRY_THEME_DIR . '/styles/css/dynamic-css.php' );
}

/**
 * Common Dynamic CSS
 */
if ( file_exists( INSPIRY_COMMON_DIR . '/css/dynamic-css.php' ) ) {
	include_once( INSPIRY_COMMON_DIR . '/css/dynamic-css.php' );
}