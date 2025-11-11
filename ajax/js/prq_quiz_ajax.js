(function($){
    $(function(){
        var step = 1,
            $steps = $('.bsp-step'),
            maxStep = $steps.length,
            $prevBtn = $('#bsp-prev'),
            $nextBtn = $('#bsp-next'),
            $result = $('#bsp-result');

        function showStep(n){
            $steps.hide().filter('[data-step="'+n+'"]').show();
            $prevBtn.toggle(n > 1);
            $nextBtn.text(n === maxStep ? (prq_quiz_ajax.i18n.submit || 'See My Wine Match') : (prq_quiz_ajax.i18n.next || 'Next'));
        }

        function validateStep(){
            var $current = $steps.filter('[data-step="'+step+'"]');
            if(!$current.find('input[type=radio]:checked').length){
                alert(prq_quiz_ajax.i18n.select_option || 'Please select an option to continue.');
                return false;
            }
            return true;
        }

        function getAnswers(){
            var data = { action: 'prq_recommend', nonce: prq_quiz_ajax.nonce };
            $steps.each(function(){
                var $s = $(this),
                    name = $s.find('input[type=radio]').first().attr('name');
                if(name) data[name] = $s.find('input[name="'+name+'"]:checked').val() || '';
            });
            return data;
        }

        function submitQuiz(){
            $result.html('<p>' + (prq_quiz_ajax.i18n.finding_match || 'Finding your wine match…') + '</p>').show();

            $.post(prq_quiz_ajax.ajax_url, getAnswers(), function(resp){
                if(resp.success){
                    if(resp.data.type === 'redirect' && resp.data.url){
                        window.location.href = resp.data.url;
                    } else if(resp.data.html){
                        $result.html(resp.data.html);
                    } else {
                        $result.html('<p class="bsp-error">' + (prq_quiz_ajax.i18n.unexpected || 'Unexpected error.') + '</p>');
                    }
                } else {
                    $result.html('<p class="bsp-error">' + (resp.data?.message || prq_quiz_ajax.i18n.try_again || 'Please try again.') + '</p>');
                }
            }, 'json').fail(function(){
                $result.html('<p class="bsp-error">' + (prq_quiz_ajax.i18n.network_error || 'Network error. Please try again.') + '</p>');
            });
        }

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
            if(step > 1) { step--; showStep(step); }
        });

        $(document).on('click', '#bsp-add-to-cart', function(e){
            e.preventDefault();
            var pid = $(this).data('productid');
            $.post(prq_quiz_ajax.ajax_url, {
                action: 'prq_add_to_cart',
                nonce: prq_quiz_ajax.nonce,
                product_id: pid
            }, function(resp){
                alert(resp.success ? (prq_quiz_ajax.i18n.added_to_cart || 'Product added to cart.') : (resp.data?.message || prq_quiz_ajax.i18n.cart_error || 'Could not add product to cart.'));
            }, 'json');
        });

        showStep(step);
    });
})(jQuery);
