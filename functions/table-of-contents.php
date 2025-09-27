<?php
    if ( ! function_exists( 'mdr_shortcode_table_of_contents' ) ) {
        /**
         * Shortcode handler for [table_of_contents].
         *
         * @param array       $atts    Shortcode attributes.
         * @param string|null $content Content inside shortcode.
         *
         * @return string Rendered HTML for the TOC inside a Bootstrap accordion.
         */
        function mdr_shortcode_table_of_contents( $atts, $content = null ) {
            global $post;

            if ( ! $post instanceof WP_Post ) {
                return '';
            }

            $atts = shortcode_atts( array(), $atts, 'table_of_contents' );

            $table_of_contents = mdr_get_table_of_contents( $post->post_content );

            $html = '';
            if ( ! empty( $table_of_contents['list'] ) ) {
                $html .= '<div class="accordion" id="accordion-toc">';
                $html .= '  <div class="accordion-item">';
                $html .= '    <h2 class="accordion-header" id="heading-toc">';
                $html .= '      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-toc" aria-expanded="false" aria-controls="collapse-toc">';
                $html .=            esc_html__( 'Table of contents', TEXT_DOMAIN );
                $html .= '      </button>';
                $html .= '    </h2>';
                $html .= '    <div id="collapse-toc" class="accordion-collapse collapse" aria-labelledby="heading-toc" data-bs-parent="#accordion-toc">';
                $html .= '      <div class="accordion-body">';
                $html .=            $table_of_contents['list'];
                $html .= '      </div>';
                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '</div>';
            }

            return apply_filters( 'mdr_shortcode_table_of_contents', $html, $atts );
        }
        add_shortcode( 'table_of_contents', 'mdr_shortcode_table_of_contents' );
    }

    if ( ! function_exists( 'mdr_table_of_contents_section_anchors' ) ) {
        /**
         * Add anchors to headings in post content.
         *
         * @param string $content Post content.
         *
         * @return string Modified content with anchors added.
         */
        function mdr_table_of_contents_section_anchors( $content ) {
            $data = mdr_get_table_of_contents( $content );

            if ( ! empty( $data['sections'] ) && ! empty( $data['sections_with_ids'] ) ) {
                foreach ( $data['sections'] as $k => $section ) {
                    if ( isset( $data['sections_with_ids'][ $k ] ) ) {
                        $content = str_replace( $section, $data['sections_with_ids'][ $k ], $content );
                    }
                }
            }

            return $content;
        }
        add_filter( 'the_content', 'mdr_table_of_contents_section_anchors', 10 );
    }

    if ( ! function_exists( 'mdr_get_table_of_contents' ) ) {
        /**
         * Generate TOC data structure from content.
         *
         * @param string $content Post content.
         *
         * @return array {
         *     @type string $list             HTML list of headings.
         *     @type array  $sections         Original headings.
         *     @type array  $sections_with_ids Headings with anchor IDs.
         * }
         */
        function mdr_get_table_of_contents( $content ) {
            if ( empty( $content ) ) {
                return array( 'list' => '', 'sections' => array(), 'sections_with_ids' => array() );
            }

            preg_match_all( '/(<h([1-6])[^<>]*>)(.+?)(<\/h[1-6]>)/i', $content, $matches, PREG_SET_ORDER );

            $level              = null; // Start null, set to first heading level.
            $list               = array();
            $sections           = array();
            $sections_with_ids  = array();
            $used_slugs         = array();

            foreach ( $matches as $val ) {
                $heading_text = trim( wp_strip_all_tags( $val[3] ) );
                if ( '' === $heading_text ) {
                    continue;
                }

                // Generate a unique slug for anchor.
                $slug = sanitize_title( $heading_text );
                $original_slug = $slug;
                $i = 2;
                while ( in_array( $slug, $used_slugs, true ) ) {
                    $slug = $original_slug . '-' . $i;
                    $i++;
                }
                $used_slugs[] = $slug;

                $list_class      = 'toc-list';
                $list_item_class = 'toc-item';
                $link_class      = 'toc-link';

                $current_level = (int) $val[2];

                if ( is_null( $level ) ) {
                    // First heading
                    $list[] = '<li class="' . esc_attr( $list_item_class ) . '"><a class="' . esc_attr( $link_class ) . '" href="#' . esc_attr( $slug ) . '">' . esc_html( $heading_text ) . '</a>';
                } elseif ( $current_level === $level ) {
                    $list[] = '</li><li class="' . esc_attr( $list_item_class ) . '"><a class="' . esc_attr( $link_class ) . '" href="#' . esc_attr( $slug ) . '">' . esc_html( $heading_text ) . '</a>';
                } elseif ( $current_level > $level ) {
                    $list[] = '<ol class="' . esc_attr( $list_class ) . '"><li class="' . esc_attr( $list_item_class ) . '"><a class="' . esc_attr( $link_class ) . '" href="#' . esc_attr( $slug ) . '">' . esc_html( $heading_text ) . '</a>';
                } else { // $current_level < $level
                    $diff = $level - $current_level;
                    $list[] = str_repeat( '</li></ol>', $diff ) . '</li><li class="' . esc_attr( $list_item_class ) . '"><a class="' . esc_attr( $link_class ) . '" href="#' . esc_attr( $slug ) . '">' . esc_html( $heading_text ) . '</a>';
                }

                $sections[]          = $val[1] . $heading_text . $val[4];
                $sections_with_ids[] = '<h' . $current_level . ' id="' . esc_attr( $slug ) . '">' . esc_html( $heading_text ) . $val[4];

                $level = $current_level;
            }

            // Close remaining open lists.
            while ( $level > 1 ) {
                $list[] = '</li></ol>';
                $level--;
            }

            $html = ! empty( $list ) ? '<ol class="toc-list">' . implode( '', $list ) . '</ol>' : '';

            return array(
                'list'             => $html,
                'sections'         => $sections,
                'sections_with_ids'=> $sections_with_ids,
            );
        }
    }
