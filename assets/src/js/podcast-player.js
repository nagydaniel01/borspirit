import $ from 'jquery';

$(document).ready(function () {
    function getAudioContainer(el) {
        return $(el).closest('.card--podcast, .section--single-podcast');
    }

    function getAudio(el) {
        const $container = getAudioContainer(el);
        return $container.find('audio')[0] || null;
    }

    function parseTimeString(timeStr) {
        const parts = timeStr.split(':').map(Number).reverse();
        return parts.reduce((total, part, index) => total + part * Math.pow(60, index), 0);
    }

    function pauseAllAudioExcept(currentAudio) {
        $('audio').each(function () {
            if (this !== currentAudio) {
                this.pause();
                this.currentTime = 0;
                updateAudioBlockState(this, 'stop');
            }
        });
    }

    function updateAudioBlockState(audio, state) {
        const $block = $(audio).closest('.card__audio-wrapper');
        $block.removeClass('is-playing is-paused is-stoped');

        if (state === 'play') {
            $block.addClass('is-playing');
        } else if (state === 'pause') {
            $block.addClass('is-paused');
        } else if (state === 'stop') {
            $block.addClass('is-stoped');
        }
    }

    // --- AUDIO CONTROL EVENTS ---
    $(document).on('click', '.audio-controls__button--play', function () {
        const audio = getAudio(this);
        if (audio) {
            pauseAllAudioExcept(audio);
            audio.play();
            updateAudioBlockState(audio, 'play');
        }
    });

    $(document).on('click', '.audio-controls__button--pause', function () {
        const audio = getAudio(this);
        if (audio) {
            audio.pause();
            updateAudioBlockState(audio, 'pause');
        }
    });

    $(document).on('click', '.audio-controls__button--stop', function () {
        const audio = getAudio(this);
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
            updateAudioBlockState(audio, 'stop');
        }
    });

    $(document).on('click', '.audio-controls__button--volume-up', function () {
        const audio = getAudio(this);
        if (audio) audio.volume = Math.min(audio.volume + 0.1, 1);
    });

    $(document).on('click', '.audio-controls__button--volume-down', function () {
        const audio = getAudio(this);
        if (audio) audio.volume = Math.max(audio.volume - 0.1, 0);
    });

    $(document).on('click', '.audio-controls__button--mute', function () {
        const audio = getAudio(this);
        if (audio) {
            audio.muted = !audio.muted;
            $(this).toggleClass('is-muted', audio.muted);
        }
    });

    $(document).on('click', '.audio-controls__button--rewind', function () {
        const audio = getAudio(this);
        if (audio) audio.currentTime -= 10;
    });

    $(document).on('click', '.audio-controls__button--forward', function () {
        const audio = getAudio(this);
        if (audio) audio.currentTime += 30;
    });

    $(document).on('click', '.audio-controls__button--refresh', function () {
        const audio = getAudio(this);
        if (audio) audio.currentTime = 0;
    });

    // --- CHAPTERS ---
    $(document).on('click', '.js-chapter', function () {
        const timeStr = $(this).data('time');
        const audio = getAudio(this);
        if (!audio) return;

        pauseAllAudioExcept(audio);

        const seconds = parseTimeString(timeStr);
        if (seconds != null) {
            audio.currentTime = seconds;
            audio.play().catch(err => console.error("Playback error:", err));
        }
    });
});
