<?php
class Default_Menu_Walker extends Walker_Nav_Menu {

    // Track mega menu items
    private $mega_menu = false;

    // Start Level
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $level_class = 'level' . ($depth + 1);

        // Wrap top-level mega menu in a div
        if ($depth === 0 && $this->mega_menu) {
            $output .= "\n$indent<div class=\"nav__mega-menu\">\n";
            $output .= "$indent\t<ul class=\"nav__list $level_class\">\n";
        } else {
            $output .= "\n$indent<ul class=\"nav__list $level_class\">\n";
        }
    }

    // End Level
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);

        if ($depth === 0 && $this->mega_menu) {
            $output .= "$indent\t</ul>\n";
            $output .= "$indent</div>\n";
        } else {
            $output .= "$indent</ul>\n";
        }

        // Reset mega menu flag after closing top-level submenu
        if ($depth === 0) {
            $this->mega_menu = false;
        }
    }

    // Start Element
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Base classes
        $classes[] = 'nav__item';
        $classes[] = 'level' . $depth;

        // Check for children
        $has_children = !empty($args->walker->has_children);
        if ($has_children) {
            $classes[] = 'has-children';
        }

        // Check for mega menu
        $is_mega = get_post_meta($item->ID, '_is_mega_menu', true);
        if ($is_mega && $depth === 0) {
            $classes[] = 'has-mega-menu';
            $this->mega_menu = true; // set flag for start_lvl
        }

        $class_names = join(' ', array_filter($classes));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        $output .= '<li' . $class_names . '>';

        // Link attributes
        $atts = array();
        $atts['title']  = !empty($item->title) ? $item->title : '';
        $atts['href']   = !empty($item->url) ? $item->url : '#';
        $atts['class']  = 'nav__link js-nav-link level' . $depth;

        if ($has_children) {
            $atts['aria-haspopup'] = 'true';
        }

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $output .= '<a' . $attributes . '>' . $title . '</a>';
    }

    // End Element
    function end_el(&$output, $item, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }
}
