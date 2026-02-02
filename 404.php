<?php
/**
 * Template Name: Page 404
 */
?>

<?php get_header(); ?>

<?php
    $page_404 = get_pages(
        array(
            'meta_key' => '_wp_page_template',
            'meta_value' => '404.php'
        )
    );
    $page_id = $page_404[0]->ID ?? null;
    $page_title = $page_id ? get_the_title($page_id) : __( 'Oops! Page not found', 'borspirit' );
    $page_content = $page_id ? get_the_content(null, false, $page_id) : '';

    // Fallback content if page content is empty
    if ( empty( $page_content ) ) {
        $fallback_text = __( 'The page you are looking for does not exist. It might have been moved or deleted.', 'borspirit' ) . "\n\n";
        $fallback_text .= __( 'Try using the navigation menu or go back to the homepage.', 'borspirit' );
        $page_content = wpautop( $fallback_text );
    }

    $back_url = wp_get_referer() ?: home_url();
?>

<main class="page page--404">
    <div class="container">
        <div class="page__inner text-center">
            <?php if ($page_title) : ?>
                <h1 class="page__title"><?php echo $page_title; ?></h1>
            <?php endif; ?>
    
            <?php if ($page_content) : ?>
                <div class="page__content">
                    <?php echo $page_content; ?>
                </div>
            <?php endif; ?>
    
            <a href="<?php echo esc_url( trailingslashit( $back_url ) ); ?>" class="btn btn-outline-primary btn-lg page__button">
                <?php echo esc_html__( 'Back to previous page', 'borspirit' ); ?>
            </a>
        </div>
    </div>
</main>

<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>
