<?php
    $site_name         = get_bloginfo('name') ?: get_field('site_name', 'option') ?: '';
    $custom_logo_id    = get_theme_mod('custom_logo') ?? null;
    $acf_logo          = get_field('site_logo', 'option') ?? null;
    $site_phone        = get_field('site_phone', 'option') ?? '';
    $site_email        = get_field('site_email', 'option') ?? '';
    $social            = get_field('social_items', 'option') ?? [];
    $copyright         = get_field('copyright', 'option') ?? '';
    $site_payment_logo = get_field('site_payment_logo', 'option') ?? null;

    $site_logo = null;

    switch ( true ) {
        case ! empty( $acf_logo ):
            switch ( true ) {
                // ACF returns ID
                case is_numeric( $acf_logo ):
                    $image_data = wp_get_attachment_image_src( (int)$acf_logo, 'full' );
                    $site_logo = [
                        'ID'     => (int)$acf_logo,
                        'url'    => $image_data[0] ?? '',
                        'width'  => $image_data[1] ?? '',
                        'height' => $image_data[2] ?? '',
                        'alt'    => get_post_meta( $acf_logo, '_wp_attachment_image_alt', true ) ?: $site_name,
                    ];
                    break;

                // ACF returns array
                case is_array( $acf_logo ):
                    $site_logo = $acf_logo;
                    break;
            }
            break;

        case ! empty( $custom_logo_id ):
            $image_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            $site_logo = [
                'ID'     => $custom_logo_id,
                'url'    => $image_data[0] ?? '',
                'width'  => $image_data[1] ?? '',
                'height' => $image_data[2] ?? '',
                'alt'    => get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true ) ?: $site_name,
            ];
            break;

        default:
            $site_logo = null;
            break;
    }

    $locations         = get_nav_menu_locations();

    // Phone link
    $phone_link = '';
    if (!empty($site_phone)) {
        $phone_link = preg_replace('/[^0-9\+]/', '', $site_phone);
    }

    // Email obfuscation
    $email = $email_obfuscated = '';
    if (!empty($site_email)) {
        $clean_email      = sanitize_email($site_email);
        $email            = antispambot($clean_email);
        $email_obfuscated = antispambot($clean_email, 1);
    }

    // Footer menus
    $footer_menus = ['footer_menu_1', 'footer_menu_2', 'footer_menu_3', 'footer_menu_4'];
    $active_menus = [];

    // Count active menus
    foreach ($footer_menus as $theme_location) {
        if ($locations && has_nav_menu($theme_location)) {
            $active_menus[] = $theme_location;
        }
    }

    // Include contact block as one column
    $columns_count = count($active_menus) + 1;

    // Determine Bootstrap column class dynamically
    switch ($columns_count) {
        case 1:
            $col_class = 'col-12';
            break;
        case 2:
            $col_class = 'col-md-6 col-xl-6';
            break;
        case 3:
            $col_class = 'col-md-6 col-xl-4';
            break;
        default:
            $col_class = 'col-md-6 col-xl';
            break;
    }
?>

