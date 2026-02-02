<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, shrink-to-fit=no">
    <?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div class="symbols d-none">
        <?php get_template_part('assets/dist/php/sprites', ''); ?>
    </div>
    
    <?php get_template_part('template-parts/global/header', ''); ?>