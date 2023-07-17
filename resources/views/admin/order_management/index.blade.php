@extends('admin.layout.master')

@section('title', 'Order Management')

@section('content')

    <h1 class="mb-1">
        Order Management
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="col mt-2">
                    <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="form-group">
                            <input type="text" name="tracking_numbers" class="dt_search tracking_numbers"
                                   placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <select name="shipment_status" id="shipment_status" class="form-control select2 dt_search" multiple="multiple" >
                                @foreach($shipment_status as $status)
                                     <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                                </select>
                             </div>
                         </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                                </div>
                                <input type="text" name="booking_from_date"
                                       class="form-control bg-primary border-primary white rounded-right"
                                       id="booking_from_date" placeholder="Booking Date From">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                </div>
                                <input type="text" name="booking_to_date"
                                       class="form-control bg-primary border-primary white rounded-right"
                                       id="booking_to_date" placeholder="Booking Date To">
                            </div>
                        </div>

                        <div class="form-group col-md-5 mt-2 justify-content-center">
                            <button id="datatable_filter_btn" type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i
                                        class="la la-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">Shipment ID</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Business Category</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Payment Status</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Vendor</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        {{--<th class="border-primary border-darken-1">Consignee Contact</th>--}}
{{--                        <th class="border-primary border-darken-1">Consignee Address</th>--}}
{{--                        <th class="border-primary border-darken-1">Collection Amount</th>--}}
                        <th class="border-primary border-darken-1">Booking Date</th>
                        <th class="border-primary border-darken-1">Payment Mode</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="complaint_channel" id="complaint_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6 d-none" id="alternate_phone_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="alternate_phone" class="form-control" id="alternate_phone" placeholder="Enter Alternate Number" data-rule-required="true" data-msg-required="Alternate Number is required">
                                        </fieldset>
                                    </div>
                                    {{-- <div class="col-6 d-none" id="cod_amount_input">
                                        <fieldset class="form-group">
                                            <input type="text" name="cod_amount" class="form-control" id="cod_amount" placeholder="Enter COD Amount" data-rule-required="true" data-msg-required="COD Amount is required">
                                        </fieldset>
                                    </div> --}}
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="request_channel" id="request_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel2)
                                                    <option value="{{$channel2->id}}">{{$channel2->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="feedback_channel_request" id="feedback_channel_request" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="claims d-none" id="request_claims">
                                <input type="hidden" name="shipment_ids" id="shipment_ids">
                                <input type="hidden" name="case_nature_id" id="case_nature_id">
                                <input type="hidden" name="complaint_id" id="complaint_id">
                                <input type="hidden" name="channel_id" id="channel_id">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_tclaim" id="case_nature_claim" class="form-control select2">
                                                @foreach($case_nature_type_claims as $claim)
                                                    <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="claim_channel" id="claim_channel" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
                                        </fieldset>
                                    </div>
                                    <div class="col-8 d-none" id="receiving_sheet_div">
                                        <fieldset class="form-group">
{{--                                            <select name="receiving_sheet_id"  id="request_id" class="form-control select2" data-rule-required="true" data-msg-required="Please Select Receiving Sheet">--}}
                                            <select name="receiving_sheet_id"  id="request_id" class="form-control select2">

                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8 text-left" id="claim_product_picture_div">
                                        <fieldset class="form-group">
                                            <label for="product_picture"><b>Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>
                                    <div class="col-8 text-left" id="claim_invoice_picture_div">
                                        <fieldset class="form-group">
                                            <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="description" id="claim_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddFeedbackModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddFeedbackModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Feedback</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_feedback_form" action="{{route('admin.crm.feedback.add')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="feedback_channel" id="feedback_channel" class="form-control select2">
                                            @foreach($case_nature_channels as $channel1)
                                                <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <textarea class="form-control" name="feedback_description" id="feedback_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                    </fieldset>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewFeedback" type="submit" class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="cancelRemark" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="cancelRemark" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Remarks</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12">
                            <div class="form-group">
                                <input type="hidden" name="cancel_shipment_id" id="cancel_shipment_id" value="">
                                {{-- <label for="">Remarks</label> --}}
                                <textarea class="form-control black-border" name="cancel_remarks" id="cancel_remarks" cols="30" rows="10" data-rule-required="true" data-msg-required="Remarks is required" placeholder="Enter Remarks*"></textarea>
                                <span id="cancel_remarks_error" class="text-danger cancel-error-message"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ml-1">
                        <button type="submit" name="edit" class="btn btn-primary btn-min-width" id="CancelReasonSubmit">Submit
                        </button>
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

    <script type="text/javascript">
        $(document).ready(function () {

            $('#claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#shipment_status').select2({
                placeholder:'Search Shipment Status',
                width:'100%',
                allowClear:true
            });

            $('#alternate_phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });
            // $('#cod_amount').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false,
            //     'rightAlign': false,
            //     'digits': 2,
            //     'min': 0,
            //     'max': 1000000
            // });
            // $('#shipment_status').on("select2:unselect", function(e) {
            //     if($('#shipment_status').val() == '' && $('input[name="tracking_numbers"]').val() == ''){
            //         $('#datatable_filter_btn').attr('disabled', true);
            //     }
            // });
            var old_date_limit = '{{ Carbon\Carbon::now()->subDays(29)->toDateString() }}';


            var booking_from_date = $('#booking_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #booking_to_date').pickadate('picker').set('min', $('#track_form #booking_from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var booking_to_date = $('#booking_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_to_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #booking_from_date').pickadate('picker').set('max', $('#track_form #booking_to_date').pickadate('picker').get('select'));
                    }
                }
            });

            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 3){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                    $('#request_claims').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#AddNewRequest').addClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
            });
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#add_request_form #request_id').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Receiving Sheet ID",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            var lost_flag = true;
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            }).bind('select2:select', function () {
                var id = parseInt($(this).val());
                var value = $('#case_nature_claim').val();
                $('#request_id').empty().trigger('change');
                if (this.value && this.value == 23 && lost_flag === true) {
                    $('#receiving_sheet_div').removeClass('d-none');
                    var shipment_id = $('#requested_shipment_ids').val();
                    $.ajax({
                        url: '{!! route('admin.crm.request.lost.claim') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'shipment_id': shipment_id,
                        }
                    }).done(function (data) {


                        if (data.status == 1) {
                            var newOption = new Option(data.receiving_sheet_id, data.receiving_sheet_id, false, false);
                            $('#request_id').append(newOption).trigger('change');

                        } else {
                            lost_flag = true;
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            // $('#AddNewRequest').attr('disabled',true);
                        }

                    });
                }
                else{
                    lost_flag = true;
                    $('#receiving_sheet_div').addClass('d-none');
                    $('#AddNewRequest').attr('disabled',false);

                }
                if(id === 26){
                    $('#claim_product_cost_div').addClass('d-none');
                    $('#claim_product_picture_div').addClass('d-none');
                    $('#claim_invoice_picture_div').addClass('d-none');
                }
                else{
                    $('#claim_product_cost_div').removeClass('d-none');
                    $('#claim_product_picture_div').removeClass('d-none');
                    $('#claim_invoice_picture_div').removeClass('d-none');
                }
            });
            $('#claim_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#request_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#feedback_channel').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_feedback_form')
            });
            $('#feedback_channel_request').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });

            function print(selected_rows) {
                $.ajax({
                    url: '{!! route('admin.orders.shipment_print_status') !!}',
                    data: {
                        'shipment_ids': selected_rows
                    }
                })
                .done(function(data) {
                    if (data.status == 0) {
                        if (data.sticker) {
                            $.ajax({
                                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                method: 'POST',
                                data: {
                                    'ids[]': data.valid_ids,
                                    'admin': {!! Auth::id() !!},
                                    'sticker': 1,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {
                                var blob = new Blob([data]);
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(blob);
                                link.download = 'air_waybills.pdf';
                                link.click();
                            });
                        }
                        else {
                            $.ajax({
                                url: '{!! route('cod.shipment.book.print_air_waybill') !!}',
                                method: 'POST',
                                data: {
                                    'ids[]': data.valid_ids,
                                    'admin': {!! Auth::id() !!},
                                    'sticker': 0,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                            .done(function(data) {

                                if (data.status == 2) {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                else
                                {
                                var tab = window.open('', '_blank');

                                if (!tab) {
                                    swal({
                                        title: 'Popup Blocker Enabled!',
                                        text: 'Please add this site to your exception list.',
                                        icon: 'error',
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                                } else {
                                    tab.document.write(data);
                                    tab.document.close();
                                    tab.focus();
                                }
                            }
                            });
                        }
                    }
                    else {
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            }
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if (session('role_id') == 1 || in_array(336, session('permissions')))
                    {
                        text: '<i class="la la-arrow-down"></i> Telenor Arrival Button',
                        className: 'btn btn-primary telenor_arrival',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to mark these Shipment(s) as arrive?',
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
                            }).then(function(confirm) {
                                if (confirm) {
                                    $.ajax({
                                        url: '{!! route('admin.orders.telenor_shipments_arrival') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            // 'shipment_ids': selected_rows
                                        }
                                    })
                                        .done(function(data) {
                                            if (data.status == 0) {
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }
                                        });
                                }
                            });
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(336, session('permissions')))
                    {
                        text: '<i class="la la-arrow-down"></i> Foodpanda Arrival Button',
                        className: 'btn btn-primary foodpanda_arrival',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to mark these Shipment(s) as arrive?',
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
                            }).then(function(confirm) {
                                if (confirm) {
                                    $.ajax({
                                        url: '{!! route('admin.orders.foodpanda_shipments_arrival') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}'
                                        }
                                    })
                                        .done(function(data) {
                                            if (data.status == 0) {
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }
                                        });
                                }
                            });
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(139, session('permissions')))
                    {
                        text: '<i class="la la-reply"></i> Shipper Recall',
                        className: 'btn btn-primary shipper_recall',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            swal({
                                text: 'Are you sure, you want to move these Shipment(s) for Return?',
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
                            }).then(function(confirm) {
                                if (confirm) {
                                    $.ajax({
                                        url: '{!! route('admin.orders.shipper_recall') !!}',
                                        method: 'POST',
                                        data: {
                                            '_token': '{{ csrf_token() }}',
                                            'shipment_ids': selected_rows
                                        }
                                    })
                                    .done(function(data) {
                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }
                                        table.button('.shipper_recall').disable();
                                        table.button('.print').disable();

                                        selected_rows = [];

                                        table.rows().deselect();

                                        table.draw('false');
                                    });
                                }
                            });
                        }
                    },
                        @endif
                    {
                        text: '<i class="la la-plus"></i> Add Request',
                        className: 'btn btn-primary request_add',
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                $('#AddRequestModal').modal('show');
                                $('#requested_shipment_ids').val(selected_rows);
                                var html_rows = '';
                                var count = 1;
                                table.rows().nodes().each(function(index) {
                                    var row = table.row(index);
                                    if ($(row.node()).hasClass('selected')) {
                                        var tracking = $(row.node()).find('td.tracking_number').text();
                                        html_rows += '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking +'</b></span></div>';
                                        count++;
                                    }
                                });
                                $('#requested_shipments').html(html_rows);
                            }
                            else{
                                $('#AddFeedbackModal').modal('show');
                            }
                        }
                    },
                    {
                        text: '<i class="la la-print"></i> Print',
                        className: 'btn btn-primary print',
                        enabled: false,
                        action: function (e, dt, node, config) {

                            var rows = selected_rows.slice();

                            table.button('.shipper_recall').disable();
                            table.button('.print').disable();

                            selected_rows = [];

                            table.rows().deselect();

                            print(rows);
                        }
                    },
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.shipper_recall').enable();
                                    table.button('.print').enable();
                                }
                            });
                        }
                    },
                    {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.shipper_recall').disable();
                                        table.button('.print').disable();
                                    }
                                }
                            });
                        }
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[10, 50], [10, 50]],
                pageLength: 10,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.orders.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#track_form .tracking_numbers').val();
                        d.booking_from_date = $('input[name="booking_from_date_formatted"]').val();
                        d.booking_to_date = $('input[name="booking_to_date_formatted"]').val();
                        d.shipment_status_select = $('#shipment_status').val();
                    }
                },
                deferLoading: 0,
                rowId: 'shipment_id',
                order: [[1, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'shipment_id', name: 'shipments.id', visible: false},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'business_category', name: 'bc.id', class: 'align-middle business_category'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'service_type', name: 'service_type', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'payment_status', name: 'payment_status', class: 'align-middle payment_status'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'vendor', name: 'usi.vendor', class: 'align-middle vendor'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    // {data: 'phone', name: 'phone', class: 'align-middle phone'},
                    // {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    // {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'booking_date', name: 'shipments.created_at', class: 'align-middle booking_date'},
                    {data: 'payment_mode', name: 'pm.mode', class: 'align-middle payment_mode'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    if (data.shipper_status_id != 23) {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.shipment_id, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    var payment_select = '<select name="payment_select" id="payment_select" class="select2 form-control"></select>';
                    var business_category = '<select name="business_category" id="business_category" class="select2 form-control"></select>';
                    var payment_mode = '<select name="payment_mode" id="payment_mode" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if (column.visible()) {
                            if ($(header).is('.action') || $(header).is('.select')) {
                                $(td).appendTo($(search));
                            }else if($(header).is('.status')){
                                $(drop_select).appendTo($(search))
                                    .on( 'change', function () {
                                        column.search($(this).val(), false, false, true);
                                    } ).wrap(td);
                            }else if($(header).is('.service_type')){
                                $(service_drop_select).appendTo($(search))
                                    .on( 'change', function () {
                                        column.search($(this).val(), false, false, true);
                                    } ).wrap(td);
                            }else if($(header).is('.business_category')){
                                $(business_category).appendTo($(search))
                                    .on( 'change', function () {
                                        column.search($(this).val(), false, false, true);
                                    } ).wrap(td);
                            }else if($(header).is('.payment_status')){
                                $(payment_select).appendTo($(search))
                                    .on( 'change', function () {
                                        column.search($(this).val(), false, false, true);
                                    } ).wrap(td);
                            }
                            else if($(header).is('.payment_mode')){
                                $(payment_mode).appendTo($(search))
                                    .on( 'change', function () {
                                        column.search($(this).val(), false, false, true);
                                    } ).wrap(td);
                            }
                            else {
                                var current = $(input).appendTo($(search)).on('change', function() {
                                        column.search($(this).val(), false, false, true);
                                }).wrap(td).after(icon);

                                if (column.search()) {
                                    current.val(column.search());
                                }
                            }
                        }
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.booking_type;
                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data4 = $.map({!! $payment_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#payment_select").prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Payment",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data5 = $.map({!! $business_categories !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
                    $("#business_category").prepend('<option value="" selected></option>').select2({
                        data: data5,
                        placeholder: "Select Business Category",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data6 = $.map({!! $payment_modes !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.mode;

                        return obj;
                    });

                    $("#payment_mode").prepend('<option value="" selected></option>').select2({
                        data: data6,
                        placeholder: "Select Payment Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });
            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.shipper_recall').enable();
                    table.button('.print').enable();
                }
                else {
                   
                    table.button('.shipper_recall').disable();
                    table.button('.print').disable();
                }
            });



            $('body').on('click','.view_charges',function () {
                var shipment_id = $(this).parents('tr').attr('id');
                $('#ShipmentChargesModal').modal('show');
                $('#shipment_charges_modal_id').val(shipment_id);
                $.ajax({
                    url:'{!! route("admin.orders.charges") !!}',
                    method: 'POST',
                    data: {
                        'shipment_id': shipment_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    $('#shipment_charges_body').html(data);
                    $('#shipment_charges_modal_heading span').text(shipment_id);
                })
            });
          

            $('#case_nature_requests').on('change',function (e) {
            
                if($(this).val() == 13){
                    $('#alternate_phone_input').removeClass('d-none');
                    // $('#cod_amount_input').addClass('d-none');

                }
                // else if($(this).val() == 12){
                //     $('#cod_amount_input').removeClass('d-none');
                //     $('#alternate_phone_input').addClass('d-none');

                // }
                else{
                    // $('#cod_amount_input').addClass('d-none');
                    $('#alternate_phone_input').addClass('d-none');

                }
            });

            //Selectize
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
                // onChange: function (value) {
                //     if(value.length == 0 && $('#shipment_status').val() == ''){
                //         $('#datatable_filter_btn').attr('disabled', true);
                //     }else{
                //         $('#datatable_filter_btn').attr('disabled', false);
                //     }
                //
                // },
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                selected_rows = [];
                table.draw();
                // var tracking_numbers = $('#track_form .tracking_numbers').val();
                // var booking_from_date = $('#track_form #booking_from_date').val();
                // var booking_to_date = $('#track_form #booking_to_date').val();
                // var shipment_status = $('#track_form #shipment_status').val();
                // if (tracking_numbers != '' || (booking_from_date != '' && booking_to_date != '') || shipment_status != '') {
                //     table.draw();
                // }

            });


            var max_char = 245;
            $('#feedback_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('#service_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#add_request_form textarea, #add_feedback_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#add_request_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var case_nature_id = parseInt($('#case_nature_select').val());
                    if (case_nature_id === 1) {
                        var nature_flag = true;
                        var case_nature_complaint_id = $('#case_nature_complaints').val();
                        var case_nature_channel_id = $('#complaint_channels').val();
                        var complaint_description = $('#complaint_description').val();
                        if (!case_nature_complaint_id) {
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!case_nature_channel_id) {
                            nature_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!complaint_description) {
                            nature_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (selected_rows.length == 0) {
                            nature_flag = false;
                            var error = "Please select atleast one tracking number!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (nature_flag) {
                            $('#AddNewRequest').attr('disabled', true);
                            swal({
                                        title: 'Please Wait!',
                                        text: 'Launching Request.',
                                        icon: 'info',
                                        buttons: false,
                                        closeOnClickOutside: false,
                                        closeOnEsc: false
                                    });
                            $.ajax({
                                url: '{!! route('admin.crm.request.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_ids': selected_rows,
                                    'case_nature_id': case_nature_id,
                                    'complaint_id': case_nature_complaint_id,
                                    'channel_id': case_nature_channel_id,
                                    'description': complaint_description,
                                    'alternate_phone': $('#alternate_phone').val(),
                                    // 'cod_amount': $('#cod_amount').val(),
                                }
                            })
                                .done(function (data) {
                                    swal.close();
                                    if (data.status) {

                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        } else {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }

                                    table.button('.shipper_recall').disable();
                                    table.button('.print').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }

                    } else if (case_nature_id == 2) {
                        var nature_flag = true;
                        var case_nature_complaint_id = $('#case_nature_requests').val();
                        var case_nature_channel_id = $('#request_channels').val();
                        var service_description = $('#service_description').val();
                        if (!case_nature_complaint_id) {
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!case_nature_channel_id) {
                            nature_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!service_description) {
                            nature_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (selected_rows.length == 0) {
                            nature_flag = false;
                            var error = "Please select atleast one tracking number!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (nature_flag) {
                            $('#AddNewRequest').attr('disabled', true);
                            $.ajax({
                                url: '{!! route('admin.crm.request.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_ids': selected_rows,
                                    'case_nature_id': case_nature_id,
                                    'complaint_id': case_nature_complaint_id,
                                    'channel_id': case_nature_channel_id,
                                    'description': service_description
                                }
                            })
                                .done(function (data) {
                                    if (data.status) {
                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        } else {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }

                                    table.button('.shipper_recall').disable();
                                    table.button('.print').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');


                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }
                    } else if (case_nature_id == 3) {
                        var feedback_flag = true;
                        var feedback_channel = $('#feedback_channel_request').val();
                        var feedback_description = $('#feedback_description_request').val();
                        if (!feedback_description) {
                            feedback_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!feedback_channel) {
                            feedback_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (selected_rows.length == 0) {
                            nature_flag = false;
                            var error = "Please select at least one tracking number!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (feedback_flag) {
                            $('#AddNewRequest').attr('disabled', true);
                            $.ajax({
                                url: '{!! route('admin.crm.feedback.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'channel_id': $('#feedback_channel_request').val(),
                                    'shipment_ids': selected_rows,
                                    'description': feedback_description
                                }
                            })
                                .done(function (data) {
                                    if (data.status) {
                                        if (data.flag) {
                                            var html = '';

                                            $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        } else {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }
                                    table.button('.shipper_recall').disable();
                                    table.button('.print').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled', false);
                                });
                        }
                    } else if (case_nature_id === 4) {
                        if (selected_rows.length > 1) {
                            var error = "Cannot select more than one shipment";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $('#AddRequestModal').modal('hide');
                            $('#AddNewRequest').attr('disabled', false);
                        } else {
                            var nature_flag = true;
                            var case_nature_claim_id = $('#case_nature_claim').val();
                            var case_nature_channel_id = $('#claim_channel').val();
                            var product_cost = $('#claim_product_cost').val();
                            var check_product_picture = $('#product_picture').val();
                            var check_invoice_picture = $('#invoice_picture').val();
                            $('#shipment_ids').val(selected_rows);
                            $('#case_nature_id').val(case_nature_id);
                            $('#channel_id').val(case_nature_channel_id);
                            $('#complaint_id').val(case_nature_claim_id);
                            var formData = new FormData($('#add_request_form')[0]);
                            if (case_nature_claim_id === 17) {
                                if ($('#request_id').val() == "" || $('#request_id').val() == null) {
                                    nature_flag = false;
                                    var error = "Please select receiving sheet!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            }
                            if (!case_nature_claim_id) {
                                nature_flag = false;
                                var error = "Please select Claim type!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            if (!case_nature_channel_id) {
                                nature_flag = false;
                                var error = "Please select Channel!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            if (case_nature_claim_id !== "26") {
                                if (!check_product_picture) {
                                    nature_flag = false;
                                    var error = "Please attach Product Picture!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                if (!product_cost) {
                                    nature_flag = false;
                                    var error = "Please enter Product Cost!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                                if (!check_invoice_picture) {
                                    nature_flag = false;
                                    var error = "Please attach Invoice Picture!";
                                    toastr.error(error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }
                            }
                            if (selected_rows.length == 0) {
                                nature_flag = false;
                                var error = "Please select atleast one tracking number!";
                                toastr.error(error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                            if (nature_flag) {
                                $('#AddNewRequest').attr('disabled', true);
                                $.ajax({
                                    url: '{!! route('admin.crm.request.add') !!}',
                                    method: 'POST',
                                    enctype: 'multipart/form-data',
                                    data: formData,
                                    dataType: 'json',
                                    processData: false,
                                    contentType: false,
                                })
                                    .done(function (data) {
                                        if (data.status) {
                                            if (data.flag) {
                                                var html = '';

                                                $.each(data.already_existed_shipments, function (index, tracking_number) {
                                                    html += tracking_number + '<br/>';
                                                });

                                                if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                                content = document.createElement('div');
                                                content.innerHTML = html;

                                                swal({
                                                    title: 'Request / Complaint Cannot Be Lodged!',
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Close',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                });
                                            } else {
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            }
                                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        } else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }

                                        table.button('.shipper_recall').disable();
                                        table.button('.print').disable();

                                        selected_rows = [];

                                        table.rows().deselect();

                                        table.draw('false');

                                        $('#AddRequestModal').modal('hide');
                                        $('#request_id').val('').trigger('change');
                                        $('#receiving_sheet_div').addClass('d-none');
                                        $('#AddNewRequest').attr('disabled', false);
                                    });
                            }

                        }
                    }
                }
            });
            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#add_request_form')[0].reset();
                $('#case_nature_complaints').val('').trigger('change');
                $('#case_nature_select').val('').trigger('change');
                $('#case_nature_requests').val('').trigger('change');
                $('#complaint_channels').val('').trigger('change');
                $('#request_channels').val('').trigger('change');
                $('#feedback_channel_request').val('').trigger('change');
                $('#complaint_description').val('');
                $('#service_description').val('');
                $('#feedback_description_request').val('');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');
                $('#request_id').val('').trigger('change');
                $('#receiving_sheet_div').addClass('d-none');
                $('#alternate_phone_input').addClass('d-none');
                $('#alternate_phone').val('');
                // $('#cod_amount_input').addClass('d-none');
                // $('#cod_amount').val('');

            });

            $('#add_feedback_form').bind('submit', function (e) {
                e.preventDefault();
                var feedback_flag = true;
                var feedback_chennel = $('#feedback_channel').val();
                var feedback_description = $('#feedback_description').val();
                if(!feedback_description){
                    feedback_flag = false;
                    var error = "Please Enter Description!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(!feedback_chennel){
                    feedback_flag = false;
                    var error = "Please select Channel!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
                if(feedback_flag){
                    $('#AddNewFeedback').attr('disabled',true);
                    $.ajax({
                        url: '{!! route('admin.crm.feedback.add') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'channel_id': feedback_chennel,
                            'description' : feedback_description
                        }
                    })
                        .done(function(data) {
                            if (data.status) {
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            $('#AddFeedbackModal').modal('hide');
                            $('#AddNewFeedback').attr('disabled',false);
                        });
                }

            });
            $('#AddFeedbackModal').on('hide.bs.modal', function (e) {
                $('#feedback_channel').val('').trigger('change');
                $('#feedback_description').val('');
            });
        $('body').on('click', '.shipment_cancel', function () {
            var shipment_id = $(this).parents('tr').attr('id');
            $('#cancelRemark').modal('show');
            $("#cancel_shipment_id").val(shipment_id);
            // var reason = $('#cancel_remarks').val();
            // var shipment_id = parseInt($('#cancel_shipment_id').val());
            // swal({
            //     text: 'Are you sure, you want to Cancel these Shipment(s)?',
            //     icon: 'warning',
            //     buttons: {
            //         cancel: {
            //             text: 'No',
            //             value: null,
            //             visible: true,
            //             closeModal: true,
            //         },
            //         confirm: {
            //             text: 'Yes',
            //             value: true,
            //             visible: true,
            //             closeModal: true
            //         }
            //     },
            //     closeOnClickOutside: false,
            //     closeOnEsc: false,
            //     dangerMode: true
            // }).then(function (confirm) {
                // if (confirm) {
                    // $.ajax({
                    //     url: '{!! route('admin.orders.shipment_cancel') !!}',
                    //     method: 'POST',
                    //     data: {
                    //         '_token': '{{ csrf_token() }}',
                    //         'shipment_id': shipment_id,
                    //         'reason': reason
                    //     }
                    // })
                    // .done(function (data) {
                    //     if (data.status == 0) {
                           
                    //         toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    //     }
                    //     else {
                    //         toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    //     }
                        
                    //     table.button('.print').disable();

                    //     selected_rows = [];

                    //     table.rows().deselect();

                    //     table.draw('false');
                    // });
                // }
            // });
        });
        
        $('#CancelReasonSubmit').on('click',function () {
            var reason = $('#cancel_remarks').val();
            var shipment_id = $(this).parents('tr').attr('id');

            var shipment_id = parseInt($('#cancel_shipment_id').val());
        
            // swal({
            //     text: 'Are you sure, you want to Cancel these Shipment(s)?',
            //     icon: 'warning',
            //     buttons: {
            //         cancel: {
            //             text: 'No',
            //             value: null,
            //             visible: true,
            //             closeModal: true,
            //         },
            //         confirm: {
            //             text: 'Yes',
            //             value: true,
            //             visible: true,
            //             closeModal: true
            //         }
            //     },
            //     closeOnClickOutside: false,
            //     closeOnEsc: false,
            //     dangerMode: true
            // }).then(function (confirm) {
                // if (confirm) {
                    // $.ajax({
                    //     url: '{!! route('admin.orders.shipment_cancel') !!}',
                    //     method: 'POST',
                    //     data: {
                    //         '_token': '{{ csrf_token() }}',
                    //         'shipment_id': shipment_id,
                    //         'reason': reason
                    //     }
                    // })
                    // .done(function (data) {
                    //     if (data.status == 0) {
                           
                    //         toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    //     }
                    //     else {
                    //         toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    //     }
                        
                    //     table.button('.print').disable();

                    //     selected_rows = [];

                    //     table.rows().deselect();

                    //     table.draw('false');
                    // });
                // }
            // });
            if (validateRemarks()) {
            $.ajax({
                url: '{!! route('admin.orders.shipment_cancel_reason') !!}',
                method: 'POST',
                data: {
                    'shipment_id': shipment_id,
                    'reason': reason,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function (data) {
                if (data.status === 1) {
                    
                    table.draw('false');
                    toastr.success(data.success, 'Success!', {
                        positionClass: 'toast-bottom-center',
                        containerId: 'toast-bottom-center'
                    });
                    
                    
                } else {
                    
                    toastr.error(data.error, 'Error!', {
                        positionClass: 'toast-top-center',
                        containerId: 'toast-top-center'
                    });
                }
                $('#cancelRemark').modal('hide')
               
            });
        }
        });
        // Initialize form validation
        $('#cancelRemark').validate({
            rules: {
                cancel_remarks: {
                    required: true
                }
            },
            messages: {
                cancel_remarks: {
                    required: 'Remarks is required'
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo($('#cancel_remarks_error'));
            }
        });
        $('#cancelRemark').on('hide.bs.modal', function (e) {
           $('#cancel_remarks').val('');
           $('#cancel_remarks').removeClass('is-invalid');
           $('#cancel_remarks_error').text('').removeClass('cancel-error-message');
       });
       function validateRemarks() {
        var remarks = $('#cancel_remarks').val().trim();

        if (remarks === '') {
            $('#cancel_remarks').addClass('is-invalid');
            $('#cancel_remarks_error').text('Remark is required').addClass('cancel-error-message');
            return false;
        } else {
            $('#cancel_remarks').removeClass('is-invalid');
            $('#cancel_remarks_error').text('').removeClass('cancel-error-message');
            return true;
        }
    }

    });
    </script>
@endsection