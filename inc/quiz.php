<?php
/**
 * Plugin Name: BorSpirit Wine Quiz
 * Description: Kiterjesztett kvíz WooCommerce-hez admin felülettel és komplex ajánlási logikával. Telepítés: mentsd a fájlt a wp-content/plugins/ mappába és aktiváld.
 * Version: 1.2
 * Author: BorSpirit
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class BorSpirit_Wine_Quiz {

    const OPTION_RULES_KEY = 'bsp_quiz_rules_v1';

    public function __construct() {
        add_shortcode('borspirit_wine_quiz', array($this,'render_quiz'));
        add_action('wp_enqueue_scripts', array($this,'enqueue_scripts'));

        add_action('wp_ajax_bsp_recommend', array($this,'handle_recommend'));
        add_action('wp_ajax_nopriv_bsp_recommend', array($this,'handle_recommend'));

        add_action('wp_ajax_bsp_add_to_cart', array($this,'handle_add_to_cart'));
        add_action('wp_ajax_nopriv_bsp_add_to_cart', array($this,'handle_add_to_cart'));

        // Admin
        add_action('admin_menu', array($this,'register_admin_menu'));
        add_action('admin_post_bsp_save_rule', array($this,'admin_save_rule'));
        add_action('admin_post_bsp_delete_rule', array($this,'admin_delete_rule'));
    }

    /* -------------------------- Public UI -------------------------- */
    public function enqueue_scripts(){
        wp_enqueue_script('bsp-quiz-js', get_template_directory_uri() . '/ajax/js/bsp_quiz_ajax.js', array('jquery'), '1.2', true);
        wp_localize_script('bsp-quiz-js', 'bsp_quiz_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bsp_quiz_nonce'),
        ));
    }

    public function render_quiz($atts){
        ob_start(); ?>

<div id="bsp-quiz-wrap" class="bsp-quiz">
    <h2>Találd meg a hozzád illő bort</h2>
    <div id="bsp-quiz-steps">
        <div class="bsp-step" data-step="1">
            <p><strong>1. Milyen jellegű bort kedvelsz?</strong></p>
            <label><input type="radio" name="q1" value="light"> Könnyű, friss (pl. rozé, könnyű fehér)</label><br>
            <label><input type="radio" name="q1" value="medium"> Közepes test (pl. Chardonnay, Merlot)</label><br>
            <label><input type="radio" name="q1" value="full"> Teljes test, testes vörös (pl. Cabernet, Syrah)</label>
        </div>

        <div class="bsp-step" data-step="2" style="display:none;">
            <p><strong>2. Milyen ízvilágot szeretsz?</strong></p>
            <label><input type="radio" name="q2" value="dry"> Száraz</label><br>
            <label><input type="radio" name="q2" value="offdry"> Félszáraz</label><br>
            <label><input type="radio" name="q2" value="sweet"> Édes</label>
        </div>

        <div class="bsp-step" data-step="3" style="display:none;">
            <p><strong>3. Milyen alkalomra szeretnéd?</strong></p>
            <label><input type="radio" name="q3" value="everyday"> Mindennapi fogyasztás</label><br>
            <label><input type="radio" name="q3" value="dinner"> Vacsorához / párosításhoz</label><br>
            <label><input type="radio" name="q3" value="gift"> Ajándék / különleges alkalom</label>
        </div>

        <div class="bsp-step" data-step="4" style="display:none;">
            <p><strong>4. Regionális preferencia?</strong></p>
            <label><input type="radio" name="q4" value="hungary"> Magyar</label><br>
            <label><input type="radio" name="q4" value="europe"> Európa (pl. Franciaország, Olaszország)</label><br>
            <label><input type="radio" name="q4" value="newworld"> Újvilág (pl. Ausztrália, Chile)</label>
        </div>

    </div>

    <div class="bsp-nav">
        <button id="bsp-prev" style="display:none;">Vissza</button>
        <button id="bsp-next">Tovább</button>
    </div>

    <div id="bsp-result" style="display:none; margin-top:20px;"></div>
</div>

<?php
        return ob_get_clean();
    }

    /* -------------------------- Recommendation logic -------------------------- */
    private function get_rules(){
        $rules = get_option(self::OPTION_RULES_KEY, array());
        if (!is_array($rules)) $rules = array();
        return $rules;
    }

    private function compute_recommendation($answers){
        $rules = $this->get_rules();
        $scored = array();

        foreach($rules as $idx => $rule){
            $score = 0;
            if (empty($rule['conditions']) || !is_array($rule['conditions'])) continue;

            foreach($rule['conditions'] as $q => $expected){
                if(!isset($answers[$q]) || empty($expected)) continue;
                if (!is_array($expected)) $expected = array($expected);
                if (in_array($answers[$q], $expected)){
                    $score += intval($rule['weight']);
                }
            }

            if ($score > 0){
                $scored[] = array('rule'=>$rule, 'score'=>$score, 'index'=>$idx);
            }
        }

        usort($scored, function($a,$b){
            if ($a['score'] === $b['score']){
                $pa = isset($a['rule']['priority']) ? intval($a['rule']['priority']) : 0;
                $pb = isset($b['rule']['priority']) ? intval($b['rule']['priority']) : 0;
                return $pb - $pa;
            }
            return $b['score'] - $a['score'];
        });

        if (!empty($scored)){
            return $scored[0]['rule'];
        }

        // fallback
        if ($answers['q1'] === 'full'){
            return array('type'=>'product','value'=>111,'name'=>'Klasszikus testes vörös (fallback)');
        }
        if ($answers['q2'] === 'sweet'){
            return array('type'=>'category','value'=>'desszert-borok','name'=>'Desszertborok (fallback)');
        }

        return array('type'=>'category','value'=>'valogatott','name'=>'Válogatott borok (fallback)');
    }

    public function handle_recommend(){
        check_ajax_referer('bsp_quiz_nonce', 'nonce');

        $answers = array(
            'q1'=>isset($_POST['q1']) ? sanitize_text_field($_POST['q1']) : '',
            'q2'=>isset($_POST['q2']) ? sanitize_text_field($_POST['q2']) : '',
            'q3'=>isset($_POST['q3']) ? sanitize_text_field($_POST['q3']) : '',
            'q4'=>isset($_POST['q4']) ? sanitize_text_field($_POST['q4']) : ''
        );

        $rule = $this->compute_recommendation($answers);

        if ($rule['type'] === 'product'){
            $prod = wc_get_product($rule['value']);
            if ($prod){
                wp_send_json_success(array(
                    'type'=>'product',
                    'id'=>$rule['value'],
                    'title'=>$prod->get_name(),
                    'url'=>get_permalink($rule['value']),
                    'price'=>$prod->get_price_html(),
                ));
            } else {
                wp_send_json_error('Ajánlott termék nem található. Ellenőrizd a beállításokat.');
            }
        } else {
            $cat = get_term_by('slug', $rule['value'], 'product_cat');
            if ($cat){
                wp_send_json_success(array(
                    'type'=>'category',
                    'slug'=>$rule['value'],
                    'name'=>$cat->name,
                    'url'=>get_term_link($cat),
                ));
            } else {
                if (is_numeric($rule['value'])){
                    $catObj = get_term($rule['value'], 'product_cat');
                    if ($catObj && !is_wp_error($catObj)){
                        wp_send_json_success(array('type'=>'category','slug'=>$catObj->slug,'name'=>$catObj->name,'url'=>get_term_link($catObj)));
                    }
                }
                wp_send_json_error('Ajánlott kategória nem található. Ellenőrizd a beállításokat.');
            }
        }

        wp_die();
    }

    public function handle_add_to_cart(){
        check_ajax_referer('bsp_quiz_nonce', 'nonce');
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if (!$product_id){ wp_send_json_error('Érvénytelen termék.'); }

        if ( WC()->cart->add_to_cart($product_id) ){
            wp_send_json_success(array('message'=>'Termék hozzáadva a kosárhoz.'));
        } else {
            wp_send_json_error('Nem sikerült a kosárba helyezés.');
        }

        wp_die();
    }

    /* -------------------------- Admin UI -------------------------- */
    public function register_admin_menu(){
        add_submenu_page(
            'woocommerce',
            'BorSpirit Quiz beállítások',
            'BorSpirit Quiz',
            'manage_options',
            'borspirit-quiz',
            array($this,'render_admin_page')
        );
    }

    public function render_admin_page(){
        if (!current_user_can('manage_options')) wp_die('Nincs jogosultság.');
        $rules = $this->get_rules();
        $nonce = wp_create_nonce('bsp_admin_nonce');

        $edit_index = isset($_GET['edit']) ? intval($_GET['edit']) : -1;
        $edit_rule = ($edit_index >= 0 && isset($rules[$edit_index])) ? $rules[$edit_index] : null;

        ?>
        <div class="wrap">
            <h1>BorSpirit - Kvíz szabályok</h1>

            <h2><?php echo $edit_rule ? 'Szabály szerkesztése' : 'Új szabály hozzáadása'; ?></h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="bsp_save_rule">
                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr($nonce); ?>">
                <?php if($edit_rule): ?>
                    <input type="hidden" name="edit_index" value="<?php echo $edit_index; ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label>Név</label></th>
                        <td><input type="text" name="name" class="regular-text" required value="<?php echo $edit_rule ? esc_attr($edit_rule['name']) : ''; ?>"></td>
                    </tr>
                    <?php foreach(['q1'=>'jellegű','q2'=>'íz','q3'=>'alkalom','q4'=>'region'] as $q=>$label): ?>
                        <tr>
                            <th scope="row"><label><?php echo strtoupper($q); ?> értékek (vesszővel)</label></th>
                            <td><input type="text" name="conds[<?php echo $q; ?>]" class="regular-text" value="<?php echo $edit_rule && isset($edit_rule['conditions'][$q]) ? esc_attr(implode(',', $edit_rule['conditions'][$q])) : ''; ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th scope="row"><label>Type (product/category)</label></th>
                        <td>
                            <select name="type" class="regular-text">
                                <option value="product" <?php echo $edit_rule && $edit_rule['type']=='product' ? 'selected' : ''; ?>>product</option>
                                <option value="category" <?php echo $edit_rule && $edit_rule['type']=='category' ? 'selected' : ''; ?>>category</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Value (term slug or product ID)</label></th>
                        <td><input type="text" name="value" class="regular-text" required value="<?php echo $edit_rule ? esc_attr($edit_rule['value']) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Weight (pontszám egyezésenként)</label></th>
                        <td><input type="number" name="weight" value="<?php echo $edit_rule ? intval($edit_rule['weight']) : 10; ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Priority (magasabb előnyt élvez)</label></th>
                        <td><input type="number" name="priority" value="<?php echo $edit_rule ? intval($edit_rule['priority']) : 0; ?>" class="regular-text"></td>
                    </tr>
                </table>
                <p><input type="submit" class="button button-primary" value="<?php echo $edit_rule ? 'Szabály mentése' : 'Új szabály mentése'; ?>"></p>
            </form>

            <div class="bsp-quiz-info notice notice-info" style="padding:20px;">
                <h2 style="margin-top:0;">Kvíz pontozási magyarázat</h2>

                <p>⚖️ <strong>Súly (Weight)</strong> – „mennyit ér egy egyezés”</p>
                <p>Ez határozza meg, hogy egy adott kérdés egyezése mennyire fontos az ajánlásban.</p>
                <p><em>Példa:</em><br>
                    Szabály: <strong>Testes (q1=full), száraz (q2=dry), vacsorához (q3=dinner)</strong> → „Klasszikus vörösbor”<br>
                    Súly: 10<br>
                    Ha a felhasználó válaszai mindhárom feltétellel egyeznek: 3 egyezés × 10 pont = 30 pont.<br>
                    Ha csak két feltétel egyezik: 2 × 10 = 20 pont.<br>
                    <strong>Nagyobb súly = fontosabb egyezések, kisebb súly = kevésbé jelentős egyezés.</strong>
                </p>

                <p>🥇 <strong>Prioritás (Priority)</strong> – „döntetlen esetén ki nyer”</p>
                <p>Ha két szabály ugyanannyi pontot kap, a nagyobb prioritású szabály nyer.</p>
                <p><em>Példa:</em><br>
                    Szabály A: 30 pont, priority 1<br>
                    Szabály B: 30 pont, priority 3 → <strong>Szabály B nyer.</strong>
                </p>

                <h3>Összefoglaló táblázat</h3>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Tulajdonság</th>
                            <th>Jelentés</th>
                            <th>Hatás a döntésre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Weight (súly)</td>
                            <td>Mennyit ér egy egyezés az adott szabályban</td>
                            <td>Több egyezés × nagyobb súly = több pont</td>
                        </tr>
                        <tr>
                            <td>Priority (prioritás)</td>
                            <td>Előny döntetlen esetén</td>
                            <td>Nagyobb prioritás nyer, ha a pontszámok azonosak</td>
                        </tr>
                    </tbody>
                </table>

                <h3>🧠 Példa működésre</h3>
                <p>Felhasználó válaszai: <strong>q1=full, q2=dry, q3=dinner, q4=europe</strong></p>
                <p>Szabályok:</p>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Szabály</th>
                            <th>Feltételek</th>
                            <th>Súly</th>
                            <th>Priority</th>
                            <th>Pontszám</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A</td>
                            <td>q1=full, q2=dry, q3=dinner</td>
                            <td>10</td>
                            <td>1</td>
                            <td>3×10=30</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>q1=full, q2=dry, q3=dinner, q4=europe</td>
                            <td>7</td>
                            <td>3</td>
                            <td>4×7=28</td>
                        </tr>
                    </tbody>
                </table>
                <p><strong>Bár a B szabály több feltételt tartalmaz, az A nyer, mert a pontszáma 30, míg a B-é 28. Döntetlen esetén a priority döntené el.</strong></p>
            </div>

            <h2>Jelenlegi szabályok</h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Név</th>
                        <th>Feltételek</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Weight</th>
                        <th>Priority</th>
                        <th>Műveletek</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rules)): ?>
                    <tr><td colspan="8">Nincsenek szabályok. Hozz létre néhányat a fenti űrlappal.</td></tr>
                <?php else: foreach($rules as $i=>$r): ?>
                    <tr>
                        <td><?php echo intval($i+1); ?></td>
                        <td><?php echo esc_html($r['name']); ?></td>
                        <td>
                            <?php foreach(array('q1','q2','q3','q4') as $q){ if(!empty($r['conditions'][$q])) echo '<strong>'.$q.':</strong> '.esc_html(implode(',', (array)$r['conditions'][$q])).'<br/>'; } ?>
                        </td>
                        <td><?php echo esc_html($r['type']); ?></td>
                        <td><?php echo esc_html($r['value']); ?></td>
                        <td><?php echo esc_html($r['weight']); ?></td>
                        <td><?php echo esc_html($r['priority']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=borspirit-quiz&edit=' . $i); ?>" class="button button-primary">Szerkesztés</a>
                            <form style="display:inline;" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                                <input type="hidden" name="action" value="bsp_delete_rule">
                                <input type="hidden" name="index" value="<?php echo esc_attr($i); ?>">
                                <input type="hidden" name="bsp_admin_nonce" value="<?php echo esc_attr($nonce); ?>">
                                <input type="submit" class="button button-secondary" value="Törlés" onclick="return confirm('Biztos törlöd?');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function admin_save_rule(){
        if (!current_user_can('manage_options')) wp_die('Nincs jogosultság.');
        if (!isset($_POST['bsp_admin_nonce']) || !wp_verify_nonce($_POST['bsp_admin_nonce'],'bsp_admin_nonce')) wp_die('Érvénytelen kérés.');

        $name = sanitize_text_field($_POST['name']);
        $type = in_array($_POST['type'], array('product','category')) ? $_POST['type'] : 'category';
        $value = sanitize_text_field($_POST['value']);
        $weight = isset($_POST['weight']) ? intval($_POST['weight']) : 10;
        $priority = isset($_POST['priority']) ? intval($_POST['priority']) : 0;

        $conds = array();
        if (!empty($_POST['conds']) && is_array($_POST['conds'])){
            foreach($_POST['conds'] as $k=>$v){
                $vals = array();
                if (!empty($v)){
                    $parts = array_map('trim', explode(',', $v));
                    foreach($parts as $p) if($p!=='') $vals[] = sanitize_text_field($p);
                }
                if (!empty($vals)) $conds[$k] = $vals;
            }
        }

        $rules = $this->get_rules();

        if (isset($_POST['edit_index'])){
            $idx = intval($_POST['edit_index']);
            if (isset($rules[$idx])){
                $rules[$idx] = array(
                    'name'=>$name,
                    'conditions'=>$conds,
                    'type'=>$type,
                    'value'=>$value,
                    'weight'=>$weight,
                    'priority'=>$priority,
                );
            }
        } else {
            $rules[] = array(
                'name'=>$name,
                'conditions'=>$conds,
                'type'=>$type,
                'value'=>$value,
                'weight'=>$weight,
                'priority'=>$priority,
            );
        }

        update_option(self::OPTION_RULES_KEY, $rules);

        wp_redirect(admin_url('admin.php?page=borspirit-quiz')); exit;
    }

    public function admin_delete_rule(){
        if (!current_user_can('manage_options')) wp_die('Nincs jogosultság.');
        if (!isset($_POST['bsp_admin_nonce']) || !wp_verify_nonce($_POST['bsp_admin_nonce'],'bsp_admin_nonce')) wp_die('Érvénytelen kérés.');
        $idx = isset($_POST['index']) ? intval($_POST['index']) : -1;
        $rules = $this->get_rules();
        if (isset($rules[$idx])){
            array_splice($rules, $idx, 1);
            update_option(self::OPTION_RULES_KEY, $rules);
        }
        wp_redirect(admin_url('admin.php?page=borspirit-quiz')); exit;
    }

}

new BorSpirit_Wine_Quiz();
?>
