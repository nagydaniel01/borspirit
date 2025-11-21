<?php
    // Ensure WordPress is loaded
    if ( ! defined( 'ABSPATH' ) ) {
        // Go 4 directories up to reach WordPress root
        require_once dirname(__DIR__, 4) . '/wp-load.php';
    }

    // Make sure WooCommerce functions are available
    if ( ! class_exists( 'WooCommerce' ) ) {
        exit;
    }

    if ( ! function_exists( 'wc_send_table_based_daily_order_summary_email' ) ) {
        /**
         * Sends a daily WooCommerce order summary email in a table format.
         */
        function wc_send_table_based_daily_order_summary_email() {
            if ( ! function_exists('wc_get_orders') || ! class_exists('WC_Email') ) {
                return; // WooCommerce not loaded
            }

            $today  = date_i18n( 'Y-m-d' );
            $orders = wc_get_orders([
                'date_created' => $today,
                'limit'        => -1,
                'return'       => 'objects',
            ]);

            if ( empty( $orders ) ) {
                $message = '<p>' . sprintf( esc_html__( 'No orders were placed today (%s).', 'borspirit' ), esc_html( $today ) ) . '</p>';
            } else {
                $total_orders       = count( $orders );
                $total_revenue_excl = 0;
                $total_revenue_incl = 0;
                $product_summary    = [];

                ob_start(); ?>

                <table width="100%" cellspacing="0" cellpadding="0" style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                    <tr>
                        <td style="padding:15px 0;">
                            <strong><?php echo esc_html__( 'Date:', 'borspirit' ); ?></strong> <?php echo esc_html( $today ); ?><br/>
                            <strong><?php echo esc_html__( 'Total Orders:', 'borspirit' ); ?></strong> <?php echo esc_html( $total_orders ); ?>
                        </td>
                    </tr>

                    <?php foreach ( $orders as $order ) :
                        $order_id        = $order->get_id();
                        $order_subtotal  = $order->get_subtotal();
                        $order_total     = $order->get_total();
                        $order_tax       = $order->get_total_tax();
                        $order_shipping  = $order->get_shipping_total();
                        $currency        = $order->get_currency();

                        $total_revenue_excl += $order_subtotal;
                        $total_revenue_incl += $order_total;
                    ?>

                    <tr>
                        <td style="padding:15px 0;border-top:2px solid #ddd;">
                            <p style="font-size:16px;font-weight:bold;margin:0 0 5px;">
                                <?php printf( esc_html__( 'Order #%d', 'borspirit' ), esc_html( $order_id ) ); ?>
                            </p>
                            <p style="margin:0;">
                                <strong><?php echo esc_html__( 'Customer:', 'borspirit' ); ?></strong> <?php echo esc_html( $order->get_formatted_billing_full_name() ); ?><br>
                                <strong><?php echo esc_html__( 'Status:', 'borspirit' ); ?></strong> <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?><br>
                                <strong><?php echo esc_html__( 'Placed:', 'borspirit' ); ?></strong> <?php echo esc_html( $order->get_date_created()->date_i18n( 'Y-m-d H:i' ) ); ?>
                            </p>

                            <!-- Order Items Table -->
                            <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse;margin-top:8px;font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                <tr style="background:#f7f7f7;">
                                    <td style="border:1px solid #ddd;font-weight:bold;"><?php echo esc_html__( 'Product', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:center;font-weight:bold;"><?php echo esc_html__( 'Qty', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:right;font-weight:bold;"><?php echo esc_html__( 'Excl. Tax', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:right;font-weight:bold;"><?php echo esc_html__( 'Incl. Tax', 'borspirit' ); ?></td>
                                </tr>

                                <?php foreach ( $order->get_items() as $item ) :
                                    $product_name = $item->get_name();
                                    $qty          = $item->get_quantity();
                                    $line_excl    = $item->get_subtotal();
                                    $line_incl    = $item->get_total() + $item->get_total_tax();

                                    if ( ! isset( $product_summary[ $product_name ] ) ) {
                                        $product_summary[ $product_name ] = [ 'qty' => 0, 'excl' => 0, 'incl' => 0 ];
                                    }
                                    $product_summary[ $product_name ]['qty']  += $qty;
                                    $product_summary[ $product_name ]['excl'] += $line_excl;
                                    $product_summary[ $product_name ]['incl'] += $line_incl;
                                    ?>

                                    <tr>
                                        <td style="border:1px solid #ddd;"><?php echo esc_html( $product_name ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:center;"><?php echo esc_html( $qty ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $line_excl, ['currency' => $currency] ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $line_incl, ['currency' => $currency] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>

                            <!-- Order Totals Table -->
                            <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse;margin-top:8px;font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Subtotal (Excl. Tax):', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $order_subtotal, ['currency' => $currency] ); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Tax:', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $order_tax, ['currency' => $currency] ); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Shipping:', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $order_shipping, ['currency' => $currency] ); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Total (Incl. Tax):', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $order_total, ['currency' => $currency] ); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Product Summary Table -->
                    <tr>
                        <td style="padding:15px 0;border-top:2px solid #ddd;">
                            <p style="font-size:16px;font-weight:bold;margin-bottom:8px;"><?php echo esc_html__( 'Product Sales Summary', 'borspirit' ); ?></p>
                            <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                <tr style="background:#f7f7f7;">
                                    <td style="border:1px solid #ddd;font-weight:bold;"><?php echo esc_html__( 'Product', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:center;font-weight:bold;"><?php echo esc_html__( 'Qty Sold', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:right;font-weight:bold;"><?php echo esc_html__( 'Total (Excl. Tax)', 'borspirit' ); ?></td>
                                    <td style="border:1px solid #ddd;text-align:right;font-weight:bold;"><?php echo esc_html__( 'Total (Incl. Tax)', 'borspirit' ); ?></td>
                                </tr>
                                <?php foreach ( $product_summary as $product_name => $data ) : ?>
                                    <tr>
                                        <td style="border:1px solid #ddd;"><?php echo esc_html( $product_name ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:center;"><?php echo esc_html( $data['qty'] ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $data['excl'] ); ?></td>
                                        <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $data['incl'] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>

                    <!-- Daily Totals Table -->
                    <tr>
                        <td style="padding:15px 0;border-top:2px solid #ddd;">
                            <p style="font-size:16px;font-weight:bold;margin-bottom:8px;"><?php echo esc_html__( 'Daily Totals', 'borspirit' ); ?></p>
                            <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family: Arial, sans-serif; font-size: 14px; color: #333;">
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Total Revenue (Excl. Tax):', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $total_revenue_excl ); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Total Revenue (Incl. Tax):', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $total_revenue_incl ); ?></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ddd;"><strong><?php echo esc_html__( 'Average Order Value (Incl. Tax):', 'borspirit' ); ?></strong></td>
                                    <td style="border:1px solid #ddd;text-align:right;"><?php echo wc_price( $total_revenue_incl / $total_orders ); ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <?php
                $message = ob_get_clean();
            }

            $mailer          = WC()->mailer();
            $heading         = sprintf( __( 'Daily WooCommerce Order Summary – %s', 'borspirit' ), $today );
            $wrapped_message = $mailer->wrap_message( $heading, $message );

            $wc_email     = new WC_Email();
            $html_message = $wc_email->style_inline( $wrapped_message );

            $to = [
                get_option( 'admin_email' ),
                'nickelszgabor@borspirit.hu'
            ];
            $subject  = sprintf( __( 'Daily WooCommerce Order Summary – %s', 'borspirit' ), $today );
            $headers  = [ 'Content-Type: text/html; charset=UTF-8' ];

            $mailer->send( $to, $subject, $html_message, $headers );
        }
    }

    wc_send_table_based_daily_order_summary_email();
