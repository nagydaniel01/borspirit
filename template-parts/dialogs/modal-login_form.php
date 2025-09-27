<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <?php 
        // Check if registration is enabled on My Account page
        $registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
        $modal_title = $registration_enabled ? esc_html__( 'Login / Register', 'woocommerce' ) : esc_html__( 'Login', 'woocommerce' );
    ?>
    <div class="modal modal--alt fade" id="login_formModal" tabindex="-1" aria-labelledby="login_formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                                                <!-- Nav tabs -->
                            <ul class="nav nav-pills" id="wcLoginRegisterTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">
                                        <?php echo esc_html__( 'Login', 'woocommerce' ); ?>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">
                                        <?php echo esc_html__( 'Register', 'woocommerce' ); ?>
                                    </button>
                                </li>
                            </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
                </div>
                <div class="modal-body">
                    <div id="wc-login-form">
                        <?php if ( ! is_user_logged_in() ) : ?>
                            <?php //echo do_shortcode('[woocommerce_my_account]'); ?>

                            <!-- Tab content -->
                            <div class="tab-content" id="wcLoginRegisterTabsContent">
                                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                                    <?php echo do_shortcode( '[custom_wc_login_form]' ); ?>
                                </div>
                                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                    <?php echo do_shortcode( '[custom_wc_registration_form]' ); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>