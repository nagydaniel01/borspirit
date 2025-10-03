(function ($) {
    'use strict';
    
    $(document).ready(function() {
        function updateBookmarkStatus($button, newStatus) {
            $button.data('bookmarked', newStatus);
            var iconClass = newStatus ? 'icon-bookmark' : 'icon-bookmark-empty';
            var iconHtml = '<svg class="icon ' + iconClass + '"><use xlink:href="#' + iconClass + '"></use></svg>';
            var label = newStatus ? 'Remove from Bookmarks' : 'Add to Bookmarks';
            var buttonHtml = iconHtml + '<span>' + label + '</span>';

            $button.attr('data-bookmarked', newStatus);
            $button.html(buttonHtml);
        }

        // Function to refresh the list of bookmarks displayed in the "My Account" section
        function updateBookmarksList() {
            $.ajax({
                type: 'POST',
                url: save_post_ajax_object.ajax_url,
                data: {
                    action: 'update_bookmark_list_handler'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#bookmark-list').html(response.data.html);
                    } else {
                        $('#error-container').html('<div class="error-message alert alert-danger" role="alert">'+response.data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", status, error);
                }
            });
        }

        $(document).on('click', '#btn-bookmark', function(event) {
            event.preventDefault();

            var $button = $(this);

            var post_id = $button.data('post-id');
            var currentStatus = $button.data('bookmarked');
            var newStatus = !currentStatus;

            // Add loading state
            var loadingHtml = '<svg class="icon icon-loading"><use xlink:href="#icon-loading"></use></svg><span>Processing...</span>';
            $button.html(loadingHtml);

            // Send AJAX request to save or remove bookmark
            $.ajax({
                type: 'POST',
                url: save_post_ajax_object.ajax_url,
                data: {
                    action: 'save_post_handler',
                    post_id: post_id
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.success) {
                        console.error("Error in response:", response);
                    } else {
                        // Update the button UI to reflect new status
                        updateBookmarkStatus($button, newStatus);

                        // Refresh the bookmarks list immediately (setTimeout 0 ensures async)
                        setTimeout(function() {
                            updateBookmarksList();
                        }, 0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", status, error);
                }
            });
        });
    });
})(jQuery);
