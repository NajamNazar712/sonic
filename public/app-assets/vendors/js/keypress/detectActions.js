var keyupFiredCount = 0;
var lastKeyAction = '';
window.lastAction = '';

function DelayExecution(f, delay) {
    var timer = null;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function () {
            f.apply(context, args);
        }, delay);
    };
}

$.fn.ConvertToBarcodeTextbox = function () {
    $(this).focus(function () {
        $(this).select();
    });

    $(this).keyup(DelayExecution(function (event) {
        //This handle the case when scan.attr will disable in some pages (quick_tracking, delivery_note, return_note etc.)
        if ($(this).prop('disabled')) {
            return;
        }

        keyupFiredCount += 1;
        lastKeyAction = event.key
    }, 40));
};

$('#add_shipment_form input, #add_bag_form input, #scan_shipment_form input, #quick_tracking_form input, #delivery_note_form input').ConvertToBarcodeTextbox();

$('#add_shipment_form, #add_bag_form, #scan_shipment_form, #quick_tracking_form, #delivery_note_form').on('submit', function (event) {
    let isScanned = keyupFiredCount <= 1


    if ((lastKeyAction === 'Enter' || lastKeyAction === '') && isScanned) {
        window.lastAction = 1;
    } else {
        window.lastAction = 0;
    }

    lastKeyAction = ''
    keyupFiredCount = 0;
});



