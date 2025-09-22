<?php

defined( 'ABSPATH' ) || exit;

global $product;

?>

<!--<h2><?php //echo esc_html(get_query_var('tab_title')); ?></h2>-->

<?php
// Get all FAQ posts
$faq_posts = get_posts(array(
    'post_type' => 'faq',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC'
));
?>

<?php if ( !empty($faq_posts) ) : ?>
    <div class="accordion" id="faqAccordion">
        <?php foreach ($faq_posts as $index => $faq) : ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $index; ?>">
                        <?php echo esc_html($faq->post_title); ?>
                    </button>
                </h2>
                <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $index; ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?php echo wp_kses_post( apply_filters('the_content', $faq->post_content) ); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <?php echo wpautop( __( 'No FAQs found.', TEXT_DOMAIN ) ); ?>
<?php endif; ?>
