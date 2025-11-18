<?php 
    $main_modal_active = get_field('main_modal_active', 'option');
    $modal_title       = get_field('main_modal_title', 'option');
    $modal_body        = get_field('main_modal_body', 'option');
    $modal_button      = get_field('main_modal_button', 'option');
    $modal_background  = get_field('main_modal_background', 'option');
    $button_url        = esc_url( $modal_button['url'] ) ?? '';
    $button_title      = esc_html( $modal_button['title'] ) ?? '';
    $button_target     = $modal_button['target'] ? esc_attr( $modal_button['target'] ) : '_self';

    $has_bg_class     = $modal_background ? ' modal--has-background' : '';
    
    if ( $modal_background ) {
        $bg_url = esc_url( $modal_background['url'] );

        // Add linear gradient for darker overlay
        $background_style = ' style="background-image: linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.25)), url(' . $bg_url . '); background-size: cover; background-position: center;"';
    }
?>

<?php if ( $main_modal_active ) : ?>
    <div class="modal<?php echo esc_attr( $has_bg_class ); ?> fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"<?php echo $background_style; ?>>

                <?php if ( $modal_title ) : ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="mainModalLabel"><?php echo esc_html( $modal_title ); ?></h5>
                    </div>
                <?php endif; ?>

                <div class="modal-body">
                    <?php 
                        if ( $modal_body ) {
                            echo wpautop( wp_kses_post( $modal_body ) );
                        }
                    ?>
                </div>
                
                <?php if ( $modal_button ) : ?>
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
