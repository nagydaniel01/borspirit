import $ from 'jquery';
import * as ModalModule from 'bootstrap/js/src/modal';

$(function () {
    'use strict';

    // Only show modal if not shown this session
    if (!getCookie('main_modal_shown')) {
        // Get modal element
        const mainModalEl = document.getElementById('mainModal');

        if (mainModalEl) {
            // Delay showing the modal by 3 seconds
            setTimeout(() => {
                // Create a new modal instance with options
                const modal = new ModalModule.default(mainModalEl, {
                    //backdrop: 'static', // prevent closing when clicking outside
                    //keyboard: false     // prevent closing with Esc key
                });
                modal.show();

                const markModalShown = () => {
                    // Session cookie
                    setCookie('main_modal_shown', 'true');
                };

                // Skip button
                $(mainModalEl).find('#skip').on('click', markModalShown);

                // Primary button - mousedown ensures cookie is set before navigation
                $(mainModalEl).find('a.btn-primary').on('click', markModalShown);

                // Closing via X button
                $(mainModalEl).on('hidden.bs.modal', markModalShown);

                // Fallback: page unload
                window.addEventListener('beforeunload', markModalShown);
            }, 3000); // 3000ms = 3 seconds
        }
    }

    // Cookie helpers
    function setCookie(name, value) {
        document.cookie = `${name}=${value}; path=/;`;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            const c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
        }
        return null;
    }
});
