(function($){
    'use strict';

    $(document).ready(function(){

        $('#subscribe_form').on('submit', function(e){
            e.preventDefault();

            // Check if privacy checkbox is checked
            if( !$('#mc_privacy_policy').is(':checked') ){
                $('#mc_response').html('<div class="alert alert-danger">'+mc_form_ajax_object.msg_privacy_required+'</div>');
                return; // stop submission
            }

            var form = $(this);
            var $submitBtn = $(this).find('button[type="submit"]');

            grecaptcha.ready(function() {
                grecaptcha.execute(mc_form_ajax_object.recaptcha_site_key, {action: 'subscribe_form'})
                    .then(function(token) {

                    // Add token to the form data
                    var formData = form.serialize() + '&recaptcha_token=' + encodeURIComponent(token);

                    $.ajax({
                        url: mc_form_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'mc_form_handler',
                            user_id: mc_form_ajax_object.user_id,
                            form_data: formData // send all form fields including nonce and recaptcha_token
                        },
                        dataType: 'json',

                        // Before sending, show a loading indicator
                        beforeSend: function() {
                            $submitBtn.prop('disabled', true).addClass('disabled');
                            $('#mc_response').html('<div class="alert alert-info">'+mc_form_ajax_object.msg_sending+'</div>');
                        },

                        success: function(response){
                            if(response && typeof response === 'object'){
                                if(response.success){
                                    var message = response.data && response.data.message ? response.data.message : mc_form_ajax_object.msg_success;
                                    $('#mc_response').html('<div class="alert alert-success">'+message+'</div>');
                                } else {
                                    var message = response.data && response.data.message ? response.data.message : mc_form_ajax_object.msg_error_sending;
                                    $('#mc_response').html('<div class="alert alert-danger">'+message+'</div>');
                                }
                            } else {
                                $('#mc_response').html('<div class="alert alert-danger">'+mc_form_ajax_object.msg_unexpected+'</div>');
                            }
                        },

                        error: function(xhr, status, error){
                            var errMsg = mc_form_ajax_object.msg_network_error;
                            if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                                errMsg = xhr.responseJSON.data.message;
                            } else if(error){
                                errMsg += ' (' + error + ')';
                            }
                            $('#mc_response').html('<div class="alert alert-danger">'+errMsg+'</div>');
                        },

                        complete: function() {
                            // Optional cleanup
                            // Example: console.log('AJAX request completed');
                            $submitBtn.prop('disabled', false).removeClass('disabled');

                            // Wait 3 seconds, then fade out the response over 1 second
                            setTimeout(function() {
                                $('#mc_response').fadeOut(1000, function() {
                                    $(this).html('').show(); // Clear content and reset display
                                });
                            }, 3000);
                        }
                    });

                });
            });
        });

    });

})(jQuery);
