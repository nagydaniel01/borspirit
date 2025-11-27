<?php
    $section_classes = build_section_classes($section, 'tab');

    $section_title      = $section['tab_section_title'] ?? '';
    $section_hide_title = $section['tab_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['tab_section_lead'] ?? '';
    $tab_items          = $section['tab_items'] ?: [];
    $tab_style          = $section['tab_style'] ?? 'tabs';
    $tab_layout         = $section['tab_layout'] ?? 'horizontal';

    // Filter out empty rows
    $tab_items = array_filter($tab_items, function ($item) {
        $title       = trim($item['tab_title'] ?? '');
        $description = trim($item['tab_description'] ?? '');
        return $title !== '' || $description !== '';
    });

    // Determine nav class
    $nav_class    = $tab_style === 'pills' ? 'nav-pills' : 'nav-tabs';
    $is_vertical  = $tab_layout === 'vertical';

    $extra_classes = '';
    if ($tab_style) {
        $extra_classes .= ' section--' . $tab_style;
    }

    if ($tab_layout) {
        $extra_classes .= ' section--' . $tab_layout;
    }
?>

<?php if (!empty($tab_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--tab<?php echo esc_attr($section_classes); ?><?php echo esc_attr($extra_classes); ?>">
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
            
            <div class="section__content<?php echo $is_vertical ? ' d-flex' : ''; ?>">
                <ul class="nav <?php echo esc_attr($nav_class); ?><?php echo $is_vertical ? ' flex-column me-3' : ' mb-3'; ?>" 
                    id="<?php echo esc_attr($section_slug); ?>-tabs" role="tablist">
                    <?php foreach ($tab_items as $index => $item) :
                        $is_first = ($index === 0);
                        $item_id  = $section_slug . '_' . $index;
                        $title    = $item['tab_title'] ?? '';
                    ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link<?php echo $is_first ? ' active' : ''; ?>" id="<?php echo esc_attr($item_id); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo esc_attr($item_id); ?>" type="button" role="tab" aria-controls="<?php echo esc_attr($item_id); ?>" aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>">
                                <?php echo esc_html($title); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content<?php echo $is_vertical ? ' flex-fill' : ''; ?>" id="<?php echo esc_attr($section_slug); ?>-tabcontent">
                    <?php foreach ($tab_items as $index => $item) :
                        $is_first    = ($index === 0);
                        $item_id     = $section_slug . '_' . $index;
                        $title       = $item['tab_title'] ?? '';
                        $description = $item['tab_description'] ?? '';
                    ?>

                    <div class="tab-pane fade<?php echo $is_first ? ' show active' : ''; ?>" id="<?php echo esc_attr($item_id); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr($item_id); ?>-tab">
                        <h2><?php echo esc_html($title); ?></h2>
                        <?php echo wp_kses_post($description); ?>
                    </div>
                    
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
