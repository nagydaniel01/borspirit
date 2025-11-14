<?php
    $site_name         = get_field('site_name', 'option') ?: get_bloginfo('name');
    $site_logo         = get_field('site_logo', 'option');
    $social            = get_field('social_items', 'option');
    $copyright         = get_field('copyright', 'option');
    $site_payment_logo = get_field('site_payment_logo', 'option');

    $locations         = get_nav_menu_locations();
?>

<footer class="footer<?php echo is_product() ? ' footer--single-product' : ''; ?>">
    <div class="footer__top">
        <?php get_template_part('template-parts/forms/form', 'subscribe_form'); ?>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xl">
                    <div class="footer__block">
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
                            <nav class="footer__nav nav nav--footer nav--social">
                                <ul class="nav__list">
                                    <?php foreach ($social as $key => $row) : ?>
                                        <?php
                                            $social_image  = $row['social_image'] ?? '';
                                            $social_url    = $row['social_link']['url'] ?? '';
                                            $social_title  = $row['social_link']['title'] ?? '';
                                            $social_target = isset($row['social_link']['target']) && $row['social_link']['target'] !== '' ? $row['social_link']['target'] : '_self';
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
                                                    <span><?php echo esc_html($social_name); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                        <?php echo wpautop( do_shortcode( '[site_phone]' ) ); ?>
                        <?php echo wpautop( do_shortcode( '[site_email]' ) ); ?>
                        <?php echo wpautop( do_shortcode( '[woocommerce_settings setting="store_postcode"] [woocommerce_settings setting="store_city"], [woocommerce_settings setting="store_address"]' ) ); ?>
                        <?php echo do_shortcode( '[opening_hours]' ); ?>
                    </div>
                </div>
        
                <div class="col-md-6 col-xl">
                    <div class="footer__block footer__block--nav">
                        <?php 
                        $theme_location = 'footer_menu_1';
                        if ($locations && has_nav_menu($theme_location)) : ?>
                            <?php 
                                $menu_id = $locations[$theme_location];
                                $menu = wp_get_nav_menu_object($menu_id);
                            ?>
                            <?php if ( is_object($menu) && isset($menu->name) ) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location'    => $theme_location,
                                        'container'         => false,
                                        'menu_class'        => 'nav__list level0',
                                        'walker'            => new Custom_Nav_Walker()
                                    ));
                                ?>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6 col-xl">
                    <div class="footer__block footer__block--nav">
                        <?php 
                        $theme_location = 'footer_menu_2';
                        if ($locations && has_nav_menu($theme_location)) : ?>
                            <?php 
                                $menu_id = $locations[$theme_location];
                                $menu = wp_get_nav_menu_object($menu_id);
                            ?>
                            <?php if ( is_object($menu) && isset($menu->name) ) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location'    => $theme_location,
                                        'container'         => false,
                                        'menu_class'        => 'nav__list level0',
                                        'walker'            => new Custom_Nav_Walker()
                                    ));
                                ?>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6 col-xl">
                    <div class="footer__block footer__block--nav">
                        <?php 
                        $theme_location = 'footer_menu_3';
                        if ($locations && has_nav_menu($theme_location)) : ?>
                            <?php 
                                $menu_id = $locations[$theme_location];
                                $menu = wp_get_nav_menu_object($menu_id);
                            ?>
                            <?php if ( is_object($menu) && isset($menu->name) ) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location'    => $theme_location,
                                        'container'         => false,
                                        'menu_class'        => 'nav__list level0',
                                        'walker'            => new Custom_Nav_Walker()
                                    ));
                                ?>
                            </nav>
                        <?php endif; ?>
                        <?php if ($site_payment_logo) : ?>
                            <?php echo wp_get_attachment_image( $site_payment_logo['ID'], [$site_payment_logo['width'], $site_payment_logo['height']], false, ['class' => 'footer__image', 'alt' => esc_attr($site_payment_logo['alt'] ?? '')] ); ?>
                        <?php endif; ?>
                    </div>
                </div>
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
        <?php echo wpautop( wp_kses_post($copyright) ); ?>
    </div>
</footer>