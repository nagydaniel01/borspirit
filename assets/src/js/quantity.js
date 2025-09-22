import $ from 'jquery';

let timeout;

function triggerUpdateButton() {
    if (timeout !== undefined) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(() => {
        $('[name="update_cart"]').trigger('click');
    }, 1000);
}

$(document).ready(() => {
    $(document).on('click', 'button.plus, button.minus', function() {
        const qty   = $(this).closest('.quantity').find('.qty');

        let val     = parseFloat(qty.val());
        let max     = parseFloat(qty.attr('max'));
        let min     = parseFloat(qty.attr('min'));
        let step    = parseFloat(qty.attr('step'));

        if ($(this).is('.plus')) {
            qty.val(max && val >= max ? max : val + step);
        } else {
            qty.val(min && val <= min ? min : val - step);
        }

        $('[name="update_cart"]').prop('disabled', false);
        triggerUpdateButton();
    });

    $(document).on('change', '.woocommerce input.qty', triggerUpdateButton);

    $('.woocommerce').on('change', 'input.qty', triggerUpdateButton);
});
