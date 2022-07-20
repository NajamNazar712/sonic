@extends('admin.layout.master')

@section('title', 'CX Training')

@section('content')
    <h1>CX Training</h1>

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
                                        <th class="border-primary border-darken-1">Agent Name</th>
                                        <th class="border-primary border-darken-1">Unit</th>
                                        <th class="border-primary border-darken-1">Joining Date</th>
                                        <th class="border-primary border-darken-1">Requested Date</th>
                                        <th class="border-primary border-darken-1">Requested By</th>
                                        <th class="border-primary border-darken-1">Aging</th>
                                        <th class="border-primary border-darken-1">Training By</th>
                                        <th class="border-primary border-darken-1">Status Updated At</th>
                                        <th class="border-primary border-darken-1">Status</th>
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
                <form id="add_shipper_form" method="post" action="#" class="justify-content-center" novalidate="novalidate">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select name="agent_id" class="select2" id="agent_id" data-rule-required="true" data-msg-required="Agent is required">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <textarea type="text" class="form-control" name="description" placeholder="Enter description" id="description" data-rule-required="true" data-msg-required="Description is required"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary width-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="editShipperModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editShipperModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Mark High Alert (Edit)</h4>

                </div>
                <form id="edit_shipper_form" method="post" action="#" class="justify-content-center" novalidate="novalidate">
                    <input type="hidden" name="alert_id" id="alert_id">
                    <div class="modal-body text-center">
                        @csrf
                        <div class="form-group">
                            <select name="agent_id" class="select2" id="agent_id" data-rule-required="true" data-msg-required="Agent is required">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <textarea type="text" class="form-control" name="description" placeholder="Enter description" id="edit_description" data-rule-required="true" data-msg-required="Description is required"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary width-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div
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

            $('#shipper_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Search Shipper",
                allowClear:true,
                dropdownParent:$('#add_shipper_form')
            });

            $('#edit_shipper_select').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Search Shipper",
                dropdownParent:$('#edit_shipper_form')
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
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.qa.cx_training.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Agent Name');
                            head.push('Unit');
                            head.push('Joining Date');
                            head.push('Requested Date');
                            head.push('Requested By');
                            head.push('Aging');
                            head.push('Training By');
                            head.push('Status Updated At');
                            head.push('Status');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.sale_person);
                                row.push(values.alert_by);
                                row.push(values.description);
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
                        @if(session('role_id') == 1 || in_array(693,session('permissions')))
                    {
                        title: 'Mark ',
                        className: 'btn btn-primary',
                        text: '<i class="la la-check"></i> Mark',
                        action: function (e, dt, node, config) {
                            $('#addShipperModal #shipper_select').val('').trigger('change');
                            $('#addShipperModal #description').val('');
                            $('#addShipperModal').modal('show');
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

                ajax: '{{ route('admin.qa.cx_training.list') }}',
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return ''; }},
                    {data: 'shipper', name: 'users.name', class: 'align-middle shipper'},
                    {data: 'city', name: 'cities.name', class: 'align-middle city'},
                    {data: 'sale_person', name: 'sp.name', class: 'align-middle sale_person'},
                    {data: 'alert_by', name: 'hab.name', class: 'align-middle alert_by'},
                    {data: 'description', name: 'high_alert_shippers.description', class: 'align-middle description'},
                    {data: 'status', name: 'status', class: 'align-middle status', orderable: false, searchable: false},
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


                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action')  || $(header).is('.serial_number') || $(header).is('.status')) {
                            $(td).appendTo($(search));
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
            });

            $('#add_shipper_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {


                    swal({
                        title: 'Are You Sure?',
                        text: 'You want to mark this Shipper as High Alert!',
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

                            var shipper_id = $('#add_shipper_form #shipper_select').val();
                            var description = $('#add_shipper_form #description').val();
                            $.ajax({
                                url: '{{ route('admin.qa.high_alert.shippers.add') }}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipper_id': shipper_id,
                                    'description': description,
                                }
                            })
                                .done(function (data){
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#addShipperModal').modal('hide');
                                    table.draw(false);
                                });
                        }
                    });

                }
            });

            $('#datatable tbody').on('click', 'td.action button', function (){
                if($(this).hasClass('edit')){
                    var id = $(this).parents('tr').attr('id');
                    if(id){

                        $.ajax({
                            url: '{!! route('admin.qa.high_alert.shippers.info') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'alert_id': id,
                            }
                        }).done(function (data) {
                            if(data.status == 0){

                                $('#edit_shipper_form #alert_id').val(id);
                                $('#edit_shipper_form #edit_shipper_select').val(data.shipper_id).trigger('change');
                                $('#edit_shipper_form #edit_shipper_select').prop('disabled', true);
                                $('#edit_shipper_form #edit_description').val(data.description);
                                $('#editShipperModal').modal('show');
                            }
                            else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });
                    }
                }
                if($(this).hasClass('remove')){
                    var id = $(this).parents('tr').attr('id');
                    if(id){
                        swal({
                            title: 'Are You Sure?',
                            text: 'You want to mark this Shipper as High Alert!',
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
                                $.ajax({
                                    url: '{!! route('admin.qa.high_alert.shippers.remove') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'alert_id': id,
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                        table.draw(false);
                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                });
                            }
                        });


                    }
                }

            });


            $('#edit_shipper_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'You want to mark this Shipper as High Alert!',
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

                            var alert_id = $('#edit_shipper_form #alert_id').val();
                            // var shipper_id = $('#edit_shipper_form #edit_shipper_select').val();
                            var description = $('#edit_shipper_form #edit_description').val();
                            $.ajax({
                                url: '{{ route('admin.qa.high_alert.shippers.edit') }}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    // 'shipper_id': shipper_id,
                                    'alert_id': alert_id,
                                    'description': description,
                                }
                            })
                                .done(function (data){
                                    if(data.status == 0){
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                        $('#editShipperModal').modal('hide');
                                        table.draw(false);

                                    }
                                    else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#addShipperModal').modal('hide');
                                    table.draw(false);
                                });
                        }
                    });

                }
            });
        });
    </script>

@endsection