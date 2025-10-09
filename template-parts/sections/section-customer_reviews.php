<?php
    $section_classes = build_section_classes($section, 'customer_reviews');

    $section_title      = $section['customer_reviews_section_title'] ?? '';
    $section_hide_title = $section['customer_reviews_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['customer_reviews_section_lead'] ?? '';

    $slider             = $section['customer_reviews_slider'] ?? '';

    // Query all orders that have the _poll_feedback meta key
    $orders = wc_get_orders( array(
        'limit'         => -1,
        'meta_key'      => '_poll_feedback',
        'meta_compare'  => 'EXISTS',
        'return'        => 'objects',
        'status'        => array( 'wc-completed' ),
    ) );
?>

<?php if (!empty($orders)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--customer_reviews <?php echo esc_attr($section_classes); ?>">
        <div class="container">
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section__content">
                <?php if ( $slider != false ) : ?>
                    <div class="slider slider--post-query">
                        <div class="slider__list">
                            <?php foreach ($orders as $order) : ?>
                                <div class="slider__item">
                                    <?php
                                        $feedback_data = $order->get_meta('_poll_feedback');

                                        // Skip if feedback is missing or malformed
                                        if (empty($feedback_data) || !is_array($feedback_data)) {
                                            continue;
                                        }

                                        $template_args = array( 'order' => $order, 'feedback_data' => $feedback_data);
                                        get_template_part('template-parts/cards/card', 'review', $template_args );
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row gy-4">
                        <?php foreach ($orders as $order) : ?>
                            <div class="col-lg-6 col-xl-4">
                                <?php
                                    $feedback_data = $order->get_meta('_poll_feedback');

                                    // Skip if feedback is missing or malformed
                                    if (empty($feedback_data) || !is_array($feedback_data)) {
                                        continue;
                                    }

                                    $template_args = array( 'order' => $order, 'feedback_data' => $feedback_data);
                                    get_template_part('template-parts/cards/card', 'review', $template_args );
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
