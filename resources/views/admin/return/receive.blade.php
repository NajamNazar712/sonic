
@extends('admin.layout.master')
@section('title','Receive Return Deliveries')

@section('content')
    <h1 class="mb-1">
        Receive Return Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')



                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan Return Note Number" name="scan_return_note" id="scan_return_note">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number" name="search_tracking" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Return Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Rider Trax ID</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Pending Shipments</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Return Note Shipment(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Shipments popup -->

    <div class="modal fade text-left" id="uploadReturnNote" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="uploadReturnNote"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Return Note Image Upload</h4>

                </div>
                <div class="modal-body  text-center">
                    <form id="return_note_upload_form" class="form" action="{{route('admin.return.receive.upload_image')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="image_return_note_id" id="image_return_note_id"/>
                        <input type="hidden" name="selected_ids" id="selected_ids"/>
                        <table class="table table-bordered datatable" id="return_upload_table" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Image</th>
                                <th class="border-primary border-darken-1"></th>

                            </tr>
                            </thead>
                        </table>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="" type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
                            </div>
                            <div class="col-3">
                                <button id="ReturnNoteImageSubmitButton" type="submit" class="btn btn-primary btn-block">Upload</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style type="text/css">
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.receive.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Return Note No.');
                            head.push('Hub');
                            head.push('Rider');
                            head.push('Rider Trax ID');
                            head.push('No. Of Shipments');
                            head.push('No. Of Pending Shipments');
                            head.push('Assigned By');
                            head.push('Assigned Date');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.return_note_id_padded);
                                row.push(values.hub);
                                row.push(values.rider);
                                row.push(values.rider_trax_id);
                                row.push(values.shipments_count);
                                row.push(values.shipments_unverified_count);
                                row.push(values.assignee);
                                row.push(values.created_at);
                                row.push(values.return_note_status);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Receive Return Deliveries',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.return.receive.list') }}',
                    data: function (d) {
                        d.return_note_number = $('#scan_return_note').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'return_note_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'return_note' ,name: 'return_notes.id', class: 'align-middle return_note'},
                    { data:'hub' ,name: 'oc.name', class: 'align-middle hub'},
                    { data:'rider' ,name: 'riders.name', class: 'align-middle rider'},
                    { data:'rider_trax_id' ,name: 'riders.trax_id', class: 'align-middle rider'},
                    { data:'shipments_count_link' ,name: 'return_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    // { data:'shipments_count_link' ,name: 'return_notes.shipments_count', class: 'align-middle shipments_count_link text-center'},
                    { data:'shipments_unverified_link' ,name: 'shipments_unverified_count', class: 'align-middle shipments_unverified_link text-center',orderable: false, searchable: false},
                    { data:'assignee' ,name: 'admins.name', class: 'align-middle assignee'},
                    { data:'created_at' ,name: 'return_notes.created_at', class: 'align-middle created_at'},
                    { data:'return_note_status' ,name: 'return_notes.status', class: 'align-middle return_note_status'},
                    {data:'action' ,name: 'action', class: 'align-middle action text-center',orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();

                    if($('#scan_return_note').val() != ''){
                        if(data.length > 0){
                            scan_sound(1);
                        }else{
                            scan_sound(2);
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Created</option>' +
                        '<option value="3">Updated</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.shipments_unverified_link')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.action')){
                            $(td).appendTo($(search));
                        }
                        else if($(header).is('.return_note_status')){
                            $(status_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 10) {
                    table.draw();
                }
            });

            $('#scan_return_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                table.draw();
            });

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.return.receive.rn.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            $('body').on('click','.printreturnnote',function () {
                var returnnote = $(this).parents('tr').attr('id');
                print(returnnote);
                // console.log(returnnote)
            });

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.shipments_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.return.receive.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'return_note_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click','tr td .return_image_upload',function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $('#image_return_note_id').val(id);
                    $('#uploadReturnNote').modal('show');
                    add_row();
                }
            });


            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));

            var selected_rows = [];
            var rows_count = 0;
            var return_image_table = $('#return_upload_table').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[{
                    title: 'Add Row',
                    className: 'btn btn-primary mb-1',
                    text: '<i class="la la-plus"></i> Add Row',
                    action:function (e) {
                        add_row();
                    }
                }],
                ordering:false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {name: 'image', class: 'align-middle image form-group'},
                    {name: 'action', class: 'align-middle action'},
                ],

                rowCallback: function(row, data, index) {
                    var info = return_image_table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {

                    // this.api().table().columns.adjust();
                }
            });

            function add_row() {
                rows_count++;

                var return_image = '<input class="form-control form-control-sm" type="file" name="return_note_image_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">';
                if(rows_count == 1){
                    var remove = '';
                }else{
                    var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                }
                return_image_table.row.add([0, return_image,remove]).node().id = rows_count;
                return_image_table.draw(true);
                $('#ReturnNoteImageSubmitButton').attr('disabled', false);
                selected_rows.push(rows_count);
            }

            $('#uploadReturnNote').on('hidden.bs.modal', function () {
                $('#image_return_note_id').val('');
                return_image_table.clear();
                selected_rows = [];
            });

            $('body').on('click', 'a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, selected_rows);

                if (index !== -1) {
                    selected_rows.splice(index, 1);
                }
                return_image_table.row( $(this).parents('tr') ).remove().draw();
            });

            $('#return_note_upload_form').validate({

                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    $('#selected_ids').val(selected_rows);
                    swal({
                        title: 'Please Wait!',
                        text: 'Image is being uploaded!',
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
@endsection