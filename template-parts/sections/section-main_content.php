<?php
    $post_title = get_the_title();
    $post_slug  = sanitize_title($post_title);

    $section_classes = build_section_classes($section, 'main_content');

    $section_title       = $section['main_content_section_title'] ?: $post_title;
    $section_hide_title  = $section['main_content_section_hide_title'] ?? false;
    $section_slug        = sanitize_title($section_title);
    $section_lead        = $section['main_content_section_lead'] ?? '';
?>

<section id="<?php echo esc_attr($section_slug); ?>" class="section section--main_content<?php echo esc_attr($section_classes); ?>">
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

        <?php if (get_the_content()) : ?>
            <div class="section__content">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
