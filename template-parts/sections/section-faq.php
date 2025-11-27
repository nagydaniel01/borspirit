<?php
    $section_classes = build_section_classes($section, 'faq');

    $section_title      = $section['faq_section_title'] ?? '';
    $section_hide_title = $section['faq_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['faq_section_lead'] ?? '';
    $accordion_items    = $section['faq_items'] ?: [];
    $accordion_style    = $section['faq_style'] ?? 'chevron';
    $accordion_behavior = $section['faq_behavior'] ?? 'standard';

    // Filter out empty items (title & description both empty)
    $accordion_items = array_filter($accordion_items, function ($item) {
        $title       = trim($item->post_title ?? '');
        $description = trim($item->post_content ?? '');
        return $title !== '' || $description !== '';
    });

    // Add optional class
    $extra_classes = $accordion_style === 'plus_minus' ? ' accordion--alt' : '';
?>

<?php if (!empty($accordion_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--accordion section--faq<?php echo esc_attr($section_classes); ?>">
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
                <div class="accordion<?php echo esc_attr($extra_classes); ?>" id="accordion-<?php echo esc_attr($section_slug); ?>">
                    <?php foreach ($accordion_items as $index => $item) : 
                        $is_first    = ($index === 0);
                        $item_id     = $section_slug . '_' . $index;
                        $title       = $item->post_title ?? '';
                        $description = $item->post_content ?? '';

                        // Defaults
                        $collapse_classes = 'accordion-collapse collapse';
                        $show_class       = '';
                        $aria_expanded    = 'false';
                        $collapse_attrs   = '';

                        switch ($accordion_behavior) {
                            case 'standard':
                                if ($is_first) {
                                    $show_class    = ' show';
                                    $aria_expanded = 'true';
                                }
                                $collapse_attrs = ' data-bs-parent="#accordion-' . esc_attr($section_slug) . '"';
                                break;

                            case 'collapsed':
                                // All start collapsed, one open at a time
                                $collapse_attrs = ' data-bs-parent="#accordion-' . esc_attr($section_slug) . '"';
                                break;

                            case 'always_open':
                                if ($is_first) {
                                    $show_class    = ' show';
                                    $aria_expanded = 'true';
                                }
                                // No parent attribute allows multiple open
                                break;
                        }

                        $button_attrs = sprintf(
                            'data-bs-toggle="collapse" data-bs-target="#collapse-%1$s" aria-expanded="%2$s" aria-controls="collapse-%2$s"',
                            esc_attr($item_id),
                            $aria_expanded
                        );
                    ?>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo esc_attr($item_id); ?>">
                            <button class="accordion-button <?php echo ($aria_expanded === 'false' ? 'collapsed' : ''); ?>" type="button" <?php echo $button_attrs; ?>>
                                <?php echo esc_html($title); ?>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo esc_attr($item_id); ?>" class="<?php echo esc_attr($collapse_classes . $show_class); ?>" aria-labelledby="heading-<?php echo esc_attr($item_id); ?>" <?php echo $collapse_attrs; ?>>
                            <div class="accordion-body">
                                <?php echo wp_kses_post($description); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                </div>
            </div>    
        </div>
    </section>
<?php endif; ?>
