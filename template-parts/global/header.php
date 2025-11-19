<?php
    $site_name = get_field('site_name', 'option') ?: get_bloginfo('name');
    $site_logo = get_field('site_logo', 'option');

    $current_user = wp_get_current_user() ?? null;
    $avatar       = get_avatar( $current_user->ID, 32 );
    $first_name   = $current_user->first_name ?? '';
    $last_name    = $current_user->last_name ?? '';
    $display_name = $current_user->display_name ?? '';
    $user_name    = $display_name ? $display_name : $first_name;

     // Check if registration is enabled on My Account page
    $registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
    $modal_toggle_text = $registration_enabled ? esc_html__( 'Login/Register', 'borspirit' ) : esc_html__( 'Login', 'borspirit' );
?>

<header class="header">
    <div class="container">
        <nav class="navbar navbar-expand-xxl header__nav nav nav--main js-nav-main">
            <!-- Brand -->
            <a class="navbar-brand logo logo--header" href="<?php echo esc_url( trailingslashit( home_url() ) ); ?>">
                <?php if ( $site_logo ) : ?>
                    <?php echo wp_get_attachment_image( $site_logo['ID'], [$site_logo['width'], $site_logo['height']], false, ['class' => 'logo__image imgtosvg', 'alt' => esc_attr($site_logo['alt'] ?: $site_name)] ); ?>
                    <span class="visually-hidden"><?php echo esc_html($site_name); ?></span>
                <?php else : ?>
                    <?php echo esc_html($site_name); ?>
                <?php endif; ?>
            </a>

            <!-- Mobile header actions -->
            <div class="header-actions d-flex align-items-center d-xxl-none">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <!-- Mobile My Account / Login -->
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="header-actions__item ms-3">
                            <?php echo $avatar; ?>
                            <span class="visually-hidden"><?php echo sprintf( esc_html__( 'Hello %s!', 'borspirit' ), esc_html( $user_name ) ); ?></span>
                        </a>
                    <?php else : ?>
                        <button type="button" class="header-actions__item btn ms-3" data-bs-toggle="modal" data-bs-target="#login_formModal">
                            <svg class="icon icon-user"><use xlink:href="#icon-user"></use></svg>
                            <span class="visually-hidden"><?php echo esc_html( $modal_toggle_text ); ?></span>
                        </button>
                    <?php endif; ?>

                    <!-- Mobile Cart Trigger -->
                    <button class="header-actions__item btn position-relative ms-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMiniCart" aria-controls="offcanvasMiniCart">
                        <svg class="icon icon-bag-shopping"><use xlink:href="#icon-bag-shopping"></use></svg>
                        <span class="visually-hidden"><?php echo esc_html__( 'Cart', 'borspirit' ); ?></span>
                        <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white">
                            <span class="cart_contents_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </div>
                    </button>
                <?php endif; ?>

                <!-- Navbar Toggler -->
                <button class="header-actions__item btn ms-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu">
                    <svg class="icon icon-bars"><use xlink:href="#icon-bars"></use></svg>
                    <span class="visually-hidden"><?php echo esc_html__( 'Open menu', 'borspirit' ); ?></span>
                </button>
            </div>

            <!-- Offcanvas container (mobile right, desktop inline) -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
                <div class="offcanvas-header d-lg-none">
                    <h5 class="offcanvas-title" id="mainMenuLabel"><?php echo esc_html__( 'Navigation', 'borspirit' ); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="offcanvas-body">
                    <?php
                        if ( class_exists( 'WooCommerce' ) ) {
                            get_product_search_form();
                        } else {
                            get_search_form();
                        }
                    ?>

                    <?php
                        if ( has_nav_menu( 'primary_menu' ) ) {

                            $walker = wp_is_mobile() && class_exists( 'Custom_Mobile_Nav_Walker' )
                                ? new Custom_Mobile_Nav_Walker()
                                : ( class_exists( 'Custom_Mega_Menu_Nav_Walker' ) ? new Custom_Mega_Menu_Nav_Walker() : false );

                            if ( $walker ) {
                                wp_nav_menu( array(
                                    'theme_location' => 'primary_menu',
                                    'container'      => false,
                                    'menu_class'     => 'navbar-nav nav__list level0',
                                    'fallback_cb'    => false,
                                    'depth'          => 4,
                                    'walker'         => $walker,
                                ) );
                            } else {
                                echo '<p class="no-menu-assigned">' . esc_html__( 'Please assign a menu in Appearance → Menus.', 'borspirit' ) . '</p>';
                            }

                        } else {
                            echo '<p class="no-menu-assigned">' . esc_html__( 'Please assign a menu in Appearance → Menus.', 'borspirit' ) . '</p>';
                        }
                    ?>

                    <!-- Desktop header actions -->
                    <div class="header-actions d-none d-lg-flex ms-lg-auto">
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <!-- My Account / Login -->
                            <?php if ( is_user_logged_in() ) : ?>
                                <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="header-actions__item ms-lg-4">
                                    <?php echo $avatar; ?>
                                    <span class="ms-2"><?php echo sprintf( esc_html__( 'Hello %s!', 'borspirit' ), esc_html( $user_name ) ); ?></span>
                                </a>
                            <?php else : ?>
                                <button type="button" class="header-actions__item btn ms-lg-4" data-bs-toggle="modal" data-bs-target="#login_formModal">
                                    <svg class="icon icon-user"><use xlink:href="#icon-user"></use></svg>
                                    <span class="visually-hidden"><?php echo esc_html( $modal_toggle_text ); ?></span>
                                </button>
                            <?php endif; ?>

                            <!-- Cart Trigger -->
                            <button class="header-actions__item btn position-relative ms-lg-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMiniCart" aria-controls="offcanvasMiniCart">
                                <svg class="icon icon-bag-shopping"><use xlink:href="#icon-bag-shopping"></use></svg>
                                <span class="visually-hidden"><?php echo esc_html__( 'Cart', 'borspirit' ); ?></span>
                                <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white">
                                    <span class="cart_contents_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                </div>
                            </button>
                        <?php endif; ?>

                        <!-- Search bar Trigger -->
                        <button class="header-actions__item btn ms-lg-4" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <svg class="icon icon-magnifying-glass"><use xlink:href="#icon-magnifying-glass"></use></svg>
                            <span class="visually-hidden"><?php echo esc_html__( 'Search', 'borspirit' ); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>