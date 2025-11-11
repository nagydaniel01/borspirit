<form id="prq_quiz_form" class="form bsp-quiz-form" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <div id="bsp-quiz-steps">
        <!-- Step 1 -->
        <div class="bsp-step" data-step="1">
            <div class="mb-3">
                <label class="form-label"><strong><?php echo esc_html__('1. Milyen jellegű bort kedvelsz?', 'bsp-wine-quiz'); ?></strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q1" id="light" value="light">
                    <label class="form-check-label" for="light"><?php echo esc_html__('Könnyű, friss (pl. rozé, könnyű fehér)', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q1" id="medium" value="medium">
                    <label class="form-check-label" for="medium"><?php echo esc_html__('Közepes test (pl. Chardonnay, Merlot)', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q1" id="full" value="full">
                    <label class="form-check-label" for="full"><?php echo esc_html__('Teljes test, testes vörös (pl. Cabernet, Syrah)', 'bsp-wine-quiz'); ?></label>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bsp-step" data-step="2" style="display:none;">
            <div class="mb-3">
                <label class="form-label"><strong><?php echo esc_html__('2. Milyen ízvilágot szeretsz?', 'bsp-wine-quiz'); ?></strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q2" id="dry" value="dry">
                    <label class="form-check-label" for="dry"><?php echo esc_html__('Száraz', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q2" id="offdry" value="offdry">
                    <label class="form-check-label" for="offdry"><?php echo esc_html__('Félszáraz', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q2" id="sweet" value="sweet">
                    <label class="form-check-label" for="sweet"><?php echo esc_html__('Édes', 'bsp-wine-quiz'); ?></label>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bsp-step" data-step="3" style="display:none;">
            <div class="mb-3">
                <label class="form-label"><strong><?php echo esc_html__('3. Milyen alkalomra szeretnéd?', 'bsp-wine-quiz'); ?></strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q3" id="everyday" value="everyday">
                    <label class="form-check-label" for="everyday"><?php echo esc_html__('Mindennapi fogyasztás', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q3" id="dinner" value="dinner">
                    <label class="form-check-label" for="dinner"><?php echo esc_html__('Vacsorához / párosításhoz', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q3" id="gift" value="gift">
                    <label class="form-check-label" for="gift"><?php echo esc_html__('Ajándék / különleges alkalom', 'bsp-wine-quiz'); ?></label>
                </div>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="bsp-step" data-step="4" style="display:none;">
            <div class="mb-3">
                <label class="form-label"><strong><?php echo esc_html__('4. Regionális preferencia?', 'bsp-wine-quiz'); ?></strong></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q4" id="hungary" value="hungary">
                    <label class="form-check-label" for="hungary"><?php echo esc_html__('Magyar', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q4" id="europe" value="europe">
                    <label class="form-check-label" for="europe"><?php echo esc_html__('Európa (pl. Franciaország, Olaszország)', 'bsp-wine-quiz'); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="q4" id="newworld" value="newworld">
                    <label class="form-check-label" for="newworld"><?php echo esc_html__('Újvilág (pl. Ausztrália, Chile)', 'bsp-wine-quiz'); ?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="bsp-nav">
        <button id="bsp-prev" class="btn btn-outline-primary" style="display:none;"><?php echo esc_html__('Vissza', 'bsp-wine-quiz'); ?></button>
        <button id="bsp-next" class="btn btn-primary"><?php echo esc_html__('Tovább', 'bsp-wine-quiz'); ?></button>
    </div>

    <div id="bsp-result" style="display:none; margin-top:20px;"></div>
</form>
