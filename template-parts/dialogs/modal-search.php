<div class="modal modal--alt fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel"><?php echo esc_html__('Search for:', 'borspirit'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', 'borspirit'); ?>"></button>
            </div>
            <div class="modal-body">
                <?php
                    if ( class_exists( 'WooCommerce' ) ) {
                        // WooCommerce product search form
                        get_product_search_form();
                    } else {
                        // Default WordPress search form
                        get_search_form();
                    }
                ?>
            </div>
        </div>
    </div>
</div>
