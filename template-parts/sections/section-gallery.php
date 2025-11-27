<?php
    $section_classes = build_section_classes($section, 'gallery');

    $section_title      = $section['gallery_section_title'] ?? '';
    $section_hide_title = $section['gallery_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['gallery_section_lead'] ?? '';
    $gallery            = $section['gallery'] ?? [];
?>

<?php if (!empty($gallery)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--gallery<?php echo esc_attr($section_classes); ?>">
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
                <div class="slider slider--gallery" id="<?php echo esc_attr($section_slug); ?>-slider">
                    <div class="slider__list">
                        <?php foreach($gallery as $index => $image) : ?>
                            <figure class="slider__item">
                                <a href="<?php echo esc_url($image['url']); ?>" class="slider__link" data-fancybox="<?php echo esc_attr($section_slug); ?>-gallery" <?php if(!empty($image['caption'])): ?>data-caption="<?php echo esc_attr($image['caption']); ?>"<?php endif; ?>>
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="slider__image">
                                </a>
                                <?php //if (!empty($image['caption'])) : ?>
                                    <!--<figcaption class="slider__caption"><?php //echo esc_html($image['caption']); ?></figcaption>-->
                                <?php //endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                    <div class="slider__controls"></div>
                </div>
            </div>    
        </div>
    </section>
<?php endif; ?>
