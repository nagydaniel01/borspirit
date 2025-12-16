<?php
    /**
     * GDPR Cookie Banner
     *
     * Displays a GDPR-compliant cookie consent banner with Accept/Decline options.
     * Optional scripts (e.g., Google Analytics) will only load after acceptance.
     *
     * @package GDPRCookieBanner
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'gdpr_cookie_banner_html' ) ) {
        /**
         * Outputs the GDPR cookie consent banner HTML.
         *
         * Hooked to wp_footer.
         */
        function gdpr_cookie_banner_html() {
            ?>
            <div id="cookie-banner" class="cookie-banner" role="alert">
                <div class="cookie-content mb-2 mb-md-0">
                    <p>
                        <?php
                        /* translators: Message about cookie usage with a link to the Privacy Policy */
                        printf(
                            /* translators: %s is a link to the Privacy Policy */
                            esc_html__( '🍪 We use cookies to improve your experience. You can accept or decline optional cookies. Read our %s.', 'borspirit' ),
                            '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">' . esc_html__( 'Privacy Policy', 'borspirit' ) . '</a>'
                        );
                        ?>
                    </p>
                </div>
                <div class="cookie-buttons d-flex gap-2">
                    <button id="accept-cookies" class="btn btn-success">
                        <?php echo esc_html__( 'Accept', 'borspirit' ); ?>
                    </button>
                    <button id="decline-cookies" class="btn btn-danger">
                        <?php echo esc_html__( 'Decline', 'borspirit' ); ?>
                    </button>
                </div>
            </div>
            <?php
        }
        add_action( 'wp_footer', 'gdpr_cookie_banner_html' );
    }

    if ( ! function_exists( 'gdpr_cookie_banner_script' ) ) {
        /**
         * Outputs the JavaScript to handle GDPR cookie consent banner interactions.
         *
         * Hooked to wp_footer.
         */
        function gdpr_cookie_banner_script() {
            ?>
            <script>
            (function() {
                'use strict';
                
                try {
                    const banner = document.getElementById('cookie-banner');

                    if (!banner) return;

                    // Show banner if no choice made
                    if (!localStorage.getItem('cookiesAccepted') && !localStorage.getItem('cookiesDeclined')) {
                        setTimeout(() => {
                            banner.classList.add('show');
                        }, 500);
                    }

                    /**
                     * Load optional scripts (e.g., Google Analytics) after consent.
                     */
                    function loadOptionalScripts() {
                        try {
                            const script = document.createElement('script');
                            script.src = "https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID";
                            script.async = true;
                            document.head.appendChild(script);

                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
                            gtag('config', 'GA_MEASUREMENT_ID');
                        } catch (err) {
                            console.error('Error loading optional scripts:', err);
                        }
                    }

                    // Accept cookies
                    const acceptBtn = document.getElementById('accept-cookies');
                    if (acceptBtn) {
                        acceptBtn.addEventListener('click', () => {
                            localStorage.setItem('cookiesAccepted', 'true');
                            banner.classList.remove('show');
                            setTimeout(() => banner.remove(), 500);
                            loadOptionalScripts();
                        });
                    }

                    // Decline cookies
                    const declineBtn = document.getElementById('decline-cookies');
                    if (declineBtn) {
                        declineBtn.addEventListener('click', () => {
                            localStorage.setItem('cookiesDeclined', 'true');
                            banner.classList.remove('show');
                            setTimeout(() => banner.remove(), 500);
                            // Optional scripts are not loaded
                        });
                    }

                } catch (error) {
                    console.error('Cookie banner error:', error);
                }
            })();
            </script>
            <?php
        }
        add_action( 'wp_footer', 'gdpr_cookie_banner_script' );
    }
