<?php
if ( ! class_exists( 'Custom_Nav_Walker' ) ) {
    class Custom_Nav_Walker extends Walker_Nav_Menu {

        public function start_lvl( &$output, $depth = 0, $args = null ) {
            $t = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\t";
            $n = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\n";
            $indent = str_repeat( $t, $depth );

            $classes = ['nav__list', 'level' . ( $depth + 1 )];
            $class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );

            $atts = [
                'class' => esc_attr( $class_names ),
            ];

            $atts = apply_filters( 'nav_menu_submenu_attributes', $atts, $args, $depth );
            $attributes = $this->build_atts( $atts );

            $output .= "{$n}{$indent}<ul{$attributes}>{$n}";
        }

        public function start_el( &$output, $item, $depth = 0, $args = null, $current_object_id = 0 ) {
            $t = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\t";
            $n = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\n";
            $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

            // Classes
            $classes = empty( $item->classes ) ? [] : (array) $item->classes;
            $classes[] = 'nav__item';
            $classes[] = 'level' . $depth;

            // Detect has-children (core sets 'menu-item-has-children', but we ensure ours too)
            if ( in_array( 'menu-item-has-children', $classes, true ) ) {
                $classes[] = 'has-children';
            }

            $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
            $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
            $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

            $li_atts = [
                'id'    => esc_attr( $id ),
                'class' => esc_attr( $class_names ),
            ];

            $li_atts = apply_filters( 'nav_menu_item_attributes', $li_atts, $item, $args, $depth );
            $li_attributes = $this->build_atts( $li_atts );

            $output .= $indent . '<li' . $li_attributes . '>';

            // Title and link
            $title = apply_filters( 'the_title', $item->title, $item->ID );
            $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

            $atts = [
                'href'         => ! empty( $item->url ) ? esc_url( $item->url ) : '',
                'target'       => ! empty( $item->target ) ? esc_attr( $item->target ) : '',
                'rel'          => ! empty( $item->xfn ) ? esc_attr( $item->xfn ) : '',
                'aria-current' => $item->current ? 'page' : '',
                'class'        => 'nav__link js-nav-link level' . $depth,
            ];

            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
            $attributes = $this->build_atts( $atts );

            $item_output  = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . esc_html( $title ) . $args->link_after;

            // Add arrow icon if it has children
            if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
                $item_output .= '<span class="nav__arrow" aria-hidden="true"><svg width="10" height="6" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
            }

            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

        public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
            $t = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\t";
            $n = isset( $args->item_spacing ) && 'discard' === $args->item_spacing ? '' : "\n";
            $output .= "</li>{$n}";
        }

        protected function build_atts( $atts = [] ) {
            $attribute_string = '';
            foreach ( $atts as $attr => $value ) {
                if ( false !== $value && '' !== $value && is_scalar( $value ) ) {
                    $value             = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attribute_string .= ' ' . $attr . '="' . $value . '"';
                }
            }
            return $attribute_string;
        }
    }
}

