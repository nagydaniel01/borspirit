<?php
    $section_classes = build_section_classes($section, 'contact');

    $section_title      = $section['contact_section_title'] ?? '';
    $section_hide_title = $section['contact_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['contact_section_lead'] ?? '';
    $contact            = $section['contact'] ?? '';
    $form_id            = $section['contact_form'] ?? '';
?>

<?php if (!empty($contact)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--contact <?php echo esc_attr($section_classes); ?>">
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
                
                <div class="section__content row">
                    <div class="col-lg-6">
                        <?php echo wp_kses_post($contact); ?>
                    </div>
                    <div class="col-lg-6">
                        <?php
                            $form_args = array();
                            get_template_part('template-parts/forms/form', $form_id, $form_args);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
