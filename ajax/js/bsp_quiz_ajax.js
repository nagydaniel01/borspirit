(function($){
    $(function(){

        var step = 1;
        var $steps = $('.bsp-step');
        var maxStep = $steps.length;
        var $prevBtn = $('#bsp-prev');
        var $nextBtn = $('#bsp-next');
        var $result = $('#bsp-result');

        // Show the current step
        function showStep(n){
            $steps.hide();
            $steps.filter('[data-step="'+n+'"]').show();
            $prevBtn.toggle(n > 1);
            $nextBtn.text(n === maxStep ? bsp_quiz_ajax.submit_text || 'Beküld' : bsp_quiz_ajax.next_text || 'Tovább');
        }

        // Validate current step
        function validateStep(){
            var $current = $steps.filter('[data-step="'+step+'"]');
            var name = $current.find('input[type=radio]').first().attr('name');
            if(!$current.find('input[name="'+name+'"]:checked').length){
                alert(bsp_quiz_ajax.msg_select_option || 'Kérlek válassz egy opciót a folytatáshoz.');
                return false;
            }
            return true;
        }

        // Gather answers
        function getAnswers(){
            return {
                action: 'bsp_recommend',
                nonce: bsp_quiz_ajax.nonce,
                q1: $('input[name=q1]:checked').val() || '',
                q2: $('input[name=q2]:checked').val() || '',
                q3: $('input[name=q3]:checked').val() || '',
                q4: $('input[name=q4]:checked').val() || ''
            };
        }

        // Show recommendation
        function renderResult(data){
            var html = '';
            if(data.type === 'product'){
                html += '<div class="bsp-rec bsp-rec-product">';
                html += '<h3>' + data.title + '</h3>';
                html += '<p class="bsp-price">' + data.price + '</p>';
                html += '<p><a class="button" href="'+ data.url +'">Termék oldal</a> ';
                html += '<button id="bsp-add-to-cart" data-productid="'+ data.id +'">Kosárba</button></p>';
                html += '</div>';
            } else if(data.type === 'category'){
                html += '<div class="bsp-rec bsp-rec-cat">';
                html += '<h3>' + data.name + '</h3>';
                html += '<p><a class="button" href="'+ data.url +'">Kategória megtekintése</a></p>';
                html += '</div>';
            } else {
                html = '<p class="bsp-error">' + (data.message || bsp_quiz_ajax.msg_unexpected) + '</p>';
            }
            $result.html(html).show();
        }

        // Send quiz data via AJAX
        function submitQuiz(){
            $result.html('<p>' + (bsp_quiz_ajax.msg_sending || 'Ajánlás készül...') + '</p>').show();
            $.post(bsp_quiz_ajax.ajax_url, getAnswers(), function(resp){
                if(resp.success){
                    renderResult(resp.data);
                } else {
                    $result.html('<p class="bsp-error">Hiba: ' + resp.data + '</p>');
                }
            }, 'json').fail(function(){
                $result.html('<p class="bsp-error">' + (bsp_quiz_ajax.msg_network_error || 'Hálózati hiba. Próbáld újra később.') + '</p>');
            });
        }

        // Event handlers
        $nextBtn.on('click', function(e){
            e.preventDefault();
            if(!validateStep()) return;

            if(step < maxStep){
                step++;
                showStep(step);
            } else {
                submitQuiz();
            }
        });

        $prevBtn.on('click', function(e){
            e.preventDefault();
            if(step > 1){ step--; showStep(step); }
        });

        $(document).on('click', '#bsp-add-to-cart', function(e){
            e.preventDefault();
            var pid = $(this).data('productid');
            $.post(bsp_quiz_ajax.ajax_url, {
                action: 'bsp_add_to_cart',
                nonce: bsp_quiz_ajax.nonce,
                product_id: pid
            }, function(resp){
                alert(resp.success ? (bsp_quiz_ajax.msg_added_to_cart || 'Termék hozzáadva a kosárhoz.') : ('Hiba: ' + resp.data));
            }, 'json');
        });

        // Initialize
        showStep(step);

    });
})(jQuery);
