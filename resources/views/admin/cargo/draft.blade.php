@extends('admin.layout.master')

@section('title', 'Draft Cargo')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    Draft Cargo
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Draft Cargo ID.</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Shipments</th>
                                    <th class="border-primary border-darken-1">Cargo Type</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="shipments_count" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_title">Shipment(s)</h4>

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
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
            if ( this.context.length ) {
                body = [];

                var jsonResult = $.ajax({
                    url: '{{ route('admin.cargo.draft.list') }}',
                    data: {
                        'page': 'all',
                    },
                    success: function (result) {
                        head = [];
                        head.push('S.No');
                        head.push('Draft Cargo ID.');
                        head.push('Origin');
                        head.push('Destination');
                        head.push('Shipment(s)');
                        head.push('Cargo Type');


                        $.each(result.data, function(index, values) {
                            row = [];

                            row.push(index + 1);
                            row.push(values.id);
                            row.push(values.origin);
                            row.push(values.destination);
                            row.push(values.shipments_count);
                            row.push(values.cargo_type);

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
            buttons:[{
                extend: 'excel',
                title: 'Draft Cargos',
                className: 'btn btn-primary',
                text: '<i class="la la-file-excel-o"></i> Excel',
            }],
            scrollX: true,
            "autoWidth": false,
            lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
            pageLength: 50,
            pagingType: 'full_numbers',
            processing: true,
            serverSide: true,
            paging: false,
            ajax: '{{ route('admin.cargo.draft.list') }}',
            rowId: 'id',
            order: [[1, 'desc']],
            columns: [
                {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                {data: 'id', name:'draft_cargos.id', class: 'align-middle id'},
                {data: 'origin', name:'oc.name', class: 'align-middle origin'},
                {data: 'destination', name:'dc.name', class: 'align-middle destination'},
                {data: 'shipments_count_link', name:'draft_cargos.shipments_count', class: 'align-middle shipments_count_link'},
                {data: 'cargo_type', name:'draft_cargos.cargo_type', class: 'align-middle cargo_type'},
                {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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
                var cargo_type_select = '<select name="cargo_type_select" id="cargo_type_select" class="select2 form-control">' +
                    '<option value="1">Normal</option>' +
                    '<option value="2">Return</option>' +
                    '</select>';

                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();

                    if ($(header).is('.serial_number') || $(header).is('.action')) {
                        $(td).appendTo($(search));
                    }else if($(header).is('.cargo_type')){
                        $(cargo_type_select).appendTo($(search))
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
                $("#cargo_type_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Type",
                    width:'100%',
                    containerCssClass: 'select-xs',
                    dropdownCssClass: 'form-control-sm p-0'
                });
                this.api().table().columns.adjust();
            }
        });
    });
    var route = '{!! route('admin.tracking.index') !!}';
    $('body').on('click', 'tr td.shipments_count button', function() {
        var id = parseInt($(this).parents('tr').attr('id'));

        $('#shipments_count .modal-body').html('');

        $.ajax({
            url: '{!! route('admin.cargo.draft.shipments') !!}',
            method: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'id': id
            }
        })
            .done(function(data) {
                if (data) {
                    var tracking_numbers = '';

                    $.each(data.tracking_numbers, function(index, tracking_number) {
                        tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                    });

                    $('#shipments_count .modal-body').html(tracking_numbers);

                    $('#shipments_count').modal('show');
                }
            });
    });
    </script>

@endsection