<?php 
    $extra_classes = '';
    
    // Determine extra classes for WooCommerce pages
    if ( class_exists( 'WooCommerce' ) ) {
        // Add general WooCommerce class if any specific page matched
        $extra_classes = ' page--woocommerce';

        switch ( true ) {
            case is_shop():
                $extra_classes .= ' page--shop';
                break;
            case is_product():
                $extra_classes .= ' page--product';
                break;
            case is_cart():
                $extra_classes .= ' page--cart';
                break;
            case is_checkout():
                $extra_classes .= ' page--checkout';
                break;
            case is_account_page():
                $extra_classes .= ' page--account';
                break;
        }
    }

    // Add login state class
    $extra_classes .= is_user_logged_in() ? ' user-logged-in' : ' user-logged-out';
?>

<?php get_header(); ?>

<main class="page page--default<?php echo esc_attr($extra_classes); ?>">
    <section class="section section--default">
        <div class="container">
            <header class="page__header">
                <h1 class="page__title"><?php the_title(); ?></h1>
            </header>
            <div class="page__content">
                <?php the_content(); ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>