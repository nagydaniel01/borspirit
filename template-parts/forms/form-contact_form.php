<?php
    $current_user = wp_get_current_user();
    $prefix = 'cf_';
?>

<form id="contact_form" class="form form--contact" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <?php wp_nonce_field('contact_form_action', 'contact_form_nonce'); ?>

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr($prefix); ?>name">
            <?php echo esc_html__( 'Name', 'borspirit' ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="<?php echo esc_attr($prefix); ?>name" name="<?php echo esc_attr($prefix); ?>name" value="<?php echo esc_attr($current_user->display_name); ?>" placeholder="<?php echo esc_attr__( 'Enter your full name', 'borspirit' ); ?>" required aria-required="true">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="<?php echo esc_attr($prefix); ?>email">
                <?php echo esc_html__( 'E-mail', 'borspirit' ); ?> <span class="required">*</span>
            </label>
            <input type="email" class="form-control" id="<?php echo esc_attr($prefix); ?>email" name="<?php echo esc_attr($prefix); ?>email" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="<?php echo esc_attr__( 'Enter your email address', 'borspirit' ); ?>" required aria-required="true">
        </div>
    
        <div class="col-md-6 mb-3">
            <label class="form-label" for="<?php echo esc_attr($prefix); ?>phone">
                <?php echo esc_html__( 'Phone', 'borspirit' ); ?>
            </label>
            <input type="tel" class="form-control" id="<?php echo esc_attr($prefix); ?>phone" name="<?php echo esc_attr($prefix); ?>phone" placeholder="<?php echo esc_attr__( 'Enter your phone number', 'borspirit' ); ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr($prefix); ?>subject">
            <?php echo esc_html__( 'Subject', 'borspirit' ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="<?php echo esc_attr($prefix); ?>subject" name="<?php echo esc_attr($prefix); ?>subject" placeholder="<?php echo esc_attr__( 'Enter subject', 'borspirit' ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr($prefix); ?>message">
            <?php echo esc_html__( 'Message', 'borspirit' ); ?> <span class="required">*</span>
        </label>
        <textarea class="form-control" id="<?php echo esc_attr($prefix); ?>message" name="<?php echo esc_attr($prefix); ?>message" rows="4" placeholder="<?php echo esc_attr__( 'Write your message here...', 'borspirit' ); ?>" required aria-required="true"></textarea>
    </div>

    <fieldset class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="<?php echo esc_attr($prefix); ?>privacy_policy" name="<?php echo esc_attr($prefix); ?>privacy_policy" required aria-required="true">
            <label class="form-check-label" for="<?php echo esc_attr($prefix); ?>privacy_policy">
                <?php 
                    echo sprintf(
                        esc_html__( 'I agree to the %s', 'borspirit' ), 
                        '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">' . esc_html__( 'Privacy Policy', 'borspirit' ) . '</a>'
                    ); 
                ?>
                <span class="required">*</span>
            </label>
        </div>
    </fieldset>

    <div class="form__actions">
        <button type="submit" class="btn btn-primary mb-3">
            <span><?php echo esc_html__( 'Send message', 'borspirit' ); ?></span>
            <svg class="icon icon-paper-plane"><use xlink:href="#icon-paper-plane"></use></svg>
        </button>
        <div id="<?php echo esc_attr($prefix); ?>response" role="status" aria-live="polite"></div>
    </div>
</form>
