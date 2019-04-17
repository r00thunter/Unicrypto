 $(document).ready(function() {
    $('.info-data-table').DataTable();
});
            calculateBuy();
            function calculateBuy() {
                $('#btc_amount').bind("keyup change", function () {
        calculateWithdrawal();
    });

    $('#btc_amount').bind("keypress", function (e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        var k = String.fromCharCode(charCode);
        var dec = $('#cfg_decimal_separator').val();
        var tho = $('#cfg_thousands_separator').val();

        if (charCode != 46 && charCode != 39 && charCode != 37 && charCode > 31 && (charCode < 48 || charCode > 57) && k != dec && k != tho)
            return false;

        return true;
    });

    $('#btc_amount').focus(function () {
        if (!(parseFloat($(this).val()) > 0))
            $(this).val('');
    });

    $('#btc_amount').blur(function () {
        if (!(parseFloat($(this).val()) > 0))
            $(this).val('0');
    });
}

function calculateWithdrawal() {
    var btc_amount = ($('#btc_amount').val()) ? parseFloat($('#btc_amount').val().replace(window.tho, '')) : 0;
    var btc_fee = ($('#withdraw_btc_network_fee').html()) ? parseFloat($('#withdraw_btc_network_fee').html().replace(window.tho, '')) : 0;
    var btc_total = (btc_amount > 0) ? btc_amount - btc_fee : 0;
    var fiat_amount = ($('#fiat_amount').val()) ? parseFloat($('#fiat_amount').val().replace(window.tho, '')) : 0;
    var fiat_fee = ($('#withdraw_fiat_fee').html()) ? parseFloat($('#withdraw_fiat_fee').html().replace(window.tho, '')) : 0;
    var fiat_total = (fiat_amount > 0) ? fiat_amount - fiat_fee : 0;
    $('#withdraw_btc_total').html(formatCurrency(btc_total, 8));
    $('#withdraw_fiat_total').html(formatCurrency(fiat_total));
}

function formatCurrency(amount, is_btc, flex) {
    if (isNaN(parseFloat(amount)))
        return '0';

    amount = parseFloat(amount).toFixed(8);
    var decimal_sep = $('#cfg_decimal_separator').val();
    var thousands_sep = $('#cfg_thousands_separator').val();
    var dec_amount = (typeof is_btc != 'number') ? (is_btc ? 8 : 2) : is_btc;

    if (flex && String(amount).indexOf('.') >= 0) {
        flex = (typeof flex != 'number') ? 8 : flex;
        amount = String(amount);
        dec_detect = amount.split('.')[1].replace(/[^0-9]/g, '').length - amount.split('.')[1].replace(/[^0-9]/g, '').replace(/^[0]+/g, '').length;
        if (parseFloat(amount.split('.')[1]) > 0) {
            dec_amount = Math.max(dec_amount, dec_detect + 1);
            dec_amount = (dec_amount > flex) ? flex : dec_amount;
        }
    }
console.log("string32434 "+$('#cfg_thousands_separator').val());
        console.log("string1223 "+$('#cfg_decimal_separator').val());
    var string = parseFloat(amount).toFixed(dec_amount).toString();
    if (string.indexOf('.') >= 0) {
        var string_parts = string.split('.');
        string = string_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, $('#cfg_thousands_separator').val()) + $('#cfg_decimal_separator').val() + string_parts[1];
        return string;
    }

    return parseFloat(amount).toFixed(dec_amount).toString().replace('.', $('#cfg_decimal_separator').val()).replace(/\B(?=(\d{3})+(?!\d))/g, $('#cfg_thousands_separator').val());
}
(function () {
    window.dec = $('#cfg_decimal_separator').val();
    window.tho = $('#cfg_thousands_separator').val();
    var _parseFloat = window.parseFloat;
    window.parseFloat = function (number) {
        if (typeof number == 'string') {
            if (number.match(/(\.{1})([0-9]{0,8})$/))
                return _parseFloat(number);

            return _parseFloat(number.toString().replace(window.tho, '').replace(window.dec, '.'));
        }
        else
            return _parseFloat(number);
    };
})();