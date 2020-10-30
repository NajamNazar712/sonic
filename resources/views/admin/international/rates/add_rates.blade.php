@extends('admin.layout.master')

@section('title', 'Add Rates')

@section('content')
    <h1>Add Rates</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h2 class="font-large-1">{{$shipper->name}}
                            @if($shipper->account_type_id == 1)
                                <div class="badge badge-success pull-right">Reimbursement Account</div>
                            @else
                                <div class="badge badge-success pull-right">Corporate Invoicing Account</div>
                            @endif
                        </h2>
                        @include('admin.inc.messages')
                    </div>

                    <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.international.rates.add.submit')}}" method="post" novalidate="novalidate">
                        @csrf
                        <input type="hidden" name="shipper_id" value="{{$shipper->id}}">
                        <div class="card-content">
                            <div class="box_parent_div">
                                <div class="parent_box_div_1">
                                    <div class="card-header border-success">
                                        <input type="hidden" name="box_ids[]" value="1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3 class="display-inline card-title lead success">International Rates 1</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card border-success">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <h3>Add Hubs</h3>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group card border-success p-2">
                                                            <select name="hubs[1][]" id="select_box_1" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="wa_rows_div_1">
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
                                                        <div class="col text-center">
                                                            <label class="card-title">KG Range</label>
                                                        </div>
                                                        <div class="col text-center">
                                                            <label class="card-title">Local Charges</label>
                                                        </div>
                                                        <div class="col-1 text-center">
                                                        </div>
                                                    </div>

                                                    <div class="row" id="wa_row_1">
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up[1][1]">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">

                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down[1][1]">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">

                                                            <div class="form-group " style="padding-top: 8px;">
                                                                <input type="checkbox" class="switchery wa_switch" data-color="success" data-size="sm" name="wa_switch[1][1]"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-2 text-center">

                                                            <fieldset style="padding-top: 5px;">
                                                                <div class="input-group input-group-sm form-group">
                                                                    <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg[1][1]" data-rule-required="true" data-msg-required="This field is required">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges[1][1]">
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-1">
                                                        </div>

                                                    </div>

                                                </div>{{--weight addition div--}}
                                                <div>
                                                    <button type="button" class="btn btn-outline-success mr-1 wa_btn_1" title="Add more slabs"><i class="la la-plus"></i></button>
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Cash Handling Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="cash_handling_switch_1" class="switchery cash_handling_switch" data-color="success" data-size="sm" checked/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Range Up</label>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Range Down</label>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                    </div>
                                                </div>

                                                <div class="cash-handling-div-1 slabs">

                                                    <div class="row">
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="cash_range_up[1][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="cash_range_down[1][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="cash_charges[1][1]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required">
                                                            </fieldset>
                                                        </div>

                                                        <div class="col">
                                                        </div>

                                                    </div>

                                                </div>
                                                <div class="cash-handling-btn">
                                                    <button type="button" class="btn btn-outline-success mr-1 add_more_cash_slabs_1" title="Add more slabs" ><i class="la la-plus"></i></button>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Insurance Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="insurance_charges_switch_1" class="switchery insurance_charges_switch" data-color="success" data-size="sm" checked/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Range Up</label>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Range Down</label>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <label class="card-title">Charges</label>
                                                    </div>
                                                </div>
                                                <div class="insurance-charges-div-1 slabs">

                                                    <div class="row">
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ins_range_up[1][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ins_range_down[1][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2 text-center">
                                                            <fieldset class="form-group">
                                                                <input name="ins_charges[1][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent">
                                                            </fieldset>
                                                        </div>

                                                        <div class="col">
                                                        </div>

                                                    </div>

                                                </div>
                                                <div class="insurance-charges-btn">
                                                    <button type="button" class="btn btn-outline-success mr-1 add_more_ins_slabs_1" title="Add more slabs"><i class="la la-plus"></i></button>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h3 class="card-title">Return Charges</h3>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group ">
                                                            <input type="checkbox" name="return_charges_switch_1" class="switchery return_charges_switch" data-color="success" data-size="sm" checked/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row return-charges-div-1">

                                                    <div class="col-3 text-center">
                                                        <label class="card-title">Local Charges</label>
                                                        <fieldset class="form-group">
                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_1">
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="">
                                                    <h3 class="card-title">Discount Rates</h3>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-6">
                                                        <label class="">Title</label>
                                                        <div class='form-group'>
                                                            <input type='text' class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="discount_title_1"/>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="">Apply [to - from]</label>
                                                        <div class='input-group form-group'>
                                                            <input type='text' class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="daterange_1"/>
                                                            <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="la la-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col text-center">
                                                        <fieldset>
                                                            <div class="input-group input-group-sm form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="">Weight</span>
                                                                </div>
                                                                <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox" class="switchery discount_switch_1" name="discount_weight_switch_1" data-size="xs" />
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_weight_1" disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset>
                                                            <div class="input-group input-group-sm form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="">Cash</span>
                                                                </div>
                                                                <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="discount_cash_switch_1" class="switchery discount_switch_1" data-size="xs" />
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_cash_1" disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset>
                                                            <div class="input-group input-group-sm form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="">Insurance</span>
                                                                </div>
                                                                <div class="input-group-prepend">
                                                              <span class="input-group-text" id="">
                                                                <input type="checkbox" name="discount_insurance_switch_1" class="switchery discount_switch_1" data-size="xs" />
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_insurance_1" disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset>
                                                            <div class="input-group input-group-sm form-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="">Return</span>
                                                                </div>
                                                                <div class="input-group-prepend">
                                                              <span class="input-group-text">
                                                                <input type="checkbox"  class="switchery discount_switch_1" data-size="xs" name="discount_return_switch_1"/>
                                                              </span>
                                                                </div>
                                                                <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_return_1" disabled>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group text-center">
                                <button id="add_more_rates_hubs" type="button" class="btn btn-outline-success round btn-min-width mr-1 mb-1">Add Rates and Hub</button>
                            </div>
                            <div class="row mt-2 justify-content-center">
                                <div class="col-5 form-group">
                                    <textarea name="rate_remarks" id="rate_remarks" class="form-control" placeholder="Rate Remarks..." rows="3"></textarea>
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
            $(".daterange").daterangepicker();
            $('#select_box_1').select2({
                width:'100%',
                placeholder:"Select Hub(s)",
                allowClear:true
            });
            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5,step: 0.5, decimals: 2});
            $('.decimal').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 10000
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

            $('.percent').inputmask({
                'alias': 'numeric',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 500
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });
            $('.dec-percent').inputmask("Regex",{
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
            });


            var wa_switch = document.querySelector('.switchery.wa_switch');
            var cashhandlingswitch = document.querySelector('.switchery.cash_handling_switch');
            var insuranceChargesSwitch = document.querySelector('.switchery.insurance_charges_switch');
            var returnChargesSwitch = document.querySelector('.switchery.return_charges_switch');

            $('.wa_switch').on('change',function(){
                var wid = $(this).attr('name');
                var wswitch = document.querySelector('input[name="'+ wid +'"]');
                if (wswitch.checked === true) {

                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

                } else if (wswitch.checked === false) {
                    $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                }
            });

            function masks() {

                $('.decimal').inputmask({
                    'alias': 'decimal',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'digits': 2,
                    'min': 0.00,
                    'max': 10000
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
                    'alias': 'integer',
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    'min': 0,
                    'max': 1000000
                });
                $('.dec-percent').inputmask("Regex",{
                    'allowMinus': false,
                    'allowPlus': false,
                    'rightAlign': false,
                    regex: '^\\d{1,9}(\\.\\d{1,2})?%?$'
                });
            }
            $('body').on('click','.weight_close',function () {
                $(this).parent().parent().remove();
            });
            var box_no = 1;
            var wa_rows = 2;
            $('body').on('click','button.wa_btn_1',function () {
                let html = '<div class="row" id="wa_row_1_'+ wa_rows +'">\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up[1]['+ wa_rows +']">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down[1]['+ wa_rows +']">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                    '                                                            <input type="checkbox" class="switchery wa_switch'+ wa_rows +'" data-color="success" data-size="sm" name="wa_switch[1]['+ wa_rows +']"/>\n' +
                    '                                                        </div>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-2 text-center">\n' +
                    '\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg[1]['+ wa_rows +']" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges[1]['+ wa_rows +']">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-1">\n' +
                    '                                                   <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>';
                $('#wa_rows_div_1').append(html);
                var switches = document.querySelector('.switchery.wa_switch'+wa_rows);
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

                    if (switches.checked === true) {

                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                    } else if (switches.checked === false) {
                        $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                    }

                };
                var row_handel = 'wa_row_1_'+ wa_rows+' .validated';
                $("#"+row_handel).each(function(){
                    $( this ).rules( "add", {
                        required: true,
                    });

                });
                wa_rows++;

            });

            var cash_count = 2;
            $('body').on('click','button.add_more_cash_slabs_1',function () {
                let htmdiv = '<div class="row" id="cash_handle_1_'+ cash_count +'">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="cash_range_up[1]['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="cash_range_down[1]['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="cash_charges[1]['+cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                    '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                $('.cash-handling-div-1').append(htmdiv);
                masks();
                $("#cash_handle_1"+cash_count+" .validated").each(function(){
                    $( this ).rules( "add", {
                        required: true,
                    });

                });
                // $(this).parent().prev().find('div.slabs').append(htmdiv);
                // console.log();
                cash_count++;
            });

            var ins_count = 2;
            $('body').on('click','button.add_more_ins_slabs_1',function () {
                let htmdiv = '<div class="row" id="insurance_charge_1'+ins_count+'">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="ins_range_up[1]['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="ins_range_down[1]['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input name="ins_charges[1]['+ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '<div class="col">\n' +
                    '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                $('.insurance-charges-div-1').append(htmdiv);
                masks();
                $("#insurance_charge_1"+ins_count+" .validated").each(function(){
                    $( this ).rules( "add", {
                        required: true,
                    });

                });
                ins_count++;
            });

            cashhandlingswitch.onchange = function () {
                if(cashhandlingswitch.checked === true){
                    $('.cash-handling-div-1').find('input').prop('disabled',false);
                    $('.add_more_cash_slabs_1').prop('disabled',false);
                }else if(cashhandlingswitch.checked === false){
                    $('.cash-handling-div-1').find('input').prop('disabled',true);
                    $('.add_more_cash_slabs_1').prop('disabled',true);

                }
            };

            // InsuranceOvernight
            insuranceChargesSwitch.onchange = function () {
                if(insuranceChargesSwitch.checked === true){
                    $('.insurance-charges-div-1').find('input').prop('disabled',false);
                    $('.add_more_ins_slabs_1').prop('disabled',false);
                }else if(insuranceChargesSwitch.checked === false){
                    $('.insurance-charges-div-1').find('input').prop('disabled',true);
                    $('.add_more_ins_slabs_1').prop('disabled',true);

                }
            };

            // Return Overnight
            returnChargesSwitch.onchange = function () {
                if(returnChargesSwitch.checked === true){
                    $('.return-charges-div-1').find('input').prop('disabled',false);
                }else if(returnChargesSwitch.checked === false){
                    $('.return-charges-div-1').find('input').prop('disabled',true);

                }
            };

            //for discounts Overnight
            var ondiscountSwitch = Array.prototype.slice.call(document.querySelectorAll('.discount_switch_1'));


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

            function ONdiscount(eve) {
                if(eve.checked === true){

                    $(eve).parent().parent().next().prop('disabled',false);
                    $('input[name="discount_title_1"]').prop('disabled',false);
                    $('input[name="daterange_1"]').prop('disabled',false);

                }else if(eve.checked === false){
                    $(eve).parent().parent().next().prop('disabled',true);

                    if(ondiscountSwitch[0].checked === true || ondiscountSwitch[1].checked === true || ondiscountSwitch[2].checked === true || ondiscountSwitch[3].checked === true){
                        $('input[name="discount_title_1"]').prop('disabled',false);
                        $('input[name="daterange_1"]').prop('disabled',false);
                    }else{
                        $('input[name="discount_title_1"]').prop('disabled',true);
                        $('input[name="daterange_1"]').prop('disabled',true);
                    }

                }
            }
            var cities = @json($cities);
            var city_data = $.map(cities, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });

            $('#add_more_rates_hubs').on('click', function () {
                // $('.parent_box_div_'+box_no).find('input').prop('disabled', true);
                // $('.parent_box_div_'+box_no).find('select').prop('disabled', true);
                $('.parent_box_div_'+box_no).find('button').prop('disabled', true);
                box_no++;
                var box_div = '<div class="parent_box_div_'+ box_no +'"><div class="card-header border-success">\n' +
                    '                                    <input type="hidden" value="'+ box_no +'" name="box_ids[]">\n' +
                    '                                    <div class="row">\n' +
                    '                                        <div class="col-md-6">\n' +
                    '                                            <h3 class="display-inline card-title lead success">International Rates '+ box_no +'</h3>\n' +
                    '                                        </div>\n' +
                    '                                        <div class="col-md-6 text-right">\n' +
                    '                                            <span class="btn btn-danger rounded btn-sm-width rate_box_close" box="'+ box_no +'"><i class="ft-trash"></i></span>\n' +
                    '                                        </div>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                                <div class="card border-success">\n' +
                    '                                    <div class="card-content">\n' +
                    '                                        <div class="card-body">\n' +
                    '                                           <div class="row">\n' +
                    '                                             <div class="col-2">\n' +
                    '                                             <h3>Add Hubs</h3>\n' +
                    '                                             </div>\n' +
                    '                                             <div class="col-6">\n' +
                    '                                             <div class="form-group card border-success p-2">\n' +
                    '                                             <select name="hubs['+ box_no +'][]" id="select_box_'+ box_no +'" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                             </select>\n' +
                    '                                             </div>\n' +
                    '                                             </div>\n' +
                    '                                             </div>\n' +
                    '                                            <div id="wa_rows_div_'+ box_no +'">\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col-md-2">\n' +
                    '                                                        <h3 class="card-title">Weight Charges</h3>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Range Up</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Range Down</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Weight Addition</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">KG Range</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <label class="card-title">Local Charges</label>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-1 text-center">\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                                <div class="row" id="weight_row">\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '\n' +
                    '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                    '                                                            <input type="checkbox" class="switchery wa_switch_'+ box_no +'" data-color="success" data-size="sm" name="wa_switch['+ box_no +'][1]"/>\n' +
                    '                                                        </div>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-2 text-center">\n' +
                    '\n' +
                    '                                                        <fieldset style="padding-top: 5px;">\n' +
                    '                                                            <div class="input-group input-group-sm form-group">\n' +
                    '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                                            </div>\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges['+ box_no +'][1]">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-1">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div>\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 wa_btn_'+ box_no +'" title="Add more slabs"><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <hr>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Cash Handling Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="cash_handling_switch_'+ box_no +'" class="switchery cash_handling_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Up</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Down</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Charges</label>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <div class="cash-handling-div-'+ box_no +' slabs">\n' +
                    '\n' +
                    '                                                <div class="row">\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_range_up['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_range_down['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="cash_charges['+ box_no +'][1]" type="text" class="form-control dec-percent" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div class="cash-handling-btn">\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 add_more_cash_slabs_'+ box_no +'" title="Add more slabs" ><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Insurance Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="insurance_charges_switch_'+ box_no +'" class="switchery insurance_charges_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Up</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Range Down</label>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2 text-center">\n' +
                    '                                                    <label class="card-title">Charges</label>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="insurance-charges-div-'+ box_no +' slabs">\n' +
                    '\n' +
                    '                                                <div class="row" id="insurance_handle_1_1">\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_range_up['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_range_down['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col-md-2 text-center">\n' +
                    '                                                        <fieldset class="form-group">\n' +
                    '                                                            <input name="ins_charges['+ box_no +'][1]" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent">\n' +
                    '                                                        </fieldset>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                    <div class="col">\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '\n' +
                    '                                            </div>\n' +
                    '                                            <div class="insurance-charges-btn">\n' +
                    '                                                <button type="button" class="btn btn-outline-success mr-1 add_more_ins_slabs_'+ box_no +'" title="Add more slabs"><i class="la la-plus"></i></button>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <h3 class="card-title">Return Charges</h3>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-2">\n' +
                    '                                                    <div class="form-group ">\n' +
                    '                                                        <input type="checkbox" name="return_charges_switch_'+ box_no +'" class="switchery return_charges_switch_'+ box_no +'" data-color="success" data-size="sm" checked/>\n' +
                    '                                                    </div>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '\n' +
                    '                                            <div class="row return-charges-div-'+ box_no +'">\n' +
                    '\n' +
                    '                                                <div class="col-3 text-center">\n' +
                    '                                                    <label class="card-title">Local Charges</label>\n' +
                    '                                                    <fieldset class="form-group">\n' +
                    '                                                        <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control amount" name="return_local_charges_'+ box_no +'">\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <hr>\n' +
                    '                                            <div class="">\n' +
                    '                                                <h3 class="card-title">Discount Rates</h3>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row mt-1">\n' +
                    '                                                <div class="col-md-6">\n' +
                    '                                                    <label class="">Title</label>\n' +
                    '                                                    <div class="form-group">\n' +
                    '                                                        <input type="text" class="form-control" data-rule-required="true" data-msg-required="This field is required" disabled name="discount_title_'+ box_no +'"/>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col-md-6">\n' +
                    '                                                    <label class="">Apply [to - from]</label>\n' +
                    '                                                    <div class="input-group form-group">\n' +
                    '                                                        <input type="text" class="form-control daterange" data-rule-required="true" data-msg-required="This field is required" disabled name="daterange_'+ box_no +'"/>\n' +
                    '                                                        <div class="input-group-append">\n' +
                    '                                                            <span class="input-group-text">\n' +
                    '                                                              <span class="la la-calendar"></span>\n' +
                    '                                                            </span>\n' +
                    '                                                        </div>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                            <div class="row">\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text">Weight</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text">\n' +
                    '                                                                <input type="checkbox" class="switchery discount_switch_'+ box_no +'" name="discount_weight_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_weight_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Cash</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text" id="">\n' +
                    '                                                                <input type="checkbox" name="discount_cash_switch_'+ box_no +'" class="switchery discount_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_cash_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Insurance</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text" id="">\n' +
                    '                                                                <input type="checkbox" name="discount_insurance_switch_'+ box_no +'" class="switchery discount_switch_'+ box_no +'" data-size="xs" />\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_insurance_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                                <div class="col text-center">\n' +
                    '                                                    <fieldset>\n' +
                    '                                                        <div class="input-group input-group-sm form-group">\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                                <span class="input-group-text" id="">Return</span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <div class="input-group-prepend">\n' +
                    '                                                              <span class="input-group-text">\n' +
                    '                                                                <input type="checkbox"  class="switchery discount_switch_'+ box_no +'" data-size="xs" name="discount_return_switch_'+ box_no +'"/>\n' +
                    '                                                              </span>\n' +
                    '                                                            </div>\n' +
                    '                                                            <input type="text" data-rule-required="true" data-msg-required="This field is required" class="form-control dec-percent discount-inp" name="discount_return_'+ box_no +'" disabled>\n' +
                    '                                                        </div>\n' +
                    '                                                    </fieldset>\n' +
                    '                                                </div>\n' +
                    '                                            </div>\n' +
                    '                                        </div>\n' +
                    '                                    </div>\n' +
                    '                                </div></div>';
                    $('.box_parent_div').append(box_div);

                    masks();
                $(".daterange").daterangepicker();
                $('#select_box_'+box_no).select2({data:city_data,placeholder:'Select Hub(s)',allowClear:true});

                    var wa_switch = document.querySelector('.switchery.wa_switch_'+box_no);

                    var cashhandlingswitch = document.querySelector('.switchery.cash_handling_switch_'+box_no);
                    var insuranceChargesSwitch = document.querySelector('.switchery.insurance_charges_switch_'+box_no);
                    var returnChargesSwitch = document.querySelector('.switchery.return_charges_switch_'+box_no);


                    var switchery = new Switchery(wa_switch, { disabled: false,color: '#37BC9B',size:'small' });
                    $('.wa_switch_'+box_no).on('change',function(){
                        var wid = $(this).attr('name');
                        var wswitch = document.querySelector('input[name="'+ wid +'"]');
                        if (wswitch.checked === true) {

                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);

                        } else if (wswitch.checked === false) {
                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                        }
                    });
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

                var switchery2 = new Switchery(cashhandlingswitch, { disabled: false,color: '#37BC9B',size:'small' });
                var switchery3 = new Switchery(insuranceChargesSwitch, { disabled: false,color: '#37BC9B',size:'small' });
                var switchery4 = new Switchery(returnChargesSwitch, { disabled: false,color: '#37BC9B',size:'small' });

                //for discounts Overnight
                var discount_sw = '.discount_switch_'+box_no;
                var discountSwitch = Array.prototype.slice.call(document.querySelectorAll(discount_sw));
                $(discount_sw).each(function() {
                    new Switchery(this, { disabled: false,color: '#37BC9B',size:'small' });
                });
                // var switchery_discount = new Switchery(discountSwitch, { disabled: false,color: '#37BC9B',size:'small' });

                discountSwitch[0].onchange = function () {
                    IRdiscount(discountSwitch[0]);
                };
                discountSwitch[1].onchange = function () {
                    IRdiscount(discountSwitch[1]);
                };
                discountSwitch[2].onchange = function () {
                    IRdiscount(discountSwitch[2]);
                };
                discountSwitch[3].onchange = function () {
                    IRdiscount(discountSwitch[3]);
                };

                function IRdiscount(eve) {
                    var discount_title = $('input[name="discount_title_'+ box_no +'"]');
                    var discount_daterange = $('input[name="daterange_'+ box_no +'"]');
                    if(eve.checked === true){

                        $(eve).parent().parent().next().prop('disabled',false);
                        discount_title.prop('disabled',false);
                        discount_daterange.prop('disabled',false);

                    }else if(eve.checked === false){
                        $(eve).parent().parent().next().prop('disabled',true);

                        if(discountSwitch[0].checked === true || discountSwitch[1].checked === true || discountSwitch[2].checked === true || discountSwitch[3].checked === true){
                            discount_title.prop('disabled',false);
                            discount_daterange.prop('disabled',false);
                        }else{
                            discount_title.prop('disabled',true);
                            discount_daterange.prop('disabled',true);
                        }

                    }
                }
                var cash_handling_div = $('.cash-handling-div-'+box_no);
                var cash_handling_btn = $('.add_more_cash_slabs_'+box_no);
                cashhandlingswitch.onchange = function () {
                    if(cashhandlingswitch.checked === true){
                        cash_handling_div.find('input').prop('disabled',false);
                        cash_handling_btn.prop('disabled',false);
                    }else if(cashhandlingswitch.checked === false){
                        cash_handling_div.find('input').prop('disabled',true);
                        cash_handling_btn.prop('disabled',true);

                    }
                };

                // InsuranceOvernight
                insuranceChargesSwitch.onchange = function () {
                    var insurance_charges_div = $('.insurance-charges-div-'+box_no);
                    var insurance_charges_btn = $('.add_more_ins_slabs_'+box_no);
                    if(insuranceChargesSwitch.checked === true){
                        insurance_charges_div.find('input').prop('disabled',false);
                        insurance_charges_btn.prop('disabled',false);
                    }else if(insuranceChargesSwitch.checked === false){
                        insurance_charges_div.find('input').prop('disabled',true);
                        insurance_charges_btn.prop('disabled',true);

                    }
                };

                // Return Overnight
                returnChargesSwitch.onchange = function () {
                    var return_charges_div = $('.return-charges-div-'+box_no);
                    if(returnChargesSwitch.checked === true){
                        return_charges_div.find('input').prop('disabled',false);
                    }else if(returnChargesSwitch.checked === false){
                        return_charges_div.find('input').prop('disabled',true);

                    }
                };

                var dynamic_wa_rows = 2;
                $('body').on('click','button.wa_btn_'+box_no,function () {
                    let html = '<div class="row" id="wa_row_'+ box_no +'_'+ dynamic_wa_rows +'">\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_up['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="range_down['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '\n' +
                        '                                                        <div class="form-group " style="padding-top: 8px;">\n' +
                        '                                                            <input type="checkbox" class="switchery wa_switch_'+ box_no +'_'+ dynamic_wa_rows +'" data-color="success" data-size="sm" name="wa_switch['+ box_no +']['+ dynamic_wa_rows +']"/>\n' +
                        '                                                        </div>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col-2 text-center">\n' +
                        '\n' +
                        '                                                        <fieldset style="padding-top: 5px;">\n' +
                        '                                                            <div class="input-group input-group-sm form-group">\n' +
                        '                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="spkg['+ box_no +']['+ dynamic_wa_rows +']" data-rule-required="true" data-msg-required="This field is required">\n' +
                        '                                                            </div>\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '                                                    <div class="col text-center">\n' +
                        '                                                        <fieldset class="form-group">\n' +
                        '                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" name="local_charges['+ box_no +']['+ dynamic_wa_rows +']">\n' +
                        '                                                        </fieldset>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                    <div class="col-1">\n' +
                        '                                                   <span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span>\n' +
                        '                                                    </div>\n' +
                        '\n' +
                        '                                                </div>';
                    $('#wa_rows_div_'+box_no).append(html);
                    var switches = document.querySelector('.switchery.wa_switch_'+ box_no +'_'+dynamic_wa_rows);
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

                        if (switches.checked === true) {

                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', false);
                        } else if (switches.checked === false) {
                            $(this).parent().parent().next().children().find('input.spkg').prop('disabled', true);

                        }

                    };
                    var row_handel = 'wa_row_'+ box_no +'_'+ dynamic_wa_rows+' .validated';
                    $("#"+row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });

                    dynamic_wa_rows++;

                });

                var dynamic_cash_count = 2;
                $('body').on('click','button.add_more_cash_slabs_'+box_no, function () {
                    let htmdiv = '<div class="row" id="cash_handle_'+ box_no +'_'+ dynamic_cash_count +'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_up['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated" >\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_range_down['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="cash_charges['+ box_no +']['+dynamic_cash_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated"></fieldset></div><div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.cash-handling-div-'+box_no).append(htmdiv);
                    masks();
                    var cash_row_handel = 'cash_handle_'+ box_no +'_'+ wa_rows+' .validated';
                    $(cash_row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });
                    });
                    // $(this).parent().prev().find('div.slabs').append(htmdiv);
                    // console.log();
                    dynamic_cash_count++;
                });

                var dynamic_ins_count = 2;
                $('body').on('click','button.add_more_ins_slabs_'+box_no,function () {
                    let htmdiv = '<div class="row" id="insurance_charge_1'+dynamic_ins_count+'">\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_up['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_range_down['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control numeric validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '\n' +
                        '                                                <div class="col-md-2 text-center">\n' +
                        '                                                    <fieldset class="form-group">\n' +
                        '                                                        <input name="ins_charges['+ box_no +']['+dynamic_ins_count+']" data-rule-required="true" data-msg-required="This field is required" type="text" class="form-control dec-percent validated">\n' +
                        '                                                    </fieldset>\n' +
                        '                                                </div>\n' +
                        '<div class="col">\n' +
                        '<span class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>';
                    $('.insurance-charges-div-'+box_no).append(htmdiv);
                    masks();
                    var ins_row_handel = 'insurance_charge_'+ box_no +'_'+ wa_rows+' .validated';
                    $(ins_row_handel).each(function(){
                        $( this ).rules( "add", {
                            required: true,
                        });

                    });
                    dynamic_ins_count++;
                });



            });
            $('body').on('change', '#rate_remarks', function () {
                $(this).val($(this).val().trim());
            });
            $('body').on('click', 'span.rate_box_close', function(){
                var box = $(this).attr('box');
                $('.parent_box_div_'+box).remove();
            });
            $( "#ratesAdditionForm" ).validate({
                ignore: ":not(:visible),:disabled",
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your rates are being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();

                }
            });
        });
    </script>
@endsection