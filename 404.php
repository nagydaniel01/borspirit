<?php
/**
 * Template Name: Page 404
 */
get_header();
?>

<?php
    $page_404 = get_pages(
        array(
            'meta_key' => '_wp_page_template',
            'meta_value' => '404.php'
        )
    );
    $page_id = $page_404[0]->ID ?? null;
    $page_title = get_the_title($page_id);
    $page_content = get_the_content(null, false, $page_id);
?>

<main class="page page--404">
    <div class="container">
        <div class="page__inner">
            <?php if ($page_title) : ?>
                <h1 class="page__title"><?php echo $page_title; ?></h1>
            <?php endif; ?>
    
            <?php if ($page_content) : ?>
                <div class="page__content">
                    <?php echo $page_content; ?>
                </div>
            <?php endif; ?>
    
            <a href="<?php echo esc_url( trailingslashit( home_url() ) ); ?>" class="btn btn-secondary btn-lg page__button"><?php _e('Vissza a főoldalra', TEXT_DOMAIN); ?></a>
        </div>
    </div>
</main>

<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>