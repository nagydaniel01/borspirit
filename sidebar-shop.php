<nav class="section__navigation navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasProductFilter" aria-controls="offcanvasProductFilter">
        <?php echo esc_html__( 'Filter', 'borspirit' ); ?>
        <svg class="icon icon-filter">
            <use xlink:href="#icon-filter"></use>
        </svg>
    </button>
    <div class="offcanvas offcanvas-start" id="offcanvasProductFilter" tabindex="-1" aria-labelledby="offcanvasProductFilterLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasProductFilterLabel">
                <?php echo esc_html__( 'Filter', 'borspirit' ); ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo esc_attr('Close', 'borspirit'); ?>"></button>
        </div>
        <div class="offcanvas-body">
            <?php echo do_shortcode( '[yith_wcan_filters slug="default-preset"]' ); ?>
        </div>
    </div>
</nav>