import { Fancybox } from '@fancyapps/ui';

// Target both gallery and single sections
document.querySelectorAll('.section.section--gallery .section__content a:has(img), .section--single-post .section__content a:has(img)').forEach(link => {
    const img = link.querySelector('img');
    if (img) {
        // Many WP thumbnails have -300x200 or similar before the extension
        link.href = img.src.replace(/-\d+x\d+(?=\.[a-z]{3,4}$)/i, '');
    }
});

// Bind Fancybox with Hungarian localization
Fancybox.bind("[data-fancybox], .section.section--gallery .section__content a:has(img), .section--single-post .section__content a:has(img)", {
    l10n: {
        NEXT: localize.translations.gallery.NEXT,
        PREV: localize.translations.gallery.PREV,
        CLOSE: localize.translations.gallery.CLOSE,
    }
} );