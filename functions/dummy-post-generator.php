<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('dpg_register_menu')) {
    /**
     * Register the Dummy Post Generator submenu under Tools.
     *
     * @return void
     */
    function dpg_register_menu() {
        add_submenu_page(
            'tools.php',
            __('Dummy Post Generator', 'dummy-post-generator'), // Page title
            __('Dummy Post Generator', 'dummy-post-generator'), // Menu title
            'manage_options',                                   // Capability
            'dummy-post-generator',                             // Slug
            'dpg_admin_page'                                    // Callback function
        );
    }
    add_action('admin_menu', 'dpg_register_menu');
}

if (!function_exists('dpg_admin_page')) {
    /**
     * Render the admin settings page for dummy post and image generation.
     *
     * @return void
     */
    function dpg_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Dummy Post Generator', 'dummy-post-generator'); ?></h1>

            <!-- Section: Generate Dummy Posts -->
            <form method="post">
                <?php wp_nonce_field('dpg_generate_nonce', 'dpg_generate_nonce_field'); ?>
                <h2><?php echo esc_html__('Generate Dummy Posts', 'dummy-post-generator'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="dpg_post_type"><?php echo esc_html__('Select Post Type', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <select name="dpg_post_type" id="dpg_post_type">
                                <?php
                                $post_types = get_post_types(['public' => true], 'objects');
                                foreach ($post_types as $type) {
                                    echo '<option value="' . esc_attr($type->name) . '">' . esc_html($type->labels->singular_name) . ' (' . esc_html($type->name) . ')</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="dpg_number"><?php echo esc_html__('Number of Posts', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_number" id="dpg_number" value="5" min="1" max="100">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="dpg_post_image_width"><?php echo esc_html__('Featured Image Width (px)', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_post_image_width" id="dpg_post_image_width" value="1280" min="300" max="3840">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="dpg_post_image_height"><?php echo esc_html__('Featured Image Height (px)', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_post_image_height" id="dpg_post_image_height" value="720" min="300" max="2160">
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Generate Dummy Posts', 'dummy-post-generator')); ?>
            </form>

            <!-- Section: Upload Dummy Images -->
            <form method="post">
                <?php wp_nonce_field('dpg_images_nonce', 'dpg_images_nonce_field'); ?>
                <h2><?php echo esc_html__('Upload Dummy Images', 'dummy-post-generator'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="dpg_image_count"><?php echo esc_html__('Number of Images', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_image_count" id="dpg_image_count" value="5" min="1" max="100">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="dpg_image_width"><?php echo esc_html__('Image Width (px)', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_image_width" id="dpg_image_width" value="1280" min="300" max="3840">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="dpg_image_height"><?php echo esc_html__('Image Height (px)', 'dummy-post-generator'); ?></label></th>
                        <td>
                            <input type="number" name="dpg_image_height" id="dpg_image_height" value="720" min="300" max="2160">
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Upload Dummy Images', 'dummy-post-generator')); ?>
            </form>
        </div>
        <?php

        // Handle post generation form
        if (isset($_POST['dpg_number'], $_POST['dpg_post_type'])) {
            if (!isset($_POST['dpg_generate_nonce_field']) || !wp_verify_nonce($_POST['dpg_generate_nonce_field'], 'dpg_generate_nonce')) {
                wp_die(__('Security check failed.', 'dummy-post-generator'));
            }

            $count     = intval($_POST['dpg_number']);
            $post_type = sanitize_text_field($_POST['dpg_post_type']);
            $width     = max(100, intval($_POST['dpg_post_image_width'] ?? 1280));
            $height    = max(100, intval($_POST['dpg_post_image_height'] ?? 720));

            $post_object   = get_post_type_object($post_type);
            $singular_name = $post_object ? $post_object->labels->singular_name : $post_type;

            dpg_generate_posts($count, $post_type, $width, $height);

            printf('<div class="updated"><p>%s</p></div>', esc_html(sprintf(
                _n('%1$s dummy %2$s created successfully with unique titles, content, and images!',
                   '%1$s dummy %2$s created successfully with unique titles, content, and images!',
                   $count,
                   'dummy-post-generator'),
                $count,
                $singular_name
            )));
        }

        // Handle image upload form
        if (isset($_POST['dpg_image_count'], $_POST['dpg_image_width'], $_POST['dpg_image_height'])) {
            if (!isset($_POST['dpg_images_nonce_field']) || !wp_verify_nonce($_POST['dpg_images_nonce_field'], 'dpg_images_nonce')) {
                wp_die(__('Security check failed.', 'dummy-post-generator'));
            }

            $count  = intval($_POST['dpg_image_count']);
            $width  = max(100, intval($_POST['dpg_image_width']));
            $height = max(100, intval($_POST['dpg_image_height']));

            $uploaded = dpg_upload_dummy_images($count, $width, $height);

            printf('<div class="updated"><p>%s</p></div>', esc_html(sprintf(
                _n('%1$s dummy image uploaded successfully!',
                   '%1$s dummy images uploaded successfully!',
                   $uploaded,
                   'dummy-post-generator'),
                $uploaded
            )));
        }
    }
}

if (!function_exists('dpg_generate_posts')) {
    /**
     * Generate dummy posts with unique title, lorem ipsum content,
     * and a random featured image from Picsum.
     *
     * @param int    $count     Number of posts to generate.
     * @param string $post_type Post type slug.
     * @param int    $width     Featured image width (px).
     * @param int    $height    Featured image height (px).
     *
     * @return void
     */
    function dpg_generate_posts($count, $post_type, $width = 1280, $height = 720) {
        for ($i = 1; $i <= $count; $i++) {
            $title   = dpg_generate_random_title($post_type, $i);
            $content = dpg_generate_random_content();

            $post_data = [
                'post_title'   => $title,
                'post_content' => $content,
                'post_type'    => $post_type,
                'post_status'  => 'publish',
            ];

            $post_id = wp_insert_post($post_data);

            if ($post_id && !is_wp_error($post_id)) {
                $image_url = sprintf('https://picsum.photos/%d/%d?random=%d', $width, $height, wp_rand(1, 9999));
                $image_id  = dpg_upload_image_from_url($image_url, $post_id);
                if ($image_id) {
                    set_post_thumbnail($post_id, $image_id);
                }
            } 
        }
    }
}

if (!function_exists('dpg_generate_random_title')) {
    /**
     * Generate a random title using NATO phonetic alphabet.
     *
     * @param string $post_type Post type slug.
     * @param int    $i         Counter number.
     *
     * @return string Generated title.
     */
    function dpg_generate_random_title($post_type, $i) {
        $nato_alphabet = [
            'Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo', 'Foxtrot', 'Golf', 'Hotel',
            'India', 'Juliet', 'Kilo', 'Lima', 'Mike', 'November', 'Oscar', 'Papa',
            'Quebec', 'Romeo', 'Sierra', 'Tango', 'Uniform', 'Victor', 'Whiskey',
            'Xray', 'Yankee', 'Zulu'
        ];

        $post_type_obj = get_post_type_object($post_type);
        $singular_name = $post_type_obj && !empty($post_type_obj->labels->singular_name)
            ? $post_type_obj->labels->singular_name
            : ucfirst($post_type);

        $word = $nato_alphabet[($i - 1) % count($nato_alphabet)];

        return sprintf('%s %s %d', $word, $singular_name, $i);
    }
}

if (!function_exists('dpg_generate_random_content')) {
    /**
     * Generate random lorem ipsum-like content with multiple paragraphs.
     *
     * @return string HTML-formatted random content.
     */
    function dpg_generate_random_content() {
        $lorem_sentences = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Curabitur pretium tincidunt lacus.',
            'Nullam varius, turpis et commodo pharetra.',
            'Sed convallis magna eu sem.',
            'Donec sollicitudin molestie malesuada.',
            'Vestibulum ac diam sit amet quam vehicula elementum.',
            'Integer posuere erat a ante venenatis dapibus.',
            'Donec ullamcorper nulla non metus auctor fringilla.',
            'Cras mattis consectetur purus sit amet fermentum.',
            'Vivamus sagittis lacus vel augue laoreet rutrum.'
        ];

        $content = '';
        $num_paragraphs = rand(2, 4);

        for ($p = 0; $p < $num_paragraphs; $p++) {
            shuffle($lorem_sentences);
            $content .= '<p>' . implode(' ', array_slice($lorem_sentences, 0, rand(3, 5))) . '</p>';
        }

        return $content;
    }
}

if (!function_exists('dpg_upload_image_from_url')) {
    /**
     * Upload an image from a remote URL and attach it to a post (or library if post_id=0).
     *
     * @param string $image_url Remote image URL.
     * @param int    $post_id   Post ID to attach image to, or 0 for unattached.
     *
     * @return int|false Attachment ID on success, false on failure.
     */
    function dpg_upload_image_from_url($image_url, $post_id = 0) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            return false;
        }

        preg_match('/[^\?]+\.(jpg|jpeg|png|gif)/i', $image_url, $matches);
        $file_array = [
            'name'     => !empty($matches[0]) ? basename($matches[0]) : 'dummy-image.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $post_id);

        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        return $id;
    }
}

if (!function_exists('dpg_upload_dummy_images')) {
    /**
     * Upload multiple dummy images to the media library.
     *
     * @param int $count  Number of images to upload.
     * @param int $width  Image width (px).
     * @param int $height Image height (px).
     *
     * @return int Number of successfully uploaded images.
     */
    function dpg_upload_dummy_images($count, $width, $height) {
        $success = 0;
        for ($i = 1; $i <= $count; $i++) {
            $url = sprintf('https://picsum.photos/%d/%d?random=%d', $width, $height, wp_rand(1, 9999));
            $id = dpg_upload_image_from_url($url, 0);
            if ($id) {
                $success++;
            }
        }
        return $success;
    }
}
