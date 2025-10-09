<?php
    $current_user = wp_get_current_user();
    $prefix = 'mc_';

    // Get Mailchimp settings
    $mailchimp_api_key = get_field('mailchimp_api_key', 'option');
    $mailchimp_audience_id = get_field('mailchimp_audience_id', 'option');

    // Only show the form if both fields have values
    if ( !empty($mailchimp_api_key) && !empty($mailchimp_audience_id) ) :
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

<?php else : ?>

    <?php
        if ( current_user_can('manage_options') ) {
            printf( 
                '<div class="alert alert-danger" role="alert">%s</div>',
                sprintf(
                    __('Mailchimp configuration is missing. Please set <code>%s</code> and <code>%s</code> in the theme options.', TEXT_DOMAIN),
                    esc_html('mailchimp_api_key'),
                    esc_html('mailchimp_audience_id')
                )
            );
        } else {
            printf(
                '<div class="alert alert-warning" role="alert">%s</div>',
                esc_html__( 'Subscription form is temporarily unavailable. Please try again later.', TEXT_DOMAIN )
            );
        }
    ?>

<?php endif; // end check for Mailchimp credentials ?>
