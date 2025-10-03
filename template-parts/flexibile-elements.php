<?php
    try {
        // Use get_the_ID() as fallback if no page ID is passed
        $page_id = isset($args['page_id']) && is_numeric($args['page_id']) ? (int) $args['page_id'] : get_the_ID();

        if (empty($page_id) || !is_numeric($page_id)) {
            throw new Exception( __('Az oldalazonosító hiányzik vagy érvénytelen.', TEXT_DOMAIN) );
        }

        // Define the base directory for template section files
        $template_dir = trailingslashit(get_template_directory()) . 'template-parts/sections/';
        if (!is_dir($template_dir)) {
            throw new Exception( sprintf( __('A szükséges sablonkönyvtár nem létezik: %s', TEXT_DOMAIN), $template_dir ) );
        }

        // Check for ACF and retrieve sections
        if (!function_exists('get_field')) {
            throw new Exception( __('Az Advanced Custom Fields bővítmény nincs aktiválva. Telepítse vagy aktiválja az ACF-et a szekciók használatához.', TEXT_DOMAIN) );
        }

        $sections = get_field('sections', $page_id);

        // Process sections
        if (!empty($sections) && is_array($sections)) {
            $section_num = 0;

            foreach ($sections as $index => $section) {
                $section_num++;

                if (!is_array($section) || empty($section['acf_fc_layout'])) {
                    printf(
                        '<div class="alert alert-warning" role="alert">%s</div>',
                        esc_html( sprintf( __('A(z) #%d szekció hibásan van formázva és nem jeleníthető meg.', TEXT_DOMAIN), $section_num ) )
                    );
                    continue;
                }

                $section_name = sanitize_file_name($section['acf_fc_layout']);
                $section_file = $template_dir . 'section-' . $section_name . '.php';

                if (file_exists($section_file)) {
                    require $section_file;
                } else {
                    printf(
                        '<div class="alert alert-danger" role="alert">%s</div>',
                        sprintf(
                            __('A(z) <code>%s</code> szekció sablonja hiányzik. Kérjük, hozza létre a fájlt: <code>%s</code>', TEXT_DOMAIN),
                            esc_html( $section_name ),
                            esc_html( $section_file )
                        )
                    );
                }
            }
        } else {
            printf(
                '<div class="alert alert-info" role="alert">%s</div>',
                esc_html__('Ehhez az oldalhoz nem találhatók tartalmi szekciók.', TEXT_DOMAIN)
            );
        }

    } catch (Exception $e) {
        printf(
            '<div class="alert alert-danger" role="alert">%s</div>',
            esc_html( $e->getMessage() )
        );
    }
