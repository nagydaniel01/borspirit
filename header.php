<?php
    $favicon = get_field('favicon', 'option');
    $type    = null;
    
    if ( $favicon && !empty($favicon['url']) ) {
        $filetype = wp_check_filetype( $favicon['url'] );
        $type     = isset($filetype['type']) ? $filetype['type'] : null;
    }
    
    $theme_color = get_field('theme_color', 'option');
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <?php if( $theme_color ): ?>
        <meta name="msapplication-TileColor" content="<?php echo esc_attr($theme_color); ?>">
        <meta name="theme-color" content="<?php echo esc_attr($theme_color); ?>">
    <?php endif; ?>
    <?php if( $favicon && isset($favicon['url']) ): ?>
        <link rel="icon" href="<?php echo esc_url($favicon['url']); ?>" type="<?php echo esc_attr($type); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div class="symbols d-none">
        <?php get_template_part('assets/dist/php/sprites', ''); ?>
    </div>
    
    <?php get_template_part('template-parts/global/header', ''); ?>