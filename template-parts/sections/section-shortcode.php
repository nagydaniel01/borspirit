<?php
    $section_classes = build_section_classes($section, 'shortcode');

    $section_title      = $section['shortcode_section_title'] ?? '';
    $section_hide_title = $section['shortcode_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['shortcode_section_lead'] ?? '';
    $shortcode          = $section['shortcode'] ?? '';

    $shortcode_tag = '';
    if (preg_match('/\[([a-zA-Z0-9_-]+)/', $shortcode, $matches)) {
        $shortcode_tag = $matches[1];
    }
?>

<?php if (!empty($shortcode) && shortcode_exists($shortcode_tag)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--shortcode<?php echo esc_attr($section_classes); ?>">
        <div class="container">
            <div class="section__inner">
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
                    <?php echo do_shortcode($shortcode); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
