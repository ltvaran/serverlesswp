<?php

if ( ! function_exists( 'ere_get_property_statuses' ) ) {
	/**
	 * Display property status.
	 *
	 * @param int $post_id - Property ID.
	 */
	function ere_get_property_statuses( $post_id ) {

		$status_terms = get_the_terms( $post_id, 'property-status' );

		if ( ! empty( $status_terms ) ) {

			$status_names = '';
			$status_count = 0;

			foreach ( $status_terms as $term ) {
				if ( $status_count > 0 ) {
					$status_names .= ', ';  /* add comma before the term namee of 2nd and any later term */
				}
				$status_names .= $term->name;
				$status_count++;
			}

			if ( ! empty( $status_names ) ) {
				return $status_names;
			}
		}

		return '';
	}
}


if ( ! function_exists( 'ere_get_property_types' ) ) {
	/**
	 * Get property types
	 *
	 * @param $property_post_id
	 * @return string
	 */
	function ere_get_property_types( $property_post_id ) {
		$type_terms = get_the_terms( $property_post_id, "property-type" );
		if ( ! empty( $type_terms ) ) {
			$type_count = count( $type_terms );
			$property_types_str = '<small>';
			$loop_count = 1;
			foreach ( $type_terms as $typ_trm ) {
				$property_types_str .= $typ_trm->name;
				if ( $loop_count < $type_count && $type_count > 1 ) {
					$property_types_str .= ', ';
				}
				$loop_count++;
			}
			$property_types_str .= '</small>';
		} else {
			$property_types_str = '&nbsp;';
		}
		return $property_types_str;
	}
}


//if( ! function_exists( 'ere_display_property_label' ) ) {
//	/**
//	 * Display property label
//	 *
//	 * @param $post_id
//	 */
//	function ere_display_property_label( $post_id ){
//
//		$label_text = get_post_meta( $post_id, 'inspiry_property_label', true );
//
//		if( ! empty ( $label_text ) ) {
//			echo "<span class='rhea_property_label'>{$label_text}</span>";
//		}
//	}
//}


if( !function_exists( 'ere_get_property_cities_array' ) ) :
	/**
	 * Return associative array of property location terms. Where slug is key and name is value.
	 * @return array
	 */
	function ere_get_property_cities_array() {
		$cities_array = array();
		$city_terms = get_terms( 'property-city' );
		if ( !empty( $city_terms) && is_array( $city_terms ) ) {
			foreach ( $city_terms as $city_term ) {
				$cities_array[ $city_term->slug ] = $city_term->name;
			}
		}
		return $cities_array;
	}
endif;


if( !function_exists( 'ere_get_property_statuses_array' ) ) :
	/**
	 * Return associative array of property status terms. Where slug is key and name is value.
	 * @return array
	 */
	function ere_get_property_statuses_array() {
		$statuses_array = array();
		$status_terms = get_terms( 'property-status' );
		if ( !empty( $status_terms) && is_array( $status_terms ) ) {
			foreach ( $status_terms as $status_term ) {
				$statuses_array[ $status_term->slug ] = $status_term->name;
			}
		}
		return $statuses_array;
	}
endif;


if( !function_exists( 'ere_get_property_types_array' ) ) :
	/**
	 * Return associative array of property type terms. Where slug is key and name is value.
	 * @return array
	 */
	function ere_get_property_types_array() {
		$types_array = array();
		$type_terms = get_terms( 'property-type' );
		if ( !empty( $status_terms) && is_array( $status_terms ) ) {
			foreach ( $type_terms as $type_term ) {
				$types_array[ $type_term->slug ] = $type_term->name;
			}
		}
		return $types_array;
	}
endif;


if( !function_exists( 'ere_get_property_features_array' ) ) :
	/**
	 * Return associative array of property feature terms. Where slug is key and name is value.
	 * @return array
	 */
	function ere_get_property_features_array() {
		$features_array = array();
		$feature_terms = get_terms( 'property-feature' );
		if ( !empty( $feature_terms) && is_array( $feature_terms ) ) {
			foreach ( $feature_terms as $feature_term ) {
				$features_array[ $feature_term->slug ] = $feature_term->name;
			}
		}
		return $features_array;
	}
endif;


if( !function_exists( 'ere_any_text' ) ) :
	/**
	 * Return text string for word 'Any'
	 *
	 * @return string
	 */
	function ere_any_text() {
		$ere_any_text = get_option( 'inspiry_any_text' );
		if ( $ere_any_text ) {
			return $ere_any_text;
		}
		return esc_html__( 'Any', 'easy-real-estate' );
	}
endif;


if ( ! function_exists( 'ere_get_terms_array' ) ) {
	/**
	 * Returns terms array for a given taxonomy containing key(slug) value(name) pair
	 *
	 * @param $tax_name
	 * @param $terms_array
	 */
	function ere_get_terms_array( $tax_name, &$terms_array ) {
		$tax_terms = get_terms( array(
			'taxonomy'   => $tax_name,
			'hide_empty' => false,
		) );
		ere_add_term_children( 0, $tax_terms, $terms_array );
	}
}


if( !function_exists( 'ere_is_search_page_configured' ) ) :
	/**
	 * Check if search page settings are configured
	 */
	function ere_is_search_page_configured() {

		/* Check search page */
		$inspiry_search_page = get_option('inspiry_search_page');
		if ( ! empty( $inspiry_search_page ) ) {
			return true;
		}

		/* Check search url which is deprecated and this code is to provide backward compatibility */
		$theme_search_url = get_option('theme_search_url');
		if ( ! empty( $theme_search_url ) ) {
			return true;
		}

		/* Return false if all fails */
		return false;
	}
