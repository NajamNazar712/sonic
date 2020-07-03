@extends('admin.layout.master')

@section('title', 'Edit Tagging Escalation')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Edit Tagging Escalation
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-center">
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.escalation.tagging.edit.store') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$escalation_tagging->id}}">
                                    <div class="row justify-content-center mb-1">
                                        <div class="col">
                                            <h4>Case Nature: <strong>{{$escalation_tagging->nature->name}}</strong></h4>
                                        </div>
                                    </div>
                                    @if($escalation_tagging->case_nature == 1)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_complaints as $complaints)
                                                            @if($escalation_tagging->case_nature_type == $complaints->id)
                                                                <option value="{{$complaints->id}}" selected>{{$complaints->type}}</option>
                                                            @else
                                                                <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    @elseif($escalation_tagging->case_nature == 2)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_service_requests as $service)
                                                            @if($escalation_tagging->case_nature_type == $service->id)
                                                                <option value="{{$service->id}}" selected>{{$service->type}}</option>
                                                            @else
                                                                <option value="{{$service->id}}">{{$service->type}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    @elseif($escalation_tagging->case_nature == 4)
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <fieldset class="form-group">
                                                    <select name="case_nature_claim" id="case_nature_claim" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature Type is required">
                                                        @foreach($case_nature_type_claims as $claim)
                                                            @if($escalation_tagging->case_nature_type == $claim->id)
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
                                            <h4><strong>Select Hub(s)</strong></h4>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col">
                                            @foreach($hubs as $hub)
                                                <fieldset class="d-inline-block m-1">
                                                    @if(in_array($hub->id, $selected_hubs))
                                                        <input type="checkbox" id="hubs_{{ $hub->id }}" class="hubs" name="hubs[]" value="{{ $hub->id }}" checked>
                                                        <label for="hubs_{{ $hub->id }}">{{ $hub->name }}</label>
                                                    @else
                                                        <input type="checkbox" id="hubs_{{ $hub->id }}" class="hubs" name="hubs[]" value="{{ $hub->id }}">
                                                        <label for="hubs_{{ $hub->id }}">{{ $hub->name }}</label>
                                                    @endif
                                                </fieldset>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-4">
                                            <fieldset class="form-group">
                                                <select name="hub_status" id="hub_status" class="form-control select2">
                                                    <option value="1" @if($escalation_tagging->hub_status == 1) selected @endif>Origin</option>
                                                    <option value="2" @if($escalation_tagging->hub_status == 2) selected @endif>Destination</option>
                                                    <option value="3" @if($escalation_tagging->hub_status == 3) selected @endif>Both</option>
                                                </select>
                                            </fieldset>
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
                                                    @if(in_array($shipment_status->id, $statuses))
                                                        <input type="checkbox" id="shipment_status_{{ $shipment_status->id }}" class="shipment_statuses" name="shipment_statuses[]" value="{{ $shipment_status->id }}" checked>
                                                        <label for="shipment_status_{{ $shipment_status->id }}">{{ $shipment_status->name }}</label>
                                                    @else
                                                        <input type="checkbox" id="shipment_status_{{ $shipment_status->id }}" class="shipment_statuses" name="shipment_statuses[]" value="{{ $shipment_status->id }}">
                                                        <label for="shipment_status_{{ $shipment_status->id }}">{{ $shipment_status->name }}</label>
                                                    @endif
                                                </fieldset>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach($levels as $index => $level)
                                        <hr>
                                        <div class="row justify-content-center">
                                            <div class="col">
                                                <h4><strong>{{$level->name}}</strong></h4>
                                            </div>
                                        </div>
                                        <input type="hidden" name="level[{{$index}}]" value="{{$level->id}}">
                                        <div class="col">
                                            @if($index == 0)
                                                <div class="row justify-content-center">
                                                    <fieldset class="col-6 form-group">
                                                        <select name="admin_role_select[{{$index}}][]" id="admin_role_select_{{$index}}" class="form-control select2" data-rule-required="true" data-msg-required="User Role is required" multiple="multiple">
                                                            @foreach($admin_roles as $admin_role)
                                                                @if($selected_admin_roles[$level->id] == $admin_role->id)
                                                                    <option value="{{$admin_role->id}}" selected>{{$admin_role->name}} | {{$admin_role->department}}</option>
                                                                @else
                                                                    <option value="{{$admin_role->id}}">{{$admin_role->name}} | {{$admin_role->department}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                    <div class="col-4 form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">TAT*</span>
                                                            </div>
                                                            <input type="text" name="tat[{{$index}}]" id="tat_{{$index}}" class="form-control tat" placeholder="" data-rule-required="true" data-msg-required="TAT is required" value="{{$selected_tat[$level->id]}}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">day(s)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row justify-content-center">
                                                    <fieldset class="col-6 form-group">
                                                        <select name="admin_role_select[{{$index}}][]" id="admin_role_select_{{$index}}" class="form-control select2" multiple="multiple">
                                                            @foreach($admin_roles as $admin_role)
                                                                <option value="{{$admin_role->id}}">{{$admin_role->name}} | {{$admin_role->department}}</option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                    <div class="col-4 form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">TAT*</span>
                                                            </div>
                                                            <input type="text" name="tat[{{$index}}]" id="tat_{{$index}}" class="form-control tat" placeholder="" value="@if(isset($selected_tat[$level->id])){{$selected_tat[$level->id]}}@endif">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">day(s)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <div class="row justify-content-center">
                                                <div class="col form-group">
                                                    <div class="input-group">
                                                        <label for="additional_emails_{{$index}}"><strong>To:</strong></label>
                                                        <input type="text" name="additional_emails[{{$index}}]" id="additional_emails_{{$index}}" placeholder="abcd@gmail.com,bcda@gmai.com" value="@if(isset($selected_emails[$level->id])){{$selected_emails[$level->id]}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col form-group">
                                                    <div class="input-group">
                                                        <label for="additional_emails_cc_{{$index}}"><strong>Cc:</strong></label>
                                                        <input type="text" name="additional_emails_cc[{{$index}}]" id="additional_emails_cc_{{$index}}" placeholder="abcd@gmail.com,bcda@gmai.com" value="@if(isset($selected_emails_cc[$level->id])){{$selected_emails_cc[$level->id]}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col form-group">
                                                    <div class="input-group">
                                                        <label for="additional_emails_bcc_{{$index}}"><strong>Bcc:</strong></label>
                                                        <input type="text" name="additional_emails_bcc[{{$index}}]" id="additional_emails_bcc_{{$index}}" placeholder="abcd@gmail.com,bcda@gmai.com" value="@if(isset($selected_emails_bcc[$level->id])){{$selected_emails_bcc[$level->id]}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#settings_form .hubs').each(function() {
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

            @if($escalation_tagging->case_nature == 1)
            $('#case_nature_complaints').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @elseif($escalation_tagging->case_nature == 2)
            $('#case_nature_requests').select2({
                width:'100%',
                placeholder:"Select Service Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @elseif($escalation_tagging->case_nature == 4)
            $('#case_nature_claim').select2({
                width:'100%',
                placeholder:"Select Claim Type",
                allowClear:true,
                dropdownParent:$('#settings_form')
            });
            @endif
            @if(count($selected_hubs) > 0)
                $('#hub_status').select2({
                    width:'100%',
                    placeholder:"Select Matching Location",
                    allowClear:true,
                    dropdownParent:$('#settings_form')
                });
            @else
                $('#hub_status').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Matching Location",
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


            @foreach($levels as $index => $level)
                @if($index == 0)
                    $('#admin_role_select_{{$index}}').select2({
                        width:'100%',
                        placeholder:"Select User Role",
                        allowClear:true,
                        dropdownParent:$('#settings_form')
                    });
                @else
                    @if(isset($selected_admin_roles[$level->id]))
                        $('#admin_role_select_{{$index}}').select2({
                            width:'100%',
                            placeholder:"Select User Role",
                            allowClear:true,
                            dropdownParent:$('#settings_form')
                        }).bind('change', function () {
                            var id = $(this).val();
                            if(id != null){
                                $('#tat_{{$index}}').data('rule-required', true);
                                $('#tat_{{$index}}').data('msg-required', 'TAT is required');
                            }
                            else{
                                $('#tat_{{$index}}').data('rule-required', false);
                                $('#tat_{{$index}}-error').remove();
                            }
                        });
                    @else
                        $('#admin_role_select_{{$index}}').select2({
                            width:'100%',
                            placeholder:"Select User Role",
                            allowClear:true,
                            dropdownParent:$('#settings_form')
                        }).bind('change', function () {
                            var id = $(this).val();
                            if(id != ''){
                                $('#tat_{{$index}}').data('rule-required', true);
                                $('#tat_{{$index}}').data('msg-required', 'TAT is required');
                            }
                            else{
                                $('#tat_{{$index}}').data('rule-required', false);
                                $('#tat_{{$index}}-error').remove();
                            }
                        });
                    @endif
                @endif
                    @if(isset($selected_roles[$level->id]))
                        @if(count($selected_roles[$level->id]) > 0)
                            var role_ids = @json($selected_roles[$level->id]);
                            $('#admin_role_select_{{$index}}').val(role_ids).trigger('change');
                        @endif
                    @endif
                $('#additional_emails_{{$index}}').selectize({
                        placeholder: 'abcd@gmail.com,bcda@gmai.com',
                        delimiter: ',',
                        createOnBlur: true,
                        persist: false,
                        plugins: ['remove_button'],
                        onDropdownOpen: function (dropdown) {
                            dropdown.remove();
                        },
                        create: function (input) {
                            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            if (regex.test(input)) {
                                return {
                                    value: input,
                                    text: input
                                }
                            }
                            else {
                                return false;
                            }
                        }
                    });

                $('#additional_emails_cc_{{$index}}').selectize({
                    placeholder: 'abcd@gmail.com,bcda@gmai.com',
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function (dropdown) {
                        dropdown.remove();
                    },
                    create: function (input) {
                        var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        if (regex.test(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        else {
                            return false;
                        }
                    }
                });
                $('#additional_emails_bcc_{{$index}}').selectize({
                    placeholder: 'abcd@gmail.com,bcda@gmai.com',
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function (dropdown) {
                        dropdown.remove();
                    },
                    create: function (input) {
                        var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        if (regex.test(input)) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                        else {
                            return false;
                        }
                    }
                });
            @endforeach

            var checked_statuses;
            var checked_hubs;
            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    checked_statuses = $("input:checkbox.shipment_statuses:checked").length;
                    checked_hubs = $("input:checkbox.hubs:checked").length;
                    $flag = true;
                    if(checked_statuses <= 0){
                        error = "Please select at least one Shipment Status";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        $flag = false;
                    }
                    if(checked_hubs > 0){
                        var hub_status = $('#hub_status').val();
                        if(hub_status == null || hub_status == ""){
                            error = "Please select Matching location";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            $flag = false;
                        }
                    }
                    if($flag == true){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to add Tagging Escalation!',
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
                                    text: 'Tagging Escalation is being Added!',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                                form.submit();
                            }
                        });
                    }
                }
            });
        });
    </script>
@endsection