<?php
    $section_classes = build_section_classes($section, 'media');

    $section_title      = $section['media_section_title'] ?? '';
    $section_hide_title = $section['media_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['media_section_lead'] ?? '';
    $media              = $section['media'] ?? '';
    $poster_url         = $section['media_poster']['url'] ?? '';

    // ACF media fields
    $url       = $media['url'] ?? '';
    $mime_type = $media['mime_type'] ?? '';
    $width     = $media['width'] ?? '';
    $height    = $media['height'] ?? '';
    $is_video  = strpos($mime_type, 'video/') === 0;
    $is_audio  = strpos($mime_type, 'audio/') === 0;
?>

<?php if (!empty($media)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--media<?php echo esc_attr($section_classes); ?>">
        <div class="container">
            <div class="section__inner">
                <?php if (($section_title && !$section_hide_title) || $section_lead) : ?>
                    <div class="section__header">
                        <?php if (!$section_hide_title) : ?>
                            <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                        <?php endif; ?>
                        <?php if (!empty($section_lead)) : ?>
                            <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="section__content">
                    <?php if ($is_video) : ?>
                        <div class="section__video-wrapper ratio ratio-16x9">
                            <video width="<?php echo esc_attr($width); ?>" height="<?php echo esc_attr($height); ?>" controls <?php echo $poster_url ? 'poster="' . esc_url($poster_url) . '"' : ''; ?> class="section__video">
                                <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                <?php echo esc_html__('Your browser does not support the video tag.', 'borspirit'); ?>
                            </video>
                        </div>
                    <?php elseif ($is_audio) : ?>
                        <audio controls class="section__audio">
                            <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                            <?php echo esc_html__('Your browser does not support the audio tag.', 'borspirit'); ?>
                        </audio>
                    <?php else : ?>
                        <?php echo wpautop( esc_html__('Unsupported media type.', 'borspirit') ); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
