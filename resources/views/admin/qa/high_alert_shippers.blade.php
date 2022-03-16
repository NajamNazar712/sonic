@extends('admin.layout.master')

@section('title', 'High Alert Shippers')

@section('content')
    <h1>High Alert Shippers</h1>

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
                                        <th class="border-primary border-darken-1">Shipper</th>
                                        <th class="border-primary border-darken-1">City</th>
                                        <th class="border-primary border-darken-1">Sales Person</th>
                                        <th class="border-primary border-darken-1">High Alert By</th>
                                        <th class="border-primary border-darken-1">Description</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="addShipperModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addShipperModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Mark High Alert</h4>

                </div>
                <form id="add_report_form" method="post" action="{{route('admin.qa.high_alert.shippers.add')}}" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select class="form-control" name="station_id" id="station" data-rule-required="true" data-msg-required="Station is required">
                                @foreach($shippers as $shipper)
                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <textarea type="text" class="form-control" name="description" placeholder="Enter description" id="description" data-rule-required="true" data-msg-required="Description is required"></textarea>
                        </div>




                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary width-100" id="add_special_rider_button">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {




            $('#tagged_to').select2({
                width:'100%',
                placeholder:"Tag Person*",
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });

            $('#admin_ids').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Search Tag Persons",
                allowClear:true,
            });

            $('#monitoring_area').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Monitoring Area',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#case_nature').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Case Nature',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#nc_level').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select NC Level',
                width:'100%',
                allowClear:true,
                dropdownParent:$('#add_report_form')
            });
            $('#add_report_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
            });
            /*jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.incidence_monitoring.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Station');
                            head.push('Case #');
                            head.push('Monitoring Area');
                            head.push('Time Slot');
                            head.push('Case Nature');
                            head.push('Observations');
                            head.push('NC Level');
                            head.push('Tagged To');
                            head.push('Tagging Date');
                            head.push('Current Status');
                            head.push('Clips Link');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.station_name);
                                row.push(values.incidence_monitorings_id_padded);
                                row.push(values.area_name);
                                row.push(values.time_slot);
                                row.push(values.case_nature_type);
                                row.push(values.observation);
                                row.push(values.nc_level_name);
                                row.push(values.tagged_to);
                                row.push(values.tagging_date);
                                row.push(values.status_name);
                                row.push(values.excel_clip_link);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });*/
            /*var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                        @if(session('role_id') == 1 || in_array(536,session('permissions')))
                    {
                        text: 'Add Report',
                        className: 'btn btn-primary',
                        action: function (e, dt, node, config) {
                            $('#addReportModal').modal('show');
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'High Alert Shippers',
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

                ajax: '{{ route('admin.qa.high_alert.shippers.list') }}',
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    {data: 'station_name', name: 'station.id', class: 'align-middle station_name'},
                    {data: 'report_link', name: 'incidence_monitorings.id', class: 'align-middle incidence_monitorings_id_padded'},
                    {data: 'area_name', name: 'area.name', class: 'align-middle area_name'},
                    {data: 'time_slot', name: 'time_slot', class: 'align-middle time_slot', orderable: false, searchable: false},
                    {data: 'case_nature_type', name: 'case_nature.name', class: 'align-middle case_nature_type'},
                    {data: 'observation', name: 'incidence_monitorings.observation', class: 'align-middle observation', orderable: false, searchable: false},
                    {data: 'nc_level_name', name: 'nc_level.id', class: 'align-middle nc_level_name'},
                    {data: 'tagged_to', name: 'tagged_to', class: 'align-middle tagged_to', orderable: false, searchable: false},
                    {data: 'tagging_date', name: 'incidence_monitorings.tagging_date', class: 'align-middle tagging_date'},
                    {data: 'status_name', name: 'status.id', class: 'align-middle status_name'},
                    {data: 'clip_link', name: 'incidence_monitorings.clip_link', class: 'align-middle clip_link'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var nc_level_select = '<select name="nc_level_select" id="nc_level_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.tagged_to') || $(header).is('.time_slot')  || $(header).is('.observation')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status_name')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.nc_level_name')){
                            $(nc_level_select).appendTo($(search))
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
                    });



                    this.api().table().columns.adjust();


                }
            });*/

            $("#editIncidenceReport").on("show.bs.modal", function(e) {
                var $invoker = $(e.relatedTarget);
                var action = $invoker.attr('rel');
                var id = $(e.relatedTarget).data('target-id');


            });
            $('body').on('hidden.bs.modal','#addReportModal',function () {
                window.location.reload()
            });

        });
    </script>

@endsection