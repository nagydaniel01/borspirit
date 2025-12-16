<?php
    $section_classes = build_section_classes($section, 'wysiwyg_editor');

    $section_title         = $section['wysiwyg_editor_section_title'] ?? '';
    $section_hide_title    = $section['wysiwyg_editor_section_hide_title'] ?? false;
    $section_slug          = sanitize_title($section_title);
    $section_lead          = $section['wysiwyg_editor_section_lead'] ?? '';
    $wysiwyg_editor_layout = $section['wysiwyg_editor_layout'] ?? 'left';
    $wysiwyg_editor_items  = $section['wysiwyg_editor_items'] ?: [];

    // Filter out empty items (WYSIWYG empty)
    $wysiwyg_editor_items = array_filter($wysiwyg_editor_items, function ($item) {
        $wysiwyg_editor = trim($item['wysiwyg_editor'] ?? '');
        return $wysiwyg_editor !== '';
    });

    $extra_classes = '';
    if ($wysiwyg_editor_layout) {
        $extra_classes .= ' section--' . $wysiwyg_editor_layout;
    }
?>

<?php if (!empty($wysiwyg_editor_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--wysiwyg_editor<?php echo esc_attr($section_classes); ?><?php echo esc_attr($extra_classes); ?>">
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
                <?php foreach ($wysiwyg_editor_items as $index => $item) : 
                    $wysiwyg_editor = $item['wysiwyg_editor'] ?? '';
                    $image          = $item['wysiwyg_editor_image'] ?? '';
                    $image_id       = $image['ID'] ?? '';
                    $alt_text       = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
                    $link           = $item['wysiwyg_editor_link'] ?? '';
                    $url            = !empty($link['url']) ? esc_url($link['url']) : '';
                    $title          = !empty($link['title']) ? esc_html($link['title']) : '';
                    $target         = !empty($link['target']) ? esc_attr($link['target']) : '_self';
                    $rel            = ($target === '_blank') ? 'noopener noreferrer' : '';
                ?>

                <div class="row">
                    <div class="<?php echo $image ? 'col-md-6' : 'col'; ?>">
                        <?php echo wp_kses_post($wysiwyg_editor); ?>

                        <?php if (!empty($link)) : ?>
                            <a href="<?php echo $url; ?>" target="<?php echo $target; ?>" class="btn btn-outline-primary" <?php if ($rel) echo 'rel="' . esc_attr($rel) . '"'; ?>>
                                <?php echo $title; ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($image_id)) : ?>
                        <div class="col-md-6">
                            <div class="section__image-wrapper">
                                <?php echo wp_get_attachment_image( $image_id, 'full', false, ['class' => 'section__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy'] ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
