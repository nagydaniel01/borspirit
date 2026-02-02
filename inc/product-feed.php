<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'Custom_Product_Feed' ) ) {

        class Custom_Product_Feed {

            public function __construct() {
                add_action( 'init', [ $this, 'add_rewrite_rule' ] );
                add_filter( 'query_vars', [ $this, 'add_query_var' ] );
                add_action( 'template_redirect', [ $this, 'maybe_generate_feed' ] );
            }

            public function add_rewrite_rule() {
                add_rewrite_rule( '^product-feed$', 'index.php?product_feed=1', 'top' );
            }

            public function add_query_var( $vars ) {
                $vars[] = 'product_feed';
                return $vars;
            }

            public function maybe_generate_feed() {
                if ( intval( get_query_var( 'product_feed' ) ) ) {
                    header( 'Content-Type: application/xml; charset=' . get_bloginfo('charset'), true );

                    // Optional: manual refresh via ?refresh=1
                    if ( isset( $_GET['refresh'] ) ) {
                        delete_transient( 'product_feed_xml' );
                    }

                    echo $this->get_feed();
                    exit;
                }
            }

            private function get_feed() {
                $cache_key = 'product_feed_xml';

                // Uncomment the next 2 lines if you want caching (hourly)
                // $xml = get_transient( $cache_key );
                // if ( $xml ) return $xml;

                // Always regenerate feed on page load
                $xml = $this->generate_xml_feed();

                // Uncomment if you want to cache
                // set_transient( $cache_key, $xml, HOUR_IN_SECONDS );

                return $xml;
            }

            private function generate_xml_feed() {
                if ( ! class_exists( 'WooCommerce' ) ) {
                    return '<?xml version="1.0"?><products />';
                }

                $products = wc_get_products( [ 'limit' => -1 ] );

                $xml = new XMLWriter();
                $xml->openMemory();
                $xml->startDocument( '1.0', 'UTF-8' );
                $xml->setIndent( true );
                $xml->startElement( 'products' );
                $xml->writeElement( 'generated_at', current_time( 'mysql' ) );

                foreach ( $products as $p ) {
                    $this->write_product( $xml, $p );
                }

                $xml->endElement(); // products
                return $xml->outputMemory();
            }

            private function write_product( $xml, $p ) {
                $xml->startElement( 'product' );
                $xml->writeElement( 'id', $p->get_id() );
                $xml->writeElement( 'sku', $p->get_sku() );
                $xml->writeElement( 'name', $this->esc( $p->get_name() ) );
                $xml->writeElement( 'permalink', $p->get_permalink() );

                // Featured image URL
                $image_id = $p->get_image_id();
                $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
                $xml->writeElement( 'image', esc_url( $image_url ) );

                // Prices
                $prices = $this->get_prices( $p );
                $xml->startElement( 'prices' );
                $xml->writeElement( 'net', $prices['net'] );
                $xml->writeElement( 'tax', $prices['tax'] );
                $xml->writeElement( 'gross', $prices['gross'] );
                $xml->writeElement( 'currency', get_woocommerce_currency() );
                $xml->endElement(); // prices

                // Attributes
                $xml->startElement( 'attributes' );
                foreach ( $p->get_attributes() as $k => $a ) {
                    $xml->startElement( 'attribute' );
                    $xml->writeElement( 'name', $k );
                    $vals = $a->is_taxonomy() 
                        ? wp_get_post_terms( $p->get_id(), $a->get_name(), ['fields' => 'names'] ) 
                        : $a->get_options();
                    $xml->writeElement( 'values', implode( ',', $vals ) );
                    $xml->endElement(); // attribute
                }
                $xml->endElement(); // attributes

                // Only product_cat and product_tag
                $xml->startElement( 'taxonomies' );
                $specific_taxonomies = ['product_cat', 'product_tag'];
                foreach ( $specific_taxonomies as $tax_name ) {
                    $terms = wp_get_post_terms( $p->get_id(), $tax_name, ['fields' => 'names'] );
                    if ( ! empty( $terms ) ) {
                        $xml->startElement( 'taxonomy' );
                        $xml->writeElement( 'name', $tax_name );
                        $xml->writeElement( 'values', implode( ',', $terms ) );
                        $xml->endElement(); // taxonomy
                    }
                }
                $xml->endElement(); // taxonomies

                $xml->endElement(); // product
            }

            private function get_prices( $p ) {
                $gross = wc_get_price_including_tax( $p );
                $net   = wc_get_price_excluding_tax( $p );
                $tax   = $gross - $net;

                return [
                    'gross' => (float) number_format( $gross, 0, '.', '' ),
                    'net'   => (float) number_format( $net, 0, '.', '' ),
                    'tax'   => (float) number_format( $tax, 0, '.', '' ),
                ];
            }

            private function esc( $text ) {
                return htmlspecialchars( $text, ENT_XML1, 'UTF-8' );
            }
        }

        // Initialize the feed
        new Custom_Product_Feed();
    }
