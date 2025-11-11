<div class="modal modal--age-gate fade" id="ageGateModal" tabindex="-1" aria-labelledby="ageGateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ageGateModalLabel">
                    <?php
                        printf(
                            esc_html( get_option( 'ag_modal_title', __( 'Are you over %s years of age?', 'borspirit' ) ) ),
                            esc_html( get_option( 'ag_min_age', 18 ) )
                        );
                    ?>
                </h5>
            </div>
            <div class="modal-body">
                <?php
                    echo wpautop( sprintf(
                        get_option( 'ag_modal_content', __( 'We are committed advocates and supporters of responsible, civilized drinking. Therefore, we do not recommend the consumption of alcoholic beverages to persons under the age of %s and cannot serve them.', 'borspirit' ) ),
                        esc_html( get_option( 'ag_min_age', 18 ) )
                    ) );
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="age-yes" class="btn btn-success me-2">
                    <?php echo esc_html( get_option( 'ag_modal_btn_yes', __( 'I am old enough', 'borspirit' ) ) ); ?>
                </button>
                <button type="button" id="age-no" class="btn btn-danger">
                    <?php
                        printf(
                            esc_html( get_option( 'ag_modal_btn_no', __( 'I am under %s', 'borspirit' ) ) ),
                            esc_html( get_option( 'ag_min_age', 18 ) )
                        );
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>