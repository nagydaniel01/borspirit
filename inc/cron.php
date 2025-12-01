<?php
if ( ! function_exists( 'borspirit_schedule_daily_orders_summary' ) ) {
    /**
     * Schedule the daily WooCommerce orders summary via Action Scheduler.
     */
    function borspirit_schedule_daily_orders_summary() {

        // Ensure Action Scheduler is available
        if ( ! class_exists( 'ActionScheduler' ) ) {
            return;
        }

        // Prevent duplicate scheduling
        if ( ! as_next_scheduled_action( 'borspirit_send_daily_orders_summary' ) ) {

            //$first_run = strtotime( 'tomorrow 18:00' );

            // Use WordPress local timezone
            $tz = wp_timezone();
            $dt = new DateTime( 'tomorrow 18:00', $tz );
            $first_run = $dt->getTimestamp();

            as_schedule_recurring_action(
                $first_run,                           // First run
                DAY_IN_SECONDS,                       // Repeat every 24 hours
                'borspirit_send_daily_orders_summary' // Action hook
            );
        }
    }
    add_action( 'init', 'borspirit_schedule_daily_orders_summary' );
}

if ( ! function_exists( 'borspirit_send_daily_orders_summary_email' ) ) {
        /**
         * Send HTML detailed daily WooCommerce orders summary.
         */
        function borspirit_send_daily_orders_summary_email() {

            /*
            $today_start = strtotime('today midnight');
            $today_end   = strtotime('tomorrow midnight');
            */

            // Get WP timezone as DateTimeZone object
            $timezone = wp_timezone();

            // Yesterday 18:00 in local timezone
            $start_dt = new DateTime( 'yesterday 18:00', $timezone );
            $today_start = $start_dt->getTimestamp();

            // Today 18:00 in local timezone
            $end_dt = new DateTime( 'today 18:00', $timezone );
            $today_end = $end_dt->getTimestamp();

            $orders = wc_get_orders([
                'limit'        => -1,
                'orderby'      => 'date',
                'order'        => 'DESC',
                'date_created' => $today_start . '...' . $today_end,
                'status'       => ['wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending'],
            ]);

            $report_date = wp_date('Y. F j., l');

            // Helper functions
            $generate_items_table = function ($order) {
                $rows = '';
                foreach ($order->get_items() as $item) {
                    $quantity   = $item->get_quantity();
                    $line_total = $item->get_total();
                    $line_tax   = $item->get_total_tax();
                    $line_price = $quantity ? $line_total / $quantity : 0;

                    $metadata_html = '';
                    foreach ($item->get_formatted_meta_data() as $meta) {
                        $metadata_html .= '<br><small><strong>' . esc_html($meta->display_key) . ':</strong> ' . esc_html($meta->display_value) . '</small>';
                    }

                    $rows .= "
                    <tr>
                        <td>{$item->get_name()}{$metadata_html}</td>
                        <td>" . wc_price($line_price) . "</td>
                        <td>{$quantity}</td>
                        <td>" . wc_price($line_total) . "</td>
                        <td>" . wc_price($line_tax) . "</td>
                    </tr>";
                }

                return "
                <h4>" . esc_html__('Items', 'borspirit') . "</h4>
                <table cellpadding='6' cellspacing='0' border='1' width='100%' style='border-collapse:collapse; border-color:#ccc;'>
                    <tr style='background:#f7f7f7;'>
                        <th align='left'>" . esc_html__('Product', 'borspirit') . "</th>
                        <th align='left'>" . esc_html__('Price', 'borspirit') . "</th>
                        <th align='left'>" . esc_html__('Qty', 'borspirit') . "</th>
                        <th align='left'>" . esc_html__('Subtotal', 'borspirit') . "</th>
                        <th align='left'>" . esc_html__('Tax', 'borspirit') . "</th>
                    </tr>
                    {$rows}
                </table>";
            };

            $generate_fees_table = function ($order) {
                $fees = $order->get_fees();
                if (empty($fees)) return '';

                $rows = '';
                foreach ($fees as $fee) {
                    $rows .= "
                    <tr>
                        <td>{$fee->get_name()}</td>
                        <td>" . wc_price($fee->get_total() + $fee->get_total_tax()) . "</td>
                    </tr>";
                }

                return "
                <h4>" . esc_html__('Additional rates', 'borspirit') . "</h4>
                <table cellpadding='6' cellspacing='0' border='1' width='100%' style='border-collapse:collapse; border-color:#ccc;'>
                    <tr style='background:#f7f7f7;'>
                        <th align='left'>" . esc_html__('Fee name', 'borspirit') . "</th>
                        <th align='left'>" . esc_html__('Amount', 'borspirit') . "</th>
                    </tr>
                    {$rows}
                </table>";
            };

            $generate_order_totals_table = function ($order) {
                $total_fees = array_sum(array_map(fn($fee) => floatval($fee->get_total() + $fee->get_total_tax()), $order->get_fees()));

                return "
                <h4>" . esc_html__('Order totals', 'borspirit') . "</h4>
                <table cellpadding='6' cellspacing='0' border='1' width='100%' style='border-collapse:collapse; border-color:#ccc; margin-top:10px;'>
                    <tr>
                        <th align='left' style='background:#f7f7f7;'>&nbsp;</th>
                        <th align='left' style='background:#f7f7f7;'>" . esc_html__('Amount', 'borspirit') . "</th>
                    </tr>
                    <tr><td><strong>" . esc_html__('Subtotal', 'borspirit') . "</strong></td><td>" . wc_price($order->get_subtotal()) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Fees', 'borspirit') . "</strong></td><td>" . wc_price($total_fees) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Shipping', 'borspirit') . "</strong></td><td>" . wc_price($order->get_shipping_total()) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Tax', 'borspirit') . "</strong></td><td>" . wc_price($order->get_total_tax()) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Total', 'borspirit') . "</strong></td><td><strong>" . wc_price($order->get_total()) . "</strong></td></tr>
                    <tr><td><strong>" . esc_html__('Payment method', 'borspirit') . "</strong></td><td>{$order->get_payment_method_title()}</td></tr>
                    <tr><td><strong>" . esc_html__('Shipping method', 'borspirit') . "</strong></td><td>{$order->get_shipping_method()}</td></tr>
                </table>";
            };

            $site_name = get_bloginfo('name');

            // Start HTML
            $message = "<html><body style='font-family: Arial, sans-serif; font-size:14px; color:#333;'><div style='margin:20px;'>
                <h2>" . sprintf( esc_html__('Daily %s Orders Report – %s', 'borspirit'), esc_html($site_name), $report_date ) . "</h2>";

            if (empty($orders)) {
                $message .= "<p>" . sprintf( esc_html__('No %s orders were placed today.', 'borspirit'), esc_html($site_name) ) . "</p>";
            } else {
                $daily_totals = [
                    'total_orders' => 0,
                    'revenue_excl_tax' => 0,
                    'revenue_incl_tax' => 0,
                    'tax_total' => 0,
                    'shipping_total' => 0,
                ];

                foreach ( $orders as $order ) {

                    $message .= "<div style='border:1px solid #ddd; margin-bottom:20px; padding:15px; border-radius:6px;'>";

                    // Order header using h3 + wp_kses_post + sprintf
                    $order_header = wp_kses_post(
                        sprintf(
                            esc_html__( 'Order #%1$s (%2$s)', 'borspirit' ),
                            $order->get_order_number(),
                            wc_format_datetime( $order->get_date_created() )
                        )
                    );

                    $message .= "<h3 style='margin-top:0;'>{$order_header}</h3>";

                    $message .= "<p>";
                    $message .= "<strong>" . esc_html__( 'Status', 'borspirit' ) . ":</strong> " . wc_get_order_status_name( $order->get_status() ) . "<br>";
                    $message .= "<strong>" . esc_html__( 'Email', 'borspirit' ) . ":</strong> " . esc_html( $order->get_billing_email() ) . "<br>";
                    $message .= "<strong>" . esc_html__( 'Phone', 'borspirit' ) . ":</strong> " . esc_html( $order->get_billing_phone() );
                    $message .= "</p>";

                    // Billing address
                    if ( $order->get_formatted_billing_address() ) {
                        $message .= "<h4>" . esc_html__( 'Billing address', 'borspirit' ) . "</h4>";
                        $message .= "<p>" . wp_kses_post( $order->get_formatted_billing_address() ) . "</p>";
                    }

                    // Shipping address (if exists)
                    if ( $order->get_formatted_shipping_address() ) {
                        $message .= "<h4>" . esc_html__( 'Shipping address', 'borspirit' ) . "</h4>";
                        $message .= "<p>" . wp_kses_post( $order->get_formatted_shipping_address() ) . "</p>";
                    }

                    $message .= $generate_items_table($order);
                    $message .= $generate_fees_table($order);
                    $message .= $generate_order_totals_table($order);

                    if ($order->get_customer_note()) {
                        $message .= "<h4>" . esc_html__('Customer note', 'borspirit') . "</h4><p>" . nl2br(esc_html($order->get_customer_note())) . "</p>";
                    }

                    $message .= "</div>";

                    // Update daily totals
                    $daily_totals['total_orders']++;
                    $daily_totals['revenue_incl_tax'] += floatval($order->get_total());
                    $daily_totals['revenue_excl_tax'] += floatval($order->get_total()) - floatval($order->get_total_tax());
                    $daily_totals['tax_total'] += floatval($order->get_total_tax());
                    $daily_totals['shipping_total'] += floatval($order->get_shipping_total());
                }

                // Daily totals table
                $message .= "<h3>" . esc_html__('Daily totals summary', 'borspirit') . "</h3>
                <table cellpadding='6' cellspacing='0' border='1' width='100%' style='border-collapse:collapse; border-color:#ccc; margin-bottom:20px;'>
                    <tr style='background:#f7f7f7;'><th align='left'>" . esc_html__('Metric', 'borspirit') . "</th><th align='left'>" . esc_html__('Amount', 'borspirit') . "</th></tr>
                    <tr><td><strong>" . esc_html__('Total orders', 'borspirit') . "</strong></td><td>{$daily_totals['total_orders']}</td></tr>
                    <tr><td><strong>" . esc_html__('Total revenue (excl. tax)', 'borspirit') . "</strong></td><td>" . wc_price($daily_totals['revenue_excl_tax']) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Total revenue (incl. tax)', 'borspirit') . "</strong></td><td>" . wc_price($daily_totals['revenue_incl_tax']) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Total tax collected', 'borspirit') . "</strong></td><td>" . wc_price($daily_totals['tax_total']) . "</td></tr>
                    <tr><td><strong>" . esc_html__('Total shipping collected', 'borspirit') . "</strong></td><td>" . wc_price($daily_totals['shipping_total']) . "</td></tr>
                </table>";
            }

            $message .= "</div></body></html>";

            // Send email with date in subject
            wp_mail(
                get_option('admin_email'),
                sprintf( esc_html__('Daily %s Orders Report – %s', 'borspirit'), esc_html($site_name), $report_date ),
                $message,
                ['Content-Type: text/html; charset=UTF-8']
            );
        }
        add_action('borspirit_send_daily_orders_summary', 'borspirit_send_daily_orders_summary_email');
    }