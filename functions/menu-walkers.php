<?php
/**
 * Default Menu Walker
 * Handles both standard and mega menu rendering for WordPress navigation.
 */
class Default_Menu_Walker extends Walker_Nav_Menu {

    private bool $mega_menu = false;
    private array $tab_panes = [];
    private string $tab_buffer = '';
    private string $tab_id = '';

    /**
     * Start Level
     */
    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat("\t", $depth);
        $level_class = 'level' . ($depth + 1);

        // Root of mega menu
        if ($depth === 0 && $this->mega_menu) {
            $output .= "\n{$indent}<div class=\"nav__mega-menu\">\n";
            $output .= "{$indent}\t<ul class=\"nav nav-tabs nav__list {$level_class}\" id=\"megaMenuTabs-{$args->menu_id}\" role=\"tablist\">\n";
            return;
        }

        // Normal submenu (only if not capturing tab content)
        if (!($this->mega_menu && $depth === 1)) {
            $output .= "\n{$indent}<ul class=\"nav__list {$level_class}\">\n";
        }
    }

    /**
     * End Level
     */
    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat("\t", $depth);

        // Close mega menu root
        if ($depth === 0 && $this->mega_menu) {
            $output .= "{$indent}\t</ul>\n"; // end nav-tabs
            $output .= "{$indent}\t<div class=\"tab-content\" id=\"megaMenuContent-{$args->menu_id}\">\n";

            foreach ($this->tab_panes as $pane) {
                $output .= $pane;
            }

            $output .= "{$indent}\t</div>\n</div>\n";

            // Reset mega menu state
            $this->mega_menu = false;
            $this->tab_panes = [];
            return;
        }

        // Close normal submenu
        if (!($this->mega_menu && $depth === 1)) {
            $output .= "{$indent}</ul>\n";
        }
    }

    /**
     * Start Element
     */
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $classes = array_filter((array) ($item->classes ?? []));
        $classes[] = 'nav__item';
        $classes[] = 'level' . $depth;

        // Detect children
        $has_children = !empty($args->walker->has_children);
        if ($has_children) {
            $classes[] = 'has-children';
        }

        // Detect mega menu at root
        if ($depth === 0 && get_post_meta($item->ID, '_is_mega_menu', true)) {
            $classes[] = 'has-mega-menu';
            $this->mega_menu = true;
        }

        $class_attr = ' class="' . esc_attr(implode(' ', $classes)) . '"';
        $html = "<li{$class_attr}>";

        // Build link attributes
        $atts = [
            'href'  => !empty($item->url) ? $item->url : '#',
            'class' => 'nav__link js-nav-link level' . $depth,
            'title' => $item->title,
        ];

        // Mega menu tab links
        if ($this->mega_menu && $depth === 1) {
            $this->tab_id = 'tab-' . sanitize_title($item->title);
            $atts['data-bs-toggle'] = 'tab';
            $atts['role'] = 'tab';
            $atts['href'] = '#' . $this->tab_id;
            $atts['aria-controls'] = $this->tab_id;
        }

        $attr_str = '';
        foreach ($atts as $attr => $val) {
            $attr_str .= ' ' . $attr . '="' . esc_attr($val) . '"';
        }

        $html .= '<a' . $attr_str . '>' . esc_html($item->title) . '</a>';

        // Capture tab content at depth 2
        if ($this->mega_menu && $depth === 2) {
            $this->tab_buffer .= $html;
        } else {
            $output .= $html;
        }
    }

    /**
     * End Element
     */
    public function end_el( &$output, $item, $depth = 0, $args = [] ) {
        $html = "</li>\n";

        // Capture level-2 items inside mega menu
        if ($this->mega_menu && $depth === 2) {
            $this->tab_buffer .= $html;
            return;
        }

        // Finish a tab (depth 1 ends, buffer filled)
        if ($this->mega_menu && $depth === 1 && $this->tab_id) {
            if ($this->tab_buffer) {
                $pane_classes = 'tab-pane fade' . (empty($this->tab_panes) ? ' show active' : '');
                $pane  = '<div class="' . $pane_classes . '" id="' . esc_attr($this->tab_id) . '" role="tabpanel">';
                $pane .= '<ul class="nav__list level2">' . $this->tab_buffer . '</ul>';
                $pane .= '</div>';

                $this->tab_panes[] = $pane;
                $this->tab_buffer = '';
                $this->tab_id = '';
            }
        }

        $output .= $html;
    }
}
