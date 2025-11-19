<?php
    $order_id = $args['order_id'];
    $prefix   = 'poll_';
?>

<form id="poll_form" class="form form--poll" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <h2><?php echo esc_html__( 'Please fill our poll!', 'borspirit' ); ?></h2>
    
    <?php wp_nonce_field( 'poll' . $order_id, 'poll_nonce' ); ?>
    <input type="hidden" name="action" value="collect_feedback" />
    <input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>" />

    <fieldset class="mb-3">
        <legend>
            <?php echo esc_html__( 'How would you rate your experience?', 'borspirit' ); ?> <span class="required">*</span>
        </legend>
        <div id="<?php echo esc_attr( $prefix ); ?>rating" class="form-check-group">
            <?php for ( $i = 5; $i >= 1; $i-- ) : ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="<?php echo esc_attr( $prefix . 'rating_' . $i ); ?>" name="rating" value="<?php echo esc_attr( $i ); ?>" required aria-required="true" <?php checked( $i, 5 ); ?>>
                    <label class="form-check-label" for="<?php echo esc_attr( $prefix . 'rating_' . $i ); ?>">
                        <?php echo str_repeat( '⭐', $i ); ?>
                    </label>
                </div>
            <?php endfor; ?>
        </div>
    </fieldset>

    <fieldset class="mb-3">
        <legend>
            <?php echo esc_html__( 'How would you describe our shop?', 'borspirit' ); ?> <span class="required">*</span>
        </legend>
        <div id="<?php echo esc_attr( $prefix ); ?>like" class="form-check-group">
            <?php
                $options = [
                    'superb'         => __( 'Superb', 'borspirit' ),
                    'good enough'    => __( 'Good enough', 'borspirit' ),
                    'could be better'=> __( 'Could be better', 'borspirit' ),
                ];
                
                $index = 0;
                foreach ( $options as $value => $label ) :
                    $index++;
            ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="<?php echo esc_attr( $prefix . 'like_' . sanitize_title( $value ) ); ?>" name="like" value="<?php echo esc_attr( $value ); ?>" required aria-required="true" <?php checked( $index, 1 ); ?>>
                    <label class="form-check-label" for="<?php echo esc_attr( $prefix . 'like_' . sanitize_title( $value ) ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr( $prefix ); ?>feedback_text">
            <?php echo esc_html__( 'Your feedback', 'borspirit' ); ?>
        </label>
        <textarea class="form-control" id="<?php echo esc_attr( $prefix ); ?>feedback_text" name="feedback_text" rows="4" placeholder="<?php echo esc_attr__( 'Tell us more…', 'borspirit' ); ?>"></textarea>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn btn-primary mb-3" id="poll_form_submit">
            <span><?php echo esc_html__( 'Submit feedback', 'borspirit' ); ?></span>
            <svg class="icon icon-paper-plane"><use xlink:href="#icon-paper-plane"></use></svg>
        </button>
        <div id="<?php echo esc_attr( $prefix ); ?>response" role="status" aria-live="polite"></div>
    </div>
</form>
