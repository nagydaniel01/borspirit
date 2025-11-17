<?php
if ( ! class_exists( 'WP_Bootstrap_Navwalker' ) ) {
    class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {

        // Start submenu
        public function start_lvl( &$output, $depth = 0, $args = null ) {
            $indent = str_repeat("\t", $depth);
            $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
        }

        // End submenu
        public function end_lvl( &$output, $depth = 0, $args = null ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        // Start menu item
        public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
            $indent = ($depth) ? str_repeat("\t", $depth) : '';

            $has_children = in_array('menu-item-has-children', $item->classes);

            // LI classes
            $li_classes = ['nav-item'];
            if ($has_children && $depth === 0) {
                $li_classes[] = 'dropdown';
            } elseif ($has_children && $depth > 0) {
                $li_classes[] = 'dropend';
            }
            $output .= $indent . '<li class="' . implode(' ', $li_classes) . '">';

            // A classes
            $a_classes = [];
            if ($depth === 0) {
                $a_classes[] = 'nav-link';
                if ($has_children) $a_classes[] = 'dropdown-toggle';
            } else {
                $a_classes[] = 'dropdown-item';
                if ($has_children) $a_classes[] = 'dropdown-toggle';
            }

            // A attributes
            $atts = [
                'href' => !empty($item->url) ? esc_url($item->url) : '#',
                'class' => implode(' ', $a_classes),
            ];
            //if ($has_children) $atts['data-bs-toggle'] = 'dropdown';

            $attributes = '';
            foreach ($atts as $attr => $value) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }

            $title = apply_filters('the_title', $item->title, $item->ID);

            $output .= '<a' . $attributes . '>' . esc_html($title) . '</a>';
        }

        // End menu item
        public function end_el( &$output, $item, $depth = 0, $args = null ) {
            $output .= "</li>\n";
        }
    }
}
