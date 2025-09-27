import $ from 'jquery';

var mql = window.matchMedia('(min-width: 992px)');

function productSticky(e) {
    var addToCartButton = $('.single_add_to_cart_button'),
        productSticky = $('.block--product-sticky');

    $(window).on('scroll', function() {
        if (addToCartButton.length > 0) {
            var addToCartPosition = Math.ceil(addToCartButton.offset().top);
        }
        
        if (addToCartPosition) {
            if ($(window).scrollTop() > addToCartPosition) {
                productSticky.addClass('is-sticky');
            } else {
                productSticky.removeClass('is-sticky');
            }
        }
    });

    $(document).on('click', '.js-sticky-add-to-cart', function() {
        addToCartButton.trigger('click');
    });
}

$(function() {
    mql.addListener(productSticky);
    productSticky(mql);
});