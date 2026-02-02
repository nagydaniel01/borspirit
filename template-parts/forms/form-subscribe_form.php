<?php
    $current_user = wp_get_current_user();
    $prefix = 'mc_';

    $subscribe_form_title       = get_field('subscribe_form_title', 'option');
    $subscribe_form_description = get_field('subscribe_form_description', 'option');

    // Get Mailchimp settings
    $mailchimp_api_key = get_field('mailchimp_api_key', 'option') ?: MAILCHIMP_API_KEY;
    $mailchimp_audience_id = get_field('mailchimp_audience_id', 'option') ?: MAILCHIMP_AUDIENCE_ID;

    // Only show the form if both fields have values
    if ( !empty($mailchimp_api_key) && !empty($mailchimp_audience_id) ) :
?>

<form id="subscribe_form" class="form form--subscribe" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
            
                <h3 class="form__title"><?php echo esc_html($subscribe_form_title); ?></h3>
                <div class="form__lead"><?php echo wpautop( esc_html($subscribe_form_description) ); ?></div>
            
            </div>
            <div class="col-lg-6">
                
                <?php wp_nonce_field( 'subscribe_form_action', 'subscribe_form_nonce' ); ?>
                <input type="hidden" name="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label visually-hidden" for="<?php echo esc_attr($prefix); ?>name">
                            <?php echo esc_html__( 'Name', 'borspirit' ); ?> <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="<?php echo esc_attr($prefix); ?>name" name="<?php echo esc_attr($prefix); ?>name" value="" placeholder="<?php echo esc_attr_x( 'Enter your name', 'placeholder', 'borspirit' ); ?>" required aria-required="true">
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <label class="form-label visually-hidden" for="<?php echo esc_attr($prefix); ?>email">
                            <?php echo esc_html__( 'E-mail', 'borspirit' ); ?> <span class="required">*</span>
                        </label>
                        <input type="email" class="form-control" id="<?php echo esc_attr($prefix); ?>email" name="<?php echo esc_attr($prefix); ?>email" value="" placeholder="<?php echo esc_attr_x( 'Enter your email address', 'placeholder', 'borspirit' ); ?>" required aria-required="true">
                    </div>
                </div>

                <fieldset class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="<?php echo esc_attr($prefix); ?>privacy_policy" name="<?php echo esc_attr($prefix); ?>privacy_policy" required aria-required="true">
                        <label class="form-check-label" for="<?php echo esc_attr($prefix); ?>privacy_policy">
                            <?php 
                                echo sprintf(
                                    esc_html__( 'I have read and accept the %s', 'borspirit' ), 
                                    '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">' . esc_html( get_the_title( PRIVACY_POLICY_PAGE_ID ) ) . '</a>'
                                ); 
                            ?>
                            <span class="required">*</span>
                        </label>
                    </div>
                </fieldset>

                <div class="form__actions">
                    <button type="submit" class="btn btn-primary mb-3">
                        <span><?php echo esc_html__( 'Subscribe', 'borspirit' ); ?></span>
                        <svg class="icon icon-paper-plane"><use xlink:href="#icon-paper-plane"></use></svg>
                    </button>
                    <div id="<?php echo esc_attr($prefix); ?>response" role="status" aria-live="polite"></div>
                </div>

            </div>
        </div>
    </div>
</form>

<?php else : ?>

    <?php
        if ( current_user_can('manage_options') ) {
            printf( 
                '<div class="alert alert-danger" role="alert">%s</div>',
                sprintf(
                    __('Mailchimp configuration is missing. Please set <code>%s</code> and <code>%s</code> in the theme options.', 'borspirit'),
                    esc_html('mailchimp_api_key'),
                    esc_html('mailchimp_audience_id')
                )
            );
        } else {
            printf(
                '<div class="alert alert-warning" role="alert">%s</div>',
                esc_html__( 'Subscription form is temporarily unavailable. Please try again later.', 'borspirit' )
            );
        }
    ?>

<?php endif; // end check for Mailchimp credentials ?>
