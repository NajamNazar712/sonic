/*=========================================================================================
    File Name: wizard-steps.js
    Description: wizard steps page specific js
    ----------------------------------------------------------------------------------------
    Item Name: Modern Admin - Clean Bootstrap 4 Dashboard HTML Template
    Version: 1.0
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

// Wizard tabs with numbers setup

function getLastPartOfUrl() {
    var urlParts = window.location.href.split('/');
    return urlParts[urlParts.length - 1];
}


$(".number-tab-steps").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "fade",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
        finish: 'Submit'
    },
    onFinished: function (event, currentIndex) {
        alert("Form submitted.");
    }
});

// Wizard tabs with icons setup
$(".icons-tab-steps").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "fade",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
        finish: 'Submit'
    },
    onFinished: function (event, currentIndex) {
        alert("Form submitted.");
    }
});

// Vertical tabs form wizard setup
$(".vertical-tab-steps").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "fade",
    stepsOrientation: "vertical",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
        finish: 'Submit'
    },
    onFinished: function (event, currentIndex) {
        alert("Form submitted.");
    }
});

// Validate steps wizard

// Show form
var form = $(".steps-validation").show();

$(".steps-validation").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "fade",
    titleTemplate: '<span class="step valid">#index#</span> #title#',
    labels: {
        finish: 'Submit'
    },
    onStepChanging: function (event, currentIndex, newIndex)
    {

        if(currentIndex === 0){
            var caddress = $('input[name="company_address"]').val();
            // var cphone = $('input[name="shipper_phone"]').val();
            var cpoc = $('input[name="shipper_poc"]').val();
            var ccity = $('#shipper_city').val();
            var cproduct = $('#shipper_product_type').val();
            var payment_cycle = $('#payment_cycles').val();
            var scity = $('#shipping_city').find('option[value="'+ccity+'"]').val();
            if(scity !== undefined){

                $('#pickup_address').val(caddress);
                $('#pickup_poc').val(cpoc);
                // $('#pickup_phone').val(cphone);
                $('#shipping_city').val(ccity).trigger('change');
                $('#product_select').val(cproduct).trigger('change');
            }

            if(payment_cycle == 4){
                var twice_a_day = $('#selected_days').val();
                var daysArray = twice_a_day.split(',');

                if(!(daysArray.length === 2)){
                    $('#payment_cycle_msg').removeClass('d-none')
                    $('#payment_cycle_msg').text('Select Exactly Two Days')
                    return currentIndex;

                }else{
                    $('#payment_cycle_msg').addClass('d-none')

                }
            }

            if(payment_cycle == 5){
                var thrice_a_day = $('#selected_days').val();
                var daysArray = thrice_a_day.split(',');

                if(!(daysArray.length === 3)){
                    $('#payment_cycle_msg').removeClass('d-none')
                    $('#payment_cycle_msg').text('Select Exactly Three Days')
                    return currentIndex;
                }else{
                    $('#payment_cycle_msg').addClass('d-none')
                }
            }

            if(payment_cycle == 2){
                var weekly = $('#selected_days').val();
                if(weekly == ''){
                    $('#payment_cycle_msg').removeClass('d-none')
                    $('#payment_cycle_msg').text('Select Exactly One Day')
                    return currentIndex;
                }else{
                    $('#payment_cycle_msg').addClass('d-none')
                }
            }

            if(payment_cycle == 3){
                var day = $('#monthly').val();
                if(day == 'none'){
                    $('#payment_cycle_msg').removeClass('d-none')
                    $('#payment_cycle_msg').text('Select Exactly One Day')
                    return currentIndex;
                }else{
                    $('#payment_cycle_msg').addClass('d-none')
                }
            }

            if(payment_cycle == 6){
                var day = $('#fornite').val();
                if(day == 'none'){
                    $('#payment_cycle_msg').removeClass('d-none')
                    $('#payment_cycle_msg').text('Select Exactly One Day')
                    return currentIndex;
                }else{
                    $('#payment_cycle_msg').addClass('d-none')
                }
            }

            
        }
        // Allways allow previous action even if the current form is not valid!
        if (currentIndex > newIndex)
        {

            return true;
        }


        // Forbid next action on "Warning" step if the user is to young
        // if (newIndex === 3 && Number($("#age-2").val()) < 18)
        // {
        //     return false;
        // }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex)
        {
            // To remove error styles
            form.find(".body:eq(" + newIndex + ") label.error").remove();
            form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }


        
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onStepChanged: function (event, currentIndex, priorIndex) {
     
        if(currentIndex === 4 && getLastPartOfUrl() == "wordpress"){
            var newLi = $('<li class="clearfix"><button id="customQuotationBtn" class="btn btn-primary">Request For Custom Qoutes</button></li>');
            $('.actions ul').append(newLi);
        }else if(currentIndex != 4){
            $('#customQuotationBtn').closest('li').remove();
            console.log(1);
        }

        var isCheckedOn = $('input[name="on_main_switch"]').prop('checked');
        var isCheckedOl = $('input[name="ol_main_switch"]').prop('checked');
        var isCheckedDetain = $('input[name="detain_main_switch"]').prop('checked');
        var isCheckedSameDay = $('input[name="sameday_main_switch"]').prop('checked');

        if(isCheckedOn || isCheckedOl || isCheckedDetain || isCheckedSameDay){
            $('#customQuotationBtn').prop('disabled', true);
        }


    },
    onFinishing: function (event, currentIndex)
    {
        form.validate().settings.ignore = ":disabled";
        // if($('input[name="password"]') != $('input[name="password-confirm"]')){
        //
        //     $('#perror').css('display','block');
        // }else if($('input[name="password"]') == $('input[name="password-confirm"]')){
        //     $('#perror').css('display','none');
        //
        // }
        if($('#nature_of_account').val() == 1){
            $('#billing_information_div').remove();
        }
        return form.valid();
    },
    onFinished: function (event, currentIndex)
    {

        if ($('input[name="on_main_switch"]').is(':checked') || $('input[name="ol_main_switch"]').is(':checked') || $('input[name="detain_main_switch"]').is(':checked') || $('input[name="sameday_main_switch"]').is(':checked') || getLastPartOfUrl() != "wordpress") {
            $('#registership').submit();
        }else{
            toastr.error('Select Any One Rate', 'Error!', {
                positionClass: 'toast-top-center',
                containerId: 'toast-top-center'
            });
            return currentIndex;
        }
    }
});

// Initialize validation
$(".steps-validation").validate({
    errorClass: 'danger',
    successClass: 'success',
    highlight: function(element, errorClass) {
        $(element).removeClass(errorClass);
    },
    unhighlight: function(element, errorClass) {
        $(element).removeClass(errorClass);
    },
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
    rules: {
        email: {
            required: true,
            email: true,
            remote: {
                url: "/cod/check_email",
                type: 'GET',
                data: {
                    email: function() {
                        return $("input[name='email']").val();
                    }
                }
            }
        }
    },
    messages: {
        email: {
            required: "Email is required",
            email: "Please enter a valid email address",
            remote: "Email is already taken. Please choose another."
        }
    }
});


// Initialize plugins
// ------------------------------

// Date & Time Range
$('.datetime').daterangepicker({
    timePicker: true,
    timePickerIncrement: 30,
    locale: {
        format: 'MM/DD/YYYY h:mm A'
    }
});