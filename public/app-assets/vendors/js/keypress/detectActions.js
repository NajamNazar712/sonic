let keyupFiredCount = 0;
let lastKeyAction = '';
window.lastAction = '';

function DelayExecution(f, delay) {
    let timer = null;
    return function () {
        let context = this, args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function () {
            f.apply(context, args);
        }, delay);
    };
}

$.fn.ConvertToBarcodeTextField = function () {
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

$('#add_shipment_form input, #add_bag_form input, #scan_shipment_form input, #quick_tracking_form input, #delivery_note_form input, #quick_receive_form input, #return_note_form input').ConvertToBarcodeTextField();
$('#add_shipment_form, #add_bag_form, #scan_shipment_form, #quick_tracking_form, #delivery_note_form, #quick_receive_form, #return_note_form').on('submit', function (event) {
    event.preventDefault();
    let isScanned = keyupFiredCount <= 1;

    //'Tab' in case of scan.attr disabled
    if ((lastKeyAction === 'Enter' || lastKeyAction === '' || lastKeyAction === 'Tab') && isScanned) {
        window.lastAction = 1;
    } else {
        window.lastAction = 0;
    }

    lastKeyAction = ''
    keyupFiredCount = 0;
});



