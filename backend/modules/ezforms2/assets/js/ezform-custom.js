function getValue(selector, inputId) {
    if ($(selector).prop('tagName') == 'SELECT') {
        return $(selector).val();
    } else {
        if ($('#' + inputId).attr('type') == 'checkbox') {
            return $(selector).is(':checked') ? 1 : 0;
        } else if (typeof $('#' + inputId).attr('type') === 'undefined') {
            var value_check = $(selector).filter(':checked').val();
            if (value_check) {
                return $(selector).filter(':checked').val();
            }
            return 0;
        } else {
            return $(selector).val();
        }

    }
}