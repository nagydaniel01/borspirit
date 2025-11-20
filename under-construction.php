<?php
    $under_construction_mode_body = get_field('under_construction_mode_body', 'option');
    $social                       = get_field('social_items', 'option');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html__( 'Coming soon', 'borspirit' ); ?></title>
        <link rel="icon" href="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/borspirit-logo-white.svg' ); ?>" type="image/svg+xml">
        <link rel='stylesheet' id='borspirit-theme-css' href='https://borspirit.test/wp-content/themes/borspirit/assets/dist/css/styles.css?ver=<?php echo esc_attr( ASSETS_VERSION ); ?>' media='all' />
        <style>
            body {
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #212121;
                background-image: url('<?php echo TEMPLATE_DIR_URI . "/assets/src/images/login-background.jpg"; ?>');
                background-size: cover;
                background-position: center center;
                height: 100vh;
                font-family: "Roboto", sans-serif;
                text-align: center;
                color: #000;
            }
            body::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: -1;
                background: rgba(33,33,33,.65);
            }
            .container,
            p, th, td {
                color: #FFFFFF !important;
            }
            h1 {
                font-size: 3rem;
                margin-top: 0;
                margin-bottom: 1rem;
            }
            table {
                margin: 0 auto;
            }
            th, td {
                padding: 0 !important;
            }
            .logo {
                margin-bottom: 1.5rem;
            }
            .logo img {
                width: 240px;
                height: auto;
                padding: 0;
                margin: 0 auto;
            }
            .nav {
                display: flex;
                justify-content: center;
            }
            .nav__list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 3rem;
            }
        </style>
    </head>
    <body>
        <div class="symbols d-none">
            <?php get_template_part('assets/dist/php/sprites', ''); ?>
        </div>
        <main class="page page--default page--under-construction">
            <section class="section section--default">
                <div class="container">
                    <div class="logo">
                        <img src="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/borspirit-logo-white.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    </div>
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
                                                <?php echo esc_html($social_name); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    <?php echo wp_kses_post($under_construction_mode_body); ?>
                </div>
            </section>
        </main>
    </body>
</html>