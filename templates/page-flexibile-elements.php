<?php 
/**
 * Template Name: Flexibile Elements Template
 */
?>

<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <main class="page<?php echo is_front_page() ? ' page--home' : ''; ?>">
            <?php get_template_part('template-parts/flexibile-elements'); ?>
        </main>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
