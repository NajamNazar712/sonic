@extends('admin.layout.master')

@section('title', 'Add Launched Escalation')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add Launched Escalation
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.escalation.launched.add.store') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <div class="row justify-content-center">
                                        <div class="col-4">
                                            <fieldset class="form-group">
                                                <select name="case_nature_select" id="case_nature_select" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature is required">
                                                    @foreach($case_nature as $nature)
                                                        <option value="{{$nature->id}}">{{$nature->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="complaints d-none" id="request_complaints">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_complaints as $complaints)
                                                            <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="service d-none" id="request_service">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_service_requests as $service)
                                                            <option value="{{$service->id}}">{{$service->type}}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="claims d-none" id="request_claims">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_claim" id="case_nature_claim" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_type_claims as $claim)
                                                            <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row justify-content-center">
                                        <div class="col">
                                            <h4><strong>Select Shipment Status(es)</strong></h4>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col">
                                            @foreach($shipment_statuses as $shipment_status)
                                                <fieldset class="d-inline-block m-1">
                                                    <input type="checkbox" id="shipment_status_{{ $shipment_status->id }}" class="shipment_statuses" name="shipment_statuses[]" value="{{ $shipment_status->id }}">
                                                    <label for="permission_{{ $shipment_status->id }}">{{ $shipment_status->name }}</label>
                                                </fieldset>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row justify-content-center">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">TAT*</span>
                                                </div>
                                                <input type="text" name="tat" class="form-control tat" placeholder="" data-rule-required="true" data-msg-required="TAT is required" value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">day(s)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-4">
                                            <fieldset class="form-group">
                                                <select name="mark_as_select" id="mark_as_select" class="form-control select2" data-rule-required="true" data-msg-required="Mark as is required">
                                                    <option value="1">Valid</option>
                                                    <option value="0">Invalid</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <fieldset class="form-group">
                                                <textarea class="form-control" name="auto_comment" id="auto_comment" rows="5" placeholder="Enter Auto Comment Here..." data-rule-required="true" data-msg-required="Auto Comment is required"></textarea>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary col">Add</button>
                                        </div>
                                    </div>
                                </form>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Case Nature",
                allowClear:true,
                dropdownParent:$('#settings_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#request_service').addClass('d-none');
                    $('#request_complaints').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }else if(id === 2){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').removeClass('d-none');
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').addClass('d-none');
                }
                else if(id === 4){
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
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
                dropdownParent:$('#settings_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            $('.tat').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });

            $('#settings_form .shipment_statuses').each(function() {
                var checkbox = $(this);
                var label = checkbox.next();
                var text = label.text();

                label.remove();

                checkbox.iCheck({
                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                    checkedClass: 'checked bg-success',
                    uncheckedClass: 'bg-danger',
                    insert: '<div class="icheck_line-icon"></div>' + text
                });
            });

            $('#mark_as_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Mark As",
                allowClear:true,
                dropdownParent:$('#settings_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());

                
            });

            var checked;
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    checked = $("input[type=checkbox]:checked").length;
                    if(checked > 0){

                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to add Launched Escalation!',
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
                                swal({
                                    title: 'Please Wait!',
                                    text: 'Launched Escalation is being Added!',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                                form.submit();
                            }
                        });
                    }
                    else{
                        error = "Please select at least one Shipment Status";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                }
            });
        });
    </script>
@endsection