<footer class="footer<?php echo class_exists( 'WooCommerce' ) && is_product() ? ' footer--single-product' : ''; ?>">
    <div class="footer__top">
        <?php get_template_part('template-parts/forms/form', 'subscribe_form'); ?>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <!-- Contact Block -->
                <div class="<?php echo esc_attr($col_class); ?>">
                    <div class="footer__block">
                        <h3 class="footer__title visually-hidden"><?php echo esc_html__('Contact us', 'borspirit'); ?></h3>
                        <?php if ($site_logo) : ?>
                            <div class="logo logo--footer">
                                <a href="<?php echo esc_url( trailingslashit( home_url() ) ); ?>" class="logo__link">
                                    <?php if ( $site_logo ) : ?>
                                        <?php echo wp_get_attachment_image( $site_logo['ID'], [$site_logo['width'], $site_logo['height']], false, ['class' => 'logo__image imgtosvg', 'alt' => esc_attr($site_logo['alt'] ?: $site_name)] ); ?>
                                        <span class="visually-hidden"><?php echo esc_html($site_name); ?></span>
                                    <?php else : ?>
                                        <?php echo esc_html($site_name); ?>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($social) && is_array($social)) : ?>
                            <?php
                                $custom_names = [
                                    'linkedin'     => 'LinkedIn',
                                    'youtube'      => 'YouTube',
                                    'tiktok'       => 'TikTok'
                                ];
                            ?>
                            <nav class="footer__nav nav nav--footer">
                                <ul class="nav__list">
                                    <?php foreach ($social as $key => $row) : ?>
                                        <?php
                                            $social_image  = $row['social_image'] ?? '';
                                            $social_url    = $row['social_link']['url'] ?? '';
                                            $social_title  = $row['social_link']['title'] ?? '';
                                            $social_target = $row['social_link']['target'] ?: '_self';
                                            $host          = parse_url($social_url, PHP_URL_HOST);
                                            $parts         = explode('.', $host);
                                            $base          = ($parts[0] === 'www') ? $parts[1] : $parts[0];
                                            $social_name   = $social_title ?: $custom_names[$base] ?? ucfirst($base);
                                        ?>

                                        <?php if ($social_url) : ?>
                                            <li class="nav__item">
                                                <a href="<?php echo esc_url($social_url); ?>" target="<?php echo esc_attr($social_target); ?>" class="nav__link">
                                                    <?php if (!empty($social_image)) : ?>
                                                        <?php echo wp_get_attachment_image( $social_image['ID'], [24, 24], false, ['class' => 'icon ', 'alt'   => esc_attr($social_name)] ); ?>
                                                    <?php else : ?>
                                                        <svg class="icon icon-<?php echo esc_attr($base); ?>"><use xlink:href="#icon-<?php echo esc_attr($base); ?>"></use></svg>
                                                    <?php endif; ?>
                                                    <?php echo esc_html($social_name); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                        <?php if (!empty($site_phone) && !empty($site_email)) : ?>
                            <div class="footer__nav nav nav--footer">
                                <ul class="nav__list">

                                    <?php if ($site_phone) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo esc_attr( 'tel:' . $phone_link ); ?>" class="nav__link">
                                                <svg class="icon icon-circle-phone"><use xlink:href="#icon-circle-phone"></use></svg>
                                                <?php echo esc_html($site_phone); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($site_email) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo esc_url( 'mailto:' . $email_obfuscated ); ?>" class="nav__link">
                                                <svg class="icon icon-circle-envelope"><use xlink:href="#icon-circle-envelope"></use></svg>
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (shortcode_exists('site_address')) : ?>
                                        <li class="nav__item">
                                            <a href="<?php echo get_location_link(do_shortcode('[site_address]'), 'route', false); ?>" target="_blank" class="nav__link">
                                                <svg class="icon icon-circle-location-arrow"><use xlink:href="#icon-circle-location-arrow"></use></svg>
                                                <span>
                                                    <?php echo do_shortcode('[site_address]'); ?><br>
                                                    <small><?php echo esc_html__('Go to store', 'borspirit'); ?></small>
                                                </span>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (shortcode_exists('opening_hours')) : ?>
                            <div class="footer__opening-hours">
                                <h4><?php echo esc_html__('Opening hours', 'borspirit'); ?></h4>
                                <?php echo do_shortcode( '[opening_hours]' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer Menus -->
                <?php foreach ($active_menus as $theme_location) : ?>
                    <div class="<?php echo esc_attr($col_class); ?>">
                        <div class="footer__block footer__block--nav">
                            <?php 
                            $menu_id = $locations[$theme_location];
                            $menu = wp_get_nav_menu_object($menu_id);
                            if (is_object($menu) && isset($menu->name)) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                $walker = class_exists( 'Custom_Bootstrap_Nav_Walker' ) ? new Custom_Bootstrap_Nav_Walker() : false;

                                if ( $walker ) {
                                    wp_nav_menu([
                                        'theme_location' => $theme_location,
                                        'container'      => false,
                                        'menu_class'     => 'nav__list level0',
                                        'walker'         => new Custom_Bootstrap_Nav_Walker()
                                    ]);
                                } else {
                                    echo '<p class="no-menu-assigned">' . esc_html__( 'Please assign a menu in Appearance → Menus.', 'borspirit' ) . '</p>';
                                }
                                ?>
                            </nav>

                            <?php if ($theme_location === 'footer_menu_3' && $site_payment_logo) : ?>
                                <?php echo wp_get_attachment_image( $site_payment_logo['ID'], [$site_payment_logo['width'], $site_payment_logo['height']], false, ['class' => 'footer__image', 'alt' => esc_attr($site_payment_logo['alt'] ?? '')] ); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="footer__notice">
                <?php
                    echo wpautop( sprintf(
                        get_option( 'ag_modal_content', __( 'We are committed advocates and supporters of responsible, civilized drinking. Therefore, we do not recommend the consumption of alcoholic beverages to persons under the age of %s and cannot serve them.', 'borspirit' ) ),
                        esc_html( get_option( 'ag_min_age', 18 ) )
                    ) );
                ?>
            </div>
        </div>
    </div>
    <div class="copyright">
        <?php echo wpautop( esc_html($copyright) ); ?>
    </div>
</footer>