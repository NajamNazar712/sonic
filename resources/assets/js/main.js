$(document).ready(function () {
let axios = require('axios');
$("#BankInfoModal").on("show.bs.modal", function(e) {
    var id = $(e.relatedTarget).data('target-id');

    $.get( "/admin/accounts/pending/"+id+"/bank", function( data ) {
        $(".modal-body").html(data);
        // console.log(data);
    });

});

$("#ShippingInfoModal").on("show.bs.modal", function(e) {
    var id = $(e.relatedTarget).data('target-id');
    // console.log(id);
    $.get( "/admin/accounts/pending/"+id+"/shipping", function( data ) {
        $(".modal-body").html(data);
        // console.log(data);
    });

});
$("#RatesModal").on("show.bs.modal", function(e) {
    var id = $(e.relatedTarget).data('target-id');
    console.log(id);
    // $.get( "/admin/accounts/pending/"+id+"/rates", function( data ) {
    //     $(".modal-body").html(data);
    //     // console.log(data);
    // });

});
$("#ConfirmModal").on("show.bs.modal", function(e) {
    var id = $(e.relatedTarget).data('target-id');
    var rel = $(e.relatedTarget).attr('rel');

    $('#shid').val(id);
    $('#shstatus').val(rel);
    // axios.get('/accounts/block/active', {
    //     params: {
    //         id: id
    //     }
    // })
    //     .then(function (response) {
    //         $(".modal-body.confirmation").html(response);
    //     })
    //     .catch(function (error) {
    //         console.log(error);
    //     });

});
// $('body').on('click','#confirmAction',function () {
//    var uid = $('#shid').val();
//    var status = $('#shstatus').val();
//    axios.post('/account/status',{
//        params:{
//            uid: uid,
//            status: status
//        }
//    })
//        .then(function (response) {
//             console.log(response);
//        })
//        .catch(function(error){
//
//        });
// });
    $(".daterange").daterangepicker();
    var clickCheckbox = document.querySelector('.switchery.weightAddition');
    var cashhandlingswitch = document.querySelector('.switchery.cashChargesOvernight');
    var insuranceChargesSwitch = document.querySelector('.switchery.insuranceChargesOvernight');
    var returnChargesSwitch = document.querySelector('.switchery.returnChargesOvernight');
    var packagingChargesSwitch = document.querySelector('.switchery.packagingChargesOvernight');

        clickCheckbox.onchange = function () {
            if (clickCheckbox.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (clickCheckbox.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        };
        var count = 1;
    $('body').on('click','#waddition_btn',function () {
        let htmdiv = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAddition'+count+'" data-color="success" data-size="sm" name="wa_switch[]"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="wa_spkg[]"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="wa_national_charges[]"></fieldset></div></div>';
            $('.weight-addition-overnight').append(htmdiv);
            var switches = document.querySelector('.switchery.weightAddition'+count);
            var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
            $(".touchspin-color").TouchSpin();
            // var switchery = new Switchery('.switchery.weightAddition'+count, { color: '#37BC9B' });
            count++;
        switches.onchange = function () {
            if (switches.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

            } else if (switches.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }
        };
    });

    //addMoreSlabs
    $('body').on('click','#addMoreSlabs',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="cash_range_up[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="cash_range_down[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="cash_charges[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.cash-handling-div-overnight').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
    $('body').on('click','#addMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ins_range_up[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ins_range_down[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ins_charges[]" type="number" class="form-control" id="" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.insurance-charges-div-overnight').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //Cash handling
    // cashChargesOvernight
    cashhandlingswitch.onchange = function () {
        if(cashhandlingswitch.checked === true){
            // $('.cash-handling-div').
            $('.cash-handling-div-overnight').find('input').prop('disabled',false);
            $('.cash-handling-btn-overnight').find('button').prop('disabled',false);
        }else if(cashhandlingswitch.checked === false){
            $('.cash-handling-div-overnight').find('input').prop('disabled',true);
            $('.cash-handling-btn-overnight').find('button').prop('disabled',true);

        }
    };

    // InsuranceOvernight
    insuranceChargesSwitch.onchange = function () {
        if(insuranceChargesSwitch.checked === true){
            // $('.cash-handling-div').
            $('.insurance-charges-div-overnight').find('input').prop('disabled',false);
            $('.insurance-charges-btn-overnight').find('button').prop('disabled',false);
        }else if(insuranceChargesSwitch.checked === false){
            $('.insurance-charges-div-overnight').find('input').prop('disabled',true);
            $('.insurance-charges-btn-overnight').find('button').prop('disabled',true);

        }
    };
    // Return Overnight
    returnChargesSwitch.onchange = function () {
        if(returnChargesSwitch.checked === true){
            // $('.cash-handling-div').
            $('.return-charges-div-overnight').find('input').prop('disabled',false);
        }else if(returnChargesSwitch.checked === false){
            $('.return-charges-div-overnight').find('input').prop('disabled',true);

        }
    };// Packaging Charges Overnight
    packagingChargesSwitch.onchange = function () {
        if(packagingChargesSwitch.checked === true){
            // $('.cash-handling-div').
            $('.packaging-charges-div-overnight').find('input').prop('disabled',false);
        }else if(packagingChargesSwitch.checked === false){
            $('.packaging-charges-div-overnight').find('input').prop('disabled',true);

        }
    };

});
