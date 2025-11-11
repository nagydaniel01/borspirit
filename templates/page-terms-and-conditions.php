<?php
/**
 * Template Name: Terms and Conditions Template
 */
?>

<?php get_header(); ?>

<?php
    $page_id = TERMS_PAGE_ID;
    $page = get_post($page_id);

    if ($page) {
        $title = apply_filters('the_title', $page->post_title);
        $content = apply_filters('the_content', $page->post_content);
    }
?>

<main class="page page--default">
    <section class="section section--default">
        <div class="container">
            <header class="page__header">
                <h1 class="page__title"><?php echo esc_html($title ?? __('Terms and Conditions', 'borspirit')); ?></h1>
            </header>
            <div class="page__content">
                <?php echo $content ?? wpautop( esc_html__('No content found.', 'borspirit') ); ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
