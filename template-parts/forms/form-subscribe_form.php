<?php
    $current_user = wp_get_current_user();
    $prefix = 'mc_';
?>

<form id="subscribe_form" class="form form--subscribe" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <?php wp_nonce_field( 'subscribe_form_action', 'subscribe_form_nonce' ); ?>
    <input type="hidden" name="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label visually-hidden" for="<?php echo esc_attr($prefix); ?>name">
                <?php echo esc_html__( 'Name', TEXT_DOMAIN ); ?> <span class="required">*</span>
            </label>
            <input type="text" class="form-control" id="<?php echo esc_attr($prefix); ?>name" name="<?php echo esc_attr($prefix); ?>name" value="" placeholder="<?php echo esc_attr__( 'Enter your name', TEXT_DOMAIN ); ?>" required aria-required="true">
        </div>
    
        <div class="col-md-6 mb-3">
            <label class="form-label visually-hidden" for="<?php echo esc_attr($prefix); ?>email">
                <?php echo esc_html__( 'E-mail', TEXT_DOMAIN ); ?> <span class="required">*</span>
            </label>
            <input type="email" class="form-control" id="<?php echo esc_attr($prefix); ?>email" name="<?php echo esc_attr($prefix); ?>email" value="" placeholder="<?php echo esc_attr__( 'Enter your email address', TEXT_DOMAIN ); ?>" required aria-required="true">
        </div>
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
            <span><?php echo esc_html__( 'Subscribe', TEXT_DOMAIN ); ?></span>
            <svg class="icon icon-paper-plane"><use xlink:href="#icon-paper-plane"></use></svg>
        </button>
        <div id="<?php echo esc_attr($prefix); ?>response" role="status" aria-live="polite"></div>
    </div>
</form>