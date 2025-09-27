<?php

defined( 'ABSPATH' ) || exit;

global $product;

?>

<?php
// Get icon items from global "product_page_icon_items" option
$icon_items = get_field( 'product_page_icon_items', 'option' ) ?? [];

// Filter out icon items where both image and text are empty
$icon_items = array_filter( $icon_items ?? [], function ($item) {
    $image_id = $item['product_page_icon_image']['ID'] ?? '';
    $text     = trim( $item['product_page_icon_text'] ?? '' );
    
    return $image_id !== '' && $text !== '';
} );

// Determine current customer country
$customer_country = WC()->customer->get_shipping_country();

// If new visitor or no shipping country set, fallback to geolocation
if ( empty( $customer_country ) ) {
    $geo = WC_Geolocation::geolocate_ip();
    $customer_country = $geo['country'] ?? '';
}

// Get free shipping message for current zone
$free_shipping_message = '';

if ( $customer_country ) {
    $package = [
        'destination' => [
            'country'  => $customer_country,
            'state'    => '',
            'postcode' => '',
            'city'     => '',
            'address'  => '',
        ],
    ];

    $customer_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
    $methods = $customer_zone->get_shipping_methods();

    foreach ( $methods as $method ) {
        if ( ! is_object( $method ) ) continue;

        if ( $method->id === 'free_shipping' && isset( $method->enabled ) && $method->enabled === 'yes' ) {
            if ( isset( $method->min_amount ) && is_numeric( $method->min_amount ) && $method->min_amount > 0 ) {
                $formatted_amount = wc_price( $method->min_amount );

                $free_shipping_message = sprintf(
                    /* translators: %s: formatted minimum order amount for free shipping */
                    __('Ingyenes szállítás %s felett.', 'your-text-domain'),
                    $formatted_amount
                );
                break;
            }
        }
    }
}
?>

<div class="section__content">
    <?php if ( ! empty( $icon_items ) || ! empty( $free_shipping_message ) ) : ?>
        <div class="section__list">

            <?php if ( ! empty( $free_shipping_message ) ) : ?>
                <div class="section__listitem">
                    <svg class="section__icon icon icon-truck"><use xlink:href="#icon-truck"></use></svg>
                    <span class="section__text">
                        <?php echo wp_kses_post( $free_shipping_message ); ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php foreach ( $icon_items as $item ) : 
                $image_id = $item['product_page_icon_image']['ID'] ?? '';
                $text     = trim( $item['product_page_icon_text'] ?? '' );
            ?>
                <div class="section__listitem">
                    <?php if ( $image_id  ) : ?>
                        <?php echo wp_get_attachment_image( $image_id, 'thumbnail', false, ['class' => 'section__icon icon imgtosvg'] ); ?>
                    <?php endif; ?>
                    <?php if ( $text ) : ?>
                        <span class="section__text">
                            <?php echo wp_kses_post( $text ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php echo wpautop( __( 'No icon items found.', TEXT_DOMAIN ) ); ?>
    <?php endif; ?>
</div>
