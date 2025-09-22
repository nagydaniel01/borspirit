import $ from 'jquery';

$(document).ready(function () {
    // Cache the header element for performance
    const $header = $('.header');

    // Track the last scroll position
    let lastScroll = $(window).scrollTop();

    // Flag to prevent multiple requestAnimationFrame calls
    let ticking = false;

    // Flag to detect if an offcanvas menu is open
    let offcanvasOpen = false;

    // Scroll threshold after which the header hides
    const HIDE_THRESHOLD = 76;

    /**
     * Handle scroll events for header visibility
     */
    function handleScroll() {
        const currentScroll = $(window).scrollTop();

        // Disable transition when near top for smooth initial load
        if (currentScroll < HIDE_THRESHOLD) {
            $header.css('transition', 'none');
        } else {
            $header.css('transition', 'transform 0.25s ease-in-out, box-shadow 0.25s ease, background-color 0.25s ease');
        }

        if (!offcanvasOpen) {
            if (currentScroll > HIDE_THRESHOLD) {
                if (currentScroll > lastScroll) {
                    // Scrolling down → hide header
                    $header.addClass('is-hidden').removeClass('is-sticky');
                } else if (currentScroll < lastScroll) {
                    // Scrolling up → show header
                    $header.removeClass('is-hidden').addClass('is-sticky');
                }
            } else {
                // At the top of the page → show header without gap
                $header.removeClass('is-hidden is-sticky');
            }
        }

        // Update last scroll position
        lastScroll = currentScroll;
        ticking = false;
    }

    /**
     * Throttle scroll events using requestAnimationFrame
     */
    $(window).on('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(handleScroll);
            ticking = true;
        }
    });

    /**
     * Handle Bootstrap offcanvas events
     */
    document.querySelectorAll('.offcanvas').forEach(offcanvas => {
        offcanvas.addEventListener('show.bs.offcanvas', () => {
            $header.addClass('is-open');
            document.body.classList.add('offcanvas-open'); // add class to body
            offcanvasOpen = true;
        });

        offcanvas.addEventListener('hidden.bs.offcanvas', () => {
            $header.removeClass('is-open');
            document.body.classList.remove('offcanvas-open'); // remove class from body
            offcanvasOpen = false;
        });
    });
});
