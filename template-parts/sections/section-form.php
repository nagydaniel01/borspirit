<?php
    $section_classes = build_section_classes($section, 'form');

    $section_title      = $section['form_section_title'] ?? '';
    $section_hide_title = $section['form_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['form_section_lead'] ?? '';
    $form_id            = $section['form'] ?? '';
?>

<?php if (!empty($form_id)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--form<?php echo esc_attr($section_classes); ?>">
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
                <?php
                    $template_args = [];
                    get_template_part('template-parts/forms/form', $form_id, $template_args);
                ?>
            </div>    
        </div>
    </section>
<?php endif; ?>
