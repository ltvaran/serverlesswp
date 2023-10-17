<div id="dashboard-membership" class="dashboard-membership">
	<?php
	$ims_functions = IMS_Functions();

	// Membership enable option.
	if ( ! empty( $ims_functions::is_memberships() ) ) {

		// Get current user.
		$current_user       = wp_get_current_user();
		$current_membership = $ims_functions::ims_get_membership_by_user( $current_user );
		$current_user_id = $current_user->ID;
		
		if ( is_array( $current_membership ) && ! empty( $current_membership ) ) {
			?>
            <div class="membership-info">
                <div class="dl-list">
					<?php
					if ( isset( $current_membership['title'] ) ) :
						?>
                        <dl>
                            <dt><?php esc_html_e( 'Current Package', 'framework' ); ?></dt>
                            <dd><?php echo esc_html( $current_membership['title'] ); ?></dd>
                        </dl>
					<?php
					endif;

					if ( isset( $current_membership['properties'] ) ) :
						?>
                        <dl>
                            <dt><?php esc_html_e( 'Allowed Properties', 'framework' ); ?></dt>
                            <dd>
								<?php
								if ( isset( $current_membership['properties'] ) && ! empty( $current_membership['properties'] ) ) {
									echo esc_html( $current_membership['properties'] );
								} else {
									esc_html_e( '0', 'framework' );
								}
								?>
                            </dd>
                        </dl>
                        <dl>
                            <dt><?php esc_html_e( 'Properties Remained', 'framework' ); ?></dt>
                            <dd>
								<?php
								$ims_properties = get_user_meta( $current_user_id, 'ims_current_properties', true );
								if ( ! empty( $ims_properties ) ) {
									echo esc_html( $ims_properties );
								} else {
									esc_html_e( '0', 'framework' );
								}
								?>
                            </dd>
                        </dl>
					<?php
					endif;

					if ( isset( $current_membership['featured_prop'] ) ) :
						?>
                        <dl>
                            <dt><?php esc_html_e( 'Allowed Featured Properties', 'framework' ); ?></dt>
                            <dd>
								<?php
								if ( isset( $current_membership['featured_prop'] ) && ! empty( $current_membership['featured_prop'] ) ) {
									echo esc_html( $current_membership['featured_prop'] );
								} else {
									esc_html_e( '0', 'framework' );
								}
								?>
                            </dd>
                        </dl>
                        <dl>
                            <dt><?php esc_html_e( 'Featured Properties Remained', 'framework' ); ?></dt>
                            <dd>
								<?php
								$ims_featured = get_user_meta( $current_user_id, 'ims_current_featured_props', true );
								if ( ! empty( $ims_featured ) ) {
									echo esc_html( $ims_featured );
								} else {
									esc_html_e( '0', 'framework' );
								}
								?>
                            </dd>
                        </dl>
					<?php
					endif;
					?>
                    <dl>
                        <dt><?php esc_html_e( 'Expiry Date', 'framework' ); ?></dt>
                        <dd><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_user_meta( $current_user_id, 'ims_membership_due_date', true ) ) ); ?></dd>
                    </dl>
                </div>
            </div>
			<p class="description"><strong><?php echo esc_html__( 'Note:', 'framework' ) . ' '; ?></strong><?php esc_html_e( 'You would need to cancel current membership before you upgrade or downgrade to any other membership package.', 'framework' ); ?></p>
            <a class="cancel-membership btn btn-primary">
                <i class="far fa-times-circle"></i>
				<?php esc_html_e( 'Cancel Membership', 'framework' ); ?>
            </a>
			<?php
			$ims_functions = IMS_Functions();
			$user_obj      = wp_get_current_user();
			$ims_functions->cancel_user_membership_form( $user_obj );
		} else {
			get_template_part( 'common/dashboard/packages' );
		}
	}
	?>
</div><!-- #dashboard-membership -->