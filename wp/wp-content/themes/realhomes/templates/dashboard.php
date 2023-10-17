<?php
/**
 * Template Name: Dashboard
 *
 * @package realhomes
 *
 * @since 3.12
 */
global $dashboard_globals, $current_module;

$dashboard_globals = realhomes_dashboard_globals();
$submodule           = '';
$current_module    = $dashboard_globals['current_module'];

if ( isset( $dashboard_globals['submodule'] ) && ! empty( $dashboard_globals['submodule'] ) ) {
	$submodule = $dashboard_globals['submodule'];
}

$submit_pages = array( 'submit-property', 'edit-property' );
if ( in_array( $current_module, $submit_pages ) || in_array( $submodule, $submit_pages ) ) {
	do_action( 'realhomes_dashboard_submit_property' );
}

get_header();
?>
    <div id="dashboard" class="dashboard">
		<?php
		get_template_part( 'common/dashboard/sidebar' );
		get_template_part( 'common/dashboard/content' );
		?>
    </div><!-- #dashboard -->
<?php
get_footer();