<?php
    $section_classes = build_section_classes($section, 'gravity_forms');

    $section_title      = $section['gravity_forms_section_title'] ?? '';
    $section_hide_title = $section['gravity_forms_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['gravity_forms_section_lead'] ?? '';
    $form_id            = $section['gform'] ?? '';
?>

<?php if (!empty($form_id)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--gravity_forms <?php echo esc_attr($section_classes); ?>">
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
                // Ensure Gravity Forms is loaded
                if (!class_exists('GFAPI')) {
                    echo esc_html__('A Gravity Forms nincs telepítve vagy nincs aktiválva.', 'borspirit');
                    return;
                }

                $form = GFAPI::get_form((int) $form_id);

                if (!$form) {
                    echo esc_html__('Űrlap nem található.', 'borspirit');
                    return;
                }

                // Retrieve form display settings
                $title_enabled       = 'false';
                $description_enabled = !empty($form['description']) ? 'true' : 'false';
                $is_ajax             = 'true';
                $tabindex            = '4';

                // Render the form
                echo do_shortcode("[gravityform id=\"$form_id\" title=\"$title_enabled\" description=\"$description_enabled\" ajax=\"$is_ajax\" tabindex=\"$tabindex\" theme=\"gravity\"]");
                ?>
            </div>    
        </div>
    </section>
<?php endif; ?>
