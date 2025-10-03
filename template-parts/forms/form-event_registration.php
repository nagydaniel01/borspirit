<?php
    $current_user = wp_get_current_user();
    $prefix = 'reg_';
?>

<form id="event_registration_form" class="form form--event_registration" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <?php wp_nonce_field( 'event_registration_form_action', 'event_registration_form_nonce' ); ?>
    <input type="hidden" name="event_id" value="<?php echo esc_attr( get_the_ID() ); ?>">

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr($prefix); ?>name">
            <?php echo esc_html__( 'Name', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="<?php echo esc_attr($prefix); ?>name" name="<?php echo esc_attr($prefix); ?>name" value="<?php echo esc_attr( $current_user->display_name ); ?>" placeholder="<?php echo esc_attr__( 'Enter your full name', TEXT_DOMAIN ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="<?php echo esc_attr($prefix); ?>email">
            <?php echo esc_html__( 'E-mail', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="email" class="form-control" id="<?php echo esc_attr($prefix); ?>email" name="<?php echo esc_attr($prefix); ?>email" value="<?php echo esc_attr( $current_user->user_email ); ?>" placeholder="<?php echo esc_attr__( 'Enter your email address', TEXT_DOMAIN ); ?>" required aria-required="true">
    </div>
    
    <fieldset class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="<?php echo esc_attr($prefix); ?>privacy_policy" name="<?php echo esc_attr($prefix); ?>privacy_policy" required aria-required="true">
            <label class="form-check-label" for="<?php echo esc_attr($prefix); ?>privacy_policy">
                <?php 
                    echo sprintf(
                        esc_html__( 'I agree to the %s', TEXT_DOMAIN ), 
                        '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">' . esc_html__( 'Privacy Policy', TEXT_DOMAIN ) . '</a>'
                    ); 
                ?>
                <span class="required">*</span>
            </label>
        </div>
    </fieldset>

    <div class="form__actions">
        <button type="submit" class="btn btn-primary mb-3">
            <span><?php echo esc_html__( 'Register', TEXT_DOMAIN ); ?></span>
            <svg class="icon icon-paper-plane"><use xlink:href="#icon-paper-plane"></use></svg>
        </button>
        <div id="<?php echo esc_attr($prefix); ?>response" role="status" aria-live="polite"></div>
    </div>
</form>
