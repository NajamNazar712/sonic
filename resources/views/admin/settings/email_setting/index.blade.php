@extends('admin.layout.master')

@section('title', 'SMS Notifications Limit')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                   SMS Notifications Limit
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            {{--todo--}}
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.sms_notifications_limit.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="bg-blue">
                                            <h3 class="form-section white"><b>ID:</b> 216 | <b>Name:</b> Return Delivered To Shipper</h3>
                                        </div>
                                        <div class="col-12 mb-5" id="shippers_wrapper">
                                            <div class="col-12 form-group">
                                                <label class="mr-2 font-small-3"><b>All Shippers: </b></label>
                                                <input type="checkbox" name="all_shipper_toggle" id="all_shipper_toggle" class="switchery all_shipper_toggle" data-size="sm" data-switchery="true" @if(isset($all_shippers) && $all_shippers == 1) checked @endif>
                                            </div>

                                            {{-- <div class="col-12 form-group d-none" id="excluded_users_container">
                                                <label class="mr-2 font-small-3"><b>Exclude Shipper(s) </b></label>
                                                <select name="excluded_users[]" id="excluded_users" class="form-control select2" multiple="multiple">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 form-group d-none" id="only_users_container">
                                                <label class="mr-2 font-small-3"><b>Only Shipper(s) </b></label>
                                                <select name="only_users[]" id="only_users" class="form-control select2" data-rule-required="true"  data-msg-required="This Field is required" multiple="multiple">
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                        </div>

                                        {{-- @foreach($notification_details as $notification_detail)
                                            <div class="bg-blue">
                                                <h3 class="form-section white"><b>ID:</b> {{$notification_detail['id']}} | <b>Name:</b> {{$notification_detail['name']}}</h3>
                                            </div>
                                            <div class="col-12 mb-5">
                                                <div class= "row"> 
                                                    <div class="col-3 form-group">
                                                        
                                                        <label class="mr-2 font-small-3"><b>Sending Frequency: </b></label>
                                                        <input type="text" name="notifications[{{$notification_detail['id']}}][sending_frequency]" id="sending_frequency_{{$notification_detail['id']}}"value="{{$notification_detail['sending_frequency']}}"  class="sending_frequency_{{$notification_detail['id']}} form-control " placeholder="Sending Frequency*">
                                                        
                                                    </div>
                                                    <div class="col-3 form-group">
                                                        
                                                        <label class="mr-2 font-small-3"><b>Charging Frequency: </b></label>
                                                        <input type="text" name="notifications[{{$notification_detail['id']}}][charging_frequency]" id="charging_frequency_{{$notification_detail['id']}}" value="{{$notification_detail['charging_frequency']}}" class="charging_frequency_{{$notification_detail['id']}} form-control " placeholder="Charging Frequency*" >
                                                    </div>
                                                    <div class="col-3 form-group mt-3">
                                                        <input type="hidden" name="notifications[{{$notification_detail['id']}}][id]" value="{{$notification_detail['id']}}">
                                                        <label class="mr-2 font-small-3"><b>All Shippers: </b></label>
                                                        <input type="checkbox" name="notifications[{{$notification_detail['id']}}][all_shipper_toggle]" id="all_shipper_toggle_{{$notification_detail['id']}}" class="switchery all_shipper_toggle_{{$notification_detail['id']}}" data-size="sm" data-switchery="true" @if($notification_detail['shipper_toggle'] == 1) checked @endif>
                                                    </div>
                                                    <div class="col-3 form-group mt-3">
                                                       
                                                        <label class="mr-2 font-small-3"><b>Charged SMS: </b></label>
                                                        <input type="checkbox" name="notifications[{{$notification_detail['id']}}][charged_sms]" id="charged_sms_{{$notification_detail['id']}}" class="switchery charged_sms_{{$notification_detail['id']}}" data-size="sm" data-switchery="true" @if($notification_detail['charged_sms_toggle'] == 1) checked @endif>
                                                    </div>
                                                </div>

                                                <div class="col-12 form-group d-none" id="excluded_users_container_{{$notification_detail['id']}}">
                                                    <label class="mr-2 font-small-3"><b>Exclude Shipper(s) </b></label>
                                                    <select name="notifications[{{$notification_detail['id']}}][excluded_users][]" id="excluded_users_{{$notification_detail['id']}}" class="form-control select2" multiple="multiple">
                                                        @foreach($shippers as $shipper)
                                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 form-group d-none" id="only_users_container_{{$notification_detail['id']}}">
                                                    <label class="mr-2 font-small-3"><b>Only Shipper(s) </b></label>
                                                    <select name="notifications[{{$notification_detail['id']}}][only_users][]" id="only_users_{{$notification_detail['id']}}" class="form-control select2" data-rule-required="true"  data-msg-required="This Field is required" multiple="multiple">
                                                        @foreach($shippers as $shipper)
                                                            <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach --}}
                                        <div class="col-md-12 form-group">
                                            <button type="submit" class="col-md-4 btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{--todo--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <style>
        .select2-search__field
        {
            width: 200px !important;
        }
    </style>

    <script>
        // $(document).ready(function () {
        //     //    todo
        //     $('#excluded_users').select2({
        //         placeholder: 'Select Excluded Shippers',
        //         width: '100%',
        //         allowClear: true
        //     });

        //     $('#only_users').select2({
        //         placeholder: 'Select Only Shippers',
        //         width: '100%',
        //         allowClear: true
        //     });

        //     @if(isset($all_shippers) && $all_shippers == 1)
        //     $("#excluded_users_container").removeClass('d-none');
        //     @else
        //     $("#only_users_container").removeClass('d-none');
        //             @endif


        //             @if(count($excluded_shippers) > 0)
        //     var ids = @json($excluded_shippers);
        //     $('#excluded_users').val(ids).trigger('change');
        //             @endif

        //             @if(count($only_shippers) > 0)
        //     var ids = @json($only_shippers);
        //     $('#only_users').val(ids).trigger('change');
        //     @endif


        //     @foreach($notification_details as $notification_detail)
        //         @if($notification_detail['shipper_toggle'] == 1)
        //             $("#excluded_users_container_{{$notification_detail['id']}}").removeClass('d-none');
        //             @if($notification_detail['shippers'] != null)
        //                 var ids = @json($notification_detail['shippers']);
        //                 $("#excluded_users_{{$notification_detail['id']}}").val(ids).trigger('change');
        //             @endif
        //         @else
        //             $("#only_users_container_{{$notification_detail['id']}}").removeClass('d-none');
        //             @if($notification_detail['shippers'] != null)
        //                 var ids = @json($notification_detail['shippers']);
        //                 $("#only_users_{{$notification_detail['id']}}").val(ids).trigger('change');
        //             @endif
        //         @endif

        //         @if($notification_detail['charged_sms_toggle'] == 1)
        //             $("#charging_frequency_{{$notification_detail['id']}}").prop('readonly', false);
        //             $("#sending_frequency_{{$notification_detail['id']}}").prop('readonly', false);
        //         @else
        //             $("#charging_frequency_{{$notification_detail['id']}}").prop('readonly', true);
        //             $("#sending_frequency_{{$notification_detail['id']}}").prop('readonly', true);
        //         @endif

        //         $("#excluded_users_{{$notification_detail['id']}}").select2({
        //             placeholder: 'Select Excluded Shippers',
        //             width: '100%',
        //             allowClear: true
        //         });

        //         $("#only_users_{{$notification_detail['id']}}").select2({
        //             placeholder: 'Select Only Shippers',
        //             width: '100%',
        //             allowClear: true
        //         });

        //         $("#only_users_{{$notification_detail['id']}}").on('select2:unselecting', function(event) {
        //             var users_to_keep = @json($notification_detail['sms_enable_shippers']);
        //             var unselectedItemId = Number(event.params.args.data.id);
        //             if (users_to_keep.includes(unselectedItemId)) {
        //                 event.preventDefault();
        //                 swal({
        //                     text: 'You can not remove shipper' + ' ' +  event.params.args.data.text + ' ' + 'as its sms charges status is enable.',
        //                     title: 'Cannot Remove',
        //                     icon: 'warning',
        //                     dangerMode: true
        //                 })
        //             }
        //         });

        //         $("#all_shipper_toggle_{{$notification_detail['id']}}").change(function () {
        //             if ($("#all_shipper_toggle_{{$notification_detail['id']}}").is(':checked')) {
        //                 $("#excluded_users_container_{{$notification_detail['id']}}").removeClass('d-none');
        //                 $("#only_users_container_{{$notification_detail['id']}}").addClass('d-none');
        //             } else {
        //                 $("#excluded_users_container_{{$notification_detail['id']}}").addClass('d-none');
        //                 $("#only_users_container_{{$notification_detail['id']}}").removeClass('d-none');
        //             }
        //         });

        //         $("#charged_sms_{{$notification_detail['id']}}").change(function () {
        //             if ($("#charged_sms_{{$notification_detail['id']}}").is(':checked')) {
        //                 $("#charging_frequency_{{$notification_detail['id']}}").prop('readonly', false);
        //                 $("#sending_frequency_{{$notification_detail['id']}}").prop('readonly', false);
        //             } else {
        //                 $("#charging_frequency_{{$notification_detail['id']}}").val('');
        //                 $("#sending_frequency_{{$notification_detail['id']}}").val('');
        //                 $("#charging_frequency_{{$notification_detail['id']}}").prop('readonly', true);
        //                 $("#sending_frequency_{{$notification_detail['id']}}").prop('readonly', true);
        //             }
        //         });

        //         $("#charging_frequency_{{$notification_detail['id']}}").inputmask({
        //             'alias': 'integer',
        //             'allowMinus': false,
        //             'allowPlus': false
        //         });
        //         $("#sending_frequency_{{$notification_detail['id']}}").inputmask({
        //             'alias': 'integer',
        //             'allowMinus': false,
        //             'allowPlus': false
        //         });

        //         $.validator.addMethod("sending_frequency_{{$notification_detail['id']}}",
        //             function(value, element) {
        //                 sending_value = parseFloat($("#sending_frequency_{{$notification_detail['id']}}").val()); 

        //                 if ($("#charged_sms_{{$notification_detail['id']}}").is(':checked') && isNaN(sending_value)) {
        //                    //console.log(sending_value)
        //                     return false;
        //                 }
        //                 return true;
        //             },
        //             "Sending Frequency and must be a valid number."
        //         );

        //         $.validator.addMethod("charging_frequency_{{$notification_detail['id']}}",
        //             function(value, element) {
        //                 charging_value = parseFloat($("#charging_frequency_{{$notification_detail['id']}}").val());
        //                 sending_value = parseFloat($("#sending_frequency_{{$notification_detail['id']}}").val());
                        
        //                 if ($("#charged_sms_{{$notification_detail['id']}}").is(':checked')) {
        //                     //console.log(charging_value)  
        //                     if(isNaN(charging_value)) {
        //                         return false;
        //                     }
        //                     return charging_value <= sending_value;
        //                 } else {
        //                     return true;
        //                 }
                        
                       
        //             },
        //             "Charging Frequency can't be greater than Sending Frequency and must be a valid number."
        //         );
                
        //     @endforeach

        //     $('#settings_form').validate({
        //         errorClass: 'danger',
        //         successClass: 'success',
        //         errorPlacement: function (error, element) {
        //             error.addClass('w-100').appendTo(element.parent('.form-group'));
        //         },
        //         submitHandler: function (form) {

        //             if ($('#all_shipper_toggle').is(':checked')) {
        //                 $('#only_users').val(null).trigger('change');
        //             } else {
        //                 $('#excluded_users').val(null).trigger('change');
        //             }

        //             swal({
        //                 title: 'Please Wait!',
        //                 text: 'Setting is being updated!',
        //                 icon: 'info',
        //                 buttons: false,
        //                 closeOnClickOutside: false,
        //                 closeOnEsc: false
        //             });
        //             form.submit();
        //         }
        //     });
        //     $("#all_shipper_toggle").change(function () {
        //         if ($("#all_shipper_toggle").is(':checked')) {
        //             $("#excluded_users_container").removeClass('d-none');
        //             $("#only_users_container").addClass('d-none');
        //         } else {
        //             $("#excluded_users_container").addClass('d-none');
        //             $("#only_users_container").removeClass('d-none');
        //         }
        //     });

        //     //    todo
        // });

    </script>
@endsection
