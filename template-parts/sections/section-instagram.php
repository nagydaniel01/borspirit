<?php
    $section_title  = $section['instagram_section_title'] ?? '';
    $section_slug   = sanitize_title($section_title);
    $section_lead   = $section['instagram_section_lead'] ?? '';

    // Configuration
    $client_id                  = '4215439642034414';
    $client_secret              = '56392b9ad5a0ad07ddaaf79b881748b4';
    $redirect_uri               = 'https://lindamestyle.test/instagram/';
    $theme_directory            = get_template_directory();
    $token_file                 = $theme_directory . '/instagram_access_token.json';

    //$instagram_user_data = instagram_get_user_data($client_id, $client_secret, $redirect_uri, $token_file);
    $instagram             = instagram_get_all_media($client_id, $client_secret, $redirect_uri, $token_file);
?>

<?php if (!empty($instagram)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--instagram">
        <div class="container">
            <?php if ($section_title || $section_lead) : ?>
                <div class="section__header">
                    <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="section__content">
                <?php
                    dump($instagram);
                ?>
                Instagram app name
                LindaMeStyle-IG

                Instagram app ID
                4215439642034414
                
                Instagram app secret
                56392b9ad5a0ad07ddaaf79b881748b4

                Access tokens
                IGAA7564ZAvgO5BZAE90MDBDY0JXUmsxVzc4MlNnbnQzaHFFUnROUFpNZAWdadVdxTnJLY21JY1lmMDFicDFqZAjRHLUtZANjNUR3dIWHJMM0N3bmFmdjNzT0dXUTBYQktfSTRUVkJhYy15MDc3ZATZAFREl6aGR5SnA1TVdqSEdxSkt2VQZDZD
            </div>
        </div>
    </section>
<?php endif; ?>
