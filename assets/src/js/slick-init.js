import $ from 'jquery';
import 'slick-slider';

/**
 * Stops a Slick slider autoplay permanently at the last slide.
 * @param {jQuery} slider - The jQuery slider element
 */
function stopAtLastSlide(slider) {
    if (!slider.length) return;

    slider.on('afterChange', function(event, slick, currentSlide) {
        if (currentSlide === slick.slideCount - 1) {
            $(this).slick('slickPause'); // stop autoplay permanently
        }
    });
}

/**
 * Starts/stops Slick slider autoplay based on viewport visibility.
 * @param {jQuery} slider - The jQuery slider element
 * @param {number} visibilityThreshold - IntersectionObserver threshold (0 to 1)
 */
function viewportAutoplay(slider, visibilityThreshold = 0.5) {
    if (!slider.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const currentSlide = slider.slick('slickCurrentSlide');
                const lastSlide = slider.slick('getSlick').slideCount - 1;
                if (currentSlide < lastSlide) {
                    slider.slick('slickPlay');
                }
            } else {
                slider.slick('slickPause');
            }
        });
    }, { threshold: visibilityThreshold });

    observer.observe(slider[0]);
}

var productGallerySlider = $('.woocommerce-product-gallery__wrapper');

if (productGallerySlider) {
    productGallerySlider.slick({
        mobileFirst: true,
        infinite: true,
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

var winerySlider = $('.woocommerce-products-header__gallery');

if (winerySlider) {
    winerySlider.slick({
        mobileFirst: true,
        autoplay: true,
        autoplaySpeed: 3000,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
    });

    // Apply functions
    stopAtLastSlide(winerySlider);
    viewportAutoplay(winerySlider);
}

var productReviewsSlider = $('.commentlist');

if (productReviewsSlider) {
    productReviewsSlider.slick({
        mobileFirst: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
        prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
        nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    arrows: true,
                }
            },
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    arrows: true,
                }
            }
        ]
    });

    viewportAutoplay(productReviewsSlider);
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

// Product-query sliders
$('.slider--product-query').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        arrows: true
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        arrows: true
                    }
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        arrows: true
                    }
                }
            ]
        });
    }
});

// Term-query sliders
$('.slider--term-query').each(function() {
    const $slider = $(this).find('.slider__list');
    const $controls = $(this).find('.slider__controls');

    if ($slider.length) {

        // Remove any existing arrows inside the controls
        $controls.empty();

        $slider.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        arrows: true
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        arrows: true
                    }
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        arrows: true
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
            arrows: false,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            appendArrows: $controls,
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        arrows: true
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        arrows: true
                    }
                }
            ]
        });
    }
});