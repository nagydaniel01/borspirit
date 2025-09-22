import { Fancybox } from '@fancyapps/ui';

// Target both gallery and single sections
document.querySelectorAll('.section.section--gallery .section__content a:has(img), .section.section--single .section__content a:has(img)').forEach(link => {
    const img = link.querySelector('img');
    if (img) {
        // Many WP thumbnails have -300x200 or similar before the extension
        link.href = img.src.replace(/-\d+x\d+(?=\.[a-z]{3,4}$)/i, '');
    }
});

// Bind Fancybox to both selectors
Fancybox.bind("[data-fancybox], .section.section--gallery .section__content a:has(img), .section.section--single .section__content a:has(img)");
