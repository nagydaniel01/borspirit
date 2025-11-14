import $ from 'jquery';

$(document).ready(function () {
    const $footerRow = $('.footer__bottom .row');
    const breakpoint = 768; // Bootstrap breakpoint (adjust if needed)
    let isAccordion = false;

    function initAccordion() {
        const windowWidth = $(window).width();

        if (windowWidth < breakpoint && !isAccordion) {
            // Activate accordion
            $footerRow.addClass('accordion');
            $footerRow.find('.footer__block--nav').each(function (index) {
                const $block = $(this);
                const $parentCol = $block.parent();
                const id = `footerAccordion${index}`;
                const headerId = `footerAccordionHeader${index}`;

                // Save original order for desktop restoration
                $block.data('originalParent', $parentCol);
                $block.data('originalIndex', $parentCol.index());

                // Find the existing title
                const $title = $block.find('.footer__title');

                // Make the title the accordion button
                $title.addClass('accordion-button collapsed')
                      .attr({
                          'id': headerId,
                          'type': 'button',
                          'data-bs-toggle': 'collapse',
                          'data-bs-target': `#${id}`,
                          'aria-expanded': 'false',
                          'aria-controls': id
                      });

                // Wrap the title in header if not already
                if (!$title.parent().hasClass('accordion-header')) {
                    $title.wrap(`<div class="accordion-header" id="${headerId}"></div>`);
                }

                // Create collapse wrapper
                const $collapse = $('<div>', {
                    class: 'accordion-collapse collapse',
                    id: id,
                    'aria-labelledby': headerId,
                    'data-bs-parent': '.footer__bottom .row'
                });

                // Move the rest of the block content inside the collapse
                $collapse.append($block.contents().not($title.parent()));

                // Append collapse to block
                $block.append($collapse);
            });

            isAccordion = true;

        } else if (windowWidth >= breakpoint && isAccordion) {
            // Deactivate accordion and restore original columns
            $footerRow.removeClass('accordion');

            $footerRow.find('.footer__block--nav').each(function () {
                const $block = $(this);
                const $collapse = $block.find('.accordion-collapse');
                const $header = $block.find('.accordion-header');

                // Move contents back out of collapse
                $collapse.contents().appendTo($block);

                // Remove collapse and accordion classes
                $collapse.remove();
                $header.find('.accordion-button').removeClass('accordion-button collapsed')
                                                .removeAttr('type data-bs-toggle data-bs-target aria-expanded aria-controls');
                $header.replaceWith($header.contents());
            });

            // Restore original order
            $footerRow.find('.footer__block--nav').each(function () {
                const $block = $(this);
                const $originalParent = $block.data('originalParent');
                const index = $block.data('originalIndex');

                if ($originalParent) {
                    const $children = $originalParent.children();
                    if (index >= $children.length) {
                        $originalParent.append($block);
                    } else {
                        $children.eq(index).before($block);
                    }
                }
            });

            isAccordion = false;
        }
    }

    initAccordion();
    $(window).on('resize', initAccordion);
});
