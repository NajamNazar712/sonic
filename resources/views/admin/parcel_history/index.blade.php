@extends('admin.layout.master')

@section('title', 'Open Parcel History')

@section('content')

    <h1 class="mb-1">
       Open Parcel History
    </h1>


    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                    <div class="form-group">
                        <input type="text" name="tracking_numbers" class="form-control tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                    </div>

                    <div class="form-group ml-1">
                        <button type="submit" name="track" class="btn btn-primary" value="Track">Add</button>
                    </div>
                </form>
                <div class="col">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking Number</th>
                            <th class="border-primary border-darken-1">User Type</th>
                            <th class="border-primary border-darken-1">User</th>
                            <th class="border-primary border-darken-1">City</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Amount</th>
                            <th class="border-primary border-darken-1">Date</th>
                        </tr>
                        </thead>
                    </table>
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
                    <form id="add_request_form" method="post"  action="{{ route('admin.open_parcel_history.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="container">

                            <input type="hidden" name="shipment_id" id="shipment_id">
                            <div class="row justify-content-center mb-1">
                                <b id="requested_tracking_number"></b>
                            </div>
                            <div class="row justify-content-center mb-1">
                                <div class="col-6">
                                  <select name="select_user_mode" id="select_user_mode" class="form-control select2" data-rule-required="true" data-msg-required="User Mode is required">
                                      <option value="1">
                                          Rider
                                      </option>
                                      <option value="2">
                                          Admin
                                      </option>
                                  </select>
                                </div>
                            </div>

                            <div class="row justify-content-center mb-1 d-none" id="rider_div">
                                <div class="col-6">
                                    <div class="form-group">
                                        <select name="select_rider" id="select_rider" class="form-control select2" data-rule-required="true" data-msg-required="Rider is required">
                                            @foreach($riders as $rider)
                                                <option value="{{$rider->id}}">{{$rider->name}} ({{$rider->city->name}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mb-1 d-none" id="admin_div">
                                <div class="col-6">
                                    <div class="form-group">
                                        <select name="select_admin" id="select_admin" class="form-control select2" data-rule-required="true" data-msg-required="Admin is required">
                                            @foreach($admins as $admin)
                                                <option value="{{$admin->id}}">{{$admin->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-none" id="remarks_div">
                                    <div class="modal-body">
                                        <div class="container">

                                            <div class="row justify-content-center">
                                                <div class="col-8">
                                                    <div class="form-group">
                                                        <textarea type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks" rows="5" data-rule-required="true" data-msg-required="Remarks is required" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control required" id="amount" name="amount" placeholder="Amount" data-rule-required="true" data-msg-required="Amount is required" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-8">
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
                            </div>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#select_user_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select User',
                dropdownParent: $('#add_request_form')
            }).bind('change', function() {
                var selected_user = this.value;
                if(selected_user == 1){
                    $('#rider_div').removeClass('d-none');
                    $('#admin_div').addClass('d-none');
                }
                else if(selected_user == 2){
                    $('#admin_div').removeClass('d-none');
                    $('#rider_div').addClass('d-none');
                }
                $('#remarks_div').removeClass('d-none');
            });
            var date_limit = '{{ Carbon\Carbon::now()->toDateString() }}';

            var date = $('#parcel_date').pickadate({
                firstDay: 1,
                clear: '',
                max: new Date(date_limit),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
            });

            $('#select_rider').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider',
                allowClear:true,
                dropdownParent: $('#add_request_form')
            });

            $('#select_admin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Admin',
                allowClear:true,
                dropdownParent: $('#add_request_form')
            });

            $('#track_form input.tracking_numbers').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#amount').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.open_parcel_history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking Number');
                            head.push('User Type');
                            head.push('User');
                            head.push('City');
                            head.push('Remarks');
                            head.push('Amount');
                            head.push('Date');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.user_mode);
                                row.push(values.user);
                                row.push(values.city);
                                row.push(values.remarks);
                                row.push(values.amount);
                                row.push(values.date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Open Parcel History',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: false,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.open_parcel_history.list') }}',
                    data: function (d) {
                        d.return_note_number = $('#scan_return_note').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'id',
                order: [[7, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 's.tracking_number', class: 'align-middle tracking_number'},
                    { data:'user_mode' ,name: 'open_parcel_histories.user_mode', class: 'align-middle user_mode'},
                    { data:'user' ,name: 'user', class: 'align-middle user'},
                    { data:'city' ,name: 'c.name', class: 'align-middle city'},
                    { data:'remarks' ,name: 'open_parcel_histories.remarks', class: 'align-middle remarks'},
                    { data:'amount' ,name: 'open_parcel_histories.amount', class: 'align-middle amount'},
                    { data:'date' ,name: 'open_parcel_histories.date', class: 'align-middle date'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var user_mode = '<select name="user_mode_select" id="user_mode_select" class="select2 form-control">' +
                        '<option value="1">Rider</option>' +
                        '<option value="2">Admin</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.user_mode')){
                            $(user_mode).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                        $('#user_mode_select').prepend('<option value="" selected="selected"></option>').select2({
                            width: '100%',
                            placeholder: 'Select User',
                            containerCssClass: 'select-xs',
                            dropdownCssClass: 'form-control-sm p-0'
                        })
                    });
                    this.api().table().columns.adjust();
                }
            });
            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function (form) {
                    var tracking_number =$(form).find('.tracking_numbers').val();
                    blockPagePermanently();
                    $.ajax({
                        url: '{!! route('admin.open_parcel_history.info') !!}',
                        method: 'POST',
                        data: {
                            'tracking_number': tracking_number,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(data) {
                        UnblockPagePermanently();
                        if (data.status == 1) {
                            $('#shipment_id').val(data.shipment_id);
                            var text = 'Tracking Number: ' + tracking_number;
                            $('#requested_tracking_number').text(text);
                            $('#AddRequestModal').modal('show');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
                }
            });

            $('#add_request_form').validate({
                ignore: ":not(:visible),:disabled",
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Remarks is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });

            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#select_user_mode').val('').trigger('change');
                $('#select_admin').val('').trigger('change');
                $('#select_rider').val('').trigger('change');
                $('#remarks').val('');
                $('#amount').val('');
                $('#parcel_date').val('');
                $('#rider_div').addClass('d-none');
                $('#admin_div').addClass('d-none');
                $('#remarks_div').addClass('d-none');
            });
        });
    </script>
@endsection