if ( ! class_exists( 'Custom_Mega_Menu_Walker' ) ) {
    /**
     * Custom Mega Menu Walker
     * Handles both standard and mega menu rendering for WordPress navigation.
     */
    class Custom_Mega_Menu_Walker extends Walker_Nav_Menu {

        // Mega menu state flags
        private bool $mega_menu = false;
        private array $tab_panes = [];
        private string $tab_buffer = '';
        private string $tab_id = '';
        private bool $first_tab_done = false;

        // Tracking current and root parent IDs
        private int $current_parent_id = 0;
        private int $root_parent_id = 0;

        /**
         * Start level output
         */
        public function start_lvl( &$output, $depth = 0, $args = null ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat( $t, $depth );
            $level_class = 'level' . ($depth + 1);

            // Mega menu top-level wrapper
            if ($depth === 0 && $this->mega_menu) {
                $root_id = $this->root_parent_id ?: 'root';
                $output .= "\n{$indent}<!-- Mega Menu Wrapper Start -->\n";
                $output .= "{$indent}<div id=\"megaMenu-{$root_id}\" class=\"nav__mega-menu\">\n";
                $output .= "{$indent}\t<div class=\"container\">\n";
                $output .= "{$indent}\t\t<ul id=\"megaMenuTabs-{$root_id}\" class=\"nav nav-tabs nav__list {$level_class}\" role=\"tablist\">\n";
                return;
            }

            // Handle nested levels INSIDE tab panes (level ≥ 2)
            if ($this->mega_menu && $depth >= 2) {
                $this->tab_buffer .= "\n{$indent}<ul class=\"nav__list {$level_class}\">\n";
                return;
            }

            // Standard submenu
            if (!($this->mega_menu && $depth === 1)) {
                $output .= "\n{$indent}<ul class=\"nav__list {$level_class}\">\n";
            }
        }

        /**
         * End level output
         */
        public function end_lvl( &$output, $depth = 0, $args = null ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent  = str_repeat( $t, $depth );

            // Mega menu top-level closing
            if ($depth === 0 && $this->mega_menu) {
                $root_id = $this->root_parent_id ?: 'root';
                $output .= "{$indent}\t\t</ul><!-- Tabs End -->\n";
                $output .= "{$indent}\t\t<div id=\"megaMenuContent-{$root_id}\" class=\"tab-content\">\n";

                foreach ($this->tab_panes as $pane) {
                    $output .= $pane;
                }

                $output .= "{$indent}\t\t</div><!-- Tab Content End -->\n";
                $output .= "{$indent}\t</div><!-- Container End -->\n";
                $output .= "{$indent}</div><!-- Mega Menu Wrapper End -->\n";

                // Reset state
                $this->mega_menu = false;
                $this->tab_panes = [];
                $this->first_tab_done = false;
                return;
            }

            // Close nested levels inside tab panes
            if ($this->mega_menu && $depth >= 2) {
                $this->tab_buffer .= "{$indent}</ul>{$n}";
                return;
            }

            // Standard submenu closing
            if (!($this->mega_menu && $depth === 1)) {
                $output .= "$indent</ul>{$n}";
            }
        }

        /**
         * Start element output
         */
        public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }

            $classes = array_filter((array) ($item->classes ?? []));
            $classes[] = 'nav__item';
            $classes[] = 'level' . $depth;

            $this->current_parent_id = (int) $item->menu_item_parent;
            if ($depth === 0) {
                $this->root_parent_id = (int) $item->ID;
            }

            // Detect children
            $has_children = !empty($args->walker->has_children);
            if ($has_children) {
                $classes[] = 'has-children';
            }

            // Detect mega menu
            if ($depth === 0 && get_post_meta($item->ID, '_is_mega_menu', true)) {
                $classes[] = 'has-mega-menu';
                $this->mega_menu = true;
            }

            $class_attr = implode(' ', array_map('esc_attr', $classes));
            $id_attr = 'menu-item-' . esc_attr($item->ID);
            $html = "<li id=\"{$id_attr}\" class=\"{$class_attr}\">";

            // Anchor attributes
            $atts = [
                'href'  => !empty($item->url) ? esc_url($item->url) : '#',
                'id'    => 'menu-link-' . $item->ID,
                'class' => 'nav__link js-nav-link level' . $depth,
                //'title' => $item->title,
            ];

            // Mega menu tab handling (depth 1)
            if ($this->mega_menu && $depth === 1) {
                $this->tab_id = 'tab-' . sanitize_title($item->title);
                $atts['data-bs-toggle'] = 'tab';
                $atts['role'] = 'tab';
                $atts['href'] = '#' . $this->tab_id;
                $atts['aria-controls'] = $this->tab_id;

                if (!$this->first_tab_done) {
                    $atts['class'] .= ' active';
                    $atts['aria-selected'] = 'true';
                    $this->first_tab_done = true;
                } else {
                    $atts['aria-selected'] = 'false';
                }
            }

            // Accessibility for dropdowns
            if ($depth === 0 && $has_children) {
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
            }

            // Build attributes string
            $attr_str = $this->build_atts( $atts );

            // ---- Add arrow icons ----
            $icon_html = '';
            if ($depth === 0 && $has_children) {
                if ($this->mega_menu) {
                    // Mega menu arrow icon (can be SVG or font icon)
                    $icon_html = '<span class="nav__arrow nav__arrow--mega-menu" aria-hidden="true"><svg width="10" height="6" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
                } else {
                    // Regular dropdown arrow
                    $icon_html = '<span class="nav__arrow" aria-hidden="true"><svg width="10" height="6" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
                }
            }

            $html .= '<a' . $attr_str . '>' . esc_html($item->title) . $icon_html . '</a>';

            // Buffer ALL deeper levels (2+)
            if ($this->mega_menu && $depth >= 2) {
                $this->tab_buffer .= $html;
            } else {
                $output .= $html;
            }
        }

        /**
         * End element output
         */
        public function end_el( &$output, $item, $depth = 0, $args = [] ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }

            $html = "</li>{$n}";

            // Buffer ALL deeper levels (2+)
            if ($this->mega_menu && $depth >= 2) {
                $this->tab_buffer .= $html;
                return;
            }

            // Create tab pane for level 1 mega menu items
            if ($this->mega_menu && $depth === 1 && $this->tab_id) {
                if ($this->tab_buffer) {
                    $pane_classes = 'tab-pane fade' . (count($this->tab_panes) === 0 ? ' show active' : '');
                    $pane  = '<div id="' . esc_attr($this->tab_id) . '" class="' . esc_attr($pane_classes) . '" role="tabpanel">';
                    $pane .= '<ul class="nav__list level2">' . $this->tab_buffer . '</ul>';
                    $pane .= '</div><!-- Tab Pane End -->';

                    $this->tab_panes[] = $pane;
                    $this->tab_buffer = '';
                    $this->tab_id = '';
                }
            }

            $output .= $html;
        }

        /**
         * Compatibility: Builds a string of HTML attributes from an array.
         */
        protected function build_atts( $atts = array() ) {
            $attribute_string = '';
            foreach ( $atts as $attr => $value ) {
                if ( false !== $value && '' !== $value && is_scalar( $value ) ) {
                    $value             = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attribute_string .= ' ' . $attr . '="' . $value . '"';
                }
            }
            return $attribute_string;
        }
    }
}
