import $ from 'jquery';
import * as ModalModule from 'bootstrap/js/src/modal';

$(function () {
    'use strict';
    
    // Only show modal if age not verified
    if (!getCookie('age_verified')) {
        // Get modal element
        const myModalEl = document.getElementById('ageGateModal');

        if (myModalEl) {
            // Create a new modal instance with options
            const modal = new ModalModule.default(myModalEl, {
                backdrop: 'static', // prevent closing when clicking outside
                keyboard: false     // prevent closing with Esc key
            });
            modal.show();

            // "Yes" button handler
            $('#age-yes').on('click', function () {
                setCookie('age_verified', 'true', localize.ag_cookie_days);
                modal.hide();
            });

            // "No" button handler
            $('#age-no').on('click', function () {
                window.location.href = localize.ag_redirect_url;
            });
        }
    }

    // Cookie helpers
    function setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = `${name}=${value || ''}${expires}; path=/`;
    }

    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            const c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
        }
        return null;
    }
});
