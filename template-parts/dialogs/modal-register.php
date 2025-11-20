<?php if ( ! is_user_logged_in() ) : ?>
    <?php 
        $modal_title = get_field('register_modal_title', 'option');
        $modal_body  = get_field('register_modal_body', 'option');
    ?>
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel"><?php echo esc_html( $modal_title ?: __('Want to know more? Register now!', 'borspirit') ); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', 'borspirit'); ?>"></button>
                </div>
                <div class="modal-body">
                    <?php 
                        if ( $modal_body ) {
                            echo wp_kses_post( $modal_body );
                        } else {
                            echo wpautop( esc_html__('Would you like to save your favorites?', 'borspirit') );
                        }
                    ?>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-primary">
                        <?php echo esc_html__('Login/Register', 'borspirit'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
