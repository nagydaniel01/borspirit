import $ from 'jquery';

$('.text-collapse').each(function() {
    var textCollapse = $(this),
        textCollapseText = textCollapse.find('.text-collapse__text'),
        textCollapseToggle = textCollapse.find('.js-collapse-toggle');

    if (textCollapse) {
        var toggleHeight = textCollapseText.attr('data-height');
        var textHeight = textCollapseText.outerHeight();

        textCollapseText.css({ maxHeight: toggleHeight + 'px' });

        if (textHeight > toggleHeight) {
            textCollapse.addClass('is-truncated');
            textCollapseToggle.removeClass('is-hidden');
        }
    }

    textCollapseToggle.on('click', function(e) {
        textCollapse.toggleClass('is-truncated');

        if (textCollapseText.attr('style')) {
            textCollapseText.removeAttr('style');
        } else {
            textCollapseText.css({ maxHeight: toggleHeight + 'px' });
        }

        textCollapseToggle.toggleClass('is-active');
        if (textCollapseToggle.hasClass('is-active')) {
            textCollapseToggle.html(localize.translations.read_less);
        } else {
            textCollapseToggle.html(localize.translations.read_more);
        }

        e.preventDefault();
    });
});