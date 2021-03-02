@extends('admin.layout.master')

@section('title', 'Riders Pending Request')

@section('content')
    <h1>Riders Pending Request</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Rider Name</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Created At</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="approveRiderModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="approveRiderModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Approve Rider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.management.riders.rider_request.approve')}}" method="post" class="mt-1"
                      id="approveRiderForm" novalidate="novalidate">
                    {{csrf_field()}}
                    <div class="modal-body" id="riderApproveDiv">

                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="rider_type" id="rider_type_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($rider_types as $rider_type)
                                            <option value="{{$rider_type->id}}">{{$rider_type->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="city_id" id="city_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>

                        <div id="riderInfoDiv">

                            <input type="hidden" class="form-control" name="rider_request_id" id="rider_request_id">

                            <div class="row mb-2">
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="rider_name" id="rider_name"
                                               placeholder="Rider Name" required data-rule-required="true"
                                               data-msg-required="This field is required">
                                    </fieldset>

                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="phone" id="rider_phone"
                                               placeholder="Phone No." required data-rule-required="true"
                                               data-msg-required="This field is required"
                                               data-rule-remote="{{ route('admin.management.rider.phone_unique') }}"
                                               data-msg-remote="Phone must be unique">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="cnic" id="rider_cnic"
                                               placeholder="CNIC" required data-rule-required="true"
                                               data-msg-required="This field is required">
                                    </fieldset>
                                </div>
                                <div class="col">
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" name="pin" id="rider_pin"
                                               placeholder="PIN" required data-rule-required="true"
                                               data-msg-required="This field is required" data-rule-minlength="4"
                                               data-rule-maxlength="4">
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col">
                                <fieldset class="form-group">
                                    <textarea name="address" class="form-control" placeholder="Address" id="address"
                                              cols="30" rows="5" required data-rule-required="true"
                                              data-msg-required="This field is required"></textarea>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="rider_category" id="category_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="route_id" id="route_list" class="form-control select2"
                                            data-rule-required="true" data-msg-required="This field is required">
                                        @foreach($routes as $route)
                                            <option value="{{$route->id}}">{{$route->code}} ({{$route->start}}
                                                - {{$route->start}})
                                            </option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning btn-min-width mr-1 mb-1" id="confirmAction">Add
                            Rider
                        </button>
                        <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#rider_type_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Type',
                dropdownParent: $('#approveRiderModal')
            });

            $('#city_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select City',
                dropdownParent: $('#approveRiderModal')
            });
            $('#route_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Route',
                dropdownParent: $('#approveRiderModal')
            });
            $('#category_list').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Rider Category',
                dropdownParent: $('#approveRiderModal')
            });
            $('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').attr('disabled', 'disabled');
            $('#rider_trax_id').removeAttr('disabled');

            $('#city_list').on('change', function () {
                var routelist = $('#route_list');
                var id = $('#city_list').val();
                if ($(this).val() != '') {
                }
                $.ajax({
                    url: '{!! route('admin.management.rider.category.ajax') !!}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'id': id,
                    },
                    success: function (data) {

                        routelist.empty();
                        for (var i = 0; i < data.length; i++) {
                            var option = new Option(data[i].code + ' (' + data[i].start + ' to ' + data[i].end + ')', data[i].id, true, true);
                            routelist.append(option).trigger('change');
                        }
                        routelist.append('<option value="other">Other</option>').trigger('change');
                    }
                });
            });
            $('#route_list').on('change', function () {
                var selection = $(this).val();
                if (selection == 'other') {
                    $('#new_route_div').removeClass('d-none');
                } else {
                    $('#new_route_div').addClass('d-none');
                }
            });
            $("#approveRiderForm").validate({

                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $('#riderInfoDiv input,#riderInfoDiv textarea,#riderInfoDiv select').removeAttr('disabled');
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Rider is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();

                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.riders.rider_request.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Rider Name');
                            head.push('CNIC');
                            head.push('Phone No.');
                            head.push('Created At');
                            head.push('Updated At');
                            head.push('City');
                            head.push('Status');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.rider_name);
                                row.push(values.cnic);
                                row.push(values.phone_no);
                                row.push(values.created_at);
                                row.push(values.updated_at);
                                row.push(values.city_name);
                                row.push(values.status);
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
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Riders Pending Request',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('admin.management.riders.rider_request.list') }}',
                order: [[4, 'desc']],
                rowId: 'id',
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'rider_name', name: 'rider_requests.name', class: 'align-middle rider_name'},
                    {data: 'cnic', name: 'cnic', class: 'align-middle cnic'},
                    {data: 'phone_no', name: 'phone_no', class: 'align-middle phone_no'},
                    {data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
                    {data: 'updated_at', name: 'updated_at', class: 'align-middle updated_at'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select') || $(header).is('.status')) {
                            $(td).appendTo($(search));
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Rider Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    this.api().table().columns.adjust();
                }
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.sms').enable();
                } else {
                    table.button('.sms').disable();
                }
            });

            $('body').on('click', '.approve', function (e) {
                var id = $(this).data('target-id');
                var rider_name = table.row($(this).parents('tr')).data().rider_name;
                var cnic = table.row($(this).parents('tr')).data().cnic;
                var phone_no = table.row($(this).parents('tr')).data().phone_no;
                var pin = table.row($(this).parents('tr')).data().pin;
                var city_id = table.row($(this).parents('tr')).data().city_id;
                $('#rider_name').val(rider_name);
                $('#rider_cnic').val(cnic);
                $('#rider_phone').val(phone_no);
                $('#rider_request_id').val(id);
                $('#rider_pin').val(pin);
                $('#city_list').val(city_id).trigger('change');
                $('#approveRiderModal').modal('show');

            });

            $('body').on('hidden.bs.modal', '#approveRiderModal', function () {
                $('#rider_trax_id').val('');
                $('#address').val('');
                $('#rider_type_list').val(null).trigger('change');
                $('#city_list').val(null).trigger('change');
                $('#category_list').val(null).trigger('change');
                $('#route_list').val(null).trigger('change');
            });
        });
    </script>

@endsection