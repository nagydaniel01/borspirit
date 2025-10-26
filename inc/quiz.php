<?php
/**
 * BorSpirit Wine Quiz - Theme integration (English UI)
 *
 * Place this file in your theme (e.g. /inc/wine-quiz.php) and include it from functions.php:
 *   require_once get_template_directory() . '/inc/wine-quiz.php';
 *
 * Text domain: 'borspirit'
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'BorSpirit_Wine_Quiz' ) ) :

class BorSpirit_Wine_Quiz {

    const OPTION_RULES_KEY     = 'bsp_quiz_rules_v1';
    const OPTION_QUESTIONS_KEY = 'bsp_quiz_questions_v1';
    private $td = 'borspirit';
    private $max_questions = 4; // keep compatibility with q1..q4

    public function __construct() {
        add_shortcode( 'borspirit_wine_quiz', array( $this, 'render_quiz' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // AJAX
        add_action( 'wp_ajax_bsp_recommend', array( $this, 'handle_recommend' ) );
        add_action( 'wp_ajax_nopriv_bsp_recommend', array( $this, 'handle_recommend' ) );
        add_action( 'wp_ajax_bsp_add_to_cart', array( $this, 'handle_add_to_cart' ) );
        add_action( 'wp_ajax_nopriv_bsp_add_to_cart', array( $this, 'handle_add_to_cart' ) );

        // Admin: menu and posts
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        // Rules handlers (existing)
        add_action( 'admin_post_bsp_save_rule', array( $this, 'admin_save_rule' ) );
        add_action( 'admin_post_bsp_delete_rule', array( $this, 'admin_delete_rule' ) );

        // Questions handlers (new)
        add_action( 'admin_post_bsp_save_question', array( $this, 'admin_save_question' ) );
        add_action( 'admin_post_bsp_delete_question', array( $this, 'admin_delete_question' ) );
    }

    /* -------------------------- Public UI -------------------------- */
    public function enqueue_scripts() {
        // Ensure your JS file exists at this location in the theme
        $script_path = get_template_directory_uri() . '/ajax/js/bsp_quiz_ajax.js';

        wp_register_script( 'bsp-quiz-js', $script_path, array( 'jquery' ), '1.3', true );
        wp_enqueue_script( 'bsp-quiz-js' );

        wp_localize_script( 'bsp-quiz-js', 'bsp_quiz_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bsp_quiz_nonce' ),
            'i18n'     => array(
                // Navigation
                'next'           => __( 'Next', $this->td ),
                'submit'         => __( 'See My Wine Match', $this->td ),
                'select_option'  => __( 'Please select an option to continue.', $this->td ),

                // Messages during request
                'finding_match'  => __( 'Curating your wine match…', $this->td ),
                'network_error'  => __( 'Network error. Please try again shortly.', $this->td ),
                'unexpected'     => __( 'An unexpected error occurred.', $this->td ),
                'try_again'      => __( 'Please try again.', $this->td ),

                // CTA labels
                'view_product'    => __( 'View Product', $this->td ),
                'view_collection' => __( 'Explore Collection', $this->td ),
                'add_to_cart'     => __( 'Add to Cart', $this->td ),

                // Cart responses
                'added_to_cart'  => __( 'The wine has been added to your cart.', $this->td ),
                'cart_error'     => __( 'Could not add product to cart.', $this->td ),
            )
        ) );
    }

    public function render_quiz( $atts ) {
        $td = $this->td;
        $questions = $this->get_questions();

        // If no questions configured, fallback to the old static setup
        if ( empty( $questions ) ) {
            // use the same defaults as earlier
            $questions = $this->default_questions();
        }

        ob_start(); ?>
        <div id="bsp-quiz-wrap" class="bsp-quiz">
            <!--<h2 class="mb-4"><?php //echo esc_html__( 'Discover the wine that matches your palate', $td ); ?></h2>-->

            <div id="bsp-quiz-steps">
                <?php foreach ( $questions as $index => $q ) :
                    $step_num = $index + 1;
                    $name = 'q' . $step_num;
                    $answers = isset( $q['answers'] ) && is_array( $q['answers'] ) ? $q['answers'] : array();
                ?>
                <div class="bsp-step mb-3" data-step="<?php echo esc_attr( $step_num ); ?>" <?php echo $step_num !== 1 ? 'style="display:none;"' : ''; ?>>
                    <p class="fw-bold"><?php echo esc_html( $step_num . '. ' . $q['text'] ); ?></p>
                    <?php if ( empty( $answers ) ) : ?>
                        <p class="text-muted"><em><?php echo esc_html__( 'No answers defined for this question (admin).', $td ); ?></em></p>
                    <?php else: foreach ( $answers as $a ) :
                        $val = isset( $a['value'] ) ? $a['value'] : '';
                        $label = isset( $a['label'] ) ? $a['label'] : $val;
                    ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>" id="<?php echo esc_attr($name . '-' . $val); ?>">
                        <label class="form-check-label" for="<?php echo esc_attr($name . '-' . $val); ?>">
                            <?php echo esc_html( $label ); ?>
                        </label>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button id="bsp-prev" class="btn btn-outline-primary" style="display:none;"><?php echo esc_html__( 'Back', $td ); ?></button>
                <button id="bsp-next" class="btn btn-primary"><?php echo esc_html__( 'Next', $td ); ?></button>
            </div>

            <div id="bsp-result" class="mt-4" style="display:none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /* -------------------------- Questions storage (Options API) -------------------------- */

    private function get_questions() {
        $questions = get_option( self::OPTION_QUESTIONS_KEY, array() );
        if ( ! is_array( $questions ) ) $questions = array();

        // ensure keys and limit to max_questions
        $questions = array_values( $questions ); // reindex
        return array_slice( $questions, 0, $this->max_questions );
    }

    private function default_questions() {
        return array(
            array(
                'key' => 'q1',
                'text' => __( 'Which style do you prefer?', $this->td ),
                'answers' => array(
                    array( 'value'=>'light', 'label'=>__( 'Light & fresh (e.g. rosé, light white)', $this->td ) ),
                    array( 'value'=>'medium', 'label'=>__( 'Medium-bodied (e.g. Chardonnay, Merlot)', $this->td ) ),
                    array( 'value'=>'full', 'label'=>__( 'Full-bodied red (e.g. Cabernet, Syrah)', $this->td ) ),
                ),
            ),
            array(
                'key' => 'q2',
                'text' => __( 'Which flavor profile do you favor?', $this->td ),
                'answers' => array(
                    array( 'value'=>'dry', 'label'=>__( 'Dry', $this->td ) ),
                    array( 'value'=>'offdry', 'label'=>__( 'Off-dry', $this->td ) ),
                    array( 'value'=>'sweet', 'label'=>__( 'Sweet', $this->td ) ),
                ),
            ),
            array(
                'key' => 'q3',
                'text' => __( 'For what occasion?', $this->td ),
                'answers' => array(
                    array( 'value'=>'everyday', 'label'=>__( 'Everyday enjoyment', $this->td ) ),
                    array( 'value'=>'dinner', 'label'=>__( 'Dinner / food pairing', $this->td ) ),
                    array( 'value'=>'gift', 'label'=>__( 'Gift / special occasion', $this->td ) ),
                ),
            ),
            array(
                'key' => 'q4',
                'text' => __( 'Regional preference?', $this->td ),
                'answers' => array(
                    array( 'value'=>'hungary', 'label'=>__( 'Hungary', $this->td ) ),
                    array( 'value'=>'europe', 'label'=>__( 'Europe (e.g. France, Italy)', $this->td ) ),
                    array( 'value'=>'newworld', 'label'=>__( 'New World (e.g. Australia, Chile)', $this->td ) ),
                ),
            ),
        );
    }

    public function admin_save_question() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Insufficient permissions.', $this->td ) );
        if ( ! isset( $_POST['bsp_admin_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['bsp_admin_nonce'] ), 'bsp_admin_nonce' ) ) wp_die( esc_html__( 'Invalid request.', $this->td ) );

        $index = isset( $_POST['edit_index'] ) ? intval( $_POST['edit_index'] ) : null;
        $text = isset( $_POST['question_text'] ) ? sanitize_text_field( wp_unslash( $_POST['question_text'] ) ) : '';
        $answers_in = isset( $_POST['answers'] ) && is_array( $_POST['answers'] ) ? $_POST['answers'] : array();

        $answers = array();
        foreach ( $answers_in as $a ) {
            $val = isset( $a['value'] ) ? sanitize_text_field( wp_unslash( $a['value'] ) ) : '';
            $label = isset( $a['label'] ) ? sanitize_text_field( wp_unslash( $a['label'] ) ) : $val;
            if ( $val !== '' ) {
                $answers[] = array( 'value' => $val, 'label' => $label );
            }
        }

        // load existing
        $questions = $this->get_questions();

        if ( $index !== null && isset( $questions[ $index ] ) ) {
            // update
            $questions[ $index ]['text'] = $text;
            $questions[ $index ]['answers'] = $answers;
        } else {
            // append if space
            if ( count( $questions ) < $this->max_questions ) {
                $questions[] = array(
                    'key' => 'q' . ( count( $questions ) + 1 ),
                    'text' => $text,
                    'answers' => $answers,
                );
            } else {
                // replace the last slot if exceed (defensive)
                $questions[ $this->max_questions - 1 ] = array(
                    'key' => 'q' . $this->max_questions,
                    'text' => $text,
                    'answers' => $answers,
                );
            }
        }

        update_option( self::OPTION_QUESTIONS_KEY, $questions );

        wp_redirect( admin_url( 'admin.php?page=borspirit-quiz' ) );
        exit;
    }

    public function admin_delete_question() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Insufficient permissions.', $this->td ) );
        if ( ! isset( $_POST['bsp_admin_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['bsp_admin_nonce'] ), 'bsp_admin_nonce' ) ) wp_die( esc_html__( 'Invalid request.', $this->td ) );

        $idx = isset( $_POST['index'] ) ? intval( $_POST['index'] ) : -1;
        $questions = $this->get_questions();
        if ( isset( $questions[ $idx ] ) ) {
            array_splice( $questions, $idx, 1 );
            // rekey q1..qN
            foreach ( $questions as $k => &$q ) {
                $q['key'] = 'q' . ( $k + 1 );
            }
            update_option( self::OPTION_QUESTIONS_KEY, $questions );
        }

        wp_redirect( admin_url( 'admin.php?page=borspirit-quiz' ) );
        exit;
    }

    /* -------------------------- Recommendation logic (unchanged) -------------------------- */
    private function get_rules() {
        $rules = get_option( self::OPTION_RULES_KEY, array() );
        if ( ! is_array( $rules ) ) $rules = array();
        return $rules;
    }

    private function compute_recommendation( $answers ) {
        $rules = $this->get_rules();
        $scored = [];

        foreach ( $rules as $idx => $rule ) {
            $score = 0;
            if ( empty( $rule['conditions'] ) || ! is_array( $rule['conditions'] ) ) continue;

            foreach ( $rule['conditions'] as $q_key => $expected ) {
                if ( ! isset( $answers[ $q_key ] ) || empty( $expected ) ) continue;

                // Handle multiple selections per question
                $user_answer = is_array($answers[ $q_key ]) ? $answers[ $q_key ] : [$answers[ $q_key ]];
                $expected_vals = is_array($expected) ? $expected : [$expected];

                if ( array_intersect($user_answer, $expected_vals) ) {
                    $score += intval( $rule['weight'] );
                }
            }

            if ( $score > 0 ) {
                $scored[] = [
                    'rule'  => $rule,
                    'score' => $score,
                    'index' => $idx,
                ];
            }
        }

        // Sort by score and priority
        usort( $scored, function( $a, $b ) {
            if ( $a['score'] === $b['score'] ) {
                $pa = isset( $a['rule']['priority'] ) ? intval( $a['rule']['priority'] ) : 0;
                $pb = isset( $b['rule']['priority'] ) ? intval( $b['rule']['priority'] ) : 0;
                return $pb - $pa;
            }
            return $b['score'] - $a['score'];
        });

        if ( ! empty( $scored ) ) {
            return $scored[0]['rule'];
        }

        // Fallback: pick first matching type if any question has an answer
        foreach ( $answers as $q => $val ) {
            if ( $val !== '' ) {
                return [
                    'type'  => 'category',
                    'value' => 'selection',
                    'name'  => __( 'Curated selection (fallback)', $this->td ),
                ];
            }
        }

        // Default fallback
        return [
            'type'  => 'category',
            'value' => 'selection',
            'name'  => __( 'Curated selection (default fallback)', $this->td ),
        ];
    }

    public function handle_recommend() {
        try {
            check_ajax_referer('bsp_quiz_nonce', 'nonce');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                wp_send_json_error(['message' => __('Invalid request method.', $this->td)], 405);
            }

            // Collect answers
            $answers = [];
            $questions = $this->get_questions();
            $count = count($questions) ?: $this->max_questions;

            for ($i = 1; $i <= $count; $i++) {
                $k = 'q' . $i;
                $answers[$k] = isset($_POST[$k]) ? sanitize_text_field(wp_unslash($_POST[$k])) : '';
            }

            $rule = $this->compute_recommendation($answers);

            if (empty($rule) || !isset($rule['type'])) {
                wp_send_json_error(['message' => __('No recommendation could be computed.', $this->td)], 422);
            }

            switch ($rule['type']) {

                case 'product':
                    $product_ids = is_array($rule['value']) ? $rule['value'] : array_map('trim', explode(',', $rule['value']));
                    $products = array_filter(array_map(fn($id) => wc_get_product(intval($id)), $product_ids));

                    if (empty($products)) {
                        wp_send_json_error(['message' => __('No valid products found.', $this->td)], 404);
                    }

                    ob_start();
                    $columns = apply_filters('loop_shop_columns', 4);
                    echo '<ul class="products columns-' . esc_attr($columns) . '">';
                    foreach ($products as $product) {
                        $GLOBALS['post'] = get_post($product->get_id());
                        setup_postdata($GLOBALS['post']);
                        wc_get_template_part('content', 'product');
                    }
                    echo '</ul>';
                    wp_reset_postdata();

                    wp_send_json_success([
                        'type' => 'product',
                        'html' => ob_get_clean(),
                    ]);
                    break;

                case 'category':
                    $cat = get_term_by('slug', $rule['value'], 'product_cat');
                    if (!$cat && is_numeric($rule['value'])) {
                        $cat = get_term(intval($rule['value']), 'product_cat');
                    }

                    if (!$cat || is_wp_error($cat)) {
                        wp_send_json_error(['message' => __('Recommended category not found.', $this->td)], 404);
                    }

                    wp_send_json_success([
                        'type' => 'redirect',
                        'url'  => get_term_link($cat),
                    ]);
                    break;

                // In handle_recommend(), add a new case:
                case 'attribute':
                    // Expected format: value = "pa_country:hungary" (taxonomy:term)
                    if ( empty( $rule['value'] ) || strpos( $rule['value'], ':' ) === false ) {
                        wp_send_json_error(['message' => __('Invalid attribute rule value.', $this->td)], 422);
                    }
                    list( $taxonomy, $term_slug ) = explode(':', $rule['value'], 2);

                    if ( ! taxonomy_exists( $taxonomy ) ) {
                        wp_send_json_error(['message' => __('Attribute taxonomy not found.', $this->td)], 404);
                    }

                    $term = get_term_by('slug', $term_slug, $taxonomy);
                    if ( ! $term || is_wp_error( $term ) ) {
                        wp_send_json_error(['message' => __('Term not found.', $this->td)], 404);
                    }

                    wp_send_json_success([
                        'type' => 'redirect',
                        'url'  => get_term_link($term),
                    ]);
                    break;

                default:
                    wp_send_json_error(['message' => __('Unexpected recommendation type.', $this->td)], 422);
            }

        } catch (Exception $e) {
            wp_send_json_error(['message' => sprintf(__('Unexpected error: %s', $this->td), $e->getMessage())], 500);
        }

        wp_die();
    }
    
    public function handle_add_to_cart() {
        check_ajax_referer( 'bsp_quiz_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        if ( ! $product_id ) wp_send_json_error( __( 'Invalid product.', $this->td ) );

        if ( function_exists( 'WC' ) && WC()->cart && WC()->cart->add_to_cart( $product_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Product added to cart.', $this->td ) ) );
        } else {
            wp_send_json_error( __( 'Could not add product to cart.', $this->td ) );
        }

        wp_die();
    }

    /* -------------------------- Admin UI (questions + rules) -------------------------- */

    public function register_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'BorSpirit Quiz Settings', $this->td ),
            __( 'BorSpirit Quiz', $this->td ),
            'manage_options',
            'borspirit-quiz',
            array( $this, 'render_admin_page' )
        );
    }

    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Insufficient permissions.', $this->td ) );

        $rules = $this->get_rules();
        $questions = $this->get_questions();
        $nonce = wp_create_nonce( 'bsp_admin_nonce' );

        // check for edit query parameters for questions and rules
        $edit_q_index = isset( $_GET['edit_q'] ) ? intval( $_GET['edit_q'] ) : -1;
        $edit_r_index = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : -1;
        $edit_q = ( $edit_q_index >= 0 && isset( $questions[ $edit_q_index ] ) ) ? $questions[ $edit_q_index ] : null;
        $edit_r = ( $edit_r_index >= 0 && isset( $rules[ $edit_r_index ] ) ) ? $rules[ $edit_r_index ] : null;
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'BorSpirit - Quiz Settings', $this->td ); ?></h1>

            <h2><?php echo $edit_q ? esc_html__( 'Edit Question', $this->td ) : esc_html__( 'Add New Question', $this->td ); ?></h2>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="bsp_save_question">
                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                <?php if ( $edit_q ): ?>
                    <input type="hidden" name="edit_index" value="<?php echo esc_attr( $edit_q_index ); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Question text', $this->td ); ?></label></th>
                        <td><input type="text" name="question_text" class="regular-text" required value="<?php echo $edit_q ? esc_attr( $edit_q['text'] ) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Answers', $this->td ); ?></label></th>
                        <td>
                            <p><?php echo esc_html__( 'Each answer needs a value (used in rules) and a label (user-facing). Leave empty rows blank.', $this->td ); ?></p>
                            <div id="bsp-answers-rows">
                                <?php
                                $existing = $edit_q && ! empty( $edit_q['answers'] ) ? $edit_q['answers'] : array();
                                // show up to 8 rows for convenience
                                $rows = max( 4, count( $existing ) );
                                for ( $r = 0; $r < $rows; $r++ ) :
                                    $val = isset( $existing[ $r ]['value'] ) ? $existing[ $r ]['value'] : '';
                                    $label = isset( $existing[ $r ]['label'] ) ? $existing[ $r ]['label'] : '';
                                ?>
                                    <div class="bsp-answer-row" style="margin-bottom:6px;">
                                        <input type="text" name="answers[<?php echo $r; ?>][value]" placeholder="<?php echo esc_attr__( 'value (for rules)', $this->td ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text">
                                        <input type="text" name="answers[<?php echo $r; ?>][label]" placeholder="<?php echo esc_attr__( 'label (user-facing)', $this->td ); ?>" value="<?php echo esc_attr( $label ); ?>" class="regular-text">
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <p><a href="#" id="bsp-add-answer" class="button"><?php echo esc_html__( 'Add answer row', $this->td ); ?></a></p>
                        </td>
                    </tr>
                </table>

                <p><input type="submit" class="button button-primary" value="<?php echo esc_attr__( $edit_q ? 'Save Question' : 'Add Question', $this->td ); ?>"></p>
            </form>

            <h2><?php echo esc_html__( 'Questions', $this->td ); ?></h2>

            <p><?php echo esc_html__( 'Manage up to 4 quiz questions. For answers, supply a value and a label. The "value" is used in rules matching and must be consistent.', $this->td ); ?></p>

            <table class="widefat fixed striped">
                <thead><tr><th>#</th><th><?php echo esc_html__( 'Question', $this->td ); ?></th><th><?php echo esc_html__( 'Answers', $this->td ); ?></th><th><?php echo esc_html__( 'Actions', $this->td ); ?></th></tr></thead>
                <tbody>
                <?php if ( empty( $questions ) ): ?>
                    <tr><td colspan="4"><?php echo esc_html__( 'No questions configured. Add one below.', $this->td ); ?></td></tr>
                <?php else: foreach ( $questions as $i => $q ): ?>
                    <tr>
                        <td><?php echo intval( $i + 1 ); ?></td>
                        <td><?php echo esc_html( $q['text'] ); ?></td>
                        <td>
                            <?php if ( ! empty( $q['answers'] ) ): foreach ( $q['answers'] as $a ) : ?>
                                <?php echo '<strong>' . esc_html( $a['value'] ) . '</strong> → ' . esc_html( $a['label'] ) . '<br/>'; ?>
                            <?php endforeach; else: ?>
                                <em><?php echo esc_html__( 'No answers', $this->td ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=borspirit-quiz&edit_q=' . $i ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Edit', $this->td ); ?></a>

                            <form style="display:inline;" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                <input type="hidden" name="action" value="bsp_delete_question">
                                <input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
                                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                                <input type="submit" class="button button-secondary" value="<?php echo esc_attr__( 'Delete', $this->td ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete this question?', $this->td ) ); ?>');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>

            <hr style="margin: 30px 0;">

            <!-- Rules UI (unchanged structure) -->
            <h2><?php echo esc_html__( 'Rules', $this->td ); ?></h2>

            <!-- Existing rules table and add/edit form (same as previous rules UI) -->
            <!-- We'll reuse the existing HTML for rules from the original plugin -->

            <h3><?php echo esc_html__( $edit_r ? 'Edit Rule' : 'Add New Rule', $this->td ); ?></h3>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="bsp_save_rule">
                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                <?php if ( $edit_r ): ?>
                    <input type="hidden" name="edit_index" value="<?php echo esc_attr( $edit_r_index ); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Name', $this->td ); ?></label></th>
                        <td><input type="text" name="name" class="regular-text" required value="<?php echo $edit_r ? esc_attr( $edit_r['name'] ) : ''; ?>"></td>
                    </tr>

                    <?php
                    $questions = $this->get_questions(); // should return array of questions with 'key', 'text', 'options'

                    foreach ( $questions as $q ) :
                        //var_dump($q);
                        
                        $q_key = $q['key'];   // e.g., q1, q2, q3...
                        $q_text = $q['text']; // full question text for label

                        $q_options = array();
                        if ( ! empty( $q['answers'] ) && is_array( $q['answers'] ) ) {
                            foreach ( $q['answers'] as $a ) {
                                $val = isset( $a['value'] ) ? $a['value'] : '';
                                $label = isset( $a['label'] ) ? $a['label'] : $val;
                                if ( $val !== '' ) $q_options[ $val ] = $label;
                            }
                        }

                        $selected_vals = $edit_r && isset( $edit_r['conditions'][ $q_key ] ) ? (array) $edit_r['conditions'][ $q_key ] : array();
                    ?>
                    <tr>
                        <th scope="row"><label><?php echo esc_html( $q_text ); ?></label></th>
                        <td>
                            <select name="conds[<?php echo esc_attr( $q_key ); ?>][]" class="regular-text" multiple>
                                <?php foreach ( $q_options as $opt_val => $opt_label ): ?>
                                    <option value="<?php echo esc_attr( $opt_val ); ?>" <?php echo in_array( $opt_val, $selected_vals, true ) ? 'selected' : ''; ?>>
                                        <?php echo esc_html( $opt_label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php echo esc_html__( 'Hold Ctrl/Cmd to select multiple options.', $this->td ); ?></p>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Type (product/category)', $this->td ); ?></label></th>
                        <td>
                            <select name="type" class="regular-text">
                                <option value="product" <?php echo $edit_r && $edit_r['type'] == 'product' ? 'selected' : ''; ?>><?php echo esc_html__( 'product', $this->td ); ?></option>
                                <option value="category" <?php echo $edit_r && $edit_r['type'] == 'category' ? 'selected' : ''; ?>><?php echo esc_html__( 'category', $this->td ); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Value (term slug or product ID)', $this->td ); ?></label></th>
                        <td><input type="text" name="value" class="regular-text" required value="<?php echo $edit_r ? esc_attr( $edit_r['value'] ) : ''; ?>"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Weight (points per match)', $this->td ); ?></label></th>
                        <td><input type="number" name="weight" value="<?php echo $edit_r ? intval( $edit_r['weight'] ) : 10; ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label><?php echo esc_html__( 'Priority (higher wins ties)', $this->td ); ?></label></th>
                        <td><input type="number" name="priority" value="<?php echo $edit_r ? intval( $edit_r['priority'] ) : 0; ?>" class="regular-text"></td>
                    </tr>
                </table>

                <p><input type="submit" class="button button-primary" value="<?php echo esc_attr__( $edit_r ? 'Save Rule' : 'Add Rule', $this->td ); ?>"></p>
            </form>

            <h2><?php echo esc_html__( 'Existing Rules', $this->td ); ?></h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( '#', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Name', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Conditions', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Type', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Value', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Weight', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Priority', $this->td ); ?></th>
                        <th><?php echo esc_html__( 'Actions', $this->td ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( empty( $rules ) ): ?>
                    <tr><td colspan="8"><?php echo esc_html__( 'No rules yet. Use the form above to add rules.', $this->td ); ?></td></tr>
                <?php else: foreach ( $rules as $i => $r ): ?>
                    <tr>
                        <td><?php echo intval( $i + 1 ); ?></td>
                        <td><?php echo esc_html( $r['name'] ); ?></td>
                        <td>
                            <?php foreach ( array( 'q1', 'q2', 'q3', 'q4' ) as $q ) {
                                if ( ! empty( $r['conditions'][ $q ] ) ) {
                                    echo '<strong>' . esc_html( $q ) . ':</strong> ' . esc_html( implode( ',', (array) $r['conditions'][ $q ] ) ) . '<br/>';
                                }
                            } ?>
                        </td>
                        <td><?php echo esc_html( $r['type'] ); ?></td>
                        <td><?php echo esc_html( $r['value'] ); ?></td>
                        <td><?php echo esc_html( $r['weight'] ); ?></td>
                        <td><?php echo esc_html( $r['priority'] ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=borspirit-quiz&edit=' . $i ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Edit', $this->td ); ?></a>
                            <form style="display:inline;" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                <input type="hidden" name="action" value="bsp_delete_rule">
                                <input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
                                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                                <input type="submit" class="button button-secondary" value="<?php echo esc_attr__( 'Delete', $this->td ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete this rule?', $this->td ) ); ?>');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <script>
        (function(){
            // small admin helper: add new answer input row
            var addBtn = document.getElementById('bsp-add-answer');
            if (addBtn) {
                addBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    var container = document.getElementById('bsp-answers-rows');
                    if (!container) return;
                    var idx = container.querySelectorAll('.bsp-answer-row').length;
                    var div = document.createElement('div');
                    div.className = 'bsp-answer-row';
                    div.style.marginBottom = '6px';
                    div.innerHTML = '<input type="text" name="answers[' + idx + '][value]" placeholder="<?php echo esc_js( esc_attr__( 'value (for rules)', $this->td ) ); ?>" class="regular-text" style="margin-right:4px;">' +
                                    '<input type="text" name="answers[' + idx + '][label]" placeholder="<?php echo esc_js( esc_attr__( 'label (user-facing)', $this->td ) ); ?>" class="regular-text">';
                    container.appendChild(div);
                }, false);
            }
        })();
        </script>

        <?php
    } // end render_admin_page

    /* -------------------------- Rules save/delete (unchanged) -------------------------- */
    public function admin_save_rule() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Insufficient permissions.', $this->td ) );
        if ( ! isset( $_POST['bsp_admin_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['bsp_admin_nonce'] ), 'bsp_admin_nonce' ) ) wp_die( esc_html__( 'Invalid request.', $this->td ) );

        $name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $type     = isset( $_POST['type'] ) && in_array( $_POST['type'], array( 'product', 'category' ), true ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'category';
        $value    = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';
        $weight   = isset( $_POST['weight'] ) ? intval( $_POST['weight'] ) : 10;
        $priority = isset( $_POST['priority'] ) ? intval( $_POST['priority'] ) : 0;

        $conds = array();
        if ( ! empty( $_POST['conds'] ) && is_array( $_POST['conds'] ) ) {
            foreach ( $_POST['conds'] as $k => $v ) {
                $vals = array();
                if ( is_array( $v ) ) {
                    foreach ( $v as $p ) {
                        $p = sanitize_text_field( wp_unslash( $p ) );
                        if ( $p !== '' ) $vals[] = $p;
                    }
                }
                if ( ! empty( $vals ) ) $conds[ $k ] = $vals;
            }
        }

        $rules = $this->get_rules();

        if ( isset( $_POST['edit_index'] ) ) {
            $idx = intval( $_POST['edit_index'] );
            if ( isset( $rules[ $idx ] ) ) {
                $rules[ $idx ] = array(
                    'name'       => $name,
                    'conditions' => $conds,
                    'type'       => $type,
                    'value'      => $value,
                    'weight'     => $weight,
                    'priority'   => $priority,
                );
            }
        } else {
            $rules[] = array(
                'name'       => $name,
                'conditions' => $conds,
                'type'       => $type,
                'value'      => $value,
                'weight'     => $weight,
                'priority'   => $priority,
            );
        }

        update_option( self::OPTION_RULES_KEY, $rules );

        wp_redirect( admin_url( 'admin.php?page=borspirit-quiz' ) );
        exit;
    }

    public function admin_delete_rule() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Insufficient permissions.', $this->td ) );
        if ( ! isset( $_POST['bsp_admin_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['bsp_admin_nonce'] ), 'bsp_admin_nonce' ) ) wp_die( esc_html__( 'Invalid request.', $this->td ) );

        $idx = isset( $_POST['index'] ) ? intval( $_POST['index'] ) : -1;
        $rules = $this->get_rules();
        if ( isset( $rules[ $idx ] ) ) {
            array_splice( $rules, $idx, 1 );
            update_option( self::OPTION_RULES_KEY, $rules );
        }

        wp_redirect( admin_url( 'admin.php?page=borspirit-quiz' ) );
        exit;
    }

} // end class

endif;

new BorSpirit_Wine_Quiz();
