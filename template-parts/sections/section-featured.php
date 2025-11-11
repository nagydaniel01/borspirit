<?php
    $section_classes = build_section_classes($section, 'featured');

    $section_title      = $section['featured_section_title'] ?? '';
    $section_hide_title = $section['featured_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['featured_section_lead'] ?? '';

    $featured_object         = $section['featured_object'] ?? null;
    $featured_title          = $section['featured_title'] ?? '';
    $featured_description    = $section['featured_description'] ?? '';
    $featured_call_to_action = $section['featured_call_to_action'] ?? '';
    $featured_image          = $section['featured_image'] ?? '';

    // If there is a featured object, use its values as defaults
    if ($featured_object) {
        $default_title         = get_the_title($featured_object);
        $default_url           = get_permalink($featured_object);
        $default_button_target = '_self';
        $default_button_text   = __( 'Learn more', 'borspirit' );
        $default_image         = get_post_thumbnail_id($featured_object);
        $default_description   = apply_filters('the_content', $featured_object->post_excerpt ?? '');
    } else {
        $default_title         = '';
        $default_url           = '';
        $default_button_target = '_self';
        $default_button_text   = __( 'Learn more', 'borspirit' );
        $default_image         = '';
        $default_description   = '';
    }

    // Allow overrides with individual fields
    $final_title         = !empty($featured_title) ? $featured_title : $default_title;
    $final_button_url    = !empty($featured_call_to_action['url'] ?? '') ? $featured_call_to_action['url'] : $default_url;
    $final_button_target = !empty($featured_call_to_action['target'] ?? '') ? $featured_call_to_action['target'] : $default_button_target;
    $final_button_text   = !empty($featured_call_to_action['title'] ?? '') ? $featured_call_to_action['title'] : $default_button_text;
    $final_image         = !empty($featured_image) ? $featured_image : ($default_image ? ['ID' => $default_image] : '');
    $final_description   = !empty($featured_description) ? $featured_description : $default_description;
?>

<?php if ($featured_object || (!empty($final_title) && !empty($featured_description) && !empty($final_image))) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--featured<?php echo esc_attr($section_classes); ?>">
        <div class="container">
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo wp_kses_post($section_title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section__content">
                <?php if (!empty($final_image) && is_array($final_image) && !empty($final_image['ID'])) : ?>
                    <div class="section__image-wrapper">
                        <?php 
                            $image_id = $final_image['ID'];
                            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $final_title;

                            echo wp_get_attachment_image($image_id, 'full', false, [
                                'class' => 'section__image',
                                'alt'   => $alt_text,
                            ]); 
                        ?>
                    </div>
                <?php endif; ?>

                <div class="section__inner">
                    <?php if (!empty($final_title)) : ?>
                        <h2 class="featured__title"><?php echo wp_kses_post($final_title); ?></h2>
                    <?php endif; ?>
    
                    <?php if (!empty($final_description)) : ?>
                        <div class="featured__description">
                            <?php 
                                // Important: if we use the_content filter, we don't wrap with wp_kses_post,
                                // since WordPress core already sanitizes allowed content.
                                echo wp_kses_post($final_description);
                            ?>
                        </div>
                    <?php endif; ?>
    
                    <?php if (!empty($final_button_url)) : ?>
                        <a href="<?php echo esc_url($final_button_url); ?>" target="<?php echo esc_attr($final_button_target); ?>" class="btn btn-outline-primary btn-lg"><?php echo esc_html($final_button_text); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
