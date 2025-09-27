<div class="modal modal--age-gate fade" id="ageGateModal" tabindex="-1" aria-labelledby="ageGateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ageGateModalLabel"><?php echo esc_html__('Age Verification', TEXT_DOMAIN); ?></h5>
            </div>
            <div class="modal-body">
                <p>
                    <?php
                        /* translators: %s: minimum age required */
                        printf(
                            esc_html__('You must be at least %s years old to enter this site.', TEXT_DOMAIN),
                            esc_html(get_option('ag_min_age', 18))
                        );
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="age-yes" class="btn btn-success me-2"><?php echo esc_html__('I am old enough', TEXT_DOMAIN); ?></button>
                <button type="button" id="age-no" class="btn btn-danger">
                    <?php
                        /* translators: %s: minimum age required */
                        printf(
                            esc_html__('I am under %s', TEXT_DOMAIN),
                            esc_html(get_option('ag_min_age', 18))
                        );
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>
