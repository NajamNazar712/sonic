@extends('admin.layout.master')

@section('title', 'FAF Charges')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    FAF Charges
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true" style="height: 400px">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.faf_charges.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}
                                        <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">FAF Charges</span>
                                                </div>
                                                <input data-rule-min="0" data-rule-max="100" step="0.01"  type="number" name="faf_charges" class="form-control faf_charges" placeholder="FAF Charges*" data-rule-required="true" data-msg-required="FAF Charges is required" value="{{!empty($faf_charges) ? $faf_charges->faf_charges : 0}}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                  </span>
                                                </div>
                                                <input type="text" name="date_range_start"
                                                       class="form-control bg-primary border-primary white rounded-right"
                                                       data-rule-required="true" data-msg-required="Field is required"
                                                       data-value="{{!empty($faf_charges) ? $faf_charges->date_range_start : Carbon\Carbon::now() }}"
                                                       id="date_range_start" placeholder="Date Range From">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                </div>
                                                <input type="text" name="date_range_end"
                                                       data-rule-required="true" data-msg-required="Field is required"
                                                       class="form-control bg-primary border-primary white rounded-right"
                                                       data-value="{{ !empty($faf_charges) ? $faf_charges->date_range_end : Carbon\Carbon::now() }}"
                                                       id="date_range_end" placeholder="Date Range To">
                                            </div>
                                        </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/cryptocoins/cryptocoins.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style type="text/css">
        .small-calender-icon{
            font-size: 17px !important;
        }
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .selectize-control {
            width: 300px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
        .cancel-error-message {
            color: red;
        }
        .black-border {
            border: 1px solid black;
        }
    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}?v=24052022" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/echarts/echarts.common.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form input.faf_charges').inputmask({
                'alias': 'integer',
                'allowMinus': true,
                'allowPlus': false,
                'max':100
            });

            var date_range_start = $('#date_range_start').pickadate({
                firstDay: 1,
                clear: '',
                min: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#date_range_start_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#date_range_end').pickadate('picker').set('min', $('#date_range_start').pickadate('picker').get('select'));
                    }
                }
            });

            var date_range_end = $('#date_range_end').pickadate({
                firstDay: 1,
                clear: '',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#date_range_end_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#date_range_start').pickadate('picker').set('max', $('#date_range_end').pickadate('picker').get('select'));
                    }
                }
            });



            $('#settings_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update faf charges!',
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
                        if(confirm){
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection