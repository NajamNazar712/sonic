@extends('admin.layout.master')

@section('title', 'Rider Attendance')

@section('content')
    <h1>Rider Attendance</h1>

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
                                    <th class="border-primary border-darken-1">Trax ID</th>
                                    <th class="border-primary border-darken-1">Rider Name</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Rider Type</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Clock In</th>
                                    <th class="border-primary border-darken-1">Clock In Location</th>
                                    <th class="border-primary border-darken-1">Clock Out</th>
                                    <th class="border-primary border-darken-1">Clock Out Location</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.attendance.rider.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Trax ID');
                            head.push('Rider Name');
                            head.push('Hub');
                            head.push('Rider Type');
                            head.push('Department');
                            head.push('Date');
                            head.push('Clock In');
                            head.push('Clock Out');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.rider_name);
                                row.push(values.city_name);
                                row.push(values.rider_type);
                                row.push(values.department);
                                row.push(values.attendance_date);
                                row.push(values.clock_in);
                                row.push(values.clock_out);
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
                        title: 'Rider Attendance',
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
                ajax: '{{ route('admin.attendance.rider.list') }}',
                order: [[6, 'desc']],
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
                    {data: 'trax_id', name: 'r.trax_id', class: 'align-middle trax_id'},
                    {data: 'rider_name', name: 'r.name', class: 'align-middle rider_name'},
                    {data: 'city_name', name: 'c.id', class: 'align-middle city_name'},
                    {data: 'rider_type', name: 'rt.id', class: 'align-middle rider_type'},
                    {data: 'department', name: '', class: 'align-middle department', sortable: false},
                    {data: 'attendance_date', name: 'rider_attendances.attendance_date', class: 'align-middle attendance_date'},
                    {data: 'clock_in', name: 'rider_attendances.clock_in', class: 'align-middle clock_in'},
                    {data: 'clock_in_location', name: '', class: 'align-middle clock_in_location', sortable: false},
                    {data: 'clock_out', name: 'rider_attendances.clock_out', class: 'align-middle clock_out'},
                    {data: 'clock_out_location', name: '', class: 'align-middle clock_out_location', sortable:false},
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
                    var rider_type = '<select name="rider_type" id="rider_type_select" class="select2 form-control"></select>';
                    var city = '<select name="city" id="city_select" class="select2 form-control"></select>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select') || $(header).is('.clock_out_location') || $(header).is('.clock_in_location')) {
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.rider_type')){
                            $(rider_type).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.city_name')){
                            $(city).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    var data = $.map({!! $rider_type !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $rider_type !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    var data1 = $.map({!! $city !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $city !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#rider_type_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Rider Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#city_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select City",
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
        });
    </script>

@endsection