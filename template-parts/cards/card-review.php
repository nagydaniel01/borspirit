<?php
    $order         = $args['order'];
    $feedback_data = $args['feedback_data'];

    if (empty($order) || empty($feedback_data) || !is_array($feedback_data)) {
        return;
    }

    $like     = $feedback_data['like'] ?? '';
    $rating   = intval($feedback_data['rating'] ?? 0);
    $feedback = $feedback_data['feedback'] ?? '';
    $date     = isset($feedback_data['date']) ? date_i18n(get_option('date_format'), strtotime($feedback_data['date'])) : '';

    $customer_first_name = $order->get_billing_first_name();

    // Build WooCommerce-style stars
    $stars = $rating > 0 ? wc_get_rating_html($rating, 5) : '';
?>

<div class="card card--review">
    <div class="card__content">
        <h3 class="card__title"><?php echo esc_html($customer_first_name ?: __('Anonymous', 'borspirit')); ?></h3>

        <?php if ($stars) : ?>
            <div class="card__rating"><?php echo $stars; ?></div>
        <?php endif; ?>

        <?php if (!empty($like) || !empty($feedback)) : ?>
            <div class="card__lead">
                <?php if ($like) : ?>
                    <?php echo wpautop( wp_kses_post($like) ); ?>
                <?php endif; ?>
        
                <?php if ($feedback) : ?>
                    <?php echo wpautop( wp_kses_post($feedback) ); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!--
        <div class="card__meta">
            <?php //if ($date) : ?>
                <time datetime="<?php //echo esc_attr(date('c', strtotime($feedback_data['date']))); ?>" class="card__date">
                    <?php //echo esc_html($date); ?>
                </time>
            <?php //endif; ?>
        </div>
        -->
    </div>
</div>