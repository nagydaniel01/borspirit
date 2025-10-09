<?php
    $section_classes = build_section_classes($section, 'card');

    $section_title      = $section['card_section_title'] ?? '';
    $section_hide_title = $section['card_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['card_section_lead'] ?? '';

    $slider             = $section['card_slider'] ?? '';

    $card_items         = $section['card_items'] ?? [];
    $card_style         = $section['card_style'] ?? 'unordered';

    // Filter out empty items (title & description both empty)
    $card_items = array_filter($card_items, function ($item) {
        $title       = trim($item['card_title'] ?? '');
        $description = trim($item['card_description'] ?? '');
        return $title !== '' || $description !== '';
    });

    $template = locate_template("template-parts/cards/card.php");
?>

<?php if (!empty($card_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--card <?php echo esc_attr($section_classes); ?><?php echo ($slider != false) ? ' section--slider' : ''; ?>">
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
                <?php if ( $slider != false ) : ?>
                    <div class="slider slider--card">
                        <div class="slider__list">
                            <?php foreach ($card_items as $key => $item) : ?>
                                <div class="slider__item">
                                    <?php
                                        if (!empty($template)) {
                                            $template_args = array(
                                                'card_image'       => $item['card_image'],
                                                'card_icon'        => $item['card_icon'],
                                                'card_title'       => $item['card_title'],
                                                'card_description' => $item['card_description'],
                                                'card_button'      => $item['card_button'],
                                            );

                                            // File does not exist, handle accordingly
                                            get_template_part('template-parts/cards/card', '', $template_args);
                                        }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <?php foreach ($card_items as $key => $item) : ?>
                        <div class="col-lg-6 col-xl-4">
                            <?php
                                if (!empty($template)) {
                                    $template_args = array(
                                        'card_image'       => $item['card_image'],
                                        'card_icon'        => $item['card_icon'],
                                        'card_title'       => $item['card_title'],
                                        'card_description' => $item['card_description'],
                                        'card_button'      => $item['card_button'],
                                    );

                                    // File does not exist, handle accordingly
                                    get_template_part('template-parts/cards/card', '', $template_args);
                                }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
