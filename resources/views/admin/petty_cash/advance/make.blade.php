@extends('admin.layout.master')
@section('title','Advance Petty Cash Statement')

@section('content')
    <h1 class="mb-1">
        Advance Petty Cash Statement
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="make_statement_form" action="{{route('admin.petty_cash.advance.submit')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="selected_rows" id="selected_rows">
                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_sdn" id="select_statement_sdn" class="form-control select2" data-rule-required="true" data-msg-required="SDN is required">
                                    @foreach($sdns as $sdn)
                                        <option value="{{$sdn->id}}">{{str_pad($sdn->id, 6, '0', STR_PAD_LEFT)}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col ">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                </div>

                                <input type="text" name="select_statement_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="select_statement_date" placeholder="Select Date" data-rule-required="true" data-msg-required="Date is required">
                            </div>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <input type="text" class="form-control reference_no" name="reference_no" id="reference_no" placeholder="Statement Reference No." data-rule-required="true" data-msg-required="Statement Reference No. is required">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_zone" id="select_statement_zone" class="form-control select2" data-rule-required="true" data-msg-required="Zone is required">
                                    @foreach($zones as $zone)
                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_hub" id="select_statement_hub" class="form-control select2" data-rule-required="true" data-msg-required="Hub is required">
                                </select>
                            </fieldset>
                        </div>

                        <div class="col">
                            <fieldset class="form-group">
                                <select name="select_statement_station_manager" id="select_statement_station_manager" class="form-control select2" data-rule-required="true" data-msg-required="Station Manager is required">
                                    @foreach($operation_managers as $manager)
                                        <option value="{{$manager->id}}">{{$manager->name}} @if($manager->trax_id != '')({{$manager->trax_id}}) @endif</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-4 col-sm-4">
                            <fieldset class="form-group">
                                <input type="text" class="form-control amount" name="amount" id="amount" placeholder="Enter Amount" data-rule-required="true" data-msg-required="Amount is required">
                            </fieldset>
                        </div>
                    </div>
                 
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <button id="statement_submit" type="submit"  class="btn btn-primary btn-block" name="submit_button" value="create"><i class="la la-list"></i> Make Statement</button>
                        </div>
                       
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style type="text/css">
        .custom-col-width{
            min-width: 150px;
        }
      
        .doe-col-width{
            min-width: 250px;
        }
        .date-col-width{
            min-width: 190px;
        }
        div.picker .picker__holder{
            width: 250px;
        }
      
        .total_amount_span{
            font-size: 24px;
            color: #64a0d2;
        }

        textarea {
            resize: both;
        }

        /*select:read-only{*/
        /*    pointer-events: none;*/
        /*}*/
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            let cities_data = "";
            let dncc_data = "";

            @if (session('print'))
            $.ajax({
                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'ids[]': '{{ session('print') }}',
                }
            })
                .done(function(data) {
                    var tab = window.open('', '_blank');

                    if(!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    }
                    else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
            @endif

            $('#select_statement_sdn').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select SDN No.',
                width:'100%',
            });

            $('#select_statement_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
            }).bind('change',function(){
                var zone = $(this).val();
                $.ajax({
                    url:'{!! route('admin.petty_cash.make.hubs') !!}',
                    type:'POST',
                    data: {
                        'zone':zone,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status){
                        var hub = $('#select_statement_hub');
                        hub.empty().trigger('change');
                        $.each(data.data,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            hub.append(newOption);
                        });
                        hub.val('').trigger('change');
                    }
                });
            });

            $('#select_statement_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
            });


            $('#select_statement_station_manager').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Station Manager',
                width:'100%',
            });

            var min_date_limit = '{{ Carbon\Carbon::now()->subDays(3)->toDateString() }}';
            var all_max_date = new Date();

            $('#select_statement_date').pickadate({
                firstDay: 1,
                today: '',
                min: new Date(min_date_limit),
                max: all_max_date,
                clear: '',
                close: '',
                weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                showMonthsShort: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
            });

            $('#make_statement_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var pressed_button = $(this.submitButton);
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to '+ pressed_button.attr('value') +' petty cash statement!',
                        icon: 'warning',
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
                    }).then(function (confirm) {
                        if (confirm) {


                            $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');
                            $('#selected_rows').val(selected_rows);
                            form.submit();
                        }
                    });

                }
            });

            $('body').on('change','#datatable tr td.details_of_expense textarea,#datatable tr td.remarks textarea',function() {
                $(this).val($(this).val().trim());
            });

        

            var result = true;
            $.validator.addMethod("reference_no",
                function(value, element) {
                    $.ajax({
                        type: "POST",
                        async: true,
                        url: '{!! route('admin.petty_cash.advance.reference') !!}', // script to validate in server side
                        data: {reference: value,'_token': '{!! csrf_token() !!}'},
                        success: function (data) {
                            if(data === 'true'){
                                result = false;
                            }else{
                                result = true;
                            }
                        }
                    });
                    return result;
                },
                "Statement Reference Number already exists."
            );

        });
    </script>
@endsection