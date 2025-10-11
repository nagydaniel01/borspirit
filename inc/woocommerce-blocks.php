<?php
    /**
     * Create Hooks For WooCommerce Cart Block
     *
     * This snippet adds before/after action hooks around WooCommerce Cart Blocks,
     * allowing developers to easily insert custom content without directly overriding
     * templates.
     *
     * @snippet       Create Hooks For WooCommerce Cart Block
     * @how-to        https://www.businessbloomer.com/woocommerce-customization
     * @author        Rodolfo Melogli, Business Bloomer
     * @compatible    WooCommerce 9
     * @community     https://businessbloomer.com/club/
     * 
     * How to use:    https://www.businessbloomer.com/woocommerce-visual-hook-guide-cart-block/
     */

    if ( ! function_exists( 'bbloomer_woocommerce_cart_block_do_actions' ) ) {

        /**
         * Filter: Wrap WooCommerce Cart Blocks with custom before/after hooks.
         *
         * @param string $block_content The content of the current block being rendered.
         * @param array  $block         The full block data, including 'blockName' and attributes.
         *
         * @return string Modified block content with before/after action hooks added if applicable.
         */
        function bbloomer_woocommerce_cart_block_do_actions( $block_content, $block ) {
            // List of WooCommerce Cart-related block names to target.
            $blocks = array(
                'woocommerce/cart',
                'woocommerce/filled-cart-block',
                'woocommerce/cart-items-block',
                'woocommerce/cart-line-items-block',
                'woocommerce/cart-cross-sells-block',
                'woocommerce/cart-cross-sells-products-block',
                'woocommerce/cart-totals-block',
                'woocommerce/cart-order-summary-block',
                'woocommerce/cart-order-summary-heading-block',
                'woocommerce/cart-order-summary-coupon-form-block',
                'woocommerce/cart-order-summary-subtotal-block',
                'woocommerce/cart-order-summary-fee-block',
                'woocommerce/cart-order-summary-discount-block',
                'woocommerce/cart-order-summary-shipping-block',
                'woocommerce/cart-order-summary-taxes-block',
                'woocommerce/cart-express-payment-block',
                'woocommerce/proceed-to-checkout-block',
                'woocommerce/cart-accepted-payment-methods-block',
            );

            // If the current block is in the list, wrap it with before/after hooks.
            if ( in_array( $block['blockName'], $blocks, true ) ) {
                ob_start();

                /**
                 * Action: Fires before the specific WooCommerce Cart block is rendered.
                 *
                 * Hook name format: bbloomer_before_{blockName}
                 */
                do_action( 'bbloomer_before_' . $block['blockName'] );

                // Output the original block content.
                echo $block_content;

                /**
                 * Action: Fires after the specific WooCommerce Cart block is rendered.
                 *
                 * Hook name format: bbloomer_after_{blockName}
                 */
                do_action( 'bbloomer_after_' . $block['blockName'] );

                // Capture and replace block content.
                $block_content = ob_get_clean();
            }

            return $block_content;
        }
        add_filter( 'render_block', 'bbloomer_woocommerce_cart_block_do_actions', 9999, 2 );
    }

    /**
     * Create Hooks For WooCommerce Checkout Block
     *
     * This snippet adds before/after action hooks around WooCommerce Checkout Blocks,
     * allowing developers to easily insert custom content without directly overriding
     * templates.
     *
     * @snippet       Create Hooks For WooCommerce Checkout Block
     * @how-to        https://www.businessbloomer.com/woocommerce-customization
     * @author        Rodolfo Melogli, Business Bloomer
     * @compatible    WooCommerce 9
     * @community     https://businessbloomer.com/club/
     * 
     */

    if ( ! function_exists( 'bbloomer_woocommerce_checkout_block_do_actions' ) ) {

        /**
         * Filter: Wrap WooCommerce Checkout Blocks with custom before/after hooks.
         *
         * @param string $block_content The content of the current block being rendered.
         * @param array  $block         The full block data, including 'blockName' and attributes.
         *
         * @return string Modified block content with before/after action hooks added if applicable.
         */
        function bbloomer_woocommerce_checkout_block_do_actions( $block_content, $block ) {
            // List of WooCommerce Checkout-related block names to target.
            $blocks = array(
                'woocommerce/checkout',
                'woocommerce/checkout-fields-block',
                'woocommerce/checkout-order-summary-block',
                'woocommerce/checkout-shipping-methods-block',
                'woocommerce/checkout-billing-address-block',
                'woocommerce/checkout-shipping-address-block',
                'woocommerce/checkout-contact-information-block',
                'woocommerce/checkout-shipping-options-block',
                'woocommerce/checkout-payment-block',
                'woocommerce/checkout-express-payment-block',
                'woocommerce/checkout-terms-block',
                'woocommerce/checkout-actions-block',
                'woocommerce/checkout-order-note-block',
                'woocommerce/checkout-pickup-options-block',
                'woocommerce/checkout-totals-block',
            );

            // If the current block is in the list, wrap it with before/after hooks.
            if ( in_array( $block['blockName'], $blocks, true ) ) {
                ob_start();

                /**
                 * Action: Fires before the specific WooCommerce Checkout block is rendered.
                 *
                 * Hook name format: bbloomer_before_{blockName}
                 */
                do_action( 'bbloomer_before_' . $block['blockName'] );

                // Output the original block content.
                echo $block_content;

                /**
                 * Action: Fires after the specific WooCommerce Checkout block is rendered.
                 *
                 * Hook name format: bbloomer_after_{blockName}
                 */
                do_action( 'bbloomer_after_' . $block['blockName'] );

                // Capture and replace block content.
                $block_content = ob_get_clean();
            }

            return $block_content;
        }
        add_filter( 'render_block', 'bbloomer_woocommerce_checkout_block_do_actions', 9999, 2 );
    }
