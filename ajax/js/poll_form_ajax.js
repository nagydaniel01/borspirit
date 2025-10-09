(function ($) {
    'use strict';

    $(document).ready(function () {

        // Handle form submission
        $('#poll_form').on('submit', function (e) {
            e.preventDefault();

            var rating = $('input[name="rating"]:checked').val();
            var like = $('input[name="like"]:checked').val();
            var feedback = $('textarea[name="feedback_text"]').val().trim();

            // Validate all fields
            if (!rating) {
                alert(poll_form_ajax_object.msg_select_rating);
                return;
            }
            /*
            if (!like) {
                alert(poll_form_ajax_object.msg_select_opinion);
                return;
            }
            if (!feedback) {
                alert(poll_form_ajax_object.msg_enter_feedback);
                return;
            }
            */

            // Disable submit button to prevent multiple submissions
            $('#poll_form_submit').prop('disabled', true).text(poll_form_ajax_object.msg_sending);

            // AJAX submission
            $.ajax({
                url: poll_form_ajax_object.ajax_url,
                type: 'POST',
                data: $('#poll_form').serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#poll_form').html('<p>' + poll_form_ajax_object.msg_success + '</p>');
                    } else {
                        $('#poll_form').html('<p>' + poll_form_ajax_object.msg_error + '</p>');
                    }
                },
                error: function () {
                    $('#poll_form').html('<p>' + poll_form_ajax_object.msg_network_error + '</p>');
                }
            });
        });

    });

})(jQuery);
