import { Modal } from 'bootstrap';

(function ($) {
    'use strict';

    $(function () {
        if (!sessionStorage.getItem('mainModalShown')) {
            setTimeout(function () {
                var $modal = $('#mainModal');
                if ($modal.length) {
                    var modal = new Modal($modal[0]);
                    modal.show();

                    // Automatically close after 5 seconds
                    /*
                    setTimeout(function () {
                        modal.hide();
                    }, 5000);
                    */

                    sessionStorage.setItem('mainModalShown', 'true');
                }
            }, 3000);
        }
    });

})(jQuery);
