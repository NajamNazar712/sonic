@extends('admin.layout.master')

@section('title', 'Edit Launched Escalation')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Edit Launched Escalation
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.escalation.launched.edit.store') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$escalation->id}}">
                                    <div class="row justify-content-center mb-1">
                                        <div class="col">
                                            <h4>Case Nature: <strong>{{$escalation->nature->name}}</strong></h4>
                                        </div>
                                    </div>
                                    @if($escalation->case_nature == 1)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_complaints as $complaints)
                                                            @if($escalation->case_nature_type == $complaints->id)
                                                                <option value="{{$complaints->id}}" selected>{{$complaints->type}}</option>
                                                            @else
                                                                <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    @elseif($escalation->case_nature == 2)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_service_requests as $service)
                                                            @if($escalation->case_nature_type == $service->id)
                                                                <option value="{{$service->id}}" selected>{{$service->type}}</option>
                                                            @else
                                                                <option value="{{$service->id}}">{{$service->type}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    @elseif($escalation->case_nature == 4)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_claim" id="case_nature_claim" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_type_claims as $claim)
                                                            @if($escalation->case_nature_type == $claim->id)
                                                                <option value="{{$claim->id}}" selected>{{$claim->type}}</option>
                                                            @else
                                                                <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    @endif
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
                                                    @if(in_array($shipment_status->id, $statuses))
                                                    <input type="checkbox" id="shipment_status_{{ $shipment_status->id }}" class="shipment_statuses" name="shipment_statuses[]" value="{{ $shipment_status->id }}" checked>
                                                    <label for="permission_{{ $shipment_status->id }}">{{ $shipment_status->name }}</label>
                                                    @else
                                                        <input type="checkbox" id="shipment_status_{{ $shipment_status->id }}" class="shipment_statuses" name="shipment_statuses[]" value="{{ $shipment_status->id }}">
                                                        <label for="permission_{{ $shipment_status->id }}">{{ $shipment_status->name }}</label>
                                                    @endif
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
                                                <input type="text" name="tat" class="form-control tat" value="{{$escalation->tat}}" placeholder="" data-rule-required="true" data-msg-required="TAT is required" value="">
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
                                                    @if($escalation->mark_as == 1)
                                                        <option value="1" selected>Valid</option>
                                                        <option value="0">Invalid</option>
                                                    @else
                                                        <option value="1">Valid</option>
                                                        <option value="0" selected>Invalid</option>
                                                    @endif
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <fieldset class="form-group">
                                                <textarea class="form-control" name="auto_comment" id="auto_comment" rows="5" placeholder="Enter Auto Comment Here..." data-rule-required="true" data-msg-required="Auto Comment is required">{{$escalation->comment}}</textarea>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary col">Update</button>
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
            $('#auto_comment').attr('disabled', false);
            
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

            @if($escalation->case_nature == 1)
            $('#case_nature_complaints').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @elseif($escalation->case_nature == 2)
            $('#case_nature_requests').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @elseif($escalation->case_nature == 4)
            $('#case_nature_claim').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @endif
            $('.tat').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 0,
                'max': 1000000
            });
            var comment_text = @json($escalation->comment);
            $('#mark_as_select').select2({
                width:'100%',
                placeholder:"Select Mark As",
                allowClear:true,
                dropdownParent:$('#settings_form')
            }).bind('change', function () {
                var id = parseInt($(this).val());
                if(id === 1){
                    $('#auto_comment').val(comment_text);
                    $('#auto_comment').attr('disabled', false);
                }else{
                    $('#auto_comment').val('');
                    $('#auto_comment').attr('disabled', true);
                }
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