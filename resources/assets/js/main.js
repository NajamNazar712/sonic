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

    function masks() {
        $('.decimal').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000
        });
        $('.amount').inputmask({
            'alias': 'decimal',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'digits': 2,
            'min': 0.00,
            'max': 1000000.00
        });
         $('.numeric').inputmask({
            'alias': 'numeric',
            'allowMinus': false,
            'allowPlus': false,
            'rightAlign': false,
            'min': 0,
            'max': 1000000
        });
    }

    $('body').on('click','#on_weight_close',function () {
        $(this).parent().parent().remove();
    });
    var count = 1;
    $('body').on('click','#waddition_btn',function () {
        // let htmdiv = '<div class="row"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'range_up\']"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'range_down\']"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight'+count+'" data-color="success" data-size="sm" name="on_wa[][\'switch\']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa[][\'spkg\']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'local_charges\']"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" id="" value="" name="on_wa[][\'national_charges\']"></fieldset></div></div>';

        let htmdiv = '<div class="row" id="on_weight_row'+count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_up['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_range_down['+count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOvernight'+count+'" data-color="success" data-size="sm" name="on_wa_switch['+count+']"/></div></div><div class="col text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="on_wa_spkg['+count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="on_wa_local_charges['+count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="on_wa_national_charges['+count+']"></fieldset></div><div class="col">\n' +
            '<span id="on_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.weight-addition-overnight').append(htmdiv);
        var switches = document.querySelector('.switchery.weightAdditionOvernight'+count);
        var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
        $(".touchspin-color").TouchSpin({
            buttondown_class: "btn btn-success",
            buttonup_class: "btn btn-success",
            buttondown_txt: '<i class="ft-minus"></i>',
            buttonup_txt: '<i class="ft-plus"></i>'
        });
        masks();

        switches.onchange = function () {

            if (switches.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
            } else if (switches.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }

        };
        $("#on_weight_row"+count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        count++;
    });
    // var switchery = new Switchery('.switchery.weightAddition'+count, { color: '#37BC9B' });


    //addMoreSlabs
    var on_slab_count = 1;
    $('body').on('click','#addMoreSlabs',function () {
        let htmdiv = '<div class="row" id="on_insurance_handle_'+on_slab_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_cash_range_up['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_cash_range_down['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_cash_charges['+on_slab_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount validated">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span id="on_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.cash-handling-div-overnight').append(htmdiv);
        masks();
        $("#on_insurance_handle_"+on_slab_count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();
    on_slab_count++;
    });
    //add more slabs insurance
    var on_ins_count = 1;
    $('body').on('click','#addMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row" id="on_insurance_charge_'+on_ins_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_ins_range_up['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_ins_range_down['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="on_ins_charges['+on_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control amount validated">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span id="on_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.insurance-charges-div-overnight').append(htmdiv);
        masks();
        $("#on_insurance_charge_"+on_ins_count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        on_ins_count++;
    });
    //Cash handling
    // cashChargesOvernight
    cashhandlingswitch = function () {
        if(cashhandlingswitch.checked === true){
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
            $('.return-charges-div-overnight').find('input').prop('disabled',false);
        }else if(returnChargesSwitch.checked === false){
            $('.return-charges-div-overnight').find('input').prop('disabled',true);

        }
    };// Packaging Charges Overnight
    packagingChargesSwitch.onchange = function () {
        if(packagingChargesSwitch.checked === true){
            $('.packaging-charges-div-overnight').find('input').prop('disabled',false);
        }else if(packagingChargesSwitch.checked === false){
            $('.packaging-charges-div-overnight').find('input').prop('disabled',true);

        }
    };


    //Overland
    var weightAdditionOverland = document.querySelector('.switchery.weightAdditionOverland0');
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
        
        // let htmdiv1 = '<div class="row" id="ol_weight_row'+overland_count+'"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOverland'+overland_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+overland_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+overland_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_national_charges[]"></fieldset></div></div>';
        let htmdiv1 = '<div class="row" id="ol_weight_row'+overland_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_up['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_range_down['+overland_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionOverland'+overland_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+overland_count+']"/></div></div><div class="col text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+overland_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="ol_wa_local_charges['+overland_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="ol_wa_national_charges['+overland_count+']"></fieldset></div><div class="col">\n' +
            '<span id="ol_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.weight-addition-overland').append(htmdiv1);
        var ol_weight_switches = document.querySelector('.switchery.weightAdditionOverland'+overland_count);
        var switchery = new Switchery(ol_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
       $(".touchspin-color").TouchSpin({
            buttondown_class: "btn btn-success",
            buttonup_class: "btn btn-success",
            buttondown_txt: '<i class="ft-minus"></i>',
            buttonup_txt: '<i class="ft-plus"></i>'
        });
       masks();
       ol_weight_switches.onchange = function () {

            if (ol_weight_switches.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
            } else if (ol_weight_switches.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }

        };
        $("#ol_weight_row"+overland_count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        overland_count++;
    });

    //addMoreSlabs
    $('body').on('click','#ol_weight_close',function () {
        $(this).parent().parent().remove();
    });
    $('body').on('click','.ol_row_delete',function () {
        $(this).parent().parent().remove();
    });
    var ol_slab_count = 1;
    $('body').on('click','#overlandaddMoreSlabs',function () {
        let htmdiv = '<div class="row" id="ol_cash_handle_'+ol_slab_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_range_up['+ol_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_range_down['+ol_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_cash_charges['+ol_slab_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.cash-handling-div-overland').append(htmdiv);
        ol_slab_count++;
        masks();
        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
     var ol_ins_count = 1;
    $('body').on('click','#oladdMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row" id="ol_insurance_charge_'+ol_ins_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_range_up['+ol_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_range_down['+ol_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="ol_ins_charges['+ol_ins_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span  class="ol_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.insurance-charges-div-overland').append(htmdiv);
            ol_ins_count++;
            masks();
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
     //detain
    var weightAdditionDetain = document.querySelector('.switchery.weightAdditionDetain0');
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
    //detain


    var detain_count = 1;
    $('body').on('click','#detain_weightadd',function () {
        
        // let htmdiv1 = '<div class="row" id="ol_weight_row'+detain_count+'"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+detain_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+detain_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+detain_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_national_charges[]"></fieldset></div></div>';
        let htmdiv1 = '<div class="row" id="detain_weight_row'+detain_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_up['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_range_down['+detain_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+detain_count+'" data-color="success" data-size="sm" name="detain_wa_switch['+detain_count+']"/></div></div><div class="col text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="detain_wa_spkg['+detain_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="detain_wa_local_charges['+detain_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="detain_wa_national_charges['+detain_count+']"></fieldset></div><div class="col">\n' +
            '<span id="detain_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.weight-addition-detain').append(htmdiv1);
        var detain_weight_switches = document.querySelector('.switchery.weightAdditionDetain'+detain_count);
        var switchery = new Switchery(detain_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
       $(".touchspin-color").TouchSpin({
            buttondown_class: "btn btn-success",
            buttonup_class: "btn btn-success",
            buttondown_txt: '<i class="ft-minus"></i>',
            buttonup_txt: '<i class="ft-plus"></i>'
        });
       masks();
       detain_weight_switches.onchange = function () {

            if (detain_weight_switches.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
            } else if (detain_weight_switches.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }

        };
        $("#detain_weight_row"+detain_count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        detain_count++;
    });

    //addMoreSlabs
    $('body').on('click','#detain_weight_close',function () {
        $(this).parent().parent().remove();
    });
    $('body').on('click','.detain_row_delete',function () {
        $(this).parent().parent().remove();
    });
    var detain_slab_count = 1;
    $('body').on('click','#detainaddMoreSlabs',function () {
        let htmdiv = '<div class="row" id="detain_cash_handle_'+detain_slab_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_range_up['+detain_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_range_down['+detain_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_cash_charges['+detain_slab_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.cash-handling-div-detain').append(htmdiv);
        detain_slab_count++;
        masks();
        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
     var detain_ins_count = 1;
    $('body').on('click','#detainaddMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row" id="detain_insurance_charge_'+detain_ins_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_range_up['+detain_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_range_down['+detain_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="detain_ins_charges['+detain_ins_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span  class="detain_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.insurance-charges-div-detain').append(htmdiv);
            detain_ins_count++;
            masks();
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
   var weightAdditionSameday = document.querySelector('.switchery.weightAdditionSameday0');
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
    //detain


    var sameday_count = 1;
    $('body').on('click','#sameday_weightadd',function () {
        
        // let htmdiv1 = '<div class="row" id="ol_weight_row'+sameday_count+'"><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_up[]"></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_range_down[]"></fieldset></div><div class="col-md-2 text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+sameday_count+'" data-color="success" data-size="sm" name="ol_wa_switch['+sameday_count+']"/></div></div><div class="col-md-2 text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="ol_wa_spkg['+sameday_count+']"></div></fieldset></div><div class="col-md-2 text-center"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_local_charges[]"></fieldset></div><div class="col-md-2"><fieldset class="form-group"><input type="number" class="form-control" min="0" value="" name="ol_wa_national_charges[]"></fieldset></div></div>';
        let htmdiv1 = '<div class="row" id="sameday_weight_row'+sameday_count+'"><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_up['+sameday_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_range_down['+sameday_count+']"></fieldset></div><div class="col text-center"><div class="form-group " style="padding-top: 8px;"><input type="checkbox" id="" class="switchery weightAdditionDetain'+sameday_count+'" data-color="success" data-size="sm" name="sameday_wa_switch['+sameday_count+']"/></div></div><div class="col text-center"><fieldset style="padding-top: 5px;"><div class="input-group input-group-sm form-group"><input type="text" class="touchspin-color input-sm spkg" value="0" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="sameday_wa_spkg['+sameday_count+']"></div></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required" value="" name="sameday_wa_local_charges['+sameday_count+']"></fieldset></div><div class="col text-center"><fieldset class="form-group"><input type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required"  name="sameday_wa_national_charges['+sameday_count+']"></fieldset></div><div class="col">\n' +
            '<span id="sameday_weight_close" class="btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.weight-addition-sameday').append(htmdiv1);
        var sameday_weight_switches = document.querySelector('.switchery.weightAdditionDetain'+sameday_count);
        var switchery = new Switchery(sameday_weight_switches, { disabled: false,color: '#37BC9B',size:'small' });
       $(".touchspin-color").TouchSpin({
            buttondown_class: "btn btn-success",
            buttonup_class: "btn btn-success",
            buttondown_txt: '<i class="ft-minus"></i>',
            buttonup_txt: '<i class="ft-plus"></i>'
        });
       masks();
       sameday_weight_switches.onchange = function () {

            if (sameday_weight_switches.checked === true) {
                // $(this).next('.spkg').attr('disabled','');
                // $(this).closest('div.col-md-2').find('input.spkg').attr('disabled','');

                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
            } else if (sameday_weight_switches.checked === false) {
                $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

            }

        };
        $("#sameday_weight_row"+sameday_count+" .validated").each(function(){
            $( this ).rules( "add", {
                required: true,
            });

        });
        sameday_count++;
    });

    //addMoreSlabs
    $('body').on('click','#sameday_weight_close',function () {
        $(this).parent().parent().remove();
    });
    $('body').on('click','.sameday_row_delete',function () {
        $(this).parent().parent().remove();
    });
    var sameday_slab_count = 1;
    $('body').on('click','#samedayaddMoreSlabs',function () {
        let htmdiv = '<div class="row" id="sameday_cash_handle_'+sameday_slab_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_range_up['+sameday_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_range_down['+sameday_slab_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_cash_charges['+sameday_slab_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.cash-handling-div-sameday').append(htmdiv);
        sameday_slab_count++;
        masks();
        // $(this).parent().prev().find('div.slabs').append(htmdiv);
        // console.log();

    });
    //add more slabs insurance
     var sameday_ins_count = 1;
    $('body').on('click','#samedayaddMoreSlabsInsurance',function () {
        let htmdiv = '<div class="row" id="sameday_insurance_charge_'+sameday_ins_count+'">\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_range_up['+sameday_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_range_down['+sameday_ins_count+']" type="text" class="form-control numeric validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '\n' +
            '                                                <div class="col-md-2 text-center">\n' +
            '                                                    <fieldset class="form-group">\n' +
            '                                                        <input name="sameday_ins_charges['+sameday_ins_count+']" type="text" class="form-control amount validated" data-rule-required="true" data-msg-required="This field is required">\n' +
            '                                                    </fieldset>\n' +
            '                                                </div>\n' +
            '<div class="col">\n' +
            '<span  class="sameday_row_delete btn btn-danger rounded btn-sm-width mr-1 mb-1"><i class="ft-x"></i></span></div></div>';
        $('.insurance-charges-div-sameday').append(htmdiv);
            sameday_ins_count++;
            masks();
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
    
    //Detain end
    //end sameday
    //for discounts Overnight
    var ondiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOvernight'));


        ondiscountSwitch[0].onchange = function () {
            ONdiscount(ondiscountSwitch[0]);
        };
        ondiscountSwitch[1].onchange = function () {
            ONdiscount(ondiscountSwitch[1]);
        };
        ondiscountSwitch[2].onchange = function () {
            ONdiscount(ondiscountSwitch[2]);
        };
        ondiscountSwitch[3].onchange = function () {
            ONdiscount(ondiscountSwitch[3]);
        };
        ondiscountSwitch[4].onchange = function () {
            ONdiscount(ondiscountSwitch[4]);
        };
        // $.each(ondiscountSwitch,function () {
        //     console.log('heeee');
        // });
    function ONdiscount(eve) {
        if(eve.checked === true){

            $(eve).parent().parent().next().prop('disabled',false);
            $('input[name="on_discount_title"]').prop('disabled',false);
            $('input[name="on_daterange"]').prop('disabled',false);

        }else if(eve.checked === false){
            $(eve).parent().parent().next().prop('disabled',true);

                if(ondiscountSwitch[0].checked === true || ondiscountSwitch[1].checked === true || ondiscountSwitch[2].checked === true || ondiscountSwitch[3].checked === true || ondiscountSwitch[4].checked === true){
                    $('input[name="on_discount_title"]').prop('disabled',false);
                    $('input[name="on_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="on_discount_title"]').prop('disabled',true);
                    $('input[name="on_daterange"]').prop('disabled',true);
                }

        }
    }
    //for discounts Overland
    var overlandDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesOverland'));


    overlandDiscountSwitch[0].onchange = function () {
        OLdiscount(overlandDiscountSwitch[0]);
    };
    overlandDiscountSwitch[1].onchange = function () {
        OLdiscount(overlandDiscountSwitch[1]);
    };
    overlandDiscountSwitch[2].onchange = function () {
        OLdiscount(overlandDiscountSwitch[2]);
    };
    overlandDiscountSwitch[3].onchange = function () {
        OLdiscount(overlandDiscountSwitch[3]);
    };
    overlandDiscountSwitch[4].onchange = function () {
        OLdiscount(overlandDiscountSwitch[4]);
    };
   function OLdiscount(eveOver) {
        if(eveOver.checked === true){

            $(eveOver).parent().parent().next().prop('disabled',false);
            $('input[name="ol_discount_title"]').prop('disabled',false);
            $('input[name="ol_daterange"]').prop('disabled',false);

        }else if(eveOver.checked === false){
            $(eveOver).parent().parent().next().prop('disabled',true);

                if(overlandDiscountSwitch[0].checked === true || overlandDiscountSwitch[1].checked === true || overlandDiscountSwitch[2].checked === true || overlandDiscountSwitch[3].checked === true || overlandDiscountSwitch[4].checked === true){
                    $('input[name="ol_discount_title"]').prop('disabled',false);
                    $('input[name="ol_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="ol_discount_title"]').prop('disabled',true);
                    $('input[name="ol_daterange"]').prop('disabled',true);
                }

        }
    }

    //for discounts Overland
    var detainDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesDetain'));


    detainDiscountSwitch[0].onchange = function () {
        Detaindiscount(detainDiscountSwitch[0]);
    };
    detainDiscountSwitch[1].onchange = function () {
        Detaindiscount(detainDiscountSwitch[1]);
    };
    detainDiscountSwitch[2].onchange = function () {
        Detaindiscount(detainDiscountSwitch[2]);
    };
    detainDiscountSwitch[3].onchange = function () {
        Detaindiscount(detainDiscountSwitch[3]);
    };
    detainDiscountSwitch[4].onchange = function () {
        Detaindiscount(detainDiscountSwitch[4]);
    };
    function Detaindiscount(eveDet) {
         if(eveDet.checked === true){

            $(eveDet).parent().parent().next().prop('disabled',false);
            $('input[name="detain_discount_title"]').prop('disabled',false);
            $('input[name="detain_daterange"]').prop('disabled',false);

        }else if(eveDet.checked === false){
            $(eveDet).parent().parent().next().prop('disabled',true);

                if(detainDiscountSwitch[0].checked === true || detainDiscountSwitch[1].checked === true || detainDiscountSwitch[2].checked === true || detainDiscountSwitch[3].checked === true || detainDiscountSwitch[4].checked === true){
                    $('input[name="detain_discount_title"]').prop('disabled',false);
                    $('input[name="detain_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="detain_discount_title"]').prop('disabled',true);
                    $('input[name="detain_daterange"]').prop('disabled',true);
                }

        }
    }
    //for discounts Overland
        var samedayDiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discountSwitchesSameday'));


        samedayDiscountSwitch[0].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[0]);
        };
        samedayDiscountSwitch[1].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[1]);
        };
        samedayDiscountSwitch[2].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[2]);
        };
        samedayDiscountSwitch[3].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[3]);
        };
        samedayDiscountSwitch[4].onchange = function () {
            SamedayDiscount(samedayDiscountSwitch[4]);
        };
        function SamedayDiscount(eveSameday) {
            if(eveSameday.checked === true){

            $(eveSameday).parent().parent().next().prop('disabled',false);
            $('input[name="sameday_discount_title"]').prop('disabled',false);
            $('input[name="sameday_daterange"]').prop('disabled',false);

        }else if(eveSameday.checked === false){
            $(eveSameday).parent().parent().next().prop('disabled',true);

                if(samedayDiscountSwitch[0].checked === true || samedayDiscountSwitch[1].checked === true || samedayDiscountSwitch[2].checked === true || samedayDiscountSwitch[3].checked === true || samedayDiscountSwitch[4].checked === true){
                    $('input[name="sameday_discount_title"]').prop('disabled',false);
                    $('input[name="sameday_daterange"]').prop('disabled',false);
                }else{
                    $('input[name="sameday_discount_title"]').prop('disabled',true);
                    $('input[name="sameday_daterange"]').prop('disabled',true);
                }

        }
    }
        //Main switches
    // overnightSwitch.onchange = function() {
    //     if(overnightSwitch.checked === true){
    //         errors = 0;
    //     }else if(overnightSwitch.checked === false){
    //         errors = 1;
    //     }
    // };
    // overlandSwitch.onchange = function() {
    //     if(overlandSwitch.checked === true){
    //         errors = 0;
    //     }else if(overlandSwitch.checked === false){
    //         errors = 1;
    //     }
    // };
    // detainSwitch.onchange = function() {
    //     if(detainSwitch.checked === true){
    //         errors = 0;
    //     }else if(detainSwitch.checked === false){
    //         errors = 1;
    //     }
    // };
    // samedayDiscountSwitch.onchange = function() {
    //     if(samedaySwitch.checked === true){
    //         errors = 0;
    //     }else if(samedaySwitch.checked === false){
    //         errors = 1;
    //     }
    // };
    //


    // var overnight_switch = new Switchery('#overnight_switch');

    // $('#overnight_switch').bind('change', function() {
    //     // var switchery = new Switchery(overnightSwitch);
    //     // overnight_switch.disable();
    //     // setTimeout(function(){ overnightSwitch.disable(); }, 1000);
    //     if(this.checked == true){
    //         $('#overnight').collapse('show');
    //     }else{
    //         $('#overnight').collapse('hide');
    //     }
    // });
// $("#overnight_switch").dblclick(function (event)
// {
//     console.log('double');
//     event.preventDefault();
// });


});
