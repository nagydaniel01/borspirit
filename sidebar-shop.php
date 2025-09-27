<div id="toolbar-product" class="toolbar toolbar--products">
    <input type="search" name="keyword" id="keyword" placeholder="<?php echo esc_html__('Search...', TEXT_DOMAIN); ?>" class="form-control search js-search">
</div>

<div class="filters filters--products">
    <div class="filters__header">
        <h2 class="filters__title"><?php echo esc_html__('Filter', TEXT_DOMAIN); ?></h2>
    </div>

    <div id="filter-list-vertical" class="page__filters-inner js-filter-list">
        <?php get_template_part('template-parts/queries/query', 'product-attributes'); ?>
    </div>

    <div class="filters__footer">
        <a class="button button--reset js-filters-reset"><?php echo esc_html__('Reset', TEXT_DOMAIN); ?></a>
    </div>
</div>

<div class="content content--products">
    <div id="product-list" class="list list--products" data-posts-per-page="4">
        <?php get_template_part( 'template-parts/queries/query', 'product' ); ?>
    </div>
</div>