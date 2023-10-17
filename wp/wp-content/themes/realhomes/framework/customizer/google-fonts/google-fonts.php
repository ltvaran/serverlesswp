<?php
/**
 * Get Google Fonts list
 *
 * @return array Google fonts list
 */
function inspiry_get_google_fonts_list() {

	$inspiry_google_fonts_list = array();
	$fonts_json_file           = wp_remote_fopen( get_template_directory_uri() . '/framework/customizer/google-fonts/google-fonts.json' );;

	if ( $fonts_json_file ) {
		$fonts = json_decode( $fonts_json_file, true );
		foreach ( $fonts as $font ) {
			if ( isset( $font['text'] ) ) {
				$inspiry_google_fonts_list[ $font['text'] ] = $font['text'];
			}
		}
		ksort( $inspiry_google_fonts_list, SORT_STRING );
	}

	return array( 'Default' => esc_html__( 'Theme Default Font', 'framework' ) ) + $inspiry_google_fonts_list;
}