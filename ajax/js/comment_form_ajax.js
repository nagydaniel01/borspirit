(function($){
    'use strict';

    $(document).ready(function(){

        // -----------------------------
        // Simple field validation
        // -----------------------------
        $.fn.validateField = function() {
            if ($(this).val().length < 3) {
                $(this).addClass('error');
                return false;
            } else {
                $(this).removeClass('error');
                return true;
            }
        };

        $.fn.validateEmail = function() {
            var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailReg.test($(this).val())) {
                $(this).addClass('error');
                return false;
            } else {
                $(this).removeClass('error');
                return true;
            }
        };

        // -----------------------------
        // AJAX form submit
        // -----------------------------
        $('#commentform').submit(function(e){
            e.preventDefault();

            var $form        = $(this),
                $submit      = $('#submit'),
                $respond     = $('#respond'),
                $commentList = $('.comment-list'),
                $cancelReply = $('#cancel-comment-reply-link'),
                $responseDiv = $('#comment_response'); // Container for inline messages

            // -----------------------------
            // Validate form fields
            // -----------------------------
            var valid = true;
            if ($('#author').length && !$('#author').validateField()) valid = false;
            if ($('#email').length && !$('#email').validateEmail()) valid = false;
            if (!$('#comment').validateField()) valid = false;
            if (!valid) return false;

            // Clear previous messages
            $responseDiv.html('');

            // -----------------------------
            // AJAX request
            // -----------------------------
            $.ajax({
                type: 'POST',
                url: comment_form_ajax_object.ajax_url,
                data: $form.serialize() + '&action=ajaxcomments',

                beforeSend: function() {
                    $submit.addClass('loadingform').val(comment_form_ajax_object.loading_text);
                },

                success: function(response) {
                    var message = response.data && response.data.message 
                                  ? response.data.message 
                                  : comment_form_ajax_object.msg_success;

                    if (response.success) {
                        var addedCommentHTML = response.data.comment;
                        var parentId = $('#comment_parent').val();

                        if (parentId !== "0") {
                            // Reply case
                            var $parent = $('#comment-' + parentId);

                            if (!$parent.children('ol.children').length) {
                                $parent.append('<ol class="children"></ol>');
                            }

                            $parent.children('ol.children').append(addedCommentHTML);
                            $parent.children('ol.children').after($respond);
                            $cancelReply.trigger('click');

                        } else {
                            // Top-level comment
                            if ($commentList.length) {
                                $commentList.append(addedCommentHTML);
                            } else {
                                $respond.before('<ol class="comment-list">' + addedCommentHTML + '</ol>');
                            }
                            $('.comment-list').after($respond);
                        }

                        // Clear textarea
                        $('#comment').val('');

                        // Scroll to new comment
                        $('html, body').animate({
                            scrollTop: $('#comment-' + response.data.comment_id).offset().top - 100
                        }, 500);

                        // Show success message
                        $responseDiv.html('<div class="alert alert-success">'+message+'</div>');

                    } else {
                        // Show error message
                        $responseDiv.html('<div class="alert alert-danger">'+message+'</div>');
                    }
                },

                error: function(xhr, status) {
                    var message;

                    if (xhr.status == 500) {
                        message = comment_form_ajax_object.error_adding;
                    } else if (status === 'timeout') {
                        message = comment_form_ajax_object.error_timeout;
                    } else {
                        // Parse WordPress error HTML if possible
                        var wpErrorHtml = xhr.responseText.split("<p>");
                        if (wpErrorHtml.length > 1) {
                            var wpErrorStr = wpErrorHtml[1].split("</p>");
                            message = wpErrorStr[0];
                        } else {
                            message = comment_form_ajax_object.error_adding;
                        }
                    }

                    // Display error inline
                    $responseDiv.html('<div class="alert alert-danger">'+message+'</div>');
                },

                complete: function() {
                    $submit.removeClass('loadingform').val(comment_form_ajax_object.post_comment);
                }
            });
        });

        // -----------------------------
        // Reply click handler
        // -----------------------------
        $(document).on('click', '.comment-reply-link', function(){
            var commentId = $(this).data('commentid');
            var $parent   = $('#comment-' + commentId);

            if (!$parent.children('ol.children').length) {
                $parent.append('<ol class="children"></ol>');
            }

            $parent.children('ol.children').after($('#respond'));
        });

        // -----------------------------
        // Cancel reply click handler
        // -----------------------------
        $(document).on('click', '#cancel-comment-reply-link', function(){
            if ($('.comment-list').length) {
                $('.comment-list').after($('#respond'));
            } else {
                $('#respond').appendTo($('#comments'));
            }
        });

    });

})(jQuery);
