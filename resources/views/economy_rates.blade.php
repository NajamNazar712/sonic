@extends('admin.layout.master')

@section('title', 'Economy Rates')

@section('content')
    <h1>Economy Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}</h2>
                        @include('admin.inc.messages')
                    </div>

                    <form id="ratesAdditionForm" class="card-body card-dashboard" action="" method="post" novalidate="novalidate">
                        @csrf
                        <div class="card-content">
                            <div class="card-header border-success">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">Zone 1</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="z1_main_switch"><input name="z1_main_switch" type="checkbox"  class="switchery z1-main-switch" data-size="sm" /></a>
                                    </div>
                                </div>
                            </div>
                            <div id="zone1" class="card border-success hide"
                                 aria-expanded="true">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="weight-addition">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <h3 class="card-title">Weight Charges</h3>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <label class="card-title">Range Up</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Range Down</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Weight Addition</label>
                                                </div>
                                                <div class="col-2 text-center">
                                                    <label class="card-title">KG Range</label>
                                                </div>
                                                <div class="col text-center">
                                                    <label class="card-title">Flat Charges</label>
                                                </div>
                                                <div class="col-1"></div>
                                            </div>
                                            <div class="row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01" name="z1_range_up[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal range_down" data-rule-required="true" data-msg-required="This field is required" value="" name="z1_range_down[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" class="switchery weightAddition" data-color="success" data-size="sm" name="z1_wa_switch[]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="z1_wa_spkg[]" data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="" name="z1_flat_charges[]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                    </div>
                                                </div>
                                        </div>{{--weight addition div--}}
                                        <button type="button" class="btn btn-outline-success mr-1 waddition_btn" title="Add more slabs"><i class="la la-plus"></i></button>
                                    </div>
                                </div>
                            </div>


                            <div class="text-center mt-2">
                                <div class="form-group">

                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Submit</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>


@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        .hide{
            display:none;
        }
    </style>


