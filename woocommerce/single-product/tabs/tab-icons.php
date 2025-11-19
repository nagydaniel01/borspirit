<?php

defined( 'ABSPATH' ) || exit;

global $product;

// --- Get selected icons from ACF checkbox field ---
$selected_icons = get_field( 'product_icons', $product->get_id() ) ?: [];

if ( empty($selected_icons) ) {
    $selected_icons = get_field( 'product_page_icons', 'option' ) ?: [];
}

// --- Existing icon items ---
$icon_items = get_field( 'icon_items', 'option' ) ?? [];
$icon_items = array_filter( $icon_items, function ($item) use ($selected_icons) {
    $image_id = $item['icon_image']['ID'] ?? '';
    $text     = trim( $item['icon_text'] ?? '' );

    // Only keep items if the sanitized text is in selected icons
    return $image_id !== '' && $text !== '' && in_array( sanitize_title($text), $selected_icons );
});

// --- Units sold message ---
$units_sold_message = '';

if ( in_array( 'units_sold_message', $selected_icons, true ) ) {
    $units_sold           = (int) $product->get_total_sales();
    $units_sold_message = sprintf(
        _n(
            '%s person has already tried it – get yours now!',
            '%s people have already tried it – get yours now!',
            $units_sold,
            'borspirit'
        ),
        number_format_i18n( $units_sold )
    );
}

// --- Free shipping message ---
$free_shipping_limit_message = '';

if ( in_array( 'free_shipping_limit_message', $selected_icons, true ) ) {
    // Only run on frontend
    if ( ! is_admin() && ! wp_doing_ajax() && function_exists( 'WC' ) ) {

        $customer_country = '';
        $wc = WC();

        // Ensure customer session is ready
        if ( isset( $wc->customer ) && is_object( $wc->customer ) ) {
            $customer_country = $wc->customer->get_shipping_country();
        }

        // Fallback to geolocation if country not set
        if ( empty( $customer_country ) && class_exists( 'WC_Geolocation' ) ) {
            $geo = WC_Geolocation::geolocate_ip();
            $customer_country = $geo['country'] ?? '';
        }

        // If we have a valid country, continue
        if ( ! empty( $customer_country ) && class_exists( 'WC_Shipping_Zones' ) ) {

            $package = [
                'destination' => [
                    'country'  => $customer_country,
                    'state'    => '',
                    'postcode' => '',
                    'city'     => '',
                    'address'  => '',
                ],
            ];

            $zone    = WC_Shipping_Zones::get_zone_matching_package( $package );
            $methods = $zone->get_shipping_methods();

            foreach ( $methods as $method ) {
                if ( ! is_object( $method ) ) {
                    continue;
                }

                if ( $method->id === 'free_shipping' && $method->enabled === 'yes' ) {
                    // Use get_option() for compatibility with newer WooCommerce versions
                    $min_amount = $method->get_option( 'min_amount' );

                    if ( is_numeric( $min_amount ) && $min_amount > 0 ) {
                        $free_shipping_limit_message = sprintf(
                            __( 'Free shipping on orders over %1$s', 'borspirit' ),
                            wc_price( $min_amount )
                        );
                        break; // Stop after finding the first valid method
                    }
                }
            }
        }
    }
}

// --- Estimated Delivery Message ---
$estimated_delivery_message = '';
if ( in_array('estimated_delivery_message', $selected_icons) ) {

    /*
    $opening_hours = [
        'monday'    => ['open' => 9, 'close' => 18],
        'tuesday'   => ['open' => 9, 'close' => 18],
        'wednesday' => ['open' => 9, 'close' => 18],
        'thursday'  => ['open' => 9, 'close' => 18],
        'friday'    => ['open' => 9, 'close' => 18],
        'saturday'  => ['open' => 9, 'close' => 14],
        'sunday'    => ['open' => 0, 'close' => 0],
    ];
    */

    $opening_hours = get_field('opening_hours', 'option');
    $opening_hours = get_opening_hours($opening_hours, $acf_mode = true);

    /*
    // Debug
    echo '<pre>';
    var_dump($opening_hours);
    echo '</pre>';
    */

    $locale = get_locale();
    $wordpress_timezone = get_option('timezone_string') ?: 'UTC';
    date_default_timezone_set($wordpress_timezone);

    $current_time = current_time('H');
    $current_day  = strtolower(current_time('l'));

    $base_delivery_days = 1; // default to next day

    // Check product stock
    global $product;
    if ($product->managing_stock()) {
        $stock_quantity = $product->get_stock_quantity();

        if ($stock_quantity === 0) {
            $base_delivery_days = 2; // out of stock
        }
    }

    // Adjust for closing hours / weekend
    if ($opening_hours[$current_day]['open'] > 0) {
        if ($current_time >= $opening_hours[$current_day]['close']) {
            $base_delivery_days++;
        }
    } else {
        $base_delivery_days++;
    }

    //$estimated_timestamp = strtotime("+$base_delivery_days days");
    $estimated_timestamp = strtotime("+$base_delivery_days weekdays");
    $formatter = new IntlDateFormatter(
        $locale,
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        $wordpress_timezone,
        IntlDateFormatter::GREGORIAN,
        'yyyy. MMMM d., EEEE'
    );
    $estimated_date = $formatter->format($estimated_timestamp);

    $estimated_delivery_message = sprintf(
        __( 'Free in-store pickup as early as %1$s', 'borspirit' ),
        $estimated_date
    );
}
?>

<div class="section__content">
    <?php if ( ! empty( $icon_items ) || ! empty( $units_sold_message ) || ! empty( $free_shipping_limit_message ) || ! empty( $estimated_delivery_message ) ) : ?>
        <div class="section__list">

            <?php if ( ! empty( $units_sold_message ) && $units_sold > 0 ) : // Only display if at least one unit has been sold ?>
                <div class="section__listitem">
                    <svg class="section__icon icon icon-wine-bottle"><use xlink:href="#icon-wine-bottle"></use></svg>
                    <span class="section__text"><?php echo esc_html( $units_sold_message ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $free_shipping_limit_message ) ) : ?>
                <div class="section__listitem">
                    <svg class="section__icon icon icon-truck"><use xlink:href="#icon-truck"></use></svg>
                    <span class="section__text"><?php echo wp_kses_post( $free_shipping_limit_message ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $estimated_delivery_message ) && $product->is_in_stock() ) : ?>
                <div class="section__listitem">
                    <svg class="section__icon icon icon-shop"><use xlink:href="#icon-shop"></use></svg>
                    <span class="section__text"><?php echo esc_html( $estimated_delivery_message ); ?></span>
                </div>
            <?php endif; ?>

            <?php foreach ( $icon_items as $item ) : 
                $image_id = $item['icon_image']['ID'] ?? '';
                $text     = trim( $item['icon_text'] ?? '' );
            ?>
                <div class="section__listitem">
                    <?php if ( $image_id ) : ?>
                        <?php echo wp_get_attachment_image( $image_id, 'thumbnail', false, ['class' => 'section__icon icon imgtosvg'] ); ?>
                    <?php endif; ?>
                    <?php if ( $text ) : ?>
                        <span class="section__text"><?php echo wp_kses_post( $text ); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
    <?php else : ?>
        <?php echo wpautop( __( 'No icon items found.', 'borspirit' ) ); ?>
    <?php endif; ?>
</div>
