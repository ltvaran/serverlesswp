<?php
/**
 * This file contains functions related to Real Estate Search
 */

if ( ! function_exists( 'inspiry_is_search_page_map_visible' ) ) :
	/**
	 * Check if search page map is set to show or hide
	 */
	function inspiry_is_search_page_map_visible() {

		if ( 'with_map' === get_option( 'inspiry_search_results_layout', 'with_map' ) ) {
			return true;
		}

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_get_search_page_url' ) ) :
	/**
	 * Get search page URL
	 */
	function inspiry_get_search_page_url() {
		/* Check search page*/
		$inspiry_search_page = get_option( 'inspiry_search_page' );
		if ( ! empty( $inspiry_search_page ) ) {

			/* WPML filter to get translated page id if translation exists otherwise default id */
			$inspiry_search_page = apply_filters( 'wpml_object_id', $inspiry_search_page, 'page', true );

			return get_permalink( $inspiry_search_page );
		}

		/* Check search url which is deprecated and this code is to provide backward compatibility */
		$theme_search_url = get_option( 'theme_search_url' );
		if ( ! empty( $theme_search_url ) ) {
			return $theme_search_url;
		}

		/* Return false if all fails */

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_is_search_fields_configured' ) ) :
	/**
	 * Checks if search fields are configured or not
	 *
	 * @return bool
	 */
	function inspiry_is_search_fields_configured() {
		$theme_search_fields = get_option( 'theme_search_fields' );
		if ( ! empty( $theme_search_fields ) && is_array( $theme_search_fields ) ) {
			return true;
		}

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_is_search_page_configured' ) ) :
	/**
	 * Check if search page settings are configured
	 */
	function inspiry_is_search_page_configured() {

		/* Check search page */
		$inspiry_search_page = get_option( 'inspiry_search_page' );
		if ( ! empty( $inspiry_search_page ) ) {
			return true;
		}

		/* Check search url which is deprecated and this code is to provide backward compatibility */
		$theme_search_url = get_option( 'theme_search_url' );
		if ( ! empty( $theme_search_url ) ) {
			return true;
		}

		/* Return false if all fails */

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_show_search_form_widget' ) ) :
	/**
	 * Checks if search form can be displayed in the sidebar.
	 *
	 * @return bool
	 */
	function inspiry_show_search_form_widget() {
		$inspiry_show_search_in_header = (int) get_option( 'inspiry_show_search_in_header', 1 );
		if ( $inspiry_show_search_in_header ) {
			return false;
		}

		return true;
	}
endif;


if ( ! function_exists( 'inspiry_show_header_search_form' ) ) :
	/**
	 * Checks if search form can be displayed in the sidebar.
	 *
	 * @return bool
	 */
	function inspiry_show_header_search_form() {
		$inspiry_show_search_in_header = (int) get_option( 'inspiry_show_search_in_header', 1 );
		if ( $inspiry_show_search_in_header ) {
			return true;
		}

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_get_search_fields' ) ) :
	/**
	 * Get search fields array
	 */
	function inspiry_get_search_fields() {
		$theme_search_fields = get_option( 'theme_search_fields' );
		if ( ! empty( $theme_search_fields ) && is_array( $theme_search_fields ) ) {
			return $theme_search_fields;
		}

		return false;
	}
endif;

if ( ! function_exists( 'inspiry_smart_placeholder' ) ) {
	function inspiry_smart_placeholder( $label_key, $default ) {

		$label = get_option( $label_key );

		if ( ! empty( $label ) ) {
			return $label;
		} else {
			return $default;
		}
	}
}

if ( ! function_exists( 'inspiry_any_value' ) ) :
	/**
	 * Return
	 * @return string|void
	 */
	function inspiry_any_value() {
		return 'any';   // NOTE: do not try to translate this as it has back-end use only and it never appears on front-end
	}
endif;


if ( ! function_exists( 'inspiry_hide_empty_locations' ) ) :
	/**
	 * Returns true if setting is configured to hide empty locations and returns false otherwise
	 *
	 * @return bool
	 */
	function inspiry_hide_empty_locations() {
		if ( ( 'true' == get_option( 'inspiry_hide_empty_locations' ) ) && ( ! is_page_template( array( 'templates/submit-property.php', 'templates/dashboard.php' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


if ( ! function_exists( 'inspiry_location_placeholder' ) ) {
	function inspiry_location_placeholder() {
		$get_location_count         = get_option( 'theme_location_select_number', 1 );
		$location_placeholder_array = array();
		for ( $i = 1; $i <= $get_location_count; $i ++ ) {
			$inspiry_location_placeholder = get_option( 'inspiry_location_placeholder_' . $i );
			if ( ! empty( $inspiry_location_placeholder ) ) {
				$location_placeholder_array[] = $inspiry_location_placeholder;
			} else {
				$location_placeholder_array[] = rh_any_text();
			}
		}

		return $location_placeholder_array;
	}
}


if ( ! function_exists( 'load_location_script' ) && 'yes' != get_option( 'inspiry_ajax_location_field' ) ) {
	/**
	 * Load Location Related Script
	 */
	function load_location_script() {

		if ( ! is_admin() ) {

			$locations_order = array(
				'orderby' => 'count',
				'order'   => 'desc',
			);

			$order = get_option( 'theme_locations_order' );
			if ( $order == 'true' ) {
				$locations_order['orderby'] = 'name';
				$locations_order['order']   = 'asc';
			}

			/* all property location terms */
			$all_locations = get_terms(
				array(
					'taxonomy'         => 'property-city',
					'hide_empty'       => inspiry_hide_empty_locations(),
					'orderby'          => $locations_order['orderby'],
					'order'            => $locations_order['order'],
					'suppress_filters' => false,
				)
			);

			if ( is_wp_error( $all_locations ) ) {
				error_log( $all_locations->get_error_message() );

				return;
			}

			/* Unset properties not needed this will improve performance on front end */
			foreach ( $all_locations as $single_location ) {

				if ( isset( $single_location->term_group ) ) {
					unset( $single_location->term_group );
				}

				if ( isset( $single_location->term_taxonomy_id ) ) {
					unset( $single_location->term_taxonomy_id );
				}

				if ( isset( $single_location->taxonomy ) ) {
					unset( $single_location->taxonomy );
				}

				if ( isset( $single_location->description ) ) {
					unset( $single_location->description );
				}

				if ( isset( $single_location->count ) ) {
					unset( $single_location->count );
				}

				if ( isset( $single_location->filter ) ) {
					unset( $single_location->filter );
				}

				// Decode slug for some langauges (e.g: Hebrew or Russian) to use in search as query paramer.
				$single_location->slug = rawurldecode( $single_location->slug );
			}

			// re-indexing city terms
			$all_locations = array_values( $all_locations );

			// select boxes names
			$location_select_names = inspiry_get_location_select_names();

			// number of select boxes based on theme option
			$location_select_count = inspiry_get_locations_number();

			// location parameters in request, if any
			$locations_in_params = array();

			if ( ( is_page_template( 'templates/submit-property.php' ) && isset( $_GET['edit_property'] ) ) || ( is_page_template( 'templates/dashboard.php' ) && isset( $_GET['id'] ) ) ) {

				$property_id = 0;

				if ( isset( $_GET['edit_property'] ) && ! empty( $_GET['edit_property'] ) ) {
					$property_id = $_GET['edit_property'];
				} elseif ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
					$property_id = $_GET['id'];
				}

				$edit_property_id = intval( trim( $property_id ) );
				$target_property  = get_post( $edit_property_id );

				if ( ! empty( $target_property ) && ( $target_property->post_type == 'property' ) ) {

					$location_terms = get_the_terms( $edit_property_id, 'property-city' );

					if ( $location_terms && ! is_wp_error( $location_terms ) ) {

						$existing_location_term = $location_terms[0];

						if ( $existing_location_term->term_id ) {

							$existing_location_ancestors = get_ancestors( $existing_location_term->term_id, 'property-city' );
							$location_term_depth         = count( $existing_location_ancestors );

							if ( $location_term_depth >= $location_select_count ) {

								$existing_location_ancestors = array_reverse( $existing_location_ancestors );
								for ( $i = 0; $i < ( $location_select_count - 1 ); $i ++ ) {
									$current_ancestor                                    = get_term_by( 'id', $existing_location_ancestors[ $i ], 'property-city' );
									$locations_in_params[ $location_select_names[ $i ] ] = $current_ancestor->slug;
								}

								// For last select box
								$locations_in_params[ $location_select_names[ $location_select_count - 1 ] ] = $existing_location_term->slug;

							} else if ( $location_term_depth < $location_select_count ) {

								$existing_location_ancestors = array_reverse( $existing_location_ancestors );
								for ( $i = 0; $i < $location_term_depth; $i ++ ) {
									$current_ancestor                                    = get_term_by( 'id', $existing_location_ancestors[ $i ], 'property-city' );
									$locations_in_params[ $location_select_names[ $i ] ] = $current_ancestor->slug;
								}

								// For last select box
								$locations_in_params[ $location_select_names[ $location_term_depth ] ] = $existing_location_term->slug;

							}

						}

					}

				}
			}

			if ( 0 == count( $locations_in_params ) ) {
				foreach ( $location_select_names as $location_name ) {
					if ( isset( $_GET[ $location_name ] ) ) {
						$locations_in_params[ $location_name ] = $_GET[ $location_name ];
					}
				}
			}

			// Any text
			$any_text = rh_any_text();
			if ( is_page_template( array( 'templates/submit-property.php', 'templates/dashboard.php' ) ) ) {
				$any_text = esc_html__( 'None', 'framework' );  // modify it to None for submit page
			}

			$multi_select_locations = 'no';
			if ( 'yes' == get_option('inspiry_search_form_multiselect_locations','yes') &&
			     ('yes' === get_option( 'inspiry_ajax_location_field', 'no' ) || $location_select_count <= 1 )) {
				$multi_select_locations = 'yes';
			}

			/* combine all data into one */
			$location_data_array = array(
				'none_text'              => esc_html__( 'None', 'framework' ),
				'any_text'               => $any_text,
				'any_value'              => inspiry_any_value(),
				'location_placeholders'  => inspiry_location_placeholder(),
				'all_locations'          => $all_locations,
				'select_names'           => $location_select_names,
				'select_count'           => $location_select_count,
				'locations_in_params'    => $locations_in_params,
				'multi_select_locations' => $multi_select_locations,
			);

			if ( is_page_template( 'templates/dashboard.php' ) ) {
				/* provide location data array before custom script */
				wp_localize_script( 'dashboard-js', 'locationData', $location_data_array );
			} else {
				/* provide location data array before custom script */
				wp_localize_script( 'inspiry-search', 'locationData', $location_data_array );
			}
		}
	}

	add_action( 'after_location_fields', 'load_location_script' );
}

if ( ! function_exists( 'advance_search_options' ) ) {
	/**
	 * Advance search options (List boxes listing in advance-search.php)
	 *
	 * @param $taxonomy_name
	 * @param array $args
	 *
	 * @return bool
	 */
	function advance_search_options( $taxonomy_name, $args = array() ) {

		$defaults = array(
			'taxonomy' => $taxonomy_name
		);

		$args           = wp_parse_args( $args, $defaults );
		$taxonomy_terms = get_terms( $args );

		if ( empty( $taxonomy_terms ) || is_wp_error( $taxonomy_terms ) ) {
			return false;
		}

		$searched_term = '';

		if ( $taxonomy_name == 'property-city' ) {
			if ( ! empty( $_GET['location'] ) ) {
				$searched_term = $_GET['location'];
			}
		}

		if ( $taxonomy_name == 'property-type' ) {
			if ( ! empty( $_GET['type'] ) ) {
				$searched_term = $_GET['type'];
			}
		}

		if ( $taxonomy_name == 'property-status' ) {
			if ( ! empty( $_GET['status'] ) ) {
				$searched_term = $_GET['status'];
			}
		}

		$get_select_placeholder = get_option( 'inspiry_property_status_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		if ( $searched_term == inspiry_any_value() || empty( $searched_term ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
		}

		if ( ! empty( $taxonomy_terms ) ) {
			foreach ( $taxonomy_terms as $term ) {
				if ( $searched_term == $term->slug ) {
					echo '<option value="' . esc_attr( rawurldecode( $term->slug ) ) . '" selected="selected">' . esc_html( $term->name ) . '</option>';
				} else {
					echo '<option value="' . esc_attr( rawurldecode( $term->slug ) ) . '">' . esc_html( $term->name ) . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'advance_hierarchical_options' ) ) {
	/**
	 * Advance hierarchical options
	 *
	 * @param $taxonomy_name
	 */
	function advance_hierarchical_options( $taxonomy_name ) {
		$taxonomy_terms = get_terms( array(
			'taxonomy'   => $taxonomy_name,
			'hide_empty' => false,
			'parent'     => 0
		) );
		$searched_term  = '';

		if ( $taxonomy_name == 'property-city' ) {
			if ( ! empty( $_GET['location'] ) ) {
				$searched_term = $_GET['location'];
			}
		}

		if ( $taxonomy_name == 'property-type' ) {
			if ( ! empty( $_GET['type'] ) ) {
				$searched_term = $_GET['type'];
			}
		}
		$inspiry_search_form_multiselect_types = get_option( 'inspiry_search_form_multiselect_types', 'yes' );

		if ( 'no' == $inspiry_search_form_multiselect_types ) {

			$get_select_placeholder = get_option( 'inspiry_property_type_placeholder' );
			if ( ! empty( $get_select_placeholder ) ) {
				$default_slelect = $get_select_placeholder;
			} else {
				$default_slelect = rh_any_text();
			}

			if ( $searched_term == inspiry_any_value() || empty( $searched_term ) ) {
				echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
			} else {
				echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
			}
		}
		// Generate options
		generate_hirarchical_options( $taxonomy_name, $taxonomy_terms, $searched_term );
	}
}


if ( ! function_exists( 'generate_hirarchical_options' ) ) {
	/**
	 * Generate Hierarchical Options
	 *
	 * @param $taxonomy_name
	 * @param $taxonomy_terms
	 * @param $searched_term
	 * @param string $prefix
	 */
	function generate_hirarchical_options( $taxonomy_name, $taxonomy_terms, $searched_terms, $prefix = " " ) {
		if ( ! empty( $taxonomy_terms ) ) {
			foreach ( $taxonomy_terms as $term ) {

				if ( is_array($searched_terms) && in_array($term->slug,$searched_terms) ) {
					echo '<option value="' . esc_attr( rawurldecode( $term->slug ) ) . '" selected="selected">' . esc_html( $prefix . $term->name ) . '</option>';
				} else {
					echo '<option value="' . esc_attr( rawurldecode( $term->slug ) ) . '">' . esc_html( $prefix . $term->name ) . '</option>';
				}

				$child_terms = get_terms( array(
					'taxonomy'   => $taxonomy_name,
					'hide_empty' => false,
					'parent'     => $term->term_id
				) );

				if ( is_wp_error( $child_terms ) ) {
					error_log( $child_terms->get_error_message() );

					return;
				}

				if ( ! empty( $child_terms ) ) {
					// recursive call
					generate_hirarchical_options( $taxonomy_name, $child_terms, $searched_terms, "- " . $prefix );
				}
			}
		}
	}
}


if ( ! function_exists( 'generate_id_based_hirarchical_options' ) ) {
	/**
	 * Generate ID Based Hirarchical Options
	 *
	 * @param $taxonomy_name
	 * @param $taxonomy_terms
	 * @param $target_terms
	 * @param string $prefix
	 */
	function generate_id_based_hirarchical_options( $taxonomy_name, $taxonomy_terms, $target_terms, $prefix = " " ) {
		if ( ! empty( $taxonomy_terms ) && ! is_wp_error( $taxonomy_terms ) ) {
			foreach ( $taxonomy_terms as $term ) {

				if ( is_array( $target_terms ) ) {
					$has_term = in_array( $term->term_id, $target_terms );
				} else {
					$has_term = $target_terms == $term->term_id;
				}

				if ( $has_term ) {
					echo '<option value="' . $term->term_id . '" selected="selected">' . $prefix . $term->name . '</option>';
				} else {
					echo '<option value="' . $term->term_id . '">' . $prefix . $term->name . '</option>';
				}

				$child_terms = get_terms( array(
					'taxonomy'   => $taxonomy_name,
					'hide_empty' => false,
					'parent'     => $term->term_id
				) );

				if ( ! empty( $child_terms ) ) {
					// recursive call
					generate_id_based_hirarchical_options( $taxonomy_name, $child_terms, $target_terms, "- " . $prefix );
				}
			}
		}
	}
}


if ( ! function_exists( 'numbers_list' ) ) {
	/**
	 * Numbers loop
	 *
	 * @param $numbers_list_for
	 */
	function numbers_list( $numbers_list_for ) {
		$numbers_array  = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		$searched_value = '';

		if ( $numbers_list_for == 'bedrooms' ) {
			if ( isset( $_GET['bedrooms'] ) ) {
				$searched_value = $_GET['bedrooms'];
			}
		}

		if ( $numbers_list_for == 'bathrooms' ) {
			if ( isset( $_GET['bathrooms'] ) ) {
				$searched_value = $_GET['bathrooms'];
			}
		}

		if ( $searched_value == inspiry_any_value() || empty( $searched_value ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . rh_any_text() . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . rh_any_text() . '</option>';
		}

		if ( ! empty( $numbers_array ) ) {
			foreach ( $numbers_array as $number ) {
				if ( $searched_value == $number ) {
					echo '<option value="' . $number . '" selected="selected">' . $number . '</option>';
				} else {
					echo '<option value="' . $number . '">' . $number . '</option>';
				}
			}
		}

	}
}

if ( ! function_exists( 'inspiry_agents_in_search' ) ) {
	function inspiry_agents_in_search() {

		$args = array(
			'post_type'        => 'agent',
			'posts_per_page'   => - 1,
			'suppress_filters' => false, // set this argument to false so WPML can filter the posts according to the language.
		);

		$agents       = new WP_Query( $args );
		$agents_ids   = wp_list_pluck( $agents->posts, 'ID' );
		$agents_names = wp_list_pluck( $agents->posts, 'post_title' );
		$agents       = array_combine( $agents_ids, $agents_names );

		/* check and store searched value if there is any */
		$searched_value = '';
		if ( isset( $_GET['agents'] ) ) {
			$searched_value = $_GET['agents'];
		}


		$inspiry_search_form_multiselect_types = get_option( 'inspiry_search_form_multiselect_agents', 'yes' );

		if ( 'no' == $inspiry_search_form_multiselect_types ) {

			$agent_placeholder = get_option( 'inspiry_property_agent_placeholder' );
			$default_slelect   = rh_any_text();
			if ( ! empty( $agent_placeholder ) ) {
				$default_slelect = $agent_placeholder;
			}

			/* Add any to select box */
			if ( $searched_value == inspiry_any_value() || empty( $searched_value ) ) {
				echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
			} else {
				echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
			}
		}

		/* loop through agent values and generate select options */
		if ( ! empty( $agents ) ) {
			foreach ( $agents as $agent_id => $agent_name ) {
				if ( is_array($searched_value) && in_array($agent_id,$searched_value)  ) {
					echo '<option value="' . $agent_id . '" selected="selected">' . $agent_name . '</option>';
				} else {
					echo '<option value="' . $agent_id . '">' . $agent_name . '</option>';
				}
			}
		}
	}
}

if ( ! function_exists( 'inspiry_min_beds' ) ) {
	/**
	 * Generate values for minimum beds select box
	 */
	function inspiry_min_beds() {
		$min_beds_values = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );

		// Get values from database and convert them to an integer array
		$inspiry_min_beds = get_option( 'inspiry_min_beds' );
		if ( ! empty( $inspiry_min_beds ) ) {
			$inspiry_min_beds_array = explode( ',', $inspiry_min_beds );
			if ( is_array( $inspiry_min_beds_array ) && ! empty( $inspiry_min_beds_array ) ) {
				$new_inspiry_min_beds_array = array();
				foreach ( $inspiry_min_beds_array as $option_beds_value ) {
					$individual_beds_value = doubleval( $option_beds_value );
					if ( $individual_beds_value >= 0 ) {
						$new_inspiry_min_beds_array[] = $individual_beds_value;
					}
				}
				if ( ! empty( $new_inspiry_min_beds_array ) ) {
					$min_beds_values = $new_inspiry_min_beds_array;
				}
			}
		}

		// check and store searched value if there is any
		$searched_value = '';
		if ( isset( $_GET['bedrooms'] ) ) {
			$searched_value = $_GET['bedrooms'];
		}

		$get_select_placeholder = get_option( 'inspiry_min_beds_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}


		// Add any to select box
		if ( empty( $searched_value ) || inspiry_any_value() == $searched_value ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
		}

		// loop through min beds values and generate select options
		if ( ! empty( $min_beds_values ) ) {
			foreach ( $min_beds_values as $option_beds_value ) {
				if ( ! empty( $searched_value ) && ( $searched_value == $option_beds_value ) ) {
					echo '<option value="' . $option_beds_value . '" selected="selected">' . $option_beds_value . '</option>';
				} else {
					echo '<option value="' . $option_beds_value . '">' . $option_beds_value . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'inspiry_min_baths' ) ) {
	/**
	 * Generate values for minimum baths select box
	 */
	function inspiry_min_baths() {
		$min_baths_values = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );

		// Get values from database and convert them to an integer array
		$inspiry_min_baths = get_option( 'inspiry_min_baths' );
		if ( ! empty( $inspiry_min_baths ) ) {
			$inspiry_min_baths_array = explode( ',', $inspiry_min_baths );
			if ( is_array( $inspiry_min_baths_array ) && ! empty( $inspiry_min_baths_array ) ) {
				$new_min_baths_array = array();
				foreach ( $inspiry_min_baths_array as $baths_value ) {
					$integer_baths_value = doubleval( $baths_value );
					if ( $integer_baths_value >= 0 ) {
						$new_min_baths_array[] = $integer_baths_value;
					}
				}
				if ( ! empty( $new_min_baths_array ) ) {
					$min_baths_values = $new_min_baths_array;
				}
			}
		}

		// check and store searched value if there is any */
		$searched_value = '';
		if ( isset( $_GET['bathrooms'] ) ) {
			$searched_value = $_GET['bathrooms'];
		}

		$get_select_placeholder = get_option( 'inspiry_min_baths_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}


		// Add any to select box */
		if ( $searched_value == inspiry_any_value() || empty( $searched_value ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
		}

		// loop through min baths values and generate select options
		if ( ! empty( $min_baths_values ) ) {
			foreach ( $min_baths_values as $baths_value ) {
				if ( ! empty( $searched_value ) && ( $searched_value == $baths_value ) ) {
					echo '<option value="' . $baths_value . '" selected="selected">' . $baths_value . '</option>';
				} else {
					echo '<option value="' . $baths_value . '">' . $baths_value . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'inspiry_min_garages' ) ) {
	/**
	 * Generate values for minimum baths select box
	 */
	function inspiry_min_garages() {
		$min_garages_values = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );

		/* Get values from database and convert them to an integer array */
		$inspiry_min_garages = get_option( 'inspiry_min_garages' );
		if ( ! empty( $inspiry_min_garages ) ) {
			$inspiry_min_garages_array = explode( ',', $inspiry_min_garages );
			if ( is_array( $inspiry_min_garages_array ) && ! empty( $inspiry_min_garages_array ) ) {
				$new_min_garages_array = array();
				foreach ( $inspiry_min_garages_array as $garages_value ) {
					$integer_garages_value = doubleval( $garages_value );
					if ( $integer_garages_value >= 0 ) {
						$new_min_garages_array[] = $integer_garages_value;
					}
				}
				if ( ! empty( $new_min_garages_array ) ) {
					$min_garages_values = $new_min_garages_array;
				}
			}
		}

		/* check and store searched value if there is any */
		$searched_value = '';
		$get_global     = $_GET;
		if ( isset( $get_global['garages'] ) ) {
			$searched_value = $get_global['garages'];
		}

		$get_select_placeholder = get_option( 'inspiry_min_garages_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		/* Add any to select box */
		if ( $searched_value == inspiry_any_value() || empty( $searched_value ) ) {
			echo '<option value="' . esc_attr( inspiry_any_value() ) . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
		} else {
			echo '<option value="' . esc_attr( inspiry_any_value() ) . '">' . esc_html( $default_slelect ) . '</option>';
		}

		/* loop through min baths values and generate select options */
		if ( ! empty( $min_garages_values ) ) {
			foreach ( $min_garages_values as $garages_value ) {
				if ( $searched_value == $garages_value ) {
					echo '<option value="' . esc_attr( $garages_value ) . '" selected="selected">' . esc_html( $garages_value ) . '</option>';
				} else {
					echo '<option value="' . esc_attr( $garages_value ) . '">' . esc_html( $garages_value ) . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'min_prices_list' ) ) {
	/**
	 * Minimum Prices
	 */
	function min_prices_list() {

		$min_price_array = array(
			1000,
			5000,
			10000,
			50000,
			100000,
			200000,
			300000,
			400000,
			500000,
			600000,
			700000,
			800000,
			900000,
			1000000,
			1500000,
			2000000,
			2500000,
			5000000
		);

		/* Get values from theme options and convert them to an integer array */
		$minimum_price_values = get_option( 'theme_minimum_price_values' );
		if ( ! empty( $minimum_price_values ) ) {
			$min_prices_string_array = explode( ',', $minimum_price_values );
			if ( is_array( $min_prices_string_array ) && ! empty( $min_prices_string_array ) ) {
				$new_min_prices_array = array();
				foreach ( $min_prices_string_array as $string_price ) {
					$integer_price = doubleval( $string_price );
					if ( $integer_price > 1 ) {
						$new_min_prices_array[] = $integer_price;
					}
				}
				if ( ! empty( $new_min_prices_array ) ) {
					$min_price_array = $new_min_prices_array;
				}
			}
		}

		$minimum_price = '';
		if ( isset( $_GET['min-price'] ) ) {
			$minimum_price = doubleval( $_GET['min-price'] );
		}

		$get_select_placeholder = get_option( 'inspiry_min_price_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		if ( $minimum_price == inspiry_any_value() || empty( $minimum_price ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . esc_html( $default_slelect ) . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . esc_html( $default_slelect ) . '</option>';
		}

		if ( ! empty( $min_price_array ) && function_exists( 'ere_format_amount' ) ) {
			foreach ( $min_price_array as $price ) {
				if ( $minimum_price == $price ) {
					echo '<option value="' . $price . '" selected="selected">' . ere_format_amount( $price ) . '</option>';
				} else {
					echo '<option value="' . $price . '">' . ere_format_amount( $price ) . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'min_prices_for_rent_list' ) ) {
	/**
	 * Minimum Prices For Rent Only
	 */
	function min_prices_for_rent_list() {

		$min_price_for_rent_array = array(
			500,
			1000,
			2000,
			3000,
			4000,
			5000,
			7500,
			10000,
			15000,
			20000,
			25000,
			30000,
			40000,
			50000,
			75000,
			100000
		);

		/* Get values from theme options and convert them to an integer array */
		$minimum_price_values_for_rent = get_option( 'theme_minimum_price_values_for_rent' );
		if ( ! empty( $minimum_price_values_for_rent ) ) {
			$min_prices_string_array = explode( ',', $minimum_price_values_for_rent );
			if ( is_array( $min_prices_string_array ) && ! empty( $min_prices_string_array ) ) {
				$new_min_prices_array = array();
				foreach ( $min_prices_string_array as $string_price ) {
					$integer_price = doubleval( $string_price );
					if ( $integer_price > 1 ) {
						$new_min_prices_array[] = $integer_price;
					}
				}
				if ( ! empty( $new_min_prices_array ) ) {
					$min_price_for_rent_array = $new_min_prices_array;
				}
			}
		}

		$minimum_price = '';
		if ( isset( $_GET['min-price'] ) ) {
			$minimum_price = doubleval( $_GET['min-price'] );
		}

		$get_select_placeholder = get_option( 'inspiry_min_price_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		if ( $minimum_price == inspiry_any_value() || empty( $minimum_price ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . $default_slelect . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . $default_slelect . '</option>';
		}

		if ( ! empty( $min_price_for_rent_array ) && function_exists( 'ere_format_amount' ) ) {
			foreach ( $min_price_for_rent_array as $price ) {
				if ( $minimum_price == $price ) {
					echo '<option value="' . $price . '" selected="selected">' . ere_format_amount( $price ) . '</option>';
				} else {
					echo '<option value="' . $price . '">' . ere_format_amount( $price ) . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'max_prices_list' ) ) {
	/**
	 * Maximum Prices
	 */
	function max_prices_list() {

		$max_price_array = array(
			5000,
			10000,
			50000,
			100000,
			200000,
			300000,
			400000,
			500000,
			600000,
			700000,
			800000,
			900000,
			1000000,
			1500000,
			2000000,
			2500000,
			5000000,
			10000000
		);

		/* Get values from theme options and convert them to an integer array */
		$maximum_price_values = get_option( 'theme_maximum_price_values' );
		if ( ! empty( $maximum_price_values ) ) {
			$max_prices_string_array = explode( ',', $maximum_price_values );
			if ( is_array( $max_prices_string_array ) && ! empty( $max_prices_string_array ) ) {
				$new_max_prices_array = array();
				foreach ( $max_prices_string_array as $string_price ) {
					$integer_price = doubleval( $string_price );
					if ( $integer_price > 1 ) {
						$new_max_prices_array[] = $integer_price;
					}
				}
				if ( ! empty( $new_max_prices_array ) ) {
					$max_price_array = $new_max_prices_array;
				}
			}
		}

		$maximum_price = '';
		if ( isset( $_GET['max-price'] ) ) {
			$maximum_price = doubleval( $_GET['max-price'] );
		}

		$get_select_placeholder = get_option( 'inspiry_max_price_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		if ( $maximum_price == inspiry_any_value() || empty( $maximum_price ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . $default_slelect . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . $default_slelect . '</option>';
		}

		if ( ! empty( $max_price_array ) && function_exists( 'ere_format_amount' ) ) {
			foreach ( $max_price_array as $price ) {
				if ( $maximum_price == $price ) {
					echo '<option value="' . $price . '" selected="selected">' . ere_format_amount( $price ) . '</option>';
				} else {
					echo '<option value="' . $price . '">' . ere_format_amount( $price ) . '</option>';
				}
			}
		}
	}
}


if ( ! function_exists( 'max_prices_for_rent_list' ) ) {
	/**
	 * Maximum Price For Rent Only
	 */
	function max_prices_for_rent_list() {

		$max_price_for_rent_array = array(
			1000,
			2000,
			3000,
			4000,
			5000,
			7500,
			10000,
			15000,
			20000,
			25000,
			30000,
			40000,
			50000,
			75000,
			100000,
			150000
		);

		/* Get values from theme options and convert them to an integer array */
		$maximum_price_for_rent_values = get_option( 'theme_maximum_price_values_for_rent' );
		if ( ! empty( $maximum_price_for_rent_values ) ) {
			$max_prices_string_array = explode( ',', $maximum_price_for_rent_values );
			if ( is_array( $max_prices_string_array ) && ! empty( $max_prices_string_array ) ) {
				$new_max_prices_array = array();
				foreach ( $max_prices_string_array as $string_price ) {
					$integer_price = doubleval( $string_price );
					if ( $integer_price > 1 ) {
						$new_max_prices_array[] = $integer_price;
					}
				}
				if ( ! empty( $new_max_prices_array ) ) {
					$max_price_for_rent_array = $new_max_prices_array;
				}
			}
		}

		$maximum_price = '';
		if ( isset( $_GET['max-price'] ) ) {
			$maximum_price = doubleval( $_GET['max-price'] );
		}

		$get_select_placeholder = get_option( 'inspiry_max_price_placeholder' );
		if ( ! empty( $get_select_placeholder ) ) {
			$default_slelect = $get_select_placeholder;
		} else {
			$default_slelect = rh_any_text();
		}

		if ( $maximum_price == inspiry_any_value() || empty( $maximum_price ) ) {
			echo '<option value="' . inspiry_any_value() . '" selected="selected">' . $default_slelect . '</option>';
		} else {
			echo '<option value="' . inspiry_any_value() . '">' . $default_slelect . '</option>';
		}

		if ( ! empty( $max_price_for_rent_array ) && function_exists( 'ere_format_amount' ) ) {
			foreach ( $max_price_for_rent_array as $price ) {
				if ( $maximum_price == $price ) {
					echo '<option value="' . $price . '" selected="selected">' . ere_format_amount( $price ) . '</option>';
				} else {
					echo '<option value="' . $price . '">' . ere_format_amount( $price ) . '</option>';
				}
			}
		}

	}
}


if ( ! function_exists( 'inspiry_get_location_titles' ) ) :
	/**
	 * Get location titles
	 *
	 * @return array Location titles
	 */
	function inspiry_get_location_titles() {

		$main_location = esc_html__( 'Main Location', 'framework' );

		if ( inspiry_is_rvr_enabled() ) {
			$main_location = esc_html__( 'City', 'framework' );
		}

		$location_select_titles = array(
			$main_location,
			esc_html__( 'Child Location', 'framework' ),
			esc_html__( 'Grand Child Location', 'framework' ),
			esc_html__( 'Great Grand Child Location', 'framework' ),
		);

		// Override select boxes titles based on theme options data.
		for ( $i = 1; $i <= 4; $i ++ ) {
			$temp_location_title = get_option( 'theme_location_title_' . $i );
			if ( $temp_location_title ) {
				$location_select_titles[ $i - 1 ] = $temp_location_title;
			}
		}

		return $location_select_titles;
	}
endif;


if ( ! function_exists( 'inspiry_get_locations_number' ) ) :
	/**
	 * Return number of location boxes required in search form
	 *
	 * @return int number of locations
	 */
	function inspiry_get_locations_number() {

		$is_location_ajax = get_option( 'inspiry_ajax_location_field', 'no' ); // Option to check if location field Ajax is enabled

		if ( 'yes' !== $is_location_ajax ) {
			$location_select_count = intval( get_option( 'theme_location_select_number' ) );
			if ( ! ( $location_select_count > 0 && $location_select_count < 5 ) ) {
				$location_select_count = 1;
			}

			return $location_select_count;
		}

		return 1;
	}
endif;


if ( ! function_exists( 'inspiry_get_location_select_names' ) ) :
	/**
	 * Return location select names
	 *
	 * @return mixed|void
	 */
	function inspiry_get_location_select_names() {
		$location_select_names = array(
			'location',
			'child-location',
			'grandchild-location',
			'great-grandchild-location'
		);

		return apply_filters( 'inspiry_location_select_names', $location_select_names );
	}
endif;


if ( ! function_exists( 'real_homes_search' ) ) {
	/**
	 * Properties Search Filter
	 *
	 * @param $search_args
	 *
	 * @return mixed
	 */
	function real_homes_search( $search_args ) {

		// number of properties
		$number_of_properties = intval( get_option( 'theme_properties_on_search' ) );
		if ( ! $number_of_properties ) {
			$number_of_properties = 4;
		}
		$search_args['posts_per_page'] = $number_of_properties;


		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) { // if is static front page
			$paged = get_query_var( 'page' );
		}

		$search_args['paged'] = $paged;


		/* Initialize Taxonomy Query Array */
		$tax_query = array();

		/* Initialize Meta Query Array */
		$meta_query = array();

		/* If search arguments already have a meta query then get it and work on that */
		if ( isset( $search_args['meta_query'] ) ) {
			$meta_query = $search_args['meta_query'];
		}

		/* Keyword Search */
		$search_args = inspiry_keyword_search( $search_args );

		/* Meta Search Filter */
		$meta_query = apply_filters( 'inspiry_real_estate_meta_search', $meta_query );

		/* Taxonomy Search Filter */
		$tax_query = apply_filters( 'inspiry_real_estate_taxonomy_search', $tax_query );

		/* If more than one taxonomies exist then specify the relation */
		$tax_count = count( $tax_query );
		if ( $tax_count > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		/* If more than one meta query elements exist then specify the relation */
		$meta_count = count( $meta_query );
		if ( $meta_count > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		/* If taxonomy query has some values then add it to search query */
		if ( $tax_count > 0 ) {
			$search_args['tax_query'] = $tax_query;
		}

		/* If meta query has some values then add it to search query */
		if ( $meta_count > 0 ) {
			$search_args['meta_query'] = $meta_query;
		}

		// check if featured properties on top option is selected
		$inspiry_featured_properties_on_top = get_option( 'inspiry_featured_properties_on_top' );
		if ( $inspiry_featured_properties_on_top == 'true' ) {
			$search_args['meta_key'] = 'REAL_HOMES_featured';
			$search_args['orderby']  = array(
				'meta_value_num' => 'DESC',
				'date'           => 'DESC',
			);
		}

		/* Sort by price */
		if ( ( isset( $_GET['min-price'] ) && ( $_GET['min-price'] != inspiry_any_value() ) ) || ( isset( $_GET['max-price'] ) && ( $_GET['max-price'] != inspiry_any_value() ) ) ) {
			$search_args['orderby']  = 'meta_value_num';
			$search_args['meta_key'] = 'REAL_HOMES_property_price';
			$search_args['order']    = 'ASC';
		}

		return $search_args;
	}

	add_filter( 'real_homes_search_parameters', 'real_homes_search' );
}


if ( ! function_exists( 'inspiry_area_search' ) ) :
	/**
	 * Add area related search arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_area_search( $meta_query ) {

		if ( isset( $_GET['min-area'] ) && ! empty( $_GET['min-area'] ) && isset( $_GET['max-area'] ) && ! empty( $_GET['max-area'] ) ) {
			$min_area = intval( $_GET['min-area'] );
			$max_area = intval( $_GET['max-area'] );
			if ( $min_area >= 0 && $max_area > $min_area ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_size',
					'value'   => array( $min_area, $max_area ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			}
		} elseif ( isset( $_GET['min-area'] ) && ! empty( $_GET['min-area'] ) ) {
			$min_area = intval( $_GET['min-area'] );
			if ( $min_area > 0 ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_size',
					'value'   => $min_area,
					'type'    => 'NUMERIC',
					'compare' => '>='
				);
			}
		} elseif ( isset( $_GET['max-area'] ) && ! empty( $_GET['max-area'] ) ) {
			$max_area = intval( $_GET['max-area'] );
			if ( $max_area > 0 ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_size',
					'value'   => $max_area,
					'type'    => 'NUMERIC',
					'compare' => '<='
				);
			}
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_area_search' );
endif;


if ( ! function_exists( 'inspiry_price_search' ) ) :
	/**
	 * Add price related search arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_price_search( $meta_query ) {
		if ( isset( $_GET['min-price'] ) && ( $_GET['min-price'] != inspiry_any_value() ) && isset( $_GET['max-price'] ) && ( $_GET['max-price'] != inspiry_any_value() ) ) {
			$min_price = doubleval( $_GET['min-price'] );
			$max_price = doubleval( $_GET['max-price'] );
			if ( $min_price >= 0 && $max_price > $min_price ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_price',
					'value'   => array( $min_price, $max_price ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			}
		} elseif ( isset( $_GET['min-price'] ) && ( $_GET['min-price'] != inspiry_any_value() ) ) {
			$min_price = doubleval( $_GET['min-price'] );
			if ( $min_price > 0 ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_price',
					'value'   => $min_price,
					'type'    => 'NUMERIC',
					'compare' => '>='
				);
			}
		} elseif ( isset( $_GET['max-price'] ) && ( $_GET['max-price'] != inspiry_any_value() ) ) {
			$max_price = doubleval( $_GET['max-price'] );
			if ( $max_price > 0 ) {
				$meta_query[] = array(
					'key'     => 'REAL_HOMES_property_price',
					'value'   => $max_price,
					'type'    => 'NUMERIC',
					'compare' => '<='
				);
			}
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_price_search' );
endif;


if ( ! function_exists( 'inspiry_property_id_search' ) ) :
	/**
	 * Add property id related search arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_property_id_search( $meta_query ) {
		if ( isset( $_GET['property-id'] ) && ! empty( $_GET['property-id'] ) ) {
			$property_id  = trim( $_GET['property-id'] );
			$meta_query[] = array(
				'key'     => 'REAL_HOMES_property_id',
				'value'   => $property_id,
				'compare' => 'LIKE',
				'type'    => 'CHAR'
			);
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_property_id_search' );
endif;


if ( ! function_exists( 'inspiry_get_beds_baths_compare_operator' ) ) :
	/**
	 * Return compare operator for beds and baths search
	 * @return string
	 */
	function inspiry_get_beds_baths_compare_operator() {
		$inspiry_beds_baths_search_behaviour = get_option( 'inspiry_beds_baths_search_behaviour' );
		switch ( $inspiry_beds_baths_search_behaviour ) {
			case 'min':
				$inspiry_compare_operator = '>=';
				break;
			case 'max':
				$inspiry_compare_operator = '<=';
				break;
			case 'equal':
				$inspiry_compare_operator = '=';
				break;
			default:
				$inspiry_compare_operator = '>=';
		}

		return $inspiry_compare_operator;
	}
endif;


if ( ! function_exists( 'inspiry_get_garages_compare_operator' ) ) :

	/**
	 * Return compare operator for garages search
	 *
	 * @return string
	 */
	function inspiry_get_garages_compare_operator() {
		$inspiry_garages_search_behaviour = get_option( 'inspiry_garages_search_behaviour' );
		switch ( $inspiry_garages_search_behaviour ) {
			case 'min':
				$inspiry_compare_operator = '>=';
				break;
			case 'max':
				$inspiry_compare_operator = '<=';
				break;
			case 'equal':
				$inspiry_compare_operator = '=';
				break;
			default:
				$inspiry_compare_operator = '>=';
		}

		return $inspiry_compare_operator;
	}
endif;


if ( ! function_exists( 'inspiry_bathrooms_search' ) ) :
	/**
	 * Add bathrooms related arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_bathrooms_search( $meta_query ) {
		if ( ( ! empty( $_GET['bathrooms'] ) ) && ( $_GET['bathrooms'] != inspiry_any_value() ) ) {
			$meta_query[] = array(
				'key'     => 'REAL_HOMES_property_bathrooms',
				'value'   => $_GET['bathrooms'],
				'compare' => inspiry_get_beds_baths_compare_operator(),
				'type'    => 'DECIMAL'
			);
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_bathrooms_search' );
endif;


if ( ! function_exists( 'inspiry_beds_search' ) ) :
	/**
	 * Add bedrooms related arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_beds_search( $meta_query ) {
		if ( ( ! empty( $_GET['bedrooms'] ) ) && ( $_GET['bedrooms'] != inspiry_any_value() ) ) {
			$meta_query[] = array(
				'key'     => 'REAL_HOMES_property_bedrooms',
				'value'   => $_GET['bedrooms'],
				'compare' => inspiry_get_beds_baths_compare_operator(),
				'type'    => 'DECIMAL'
			);
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_beds_search' );
endif;


if ( ! function_exists( 'inspiry_garages_search' ) ) :

	/**
	 * Add garages related arguments to meta query
	 *
	 * @param array $meta_query - Meta search query array.
	 *
	 * @return array
	 */
	function inspiry_garages_search( $meta_query ) {
		$get_global = $_GET;
		if ( ( ! empty( $get_global['garages'] ) ) && ( $get_global['garages'] != inspiry_any_value() ) ) {
			$meta_query[] = array(
				'key'     => 'REAL_HOMES_property_garage',
				'value'   => $get_global['garages'],
				'compare' => inspiry_get_garages_compare_operator(),
				'type'    => 'DECIMAL',
			);
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_garages_search' );
endif;

if ( ! function_exists( 'inspiry_agent_search' ) ) :
	/**
	 * Add property agent related search arguments to meta query
	 *
	 * @param $meta_query
	 *
	 * @return array
	 */
	function inspiry_agent_search( $meta_query ) {
		if ( isset( $_GET['agents'] ) && ! empty( $_GET['agents'] ) ) {
			$meta_query[] = array(
				'key'     => 'REAL_HOMES_agents',
				'value'   => $_GET['agents'],
				'compare' => 'IN',
			);
		}

		return $meta_query;
	}

	add_filter( 'inspiry_real_estate_meta_search', 'inspiry_agent_search' );
endif;

if ( ! function_exists( 'inspiry_keyword_search' ) ) {
	/**
	 * Add keyword search related arguments to search query
	 *
	 * @param $search_args
	 *
	 * @return mixed
	 */
	function inspiry_keyword_search( $search_args ) {
		if ( isset ( $_GET['keyword'] ) ) {
			$keyword = trim( $_GET['keyword'] );
			if ( ! empty( $keyword ) ) {
				$search_args['s'] = $keyword;

				return $search_args;
			}
		}

		return $search_args;
	}
}


if ( ! function_exists( 'inspiry_property_type_search' ) ) :
	/**
	 * Add property type related search arguments to taxonomy query
	 *
	 * @param $tax_query
	 *
	 * @return array
	 */
	function inspiry_property_type_search( $tax_query ) {
		if ( ( ! empty( $_GET['type'] ) ) && ( $_GET['type'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'property-type',
				'field'    => 'slug',
				'terms'    => $_GET['type']
			);
		}

		return $tax_query;
	}

	add_filter( 'inspiry_real_estate_taxonomy_search', 'inspiry_property_type_search' );
endif;


if ( ! function_exists( 'inspiry_location_search' ) ) :
	/**
	 * Add location related search arguments to taxonomy query
	 *
	 * @param $tax_query
	 *
	 * @return array
	 */
	function inspiry_location_search( $tax_query ) {
		$location_select_names = inspiry_get_location_select_names();
		$locations_count       = count( $location_select_names );
		for ( $l = $locations_count - 1; $l >= 0; $l -- ) {
			if ( isset( $_GET[ $location_select_names[ $l ] ] ) ) {
				$current_location = $_GET[ $location_select_names[ $l ] ];
				if ( ( ! empty ( $current_location ) ) && ( $current_location != inspiry_any_value() ) ) {
					$tax_query[] = array(
						'taxonomy' => 'property-city',
						'field'    => 'slug',
						'terms'    => $current_location
					);
					break;
				}
			}
		}

		return $tax_query;
	}

	add_filter( 'inspiry_real_estate_taxonomy_search', 'inspiry_location_search' );
endif;


if ( ! function_exists( 'inspiry_property_features_search' ) ) :
	/**
	 * Add features related search arguments to taxonomy query
	 *
	 * @param $tax_query
	 *
	 * @return array
	 */
	function inspiry_property_features_search( $tax_query ) {
		if ( isset( $_GET['features'] ) ) {
			$required_features_slugs = $_GET['features'];
			if ( is_array( $required_features_slugs ) ) {
				$slugs_count = count( $required_features_slugs );
				if ( $slugs_count > 0 ) {

					/* build an array of existing features slugs to validate required feature slugs */
					$existing_features_slugs = inspiry_get_features_slugs();

					foreach ( $required_features_slugs as $feature_slug ) {
						$feature_slug = rawurldecode( $feature_slug );
						/* validate feature slug */
						if ( in_array( $feature_slug, $existing_features_slugs ) ) {
							$tax_query[] = array(
								'taxonomy' => 'property-feature',
								'field'    => 'slug',
								'terms'    => $feature_slug,
							);
						}
					}
				}
			}
		}

		return $tax_query;
	}

	add_filter( 'inspiry_real_estate_taxonomy_search', 'inspiry_property_features_search' );
endif;


if ( ! function_exists( 'inspiry_property_status_search' ) ) :
	/**
	 * Add property status related search arguments to taxonomy query
	 *
	 * @param $tax_query
	 *
	 * @return array
	 */
	function inspiry_property_status_search( $tax_query ) {
		if ( ( ! empty( $_GET['status'] ) ) && ( $_GET['status'] != inspiry_any_value() ) ) {
			$tax_query[] = array(
				'taxonomy' => 'property-status',
				'field'    => 'slug',
				'terms'    => $_GET['status']
			);
		}

		// Excluding statuses
		$all_statuses = get_option( 'inspiry_search_exclude_status' );

		if ( ! empty( $all_statuses ) ) {
			$tax_query[] = array(
				array(
					'taxonomy' => 'property-status',
					'field'    => 'id',
					'terms'    => $all_statuses,
					'operator' => 'NOT IN'
				)
			);
		}

		return $tax_query;
	}

	add_filter( 'inspiry_real_estate_taxonomy_search', 'inspiry_property_status_search' );
endif;


if ( ! function_exists( 'inspiry_get_features_slugs' ) ) :
	/**
	 * Returns an array that contains slugs for all property features
	 *
	 * @return array
	 */
	function inspiry_get_features_slugs() {
		$existing_features_slugs = array();
		$existing_features       = get_terms( array(
			'taxonomy'   => 'property-feature',
			'hide_empty' => false
		) );
		$existing_features_count = count( $existing_features );
		if ( $existing_features_count > 0 ) {
			foreach ( $existing_features as $feature ) {
				$existing_features_slugs[] = rawurldecode( $feature->slug );
			}
		}

		return $existing_features_slugs;
	}
endif;


if ( ! function_exists( 'inspiry_fix_featured_meta' ) ) :
	/**
	 * To fix featured property meta data inconsistency in legacy code.
	 */
	function inspiry_fix_featured_meta() {

		// Works on search template only
		if ( is_page_template( array(
			'templates/properties-search.php',
			'templates/properties-search-half-map.php',
			'templates/properties-search-left-sidebar.php',
			'templates/properties-search-right-sidebar.php'
		) ) ) {

			// Only if featured properties on top is enabled
			$featured_properties_on_top = get_option( 'inspiry_featured_properties_on_top' );
			if ( 'true' == $featured_properties_on_top ) {

				// Take up to 100 properties and fix those.
				$fixable_properties = get_posts( array(
					'post_type'      => 'property',
					'posts_per_page' => 100,
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => 'REAL_HOMES_featured',
							'compare' => 'NOT EXISTS',
						),
						array(
							'relation' => 'AND',
							array(
								'key'     => 'REAL_HOMES_featured',
								'value'   => '1',
								'compare' => '!=',
							),
							array(
								'key'     => 'REAL_HOMES_featured',
								'value'   => '0',
								'compare' => '!=',
							)
						),
					),
				) );

				if ( $fixable_properties ) {
					foreach ( $fixable_properties as $fixable_property ) {
						update_post_meta( $fixable_property->ID, 'REAL_HOMES_featured', '0' );
					}
				}
			}
		}

	}

	add_action( 'template_redirect', 'inspiry_fix_featured_meta' );
endif;

if ( ! function_exists( 'inspiry_get_location_options' ) ) {
	/**
	 * Return Property Locations as Options List in Json format
	 */
	function inspiry_get_location_options() {

		$options = array(); // A list of location options will be passed to this array
		$number  = 15; // Number of locations that will be returned per Ajax request

		$offset = '';
        if(isset($_GET['page'])){
		$offset          = $number * ( $_GET['page'] - 1 ); // Offset of locations list for the current Ajax request
        }
		$locations_order = array(
			'orderby' => 'count',
			'order'   => 'desc',
		);

		$order = get_option( 'theme_locations_order' );
		if ( $order == 'true' ) {
			$locations_order['orderby'] = 'name';
			$locations_order['order']   = 'asc';
		}

		// Prepare a query to fetch property locations from database
		$terms_query = array(
			'taxonomy'   => 'property-city',
			'number'     => $number,
			'offset'     => $offset,
			'hide_empty' => inspiry_hide_empty_locations(),
			'orderby'    => $locations_order['orderby'],
			'order'      => $locations_order['order'],
		);

		// If there is a search parameter
		if ( isset( $_GET['query'] ) ) {
			$terms_query['name__like'] = $_GET['query'];
		}

		$locations = get_terms( $terms_query );

		// Add location first placeholder option on first ajax request.
//		if ( 0 === $offset ) {
//			if ( 'templates/submit-property.php' === $_GET['template'] ) {
//				$options[] = array( '', esc_html__( 'None', 'framework' ) );
//			} else {
//				$location_placeholder = inspiry_location_placeholder();
//				$options[]            = array( 'any', $location_placeholder[0] );
//			}
//		}

		// Build an array of locations info form their objects
		if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) {
			foreach ( $locations as $location ) {
				$options[] = array( $location->slug, $location->name );
			}
		}

		echo json_encode( $options ); // Return locations list in Json format
		die;
	}

	add_action( 'wp_ajax_inspiry_get_location_options', 'inspiry_get_location_options' );
	add_action( 'wp_ajax_nopriv_inspiry_get_location_options', 'inspiry_get_location_options' );
}

if ( ! function_exists( 'inspiry_searched_ajax_locations' ) ) {
	/**
	 * Display Property Ajax Searched Locations Select Options
	 */

	function inspiry_searched_ajax_locations() {

		$searched_locations = '';
		if ( isset( $_GET['location'] ) ) {
			$searched_locations = $_GET['location'];
		}

		if ( is_array( $searched_locations ) && ! empty( $_GET['location'] ) ) {

			foreach ( $searched_locations as $location ) {
				$searched_terms = get_term_by( 'slug', $location, 'property-city' );
				?>
                <option value="<?php echo esc_attr( $searched_terms->slug ) ?>"
                        selected="selected"><?php echo esc_html( $searched_terms->name ) ?></option>
				<?php
			}
		} elseif ( ! empty( $searched_terms ) ) {
			$searched_terms = get_term_by( 'slug', $searched_locations, 'property-city' );
			?>
            <option value="<?php echo esc_attr( $searched_terms->slug ) ?>"
                    selected="selected"><?php echo esc_html( $searched_terms->name ) ?></option>
			<?php
		}

	}
}