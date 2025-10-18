<?php
    $card_image       = $args['card_image'] ?? [];
    $card_title       = $args['card_title'] ?? '';
    $card_description = $args['card_description'] ?? '';
    $card_button      = $args['card_button'] ?? [];

    $image_id        = $card_image['ID'] ?? '';
    $image_mime_type = $card_image['mime_type'] ?? '';
    $button_url      = $card_button['url'] ?? '';
    $button_title    = $card_button['title'] ?? esc_url($button_url);
    $button_target   = isset($card_button['target']) && $card_button['target'] !== '' ? $card_button['target'] : '_self';

    // Add special class if image is an SVG
    $image_class = 'card__image';
    if ($image_mime_type === 'image/svg+xml') {
        $image_class .= ' imgtosvg';
    }
?>

<article class="card">
    <?php if ($image_id) : ?>
        <div class="card__header">
            <div class="card__image-wrapper">
                <?php echo wp_get_attachment_image($image_id, 'medium_large', false, ['class' => $image_class, 'alt' => esc_attr( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ), 'loading' => 'lazy']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card__content">
        <h3 class="card__title">
            <?php echo esc_html($card_title); ?>
        </h3>
        
        <div class="card__lead"><?php echo wp_kses_post($card_description); ?></div>

        <?php if ($button_url) : ?>
            <a href="<?php echo esc_attr($button_url); ?>" target="<?php echo esc_attr($button_target); ?>" class="card__link btn btn-primary"><?php echo esc_html($button_title); ?></a>
        <?php endif; ?>
    </div>
</article>
