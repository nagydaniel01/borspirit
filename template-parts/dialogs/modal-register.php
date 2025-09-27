<?php if ( ! is_user_logged_in() ) : ?>
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel"><?php echo esc_html__('Want to know more? Register now!', TEXT_DOMAIN); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
                </div>
                <div class="modal-body">
                    <p>Would you like to save your favorites?</p>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-primary">
                        <?php echo esc_html__('Login or Register', TEXT_DOMAIN); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>