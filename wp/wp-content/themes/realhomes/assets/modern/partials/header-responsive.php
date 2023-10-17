<?php
/**
 * Header Variation One Template
 *
 * @package    realhomes
 * @subpackage modern
 */

	$get_responsive_header = get_option('inspiry_responsive_header_option','solid');

	if($get_responsive_header == 'solid'){
		$responsive_class = ' rh_header_advance ';
	}else{
		$responsive_class = ' rh_header_responsive ';
	}
	?>


	<header class="rh_temp_header_responsive_view rh_header <?php echo esc_attr($responsive_class);?>  <?php echo is_page_template( 'templates/home.php' ) ? esc_attr( ' rh_header--shadow' ) : false; ?>">

		<div class="rh_header__wrap">

			<div class="rh_logo rh_logo_wrapper">

				<div class="rh_logo_inner">
					<?php
					$theme_sitelogo_mobile        = get_option( 'theme_sitelogo_mobile' );
					$theme_sitelogo_retina_mobile = get_option( 'theme_sitelogo_retina_mobile' );
					if ( ( isset( $theme_sitelogo_mobile ) && ! empty( $theme_sitelogo_mobile ) ) ||
					     ( isset( $theme_sitelogo_retina_mobile ) && ! empty( $theme_sitelogo_retina_mobile ) )
					) {
						get_template_part( 'assets/modern/partials/header/site-logo-responsive' );
					} else {
						get_template_part( 'assets/modern/partials/header/site-logo' );
					}
                    ?>
				</div>

			</div>
			<!-- /.rh_logo -->

			<div class="rh_menu">

				<!-- Start Main Menu-->
				<nav class="main-menu">
					<?php get_template_part( 'assets/modern/partials/header/menu-list-responsive' ); ?>
				</nav>
				<!-- End Main Menu -->

				<div class="rh_menu__user">
					<?php
					get_template_part( 'assets/modern/partials/header/user-phone' );


					?>
					<div class="user_menu_wrapper rh_user_menu_wrapper_responsive">
<!--						--><?php //get_template_part( 'assets/modern/partials/header/user-menu' ); ?>
					</div>
					<?php get_template_part( 'assets/modern/partials/header/submit-property' ); ?>
				</div>
				<!-- /.rh_menu__user -->

			</div>
			<!-- /.rh_menu -->

		</div>
		<!-- /.rh_header__wrap -->

	</header>
	<!-- /.rh_header -->
