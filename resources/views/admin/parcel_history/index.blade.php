@extends('admin.layout.master')

@section('title', 'Open Parcel Remarks')

@section('content')

    <h1 class="mb-1">
       Open Parcel Remarks
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_numbers" id="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="button" name="track" id="track" class="btn btn-primary" value="Track">Track</button>
                    </div>
                </form>

                <div class="tracking" id="tracking">
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Open Parcel Guilty Person</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                  <select name="search_user_mode" id="search_user_mode" class="form-control select2">
                                      <option value="1">
                                          Rider
                                      </option>
                                      <option value="2">
                                          Admin
                                      </option>
                                  </select>
                                </div>
                            </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{{--    Rider Modal--}}
    <div class="modal fade text-left" id="AddRiderModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRiderModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Open Parcel Guilty Person</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">

                            <input type="hidden" id="requested_rider_ids">
                            <div class="row old_scroll" id="requested_riders">

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_rider_mode" id="search_rider_mode" class="form-control select2">
                                        @foreach($rider_name as $riders)
                                            <option value="{{$riders->id}}">{{$riders->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{{--    Admin Modal--}}
    <div class="modal fade text-left" id="AddAdminModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddAdminModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Open Parcel Guilty Person</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">

                            <input type="hidden" name="user_id" id="requested_admin_id">
                            <div class="row old_scroll" id="requested_riders">

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_admin_mode" id="search_admin_mode" class="form-control select2">
                                        @foreach($admin_name as $users)
                                            <option value="{{$users->id}}">{{$users->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Parcel Guilty Model--}}
    <div class="modal fade text-left" id="AddParcelModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddPacelModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title" id="">Add Remarks For Guilty Person</h4>
                </div>

                <form id="add_remarks_form" >
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="container">

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12">
                                    <div class="form-group">
{{--                                        <input type="text" class="form-control" name="shipment_id" placeholder="Shimpent" data-rule-required="true" data-msg-required="Shipment is required">--}}
                                        <input type="hidden" id="tracking_number_id"  name="tracking_number_id"/>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
{{--                                        <input type="text" class="form-control" name="user_id" placeholder="User" data-rule-required="true" data-msg-required="User is required">--}}
                                        <input type="hidden" id="selected_user_id" name="selected_user_id" >
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control required" name="amount" placeholder="Amount" data-rule-required="true" data-msg-required="Amount is required" required>
                                    </div>
                                </div>
                                <div class="col-6">

                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                        </div>
                                        <input type="text" name="date" class="form-control bg-primary border-primary white rounded-right" id="parcel_date" placeholder="Open Parcel Date" data-value="" ata-msg-required="Date is required" required>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="addRemark">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
    <style>
        .selectize-control {
            width: 300px !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
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
        $(document).ready(function() {

            $(document).on('click', '#track', function(){
                var tracking_number = $('#tracking_numbers').val();

                $.ajax({
                    url: "{{route('admin.parcel_history.list')}}",
                    type:"POST",
                    data:{tracking_number:tracking_number, '_token':"{{csrf_token()}}"},
                    success:function(data){
                        var results = JSON.parse(data);
                        if(results.result == 'true')
                        {
                            var tracking_number_id = results.id;
                            $('#AddRequestModal').modal('show');
                            $('#search_user_mode').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select User',
                                allowClear:true
                            }).bind('change', function() {
                                var selecter = this.value;
                                if(selecter == 1){
                                    $('#AddRequestModal').modal('hide');
                                    $('#AddRiderModal').modal('show');
                                }
                                else if(selecter == 2){
                                    $('#AddRequestModal').modal('hide');
                                    $('#AddAdminModal').modal('show');
                                }
                                //
                            });

                            $('#search_rider_mode').prepend('<option value="" selected="selected"></option>').select2({
                                width: '150%',
                                placeholder: 'Select Rider',
                                allowClear:true
                            }).bind('change', function() {
                                var rider = this.value;

                                if(rider){
                                    $('#AddRiderModal').modal('hide');

                                    $('#AddParcelModal').modal('show');
                                    $('#selected_user_id').val($(this).val());
                                }
                                //
                            });

                            $('#search_admin_mode').prepend('<option value="" selected="selected"></option>').select2({
                                width: '150%',
                                placeholder: 'Select User',
                                allowClear:true
                            }).bind('change', function() {
                                var rider = this.value;

                                if(rider){
                                    $('#AddAdminModal').modal('hide');

                                    $('#AddParcelModal').modal('show');
                                    $('#selected_user_id').val($(this).val());
                                }
                                //
                            });
                            var date = $('#parcel_date').pickadate({
                                firstDay: 1,
                                width: '150%',
                                clear: 'Clear',
                                max: '{{ Carbon\Carbon::now() }}',
                                format:'dd mmmm, yyyy',
                                selectYears: true,
                                selectMonths: true,
                                formatSubmit: 'yyyy-mm-dd 00:00:00',
                                //hiddenSuffix: '_formatted',
                            });

                            $('#tracking_number_id').val(tracking_number_id);

                        }
                        else if(results.result == 'false'){
                            toastr.error('Tracking No Invalid!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    },
                    error:function(data){

                    }

                });
            })

            if($('input[name="tracking_numbers"]').val()!=0){
                $('#AddRequestModal').modal('show');
            }
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,

                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
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


            $.ajax()
            @if (app('request')->has('tracking_number'))
                url: '{!! route('admin.parcel_history.list') !!}',
            track({{ app('request')->input('tracking_number') }});
            @endif


            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    track($(form).find('.tracking_numbers').val());
                    return false;
                }
            });

            $(document).on('click', '#addRemark', function () {
                var formData = $("#add_remarks_form").serialize();
                $.ajax({
                    url:"{{route('admin.parcel_history.list')}}",
                    type:"POST",
                    data:{formData:formData, 'action':'addRemark', '_token':"{{ csrf_token() }}"},
                    success:function(data){
                        var result = JSON.parse(data);
                        if(result.message =='success'){

                        }
                        else{
                            alert('data insert failed');
                        }
                    },
                    error:function(){

                    }
                });
            });

        });
    </script>
@endsection