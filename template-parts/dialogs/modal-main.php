<?php 
    $main_modal_active = get_field('main_modal_active', 'option');
    $modal_title       = get_field('main_modal_title', 'option');
    $modal_body        = get_field('main_modal_body', 'option');

    $modal_button      = get_field('main_modal_button', 'option') ?: [];
    $button_url        = esc_url($modal_button['url'] ?? '');
    $button_title      = esc_html($modal_button['title'] ?? '');
    $button_target     = esc_attr($modal_button['target'] ?: '_self');

    $modal_background  = get_field('main_modal_background', 'option');
    $has_bg_class      = $modal_background ? ' modal--has-background' : '';
    $background_style  = $modal_background ? ' style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url(' . esc_url($modal_background['url']) . '); background-size: cover; background-position: center;"' : '';
?>

<?php if ( $main_modal_active ) : ?>
    <div class="modal<?php echo esc_attr( $has_bg_class ); ?> fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"<?php echo $background_style; ?>>
                <div class="modal-header">
                    <?php if ( $modal_title ) : ?>
                        <h5 class="modal-title" id="mainModalLabel"><?php echo esc_html( $modal_title ); ?></h5>
                    <?php endif; ?>

                    <?php if ( empty( $button_url ) ) : ?>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', 'borspirit'); ?>"></button>
                    <?php endif; ?>
                </div>
                <div class="modal-body">
                    <?php 
                        if ( $modal_body ) {
                            echo wp_kses_post( $modal_body );
                        }
                    ?>
                </div>
                <?php if ( !empty( $button_url ) ) : ?>
                    <div class="modal-footer">
                        <button type="button" id="skip" class="btn btn-outline-primary me-2" data-bs-dismiss="modal">
                            <?php echo esc_html__( 'Skip', 'borspirit' ); ?>
                        </button>
                        <a href="<?php echo $button_url; ?>" target="<?php echo $button_target; ?>" class="btn btn-primary">
                            <?php echo $button_title; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
