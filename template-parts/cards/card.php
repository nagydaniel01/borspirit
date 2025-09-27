<?php
    $card_image       = $args['card_image'] ?? [];
    $card_icon        = $args['card_icon'] ?? [];
    $card_title       = $args['card_title'] ?? '';
    $card_description = $args['card_description'] ?? '';
    $card_button      = $args['card_button'] ?? [];

    $image_id      = $card_image['ID'] ?? '';
    $icon_id       = $card_icon['ID'] ?? '';
    $button_url    = $card_button['url'] ?? '';
    $button_title  = $card_button['title'] ?? esc_url($button_url);
    $button_target = isset($card_button['target']) && $card_button['target'] !== '' ? $card_button['target'] : '_self';
?>

<article class="card">
    <?php if ($image_id) : ?>
        <div class="card__header">
            <?php echo wp_get_attachment_image($image_id, 'medium', false, ['class' => 'card__image', 'alt' => esc_attr( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ), 'loading' => 'lazy']); ?>
        </div>
    <?php endif; ?>

    <?php if ($icon_id) : ?>
        <div class="card__header">
            <?php echo wp_get_attachment_image($icon_id, 'medium', false, ['class' => 'card__icon icon imgtosvg']); ?>
        </div>
    <?php endif; ?>

    <div class="card__content">
        <h3 class="card__title">
            <?php echo esc_html($card_title); ?>
        </h3>
        
        <div class="card__lead card__lead--no-trim"><?php echo wp_kses_post($card_description); ?></div>

        <?php if ($button_url) : ?>
            <a href="<?php echo esc_attr($button_url); ?>" target="<?php echo esc_attr($button_target); ?>" class="card__link btn btn-primary"><?php echo esc_html($button_title); ?></a>
        <?php endif; ?>
    </div>
</article>
