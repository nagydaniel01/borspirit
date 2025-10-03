(function($){
    'use strict';

    $(document).ready(function(){

        $('#event_registration_form').on('submit', function(e){
            e.preventDefault();

            // Check if privacy checkbox is checked
            if( !$('#reg_privacy_policy').is(':checked') ){
                $('#reg_response').html('<div class="alert alert-danger">'+event_registration_form_ajax_object.msg_privacy_required+'</div>');
                return; // stop submission
            }

            var $submitBtn = $(this).find('button[type="submit"]');

            var data = {
                action: 'event_registration_form_handler',
                user_id: event_registration_form_ajax_object.user_id,
                form_data: $(this).serialize() // send all form fields including nonce
            };

            $.ajax({
                url: event_registration_form_ajax_object.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',

                // Before sending, show a loading indicator
                beforeSend: function() {
                    $submitBtn.prop('disabled', true).addClass('disabled');
                    $('#reg_response').html('<div class="alert alert-info">'+event_registration_form_ajax_object.msg_registering+'</div>');
                },

                success: function(response){
                    if(response && typeof response === 'object'){
                        if(response.success){
                            var message = response.data && response.data.message ? response.data.message : event_registration_form_ajax_object.msg_success;
                            $('#reg_response').html('<div class="alert alert-success">'+message+'</div>');

                            if(response.data.redirect_url){
                                // Grab values from response
                                var attendee_id = response.data.attendee_id ? response.data.attendee_id : '';

                                // Build query string
                                var queryString = '?attendee_id=' + encodeURIComponent(attendee_id);

                                setTimeout(function(){
                                    window.location.href = response.data.redirect_url + queryString;
                                }, 500); // short delay so user sees the success message
                            }
                        } else {
                            var message = response.data && response.data.message ? response.data.message : event_registration_form_ajax_object.msg_error_sending;
                            $('#reg_response').html('<div class="alert alert-danger">'+message+'</div>');
                        }
                    } else {
                        $('#reg_response').html('<div class="alert alert-danger">'+event_registration_form_ajax_object.msg_unexpected+'</div>');
                    }
                },

                error: function(xhr, status, error){
                    var errMsg = event_registration_form_ajax_object.msg_network_error;
                    if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                        errMsg = xhr.responseJSON.data.message;
                    } else if(error){
                        errMsg += ' (' + error + ')';
                    }
                    $('#reg_response').html('<div class="alert alert-danger">'+errMsg+'</div>');
                },

                complete: function() {
                    // Optional: remove loading spinner or do other cleanup
                    // Example: console.log('AJAX request completed');
                    $submitBtn.prop('disabled', false).removeClass('disabled');
                }
            });
        });

    });

})(jQuery);
