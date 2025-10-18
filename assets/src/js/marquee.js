import $ from 'jquery';

$(function () {
    const $track = $('.block__track');
    if (!$track.length) return;

    const originalHTML = $track.html();

    function fillTrack() {
        const $parent = $track.parent();
        const parentWidth = $parent.outerWidth();
        let totalWidth = $track.get(0).scrollWidth;

        // Duplicate content until it’s at least twice the container width
        while (totalWidth < parentWidth * 2) {
        $track.append(originalHTML);
        totalWidth = $track.get(0).scrollWidth;
        }
    }

    function updateDuration() {
        const fullWidth = $track.get(0).scrollWidth / 2;
        const pxPerSecond = 60;
        const duration = Math.max(6, Math.round(fullWidth / pxPerSecond));
        $track.css('animation-duration', `${duration}s`);
    }

    function rebuildTrack() {
        $track.html(originalHTML);
        fillTrack();
        updateDuration();
    }

    // Initial setup
    rebuildTrack();

    // Recalculate on resize (debounced)
    let resizeTimer;
    $(window).on('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(rebuildTrack, 200);
    });

    // Pause animation on hover
    $track.parent()
        .on('mouseenter', () => $track.css('animation-play-state', 'paused'))
        .on('mouseleave', () => $track.css('animation-play-state', 'running'));
});
