(function($){
    'use strict';

    $(document).ready(function(){

        $('#contact_form').on('submit', function(e){
            e.preventDefault();

            // Check if privacy checkbox is checked
            if( !$('#cf_privacy_policy').is(':checked') ){
                $('#cf_response').html('<div class="alert alert-danger">'+contact_form_ajax_object.msg_privacy_required+'</div>');
                return; // stop submission
            }

            var form = $(this);
            var $submitBtn = form.find('button[type="submit"]');

            grecaptcha.ready(function() {
                grecaptcha.execute(contact_form_ajax_object.recaptcha_site_key, {action: 'contact_form'})
                    .then(function(token) {

                    // Add token to the form data
                    var formData = form.serialize() + '&recaptcha_token=' + encodeURIComponent(token);

                    $.ajax({
                        url: contact_form_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'contact_form_handler',
                            user_id: contact_form_ajax_object.user_id,
                            form_data: formData // send all form fields including nonce and recaptcha_token
                        },
                        dataType: 'json',

                        // Before sending, show a loading indicator
                        beforeSend: function() {
                            $submitBtn.prop('disabled', true).addClass('disabled');
                            $('#cf_response').html('<div class="alert alert-info">'+contact_form_ajax_object.msg_sending+'</div>');
                        },

                        success: function(response){
                            if(response && typeof response === 'object'){
                                if(response.success){
                                    var message = response.data && response.data.message ? response.data.message : contact_form_ajax_object.msg_success;
                                    $('#cf_response').html('<div class="alert alert-success">'+message+'</div>');

                                    if(response.data.redirect_url){
                                        // Grab values from response
                                        var message_id = response.data.message_id ? response.data.message_id : '';

                                        // Build query string
                                        var queryString = '?message_id=' + encodeURIComponent(message_id);

                                        setTimeout(function(){
                                            window.location.href = response.data.redirect_url + queryString;
                                        }, 1000); // short delay so user sees the success message
                                    }
                                } else {
                                    var message = response.data && response.data.message ? response.data.message : contact_form_ajax_object.msg_error_sending;
                                    $('#cf_response').html('<div class="alert alert-danger">'+message+'</div>');
                                }
                            } else {
                                $('#cf_response').html('<div class="alert alert-danger">'+contact_form_ajax_object.msg_unexpected+'</div>');
                            }
                        },

                        error: function(xhr, status, error){
                            var errMsg = contact_form_ajax_object.msg_network_error;
                            if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                                errMsg = xhr.responseJSON.data.message;
                            } else if(error){
                                errMsg += ' (' + error + ')';
                            }
                            $('#cf_response').html('<div class="alert alert-danger">'+errMsg+'</div>');
                        },

                        complete: function() {
                            // Optional cleanup
                            // Example: console.log('AJAX request completed');
                            $submitBtn.prop('disabled', false).removeClass('disabled');

                            // Wait 3 seconds, then fade out the response over 1 second
                            setTimeout(function() {
                                $('#cf_response').fadeOut(1000, function() {
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
