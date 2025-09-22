import $ from 'jquery';

const mql = window.matchMedia('(min-width: 992px)');

function navigation(e) {
    $('.nav--home .nav__item.level0').each(function () {
        const $parentItem = $(this);
        const $submenu = $parentItem.children('.nav__list.level1');

        $parentItem.off('mouseenter.nav mouseleave.nav');

        if (e.matches) {
            const submenuHeight = $submenu.outerHeight();

            $submenu.css({
                'margin-bottom': -submenuHeight + 'px',
                'opacity': 0
            });

            $parentItem.on('mouseenter.nav', function () {
                $(this).children('.nav__list.level1').css({
                    'margin-bottom': '0px',
                    'opacity': 1
                });
            });

            $parentItem.on('mouseleave.nav', function () {
                const $sub = $(this).children('.nav__list.level1');
                const height = $sub.outerHeight();
                $sub.css({
                    'margin-bottom': -height + 'px',
                    'opacity': 0
                });
            });

        } else {
            $submenu.css({
                'margin-bottom': '0px',
                'opacity': 1
            });
        }
    });
}

mql.addEventListener ? mql.addEventListener('change', navigation) : mql.addListener(navigation);
navigation(mql);
