import $ from 'jquery';
import 'select2';

if ($('select.js-filter').length > 0) {
    $('select.js-filter').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
            allowClear: true,
        });
    });
}

if ($('select.js-filter-alphabetical').length > 0) {
    $('select.js-filter-alphabetical').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
            allowClear: true,
            dropdownCssClass: 'select2-dropdown--grid',
        });
    });
}