@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5,step: 0.5, decimals: 2});

            $('#z1_main_switch').on('change',function(){

                var z1mainswitch = document.querySelector('.switchery.z1-main-switch');
                if (z1mainswitch.checked === true) {
                    $('#zone1').slideDown('slow');

                } else if (z1mainswitch.checked === false) {
                    $('#zone1').slideUp('slow');
                }
            });

            $('#z2_main_switch').on('change',function(){

                var z2mainswitch = document.querySelector('.switchery.z2-main-switch');
                if (z2mainswitch.checked === true) {
                    $('#zone2').slideDown('slow');

                } else if (z2mainswitch.checked === false) {
                    $('#zone2').slideUp('slow');


                }
            });

            $('#z3_main_switch').on('change',function(){

                var z3mainswitch = document.querySelector('.switchery.z3-main-switch');
                if (z3mainswitch.checked === true) {
                    $('#zone3').slideDown('slow');

                } else if (z3mainswitch.checked === false) {
                    $('#zone3').slideUp('slow');
                }
            });

            $('#z4_main_switch').on('change',function(){

                var z4mainswitch = document.querySelector('.switchery.z4-main-switch');
                if (z4mainswitch.checked === true) {
                    $('#zone4').slideDown('slow');

                } else if (z4mainswitch.checked === false) {
                    $('#zone4').slideUp('slow');

                }
            });

            $('#z5_main_switch').on('change',function(){

                var z5mainswitch = document.querySelector('.switchery.z5-main-switch');
                if (z5mainswitch.checked === true) {
                    $('#zone5').slideDown('slow');

                } else if (z5mainswitch.checked === false) {
                    $('#zone5').slideUp('slow');
                }
            });

            $('#z6_main_switch').on('change',function(){

                var z6mainswitch = document.querySelector('.switchery.z6-main-switch');
                if (z6mainswitch.checked === true) {
                    $('#zone6').slideDown('slow');

                } else if (z6mainswitch.checked === false) {
                    $('#zone6').slideUp('slow');
                }
            });

            $('#z7_main_switch').on('change',function(){
                var z7mainswitch = document.querySelector('.switchery.z7-main-switch');
                if (z7mainswitch.checked === true) {
                    $('#zone7').slideDown('slow');

                } else if (z7mainswitch.checked === false) {
                    $('#zone7').slideUp('slow');
                }
            });

            $('#z8_main_switch').on('change',function(){

                var z8mainswitch = document.querySelector('.switchery.z8-main-switch');
                if (z8mainswitch.checked === true) {
                    $('#zone8').slideDown('slow');

                } else if (z8mainswitch.checked === false) {
                    $('#zone8').slideUp('slow');

                }
            });

            $('#z9_main_switch').on('change',function(){

                var z9mainswitch = document.querySelector('.switchery.z9-main-switch');
                if (z9mainswitch.checked === true) {
                    $('#zone9').slideDown('slow');

                } else if (z9mainswitch.checked === false) {
                    $('#zone9').slideUp('slow');
                }
            });

            $('#z10_main_switch').on('change',function(){
                var z10mainswitch = document.querySelector('.switchery.z10-main-switch');
                if (z10mainswitch.checked === true) {
                    $('#zone10').slideDown('slow');

                } else if (z10mainswitch.checked === false) {
                    $('#zone10').slideUp('slow');
                }
            });

            $('#z11_main_switch').on('change',function(){

                var z11mainswitch = document.querySelector('.switchery.z11-main-switch');
                if (z11mainswitch.checked === true) {
                    $('#zone11').slideDown('slow');

                } else if (z11mainswitch.checked === false) {
                    $('#zone11').slideUp('slow');

                }
            });

            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 100000
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

            function weight_addition(elm)
            {
                if ($(elm).prop('checked') === true) {
                    $(elm).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                } else if ($(elm).prop('checked') === false) {
                    $(elm).parent().parent().next().children().find('input.spkg').prop('disabled', true);
                }
            }
            $('.weightAddition').on('change',function(){
                weight_addition($(this));
            });


            function masks() {

                $('.decimal').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 100000
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
            }

            $('body').on('click','.weight_close',function () {
                $(this).parent().parent().remove();
            });

            $('body').on('click','.waddition_btn',function () {
                let htmdiv = '<div class="row weight_row">\n' +
                    '           <div class="col text-center">\n' +
                    '              <fieldset class="form-group">\n' +
                    '                <input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="z1_range_up[]">\n' +
                    '              </fieldset>\n' +
                    '            </div>\n' +
                    '            <div class="col text-center">\n' +
                    '              <fieldset class="form-group">\n' +
                    '                <input type="text" class="form-control decimal range_down validated" data-rule-required="true" data-msg-required="This field is required" value="" name="z1_range_down[]">\n' +
                    '              </fieldset>\n' +
                    '             </div>\n' +
                    '             <div class="col text-center">\n' +
                    '                <div class="form-group " style="padding-top: 8px;">\n' +
                    '                    <input type="checkbox" class="switchery weightAddition" data-color="success" data-size="sm" name="z1_wa_switch[]"/>\n' +
                    '                </div>\n' +
                    '              </div>\n' +
                    '              <div class="col-2 text-center">\n' +
                    '                 <fieldset style="padding-top: 5px;">\n' +
                    '                   <div class="input-group input-group-sm form-group">\n' +
                    '                       <input type="text" class="touchspin-color input-sm spkg validated" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="z1_wa_spkg[]" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                   </div>\n' +
                    '                  </fieldset>\n' +
                    '                </div>\n' +
                    '                <div class="col text-center">\n' +
                    '                  <fieldset class="form-group">\n' +
                    '                    <input type="text" class="form-control decimal validated" data-rule-required="true" data-msg-required="This field is required" value="" name="z1_flat_charges[]">\n' +
                    '                  </fieldset>\n' +
                    '                </div>\n' +
                    '                <div class="col-1">\n' +
                    '                   <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>'+
                    '                </div>\n' +
                    '          </div>';
                $(this).siblings('.weight-addition').append(htmdiv);
                var switches = document.querySelector('.switchery.weightAddition');
                var switchery = new Switchery(switches, { disabled: false,color: '#37BC9B',size:'small' });
                $(".touchspin-color").TouchSpin({
                    min: 0.5,
                    max: 100,
                    step: 0.5,
                    decimals: 2,
                    buttondown_class: "btn btn-success",
                    buttonup_class: "btn btn-success",
                    buttondown_txt: '<i class="ft-minus"></i>',
                    buttonup_txt: '<i class="ft-plus"></i>'
                });

                masks();

                switches.onchange = function () {
                    weight_addition($(this));
                };
                $(".weight_row .validated").each(function(){
                    $( this ).rules( "add", {
                        required: true,
                    });

                });
            });

            $( "#ratesAdditionForm" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });

        });

    </script>
@endsection