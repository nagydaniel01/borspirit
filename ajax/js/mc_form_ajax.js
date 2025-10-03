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

            var $submitBtn = $(this).find('button[type="submit"]');

            var data = {
                action: 'mc_form_handler', // WP AJAX action hook
                user_id: mc_form_ajax_object.user_id,
                form_data: $(this).serialize() // send all form fields including nonce
            };

            $.ajax({
                url: mc_form_ajax_object.ajax_url,
                type: 'POST',
                data: data,
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
                    // Example: console.log('Mailchimp AJAX completed');
                    $submitBtn.prop('disabled', false).removeClass('disabled');
                }
            });
        });

    });

})(jQuery);