endif;


if ( ! function_exists( 'ere_add_term_children' ) ) :
	/**
	 * A recursive function to add children terms to given array
	 *
	 * @param $parent_id
	 * @param $tax_terms
	 * @param $terms_array
	 * @param string $prefix
	 */
	function ere_add_term_children( $parent_id, $tax_terms, &$terms_array, $prefix = '' ) {
		if ( ! empty( $tax_terms ) && ! is_wp_error( $tax_terms ) ) {
			foreach ( $tax_terms as $term ) {
				if ( $term->parent == $parent_id ) {
					$terms_array[ $term->slug ] = $prefix . $term->name;
					ere_add_term_children( $term->term_id, $tax_terms, $terms_array, $prefix . '- ' );
				}
			}
		}
	}
endif;


if ( ! function_exists( 'ere_skip_sticky_properties' ) ) :
	/**
	 * Skip sticky properties
	 */
	function ere_skip_sticky_properties(){
		$skip_sticky = get_option( 'inspiry_listing_skip_sticky', false );
		if ( $skip_sticky ) {
			remove_filter( 'the_posts', 'inspiry_make_properties_stick_at_top', 10 );
		}
    }
endif;

if ( ! function_exists( 'ere_skip_home_sticky_properties' ) ) :
	/**
	 * Skip sticky properties
	 */
	function ere_skip_home_sticky_properties(){
		$skip_sticky = get_post_meta( get_the_ID(), 'inspiry_home_skip_sticky', true );
		if ( $skip_sticky ) {
			remove_filter( 'the_posts', 'inspiry_make_properties_stick_at_top', 10 );
		}
    }
endif;


if ( ! function_exists( 'ere_get_figure_caption' ) ) {
	/**
	 * Figure caption based on property statuses
	 *
	 * @param $post_id
	 * @return string
	 */
	function ere_get_figure_caption( $post_id ) {
		$status_terms = get_the_terms( $post_id, "property-status" );
		if ( ! empty( $status_terms ) ) {
			$status_classes = '';
			$status_names = '';
			$status_count = 0;
			foreach ( $status_terms as $term ) {
				if ( $status_count > 0 ) {
					$status_names .= ', ';  /* add comma before the term namee of 2nd and any later term */
					$status_classes .= ' ';
				}
				$status_names .= $term->name;
				$status_classes .= $term->slug;
				$status_count++;
			}

			if ( ! empty( $status_names ) ) {
				return '<figcaption class="' . $status_classes . '">' . $status_names . '</figcaption>';
			}

			return '';
		}
	}
}


if ( ! function_exists( 'ere_display_figcaption' ) ) {
	/**
	 * Display figure caption for given property's post id
	 *
	 * @param $post_id
	 */
	function ere_display_figcaption( $post_id ) {
		echo ere_get_figure_caption( $post_id );
	}
}


if ( ! function_exists( 'ere_is_added_to_compare' ) ) {
	/**
	 * Check if a property is already added to compare list.
	 *
	 * @param $property_id
	 * @return bool
	 */
	function ere_is_added_to_compare( $property_id ) {

		if ( $property_id > 0 ) {
			/* check cookies for property id */
			if ( isset( $_COOKIE[ 'inspiry_compare' ] ) ) {
				$inspiry_compare 	= unserialize( $_COOKIE[ 'inspiry_compare' ] );
				if ( in_array( $property_id, $inspiry_compare ) ) {
					return true;
				}
			}
		}
		return false;

	}
}

if ( ! function_exists( 'ere_additional_details_migration' ) ) {
	/**
	 * Migrate property additioanl details from old metabox key to new metabox key.
	 *
	 * @param int $post_id Property ID of which additional details has to migrate.
	 */
	function ere_additional_details_migration( $post_id ) {

		if ( ! $post_id ) {
			return;
		}

		$additional_details = get_post_meta( $post_id, 'REAL_HOMES_additional_details', true );
		if ( ! empty( $additional_details ) ) {
			$formatted_details = array();
			foreach ( $additional_details as $field => $value ) {
				$formatted_details[] = array( $field, $value );
			}

			if ( update_post_meta( $post_id, 'REAL_HOMES_additional_details_list', $formatted_details ) ) {
				delete_post_meta( $post_id, 'REAL_HOMES_additional_details' );
			}
		} else {
			// For legacy code
			$detail_titles = get_post_meta( $post_id, 'REAL_HOMES_detail_titles', true );
			if ( ! empty( $detail_titles ) ) {
				$detail_values = get_post_meta( $post_id, 'REAL_HOMES_detail_values', true );
				if ( ! empty( $detail_values ) ) {
					$additional_details = array_combine( $detail_titles, $detail_values );
					$formatted_details = array();
					foreach ( $additional_details as $field => $value ) {
						$formatted_details[] = array( $field, $value );
					}

					if ( update_post_meta( $post_id, 'REAL_HOMES_additional_details_list', $formatted_details ) ) {
						delete_post_meta( $post_id, 'REAL_HOMES_detail_titles' );
						delete_post_meta( $post_id, 'REAL_HOMES_detail_values' );
					}
				}
			}
		}
	}
}
