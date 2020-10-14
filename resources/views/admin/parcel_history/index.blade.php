@extends('admin.layout.master')

@section('title', 'Parcel History')

@section('content')

    <h1 class="mb-1">
        Parcel History
    </h1>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="track" class="btn btn-primary" value="Track">Track</button>
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
                            <div class="row">
                                <h2 class="heading">Rider</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="add_rider_mode" id="search_shipping_mode" class="form-control select2">
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
                            <div class="row">
                                <h2 class="heading">Admin</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                        @foreach($admin_name as $admins)
                                            <option value="{{$admins->id}}">{{$admins->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Parcel Guilty Model--}}
    <div class="modal fade text-left" id="AddParcelModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddParcelModal"
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
                    <form id="add_request_form" method="post" action="{{ route('admin.parcel_history.list') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="requested_shipment_ids">
                        <input type="hidden" id="user_id">
                        <textarea id="" name=""></textarea>
                        <input type="text" id="amout" value="amount">

                            <div class="row old_scroll" id="requested_shipments">

                            </div>

                            <div class="col-3">
                                <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                            </div>
                    </form>
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
            var select = $('#track_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)*',
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

            @if (app('request')->has('tracking_number'))
            track({{ app('request')->input('tracking_number') }});
            @endif

            function track(tracking_numbers){
                $.ajax({
                    url: '{!! route('tracking.track') !!}',
                    method: 'POST',
                    data: {
                        'tracking_numbers': tracking_numbers,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        select[0].selectize.clear();

                        if (data.invalid !== undefined) {
                            var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                            toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                        }

                        if (data != undefined) {

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
                            $('#add_rider_mode').prepend('<option value="" selected="selected"></option>').select2({
                                width: '100%',
                                placeholder: 'Select Rider',
                                allowClear:true
                            }).bind('change', function() {

                                //
                            });


                            $('#tracking table.datatable.tracking_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'status', class: 'align-middle status'}
                                ]
                            });
                        }
                    });
            }

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
        });
    </script>
@endsection