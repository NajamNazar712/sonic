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
                        @if($view == null)
                    <form id="ratesAdditionForm" class="card-body card-dashboard" action="{{route('admin.international.rates.economy.store',$shipper->id)}}" method="post" novalidate="novalidate">
                        @csrf
                        @else
                            <div class="card-body card-dashboard">
                        @endif
                        <div class="card-content">
                            @php
                                $count = 0;
                            @endphp
                            @foreach($zones as $zone)
                            <div class="card-header border-success @if(!$loop->first) mt-1 @endif">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="display-inline card-title lead success">{{$zone->name}}</h3>
                                    </div>
                                    @php
                                        $checked = "checked";
                                        if(\App\Http\Models\Rates\InternationalEconomyRateStatus::where('user_id',$shipper->id)->exists())
                                        {
                                            $checked = "";
                                        }
                                        if(isset($data[$zone->id]))
                                        {
                                            $checked = "checked";
                                        }

                                    @endphp
                                    <div class="col-md-6">
                                        <a href="javascript:void(0);" class="pull-right" id="z{{$zone->id}}_main_switch"><input name="z{{$zone->id}}_main_switch" type="checkbox" value="{{$zone->id}}" {{$checked}} @if($view != null) disabled @endif  class="switchery z{{$zone->id}}-main-switch" data-size="sm" /></a>
                                    </div>
                                </div>
                            </div>
                            <div id="zone{{$zone->id}}" class="card border-success hide"
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
                                            @if(isset($data[$zone->id]))
                                                @foreach($data[$zone->id] as $rate)
                                                    <div class="row weight_row">
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="{{$rate->range_up}}" @if($loop->first) data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01" @endif id="range_up_{{$count}}" name="z{{$zone->id}}_range_up[{{$count}}]" @if($view != null) disabled @endif>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal range_down" data-rule-required="true" data-msg-required="This field is required" id="range_down_{{$count}}" value="{{$rate->range_down}}" name="z{{$zone->id}}_range_down[{{$count}}]" @if($view != null) disabled @endif>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <div class="form-group " style="padding-top: 8px;">
                                                                <input type="checkbox" @if($rate->weight_addition) checked @endif class="switchery weightAddition" data-color="success" data-size="sm" name="z{{$zone->id}}_wa_switch[{{$count}}]" @if($view != null) disabled @endif/>
                                                            </div>
                                                        </div>
                                                        <div class="col-2 text-center">
                                                            <fieldset style="padding-top: 5px;">
                                                                <div class="input-group input-group-sm form-group">
                                                                    <input type="text" class="touchspin-color input-sm spkg" value="{{$rate->kg_range}}" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="z{{$zone->id}}_wa_spkg[{{$count}}]" id="spkg_{{$count}}" @if($view != null) disabled @endif data-rule-required="true" data-msg-required="This field is required">
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col text-center">
                                                            <fieldset class="form-group">
                                                                <input type="text" class="form-control decimal" data-rule-required="true" id="flat_charges_{{$count}}" @if($view != null) disabled @endif data-msg-required="This field is required" value="{{$rate->flat_charges}}" name="z{{$zone->id}}_flat_charges[{{$count}}]">
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-1">
                                                            @if($view == null)
                                                                @if(!$loop->first)
                                                                    <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @php
                                                        $count++;
                                                    @endphp
                                                @endforeach
                                            @else
                                                <div class="row weight_row">
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" value="0.01" @if($view != null) disabled @endif  data-rule-min="0.01" data-msg-min="Minimum chargeable weight can not be less than 0.01" name="z{{$zone->id}}_range_up[{{$count}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal range_down" data-rule-required="true" data-msg-required="This field is required" value="" @if($view != null) disabled @endif  name="z{{$zone->id}}_range_down[{{$count}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <div class="form-group " style="padding-top: 8px;">
                                                            <input type="checkbox" class="switchery weightAddition" data-color="success" data-size="sm" @if($view != null) disabled @endif  name="z{{$zone->id}}_wa_switch[{{$count}}]"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <fieldset style="padding-top: 5px;">
                                                            <div class="input-group input-group-sm form-group">
                                                                <input type="text" class="touchspin-color input-sm spkg" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="z{{$zone->id}}_wa_spkg[{{$count}}]" @if($view != null) disabled @endif  data-rule-required="true" data-msg-required="This field is required">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col text-center">
                                                        <fieldset class="form-group">
                                                            <input type="text" class="form-control decimal" data-rule-required="true" data-msg-required="This field is required" @if($view != null) disabled @endif  value="" name="z{{$zone->id}}_flat_charges[{{$count}}]">
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-1">
                                                        </div>
                                                </div>
                                                @php
                                                    $count++;
                                                @endphp
                                            @endif
                                        </div>
                                        @if($view == null)
                                        <button type="button" class="btn btn-outline-success mr-1 waddition_btn" data-btn-type="z{{$zone->id}}" title="Add more slabs"><i class="la la-plus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($view == null)
                            <div class="text-center mt-2">
                                <div class="form-group">
                                    @if($rate_status != null && $rate_status->status == 1 && (session('role_id') == 1 || in_array(529, session('permissions'))))
                                        <button type="button" class="btn btn-outline-success round btn-min-width mr-1 mb-1 " id="approve_btn">Approve</button>
                                    @endif

                                    <button id="addRatesSubmit" type="submit" class="btn btn-outline-primary round btn-min-width mr-1 mb-1">Submit</button>
                                        @if($rate_status != null && $rate_status->status == 1 && (session('role_id') == 1 || in_array(529, session('permissions'))))
                                        <button type="button" class="btn btn-outline-danger round btn-min-width mr-1 mb-1 " id="reject_btn">Reject</button>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    @if($view == null)
                    </form>
                    @else
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </section>


    <div class="modal fade text-left" id="RejectRatesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RejectRatesModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Write a reason to reject rates!</h4>
                </div>
                <form action="{{route('admin.international.rates.economy.approve',$shipper->id)}}" method="post" id="approve_reject_form" novalidate="novalidate">
                    @csrf
                    <input type="hidden" id="status" name="status" value="">
                <div class="modal-body">
                    <textarea id="reject_reason" name="reject_reason" data-rule-required="true" data-msg-required="Rejection Reason is required" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger">Yes</button>
                </div>

                </form>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    @if($view == null)
    <style type="text/css">
        .hide{
            display:none;
        }
    </style>
    @endif

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $(".touchspin-color").trigger("touchspin.updatesettings", {min: 0.5,step: 0.5, decimals: 2});

            @foreach($zones as $zone)
            $('#z{{$zone->id}}_main_switch').on('change',function(){

                var z{{$zone->id}}mainswitch = document.querySelector('.switchery.z{{$zone->id}}-main-switch');
                if (z{{$zone->id}}mainswitch.checked === true) {
                    $('#zone{{$zone->id}}').slideDown('slow');

                } else if (z{{$zone->id}}mainswitch.checked === false) {
                    $('#zone{{$zone->id}}').slideUp('slow');
                }
            });

                $('#z{{$zone->id}}_main_switch').trigger('change');
            @endforeach

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

            @if($view == null)
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

            @if(count($data) > 0)
                $(".weightAddition").trigger('change');
            @endif
            @endif

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

            @if($view == null)
            $('body').on('click','.weight_close',function () {
                $(this).parent().parent().remove();
            });

            count = {{$count}};

            $('body').on('click','.waddition_btn',function () {
                let type = $(this).attr('data-btn-type');
                let range_up = parseFloat($(this).siblings('.weight-addition').find(".weight_row:last-child").find(".range_down").first().val()) + 0.01;
                let htmdiv = '<div class="row weight_row">\n' +
                    '           <div class="col text-center">\n' +
                    '              <fieldset class="form-group">\n' +
                    '                <input type="text" class="form-control decimal validated" id="range_up_'+count+'" data-rule-required="true" data-msg-required="This field is required" value="'+range_up+'" name="'+type+'_range_up['+count+']">\n' +
                    '              </fieldset>\n' +
                    '            </div>\n' +
                    '            <div class="col text-center">\n' +
                    '              <fieldset class="form-group">\n' +
                    '                <input type="text" class="form-control decimal range_down validated" id="range_down'+count+'" data-rule-required="true" data-msg-required="This field is required" value="" name="'+type+'_range_down['+count+']">\n' +
                    '              </fieldset>\n' +
                    '             </div>\n' +
                    '             <div class="col text-center">\n' +
                    '                <div class="form-group " style="padding-top: 8px;">\n' +
                    '                    <input type="checkbox" class="switchery weightAddition" data-color="success" data-size="sm" name="'+type+'_wa_switch['+count+']"/>\n' +
                    '                </div>\n' +
                    '              </div>\n' +
                    '              <div class="col-2 text-center">\n' +
                    '                 <fieldset style="padding-top: 5px;">\n' +
                    '                   <div class="input-group input-group-sm form-group">\n' +
                    '                       <input type="text" class="touchspin-color input-sm spkg validated" id="spkg'+count+'" value="0.5" disabled data-bts-button-down-class="btn btn-success" data-bts-button-up-class="btn btn-success" name="'+type+'_wa_spkg['+count+']" data-rule-required="true" data-msg-required="This field is required">\n' +
                    '                   </div>\n' +
                    '                  </fieldset>\n' +
                    '                </div>\n' +
                    '                <div class="col text-center">\n' +
                    '                  <fieldset class="form-group">\n' +
                    '                    <input type="text" class="form-control decimal validated" id="flat_charges'+count+'" data-rule-required="true" data-msg-required="This field is required" value="" name="'+type+'_flat_charges['+count+']">\n' +
                    '                  </fieldset>\n' +
                    '                </div>\n' +
                    '                <div class="col-1">\n' +
                    '                   <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 weight_close"><i class="ft-x"></i></span></div></div>'+
                    '                </div>\n' +
                    '          </div>';
                $(this).siblings('.weight-addition').append(htmdiv);
                var switches = $(this).siblings('.weight-addition').find(".weight_row:last-child").find(".switchery.weightAddition")[0];
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

                count++;
            });
            @endif
            $( "#ratesAdditionForm" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });

            @if($rate_status != null && $rate_status->status == 1 && (session('role_id') == 1 || in_array(529, session('permissions'))))
                $("#approve_btn").on('click',function (){
                    swal({
                        text: 'Are you sure, you want to approve these rates?',
                        icon: 'info',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function(confirm) {
                        if (confirm) {
                            $("#approve_reject_form #status").val("Approve");
                            $("#approve_reject_form").submit();
                        }
                    });
                });

                $("#reject_btn").on('click',function (){
                    $("#approve_reject_form #status").val("Reject");
                    $("#RejectRatesModal").modal('show');
                });

                $( "#approve_reject_form" ).validate({
                    errorClass:"danger",
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.modal-body'));
                    },
                });
            @endif

        });

    </script>
@endsection