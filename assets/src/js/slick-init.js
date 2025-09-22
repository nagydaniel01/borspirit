import $ from 'jquery';
import 'slick-slider';

var productGallerySlider = $('.woocommerce-product-gallery__wrapper');

if (productGallerySlider) {
    productGallerySlider.slick({
        mobileFirst: true,
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
        nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
        responsive: [
            {
                breakpoint: 991,
                settings: {
                  arrows: true,
                }
            }
        ]
    });
}

// Select all main sliders
$('.slider--main').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        arrows: true
                    }
                }
            ]
        });
    }
});

// Select all post sliders
$('.slider--related').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                        arrows: true
                    }
                }
            ]
        });
    }
});

// Select all gallery sliders
$('.slider--gallery').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            variableWidth: true,
            arrows: true,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        arrows: true
                    }
                }
            ]
        });

        // Remove data-fancybox from cloned slides
        $slider.find('.slick-cloned [data-fancybox]').removeAttr('data-fancybox');

        // Initialize Fancybox on remaining slides
        /*
        $('[data-fancybox="gallery"]').fancybox({
            // Your options here
        });
        */
    }
});

// Post-query sliders
$('.slider--post-query').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }
});

// Post-query sliders
$('.slider--card').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                    }
                }
            ]
        });
    }
});