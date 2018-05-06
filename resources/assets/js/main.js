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
//     $(".touchspin-color").TouchSpin();
    $(".daterange").daterangepicker();
    // {
    //     timePicker24Hour: true,
    //     locale: {
    //         format: 'Y-M-DD hh:mm:ss A'
    //     }
    // });
//Overnight
    var clickCheckbox = document.querySelector('.switchery.weightAdditionOvernight');
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
        // let htmdiv = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'range_up\']"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'range_down\']"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight'+count+'" data-color="success" data-size="sm" name="on_wa[][\'switch\']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa[][\'spkg\']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'local_charges\']"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'national_charges\']"></fieldset></div></div>';

        let htmdiv = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="on_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="on_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight'+count+'" data-color="success" data-size="sm" name="on_wa_switch['+count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa_spkg['+count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="on_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="on_wa_national_charges[]"></fieldset></div></div>';
        $('.weight-addition-overnight').append(htmdiv);
        var switches = document.querySelector('.switchery.weightAdditionOvernight'+count);
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
            '                                                        <input name="on_cash_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_cash_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_cash_charges[]" type="number" class="form-control" min="0" value="">\n' +
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
            '                                                        <input name="on_ins_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_ins_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_ins_charges[]" type="number" class="form-control" min="0" value="">\n' +
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


    //Overland
    var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland');
    var cashhandlingswitchOverland = document.querySelector('.switchery.cashChargesOverland');
    var insuranceChargesSwitchOverland = document.querySelector('.switchery.insuranceChargesoverland');
    var returnChargesSwitchOverland = document.querySelector('.switchery.returnChargesOverland');
    var packagingChargesSwitchOverland = document.querySelector('.switchery.packagingChargesOverland');

    weightAdditionOverland.onchange = function () {
        if (weightAdditionOverland.checked === true) {
            // $(this).next('.spkg').attr('disabled','');
            // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

        } else if (weightAdditionOverland.checked === false) {
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

        }
    };
    //Overland


    var overland_count = 1;
    $('body').on('click','#overland_weightadd',function () {
        let htmdiv1 = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOverland'+overland_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+overland_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+overland_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_national_charges[]"></fieldset></div></div>';
        $('.weight-addition-overland').append(htmdiv1);
        let switches = document.querySelector('.switchery.weightAdditionOverland'+overland_count);
        let switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
        $(".touchspin-color").TouchSpin();
        overland_count++;
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
    $('body').on('click','#overlandaddMoreSlabs',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.cash-handling-div-overland').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
    $('body').on('click','#oladdMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.insurance-charges-div-overland').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //Cash handling
    // cashChargesOvernight
    cashhandlingswitchOverland.onchange = function () {
        if(cashhandlingswitchOverland.checked === true){
            // $('.cash-handling-div').
            $('.cash-handling-div-overland').find('input').prop('disabled',false);
            $('.cash-handling-btn-overland').find('button').prop('disabled',false);
        }else if(cashhandlingswitchOverland.checked === false){
            $('.cash-handling-div-overland').find('input').prop('disabled',true);
            $('.cash-handling-btn-overland').find('button').prop('disabled',true);

        }
    };

    // InsuranceOvernight
    insuranceChargesSwitchOverland.onchange = function () {
        if(insuranceChargesSwitchOverland.checked === true){
            // $('.cash-handling-div').
            $('.insurance-charges-div-overland').find('input').prop('disabled',false);
            $('.insurance-charges-btn-overland').find('button').prop('disabled',false);
        }else if(insuranceChargesSwitchOverland.checked === false){
            $('.insurance-charges-div-overland').find('input').prop('disabled',true);
            $('.insurance-charges-btn-overland').find('button').prop('disabled',true);

        }
    };
    // Return Overnight
    returnChargesSwitchOverland.onchange = function () {
        if(returnChargesSwitchOverland.checked === true){
            $('.return-charges-div-overland').find('input').prop('disabled',false);
        }else if(returnChargesSwitchOverland.checked === false){
            $('.return-charges-div-overland').find('input').prop('disabled',true);

        }
    };// Packaging Charges Overnight
    packagingChargesSwitchOverland.onchange = function () {
        if(packagingChargesSwitchOverland.checked === true){
            // $('.cash-handling-div').
            $('.packaging-charges-div-overland').find('input').prop('disabled',false);
        }else if(packagingChargesSwitchOverland.checked === false){
            $('.packaging-charges-div-overland').find('input').prop('disabled',true);

        }
    };
    //overland end
    //Detain start
    //Overnight
    var weightAdditionDetain = document.querySelector('.switchery.weightAdditionDetain');
    var cashhandlingswitchDetain = document.querySelector('.switchery.cashChargesDetain');
    var insuranceChargesSwitchDetain = document.querySelector('.switchery.insuranceChargesdetain');
    var returnChargesSwitchDetain = document.querySelector('.switchery.returnChargesDetain');
    var packagingChargesSwitchDetain = document.querySelector('.switchery.packagingChargesDetain');

    weightAdditionDetain.onchange = function () {
        if (weightAdditionDetain.checked === true) {
            // $(this).next('.spkg').attr('disabled','');
            // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

        } else if (weightAdditionDetain.checked === false) {
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

        }
    };
    //Detain


    var detain_count = 1;
    $('body').on('click','#detain_weightadd',function () {
        let htmdiv2 = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="detain_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="detain_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+detain_count+'" data-color="success" data-size="sm" name="detain_wa_switch['+detain_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="detain_wa_spkg['+detain_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="detain_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="detain_wa_national_charges[]"></fieldset></div></div>';
        $('.weight-addition-detain').append(htmdiv2);
        let switches = document.querySelector('.switchery.weightAdditionDetain'+detain_count);
        let switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
        $(".touchspin-color").TouchSpin();
        detain_count++;
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
    $('body').on('click','#detainaddMoreSlabs',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.cash-handling-div-detain').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
    $('body').on('click','#detainaddMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.insurance-charges-div-detain').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //Cash handling
    // cashChargesOvernight
    cashhandlingswitchDetain.onchange = function () {
        if(cashhandlingswitchDetain.checked === true){
            // $('.cash-handling-div').
            $('.cash-handling-div-detain').find('input').prop('disabled',false);
            $('.cash-handling-btn-detain').find('button').prop('disabled',false);
        }else if(cashhandlingswitchDetain.checked === false){
            $('.cash-handling-div-detain').find('input').prop('disabled',true);
            $('.cash-handling-btn-detain').find('button').prop('disabled',true);

        }
    };

    // InsuranceOvernight
    insuranceChargesSwitchDetain.onchange = function () {
        if(insuranceChargesSwitchDetain.checked === true){
            // $('.cash-handling-div').
            $('.insurance-charges-div-detain').find('input').prop('disabled',false);
            $('.insurance-charges-btn-detain').find('button').prop('disabled',false);
        }else if(insuranceChargesSwitchDetain.checked === false){
            $('.insurance-charges-div-detain').find('input').prop('disabled',true);
            $('.insurance-charges-btn-detain').find('button').prop('disabled',true);

        }
    };
    // Return Overnight
    returnChargesSwitchDetain.onchange = function () {
        if(returnChargesSwitchDetain.checked === true){
            $('.return-charges-div-detain').find('input').prop('disabled',false);
        }else if(returnChargesSwitchDetain.checked === false){
            $('.return-charges-div-detain').find('input').prop('disabled',true);

        }
    };// Packaging Charges Overnight
    packagingChargesSwitchDetain.onchange = function () {
        if(packagingChargesSwitchDetain.checked === true){
            // $('.cash-handling-div').
            $('.packaging-charges-div-detain').find('input').prop('disabled',false);
        }else if(packagingChargesSwitchDetain.checked === false){
            $('.packaging-charges-div-detain').find('input').prop('disabled',true);

        }
    };
    //Detain end
    //sameday start
    var weightAdditionSameday = document.querySelector('.switchery.weightAdditionSameday');
    var cashhandlingswitchSameday = document.querySelector('.switchery.cashChargesSameday');
    var insuranceChargesSwitchSameday = document.querySelector('.switchery.insuranceChargessameday');
    var returnChargesSwitchSameday = document.querySelector('.switchery.returnChargesSameday');
    var packagingChargesSwitchSameday = document.querySelector('.switchery.packagingChargesSameday');

    weightAdditionSameday.onchange = function () {
        if (weightAdditionSameday.checked === true) {
            // $(this).next('.spkg').attr('disabled','');
            // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

        } else if (weightAdditionSameday.checked === false) {
            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

        }
    };
    //Detain


    var sameday_count = 1;
    $('body').on('click','#sameday_weightadd',function () {
        let htmdiv2 = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="sameday_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="sameday_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionSameday'+sameday_count+'" data-color="success" data-size="sm" name="sameday_wa_switch['+sameday_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg['+sameday_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="sameday_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="sameday_wa_national_charges[]"></fieldset></div></div>';
        $('.weight-addition-sameday').append(htmdiv2);
        let switches = document.querySelector('.switchery.weightAdditionSameday'+sameday_count);
        let switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
        $(".touchspin-color").TouchSpin();
        sameday_count++;
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
    $('body').on('click','#samedayaddMoreSlabs',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.cash-handling-div-sameday').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
    $('body').on('click','#samedayaddMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_range_up[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_range_down[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_charges[]" type="number" class="form-control" min="0" value="">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                            </div>';
        $('.insurance-charges-div-sameday').append(htmdiv);

        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //Cash handling
    // cashChargesOvernight
    cashhandlingswitchSameday.onchange = function () {
        if(cashhandlingswitchSameday.checked === true){
            // $('.cash-handling-div').
            $('.cash-handling-div-sameday').find('input').prop('disabled',false);
            $('.cash-handling-btn-sameday').find('button').prop('disabled',false);
        }else if(cashhandlingswitchSameday.checked === false){
            $('.cash-handling-div-sameday').find('input').prop('disabled',true);
            $('.cash-handling-btn-sameday').find('button').prop('disabled',true);

        }
    };

    // InsuranceOvernight
    insuranceChargesSwitchSameday.onchange = function () {
        if(insuranceChargesSwitchSameday.checked === true){
            // $('.cash-handling-div').
            $('.insurance-charges-div-sameday').find('input').prop('disabled',false);
            $('.insurance-charges-btn-sameday').find('button').prop('disabled',false);
        }else if(insuranceChargesSwitchSameday.checked === false){
            $('.insurance-charges-div-sameday').find('input').prop('disabled',true);
            $('.insurance-charges-btn-sameday').find('button').prop('disabled',true);

        }
    };
    // Return Overnight
    returnChargesSwitchSameday.onchange = function () {
        if(returnChargesSwitchSameday.checked === true){
            $('.return-charges-div-sameday').find('input').prop('disabled',false);
        }else if(returnChargesSwitchSameday.checked === false){
            $('.return-charges-div-sameday').find('input').prop('disabled',true);

        }
    };// Packaging Charges Overnight
    packagingChargesSwitchSameday.onchange = function () {
        if(packagingChargesSwitchSameday.checked === true){
            // $('.cash-handling-div').
            $('.packaging-charges-div-sameday').find('input').prop('disabled',false);
        }else if(packagingChargesSwitchSameday.checked === false){
            $('.packaging-charges-div-sameday').find('input').prop('disabled',true);

        }
    };
    //end sameday
    //for discounts Overnight
    var elems = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOvernight'));


        elems[0].onchange = function () {
            ONdiscount(elems[0]);
        };
        elems[1].onchange = function () {
            ONdiscount(elems[1]);
        };
        elems[2].onchange = function () {
            ONdiscount(elems[2]);
        };
        elems[3].onchange = function () {
            ONdiscount(elems[3]);
        };
        elems[4].onchange = function () {
            ONdiscount(elems[4]);
        };
    function ONdiscount(eve) {
        if(eve.checked === true){

            $(eve).parent().parent().next().prop('disabled',false);
            // $('.cash-handling-div').
            //$('.packaging-charges-div-sameday').find('input').prop('disabled',false);
        }else if(eve.checked === false){
            //$('.packaging-charges-div-sameday').find('input').prop('disabled',true);
            $(eve).parent().parent().next().prop('disabled',true);

        }
    }
    //for discounts Overland
    var overlandSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOverland'));


    overlandSwitch[0].onchange = function () {
        ONdiscount(overlandSwitch[0]);
    };
    overlandSwitch[1].onchange = function () {
        ONdiscount(overlandSwitch[1]);
    };
    overlandSwitch[2].onchange = function () {
        ONdiscount(overlandSwitch[2]);
    };
    overlandSwitch[3].onchange = function () {
        ONdiscount(overlandSwitch[3]);
    };
    overlandSwitch[4].onchange = function () {
        ONdiscount(overlandSwitch[4]);
    };
    function ONdiscount(eve) {
        if(eve.checked === true){

            $(eve).parent().parent().next().prop('disabled',false);
           
        }else if(eve.checked === false){
            $(eve).parent().parent().next().prop('disabled',true);

        }
    }

    //for discounts Overland
    var detainSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesDetain'));


    detainSwitch[0].onchange = function () {
        ONdiscount(detainSwitch[0]);
    };
    detainSwitch[1].onchange = function () {
        ONdiscount(detainSwitch[1]);
    };
    detainSwitch[2].onchange = function () {
        ONdiscount(detainSwitch[2]);
    };
    detainSwitch[3].onchange = function () {
        ONdiscount(detainSwitch[3]);
    };
    detainSwitch[4].onchange = function () {
        ONdiscount(detainSwitch[4]);
    };
    function ONdiscount(eve) {
        if(eve.checked === true){

            $(eve).parent().parent().next().prop('disabled',false);

        }else if(eve.checked === false){
            $(eve).parent().parent().next().prop('disabled',true);

        }
    }
    //for discounts Overland
        var samedaySwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesSameday'));


        samedaySwitch[0].onchange = function () {
            ONdiscount(samedaySwitch[0]);
        };
        samedaySwitch[1].onchange = function () {
            ONdiscount(samedaySwitch[1]);
        };
        samedaySwitch[2].onchange = function () {
            ONdiscount(samedaySwitch[2]);
        };
        samedaySwitch[3].onchange = function () {
            ONdiscount(samedaySwitch[3]);
        };
        samedaySwitch[4].onchange = function () {
            ONdiscount(samedaySwitch[4]);
        };
        function ONdiscount(eve) {
            if(eve.checked === true){

                $(eve).parent().parent().next().prop('disabled',false);

            }else if(eve.checked === false){
                $(eve).parent().parent().next().prop('disabled',true);

            }
        }

        //form post
    $('body').on('click','#addRatesSubmit',function () {
        // $('#ratesAdditionForm').find(":input").prop("disabled", false);
        $('#ratesAdditionForm').submit();
    });
});
