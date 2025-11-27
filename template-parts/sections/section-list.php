<?php
    $section_classes = build_section_classes($section, 'list');

    $section_title      = $section['list_section_title'] ?? '';
    $section_hide_title = $section['list_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['list_section_lead'] ?? '';
    $list_items         = $section['list_items'] ?: [];
    $list_style         = $section['list_style'] ?? 'unordered';

    // Filter out empty items (description empty)
    $list_items = array_filter($list_items, function ($item) {
        $description = trim($item['list_description'] ?? '');
        return $description !== '';
    });

    // Determine list tag
    $list_tag = $list_style === 'ordered' ? 'ol' : 'ul';
?>

<?php if (!empty($list_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--list<?php echo esc_attr($section_classes); ?>">
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
                <?php if ($list_tag === 'ol') : ?><ol class="section__list section__list--ordered list-unstyled"><?php else : ?><ul class="section__list section__list--unordered list-unstyled"><?php endif; ?>

                <?php foreach ($list_items as $key => $item) : ?>
                    <?php $description = trim($item['list_description'] ?? ''); ?>
                    <?php if ($description) : ?>
                        <li class="section__listitem">
                            <div class="section__listitem-description"><?php echo wp_kses_post($description); ?></div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if ($list_tag === 'ol') : ?></ol><?php else : ?></ul><?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
