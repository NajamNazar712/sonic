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
        keyupFiredCount += 1; 
        lastKeyAction = event.key  
    }, 25));
};

$('#add_shipment_form input, #add_bag_form input, #scan_shipment_form input, #quick_tracking_form  input').ConvertToBarcodeTextbox();

$('#add_shipment_form, #add_bag_form, #scan_shipment_form, #quick_tracking_form').on('submit', function (event) {
    var isScanned = keyupFiredCount <= 1
    

    console.log(keyupFiredCount);
    console.log(lastKeyAction);

     if ((lastKeyAction == 'Enter' || lastKeyAction == '') && isScanned) {
        window.lastAction = 'Scanned';
    } else {
        window.lastAction = 'Manual';
    }

    lastKeyAction = ''
    keyupFiredCount = 0;
});



