import $ from 'jquery';

$(document).ready(function () {
    var $backToTop = $('.back-to-top');
    var triggerHeight = $(window).height() / 2; // 50vh

    // Show/hide button on scroll
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > triggerHeight) {
        $backToTop.addClass('is-visible');
        } else {
        $backToTop.removeClass('is-visible');
        }
    });

    // Smooth scroll to top when clicked
    /*
    $backToTop.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 600);
    });
    */
});