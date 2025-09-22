<?php
    if ( ! class_exists( 'WooCommerce' ) ) return;

    /**
     * Link previous orders to a newly created user account during registration.
     * 
     * @param int $user_id The ID of the newly created user.
     */
    if ( ! function_exists( 'link_orders_at_registration' ) ) {
        function link_orders_at_registration( $user_id ) {
            $count = wc_update_new_customer_past_orders( $user_id );
            update_user_meta( $user_id, '_wc_linked_order_count', $count );
        }

        add_action( 'woocommerce_created_customer', 'link_orders_at_registration', 20, 1);
    }

    /**
     * Display a message on the account dashboard showing the count of linked orders.
     */
    if ( ! function_exists( 'show_linked_order_count' ) ) {
        function show_linked_order_count() {

            $user_id = get_current_user_id();
        
            if ( ! $user_id ) {
                return;
            }
        
            // check if we've linked orders for this user at registration
            $count = get_user_meta( $user_id, '_wc_linked_order_count', true );
        
            if ( $count && $count > 0 ) {
            
                $name = get_user_by( 'id', $user_id )->display_name;
        
                $message  = $name ? sprintf( __( 'Welcome, %s!', 'text' ), $name ) : __( 'Welcome!', 'text' );
                $message .= ' ' . sprintf( _n( 'Your previous order has been linked to this account.', 'Your previous %s orders have been linked to this account.', $count, 'text' ), $count );
                $message .= ' <a class="button" href="' . esc_url( wc_get_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'View Orders', 'text' ) . '</a>';
        
                // add a notice with our message and delete our linked order flag
                wc_print_notice( $message, 'notice' );
                delete_user_meta( $user_id, '_wc_linked_order_count' );
            }
        }

        add_action( 'woocommerce_account_dashboard', 'show_linked_order_count', 1 );
    }