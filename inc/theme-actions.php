<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /**
     * Open a section wrapper.
     *
     * This function outputs the opening <section> tag with optional ID and classes.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $id      The ID of the section.
     *     @type string $classes Additional classes to add to the section. Default 'section'.
     * }
     */
    add_action('theme_section_open', function ($args = []) {
        $id      = !empty($args['id']) ? esc_attr($args['id']) : '';
        $classes = !empty($args['classes']) ? esc_attr($args['classes']) : 'section';

        echo "<section id=\"{$id}\" class=\"{$classes}\">";
    }, 10, 1);


    /**
     * Close a section wrapper.
     *
     * Outputs the closing </section> tag.
     */
    add_action('theme_section_close', function () {
        echo '</section>';
    });


    /**
     * Open a section container.
     *
     * Outputs the opening <div class="container"> tag.
     */
    add_action('theme_section_container_open', function () {
        echo '<div class="container">';
    });


    /**
     * Close a section container.
     *
     * Outputs the closing </div> tag.
     */
    add_action('theme_section_container_close', function () {
        echo '</div>';
    });


    /**
     * Render the section header.
     *
     * Displays a title and/or lead paragraph within a section.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string  $title      The title of the section.
     *     @type bool    $hide_title Whether to hide the title. Default false.
     *     @type string  $lead       Optional lead text/description for the section.
     * }
     */
    add_action('theme_section_header', function ($args = []) {

        $title      = $args['title'] ?? '';
        $hide_title = $args['hide_title'] ?? false;
        $lead       = $args['lead'] ?? '';

        if (!$title && !$lead) {
            return;
        }

        echo '<div class="section__header">';

        if (!$hide_title && $title) {
            echo '<h1 class="section__title">' . esc_html($title) . '</h1>';
        }

        if (!empty($lead)) {
            echo '<div class="section__lead">' . wp_kses_post($lead) . '</div>';
        }

        echo '</div>';
    }, 10, 1);


    /**
     * Render the section featured image.
     *
     * Displays the post thumbnail for a given post ID.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int $post_id The post ID. Default current post ID.
     * }
     */
    add_action('theme_section_featured_image', function ($args = []) {

        $post_id = $args['post_id'] ?? get_the_ID();

        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (!$thumbnail_id) {
            return;
        }

        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true)
            ?: get_the_title($post_id);

        echo get_the_post_thumbnail(
            $post_id,
            'large',
            [
                'class' => 'section__image',
                'alt'   => esc_attr($alt_text),
            ]
        );

    }, 10, 1);


    /**
     * Open the section content wrapper.
     *
     * Outputs the opening <div class="section__content"> tag.
     */
    add_action('theme_section_content_open', function () {
        echo '<div class="section__content">';
    });


    /**
     * Close the section content wrapper.
     *
     * Outputs the closing </div> tag.
     */
    add_action('theme_section_content_close', function () {
        echo '</div>';
    });


    /**
     * Open a card wrapper.
     *
     * Outputs the opening <article> tag for a card with optional classes.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int    $post_id Post ID.
     *     @type string $classes Additional classes for the card.
     * }
     */
    add_action('theme_card_open', function ($args = []) {
        $post_id  = !empty($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
        $classes  = !empty($args['classes']) ? esc_attr($args['classes']) : 'card';
        $data_aos = !empty($args['data_aos']) ? esc_attr($args['data_aos']) : 'fade-up';

        echo "<article id=\"{$post_id}\" class=\"{$classes}\" data-aos=\"{$data_aos}\">";
    }, 10, 1);


    /**
     * Close the card wrapper.
     */
    add_action('theme_card_close', function () {
        echo '</article>';
    });


    /**
     * Open the card link wrapper.
     *
     * Outputs the <a> tag linking to the post.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int $post_id Post ID.
     * }
     */
    add_action('theme_card_link_open', function ($args = []) {
        $post_id = $args['post_id'] ?? get_the_ID();
        echo '<a href="' . esc_url(get_permalink($post_id)) . '" class="card__link">';
    }, 10, 1);


    /**
     * Close the card link wrapper.
     */
    add_action('theme_card_link_close', function () {
        echo '</a>';
    });


    /**
     * Open the card content wrapper.
     */
    add_action('theme_card_content_open', function () {
        echo '<div class="card__content">';
    });


    /**
     * Close the card content wrapper.
     */
    add_action('theme_card_content_close', function () {
        echo '</div>';
    });


    /**
     * Render the card header image (featured or placeholder).
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type int    $image_id Attachment ID.
     *     @type string $alt_text Alt text for the image.
     * }
     */
    add_action('theme_card_header', function ($args = []) {

        $image_id = $args['image_id'] ?? 0;
        $alt_text = $args['alt_text'] ?? '';

        // Nothing to show
        if (!$image_id && (!defined('PLACEHOLDER_IMG_SRC') || !PLACEHOLDER_IMG_SRC)) {
            return;
        }

        echo '<div class="card__header">';
        echo '<div class="card__image-wrapper">';

        if ($image_id) {
            echo wp_get_attachment_image(
                $image_id,
                'medium_large',
                false,
                [
                    'class'   => 'card__image',
                    'alt'     => esc_attr($alt_text),
                    'loading' => 'lazy',
                ]
            );
        } else {
            echo '<img width="150" height="150" src="' . esc_url(PLACEHOLDER_IMG_SRC) . '" alt="" class="card__image card__image--placeholder" loading="lazy">';
        }

        echo '</div>';
        echo '</div>';

    }, 10, 1);


    /**
     * Render the card title.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $card_title Card title.
     * }
     */
    add_action('theme_card_title', function ($args = []) {
        $card_title = $args['card_title'] ?? '';
        if (!empty($card_title)) {
            echo '<h3 class="card__title">' . esc_html($card_title) . '</h3>';
        }
    }, 10, 1);


    /**
     * Render the card description/lead.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type string $card_description Card description.
     * }
     */
    add_action('theme_card_description', function ($args = []) {
        $card_description = $args['card_description'] ?? '';
        if (!empty($card_description)) {
            echo '<div class="card__lead">' . wp_kses_post($card_description) . '</div>';
        }
    }, 10, 1);


    /**
     * Render the card button.
     *
     * @param array $args {
     *     Optional. Array of arguments.
     *
     *     @type array $card_button Card button array with keys: url, title, target.
     * }
     */
    add_action('theme_card_button', function ($args = []) {
        $card_button = $args['card_button'] ?? [];
        $button_url = $card_button['url'] ?? '';
        $button_title = $card_button['title'] ?? esc_url($button_url);
        $button_target = !empty($card_button['target']) ? $card_button['target'] : '_self';

        if ($button_url) {
            echo '<a href="' . esc_url($button_url) . '" target="' . esc_attr($button_target) . '" class="card__link btn btn-primary">' . esc_html($button_title) . '</a>';
        }
    }, 10, 1);
