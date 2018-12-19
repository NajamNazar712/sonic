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
                                    <th class="border-primary border-darken-1">Origin ID.</th>
                                    <th class="border-primary border-darken-1">Shipments</th>
                                    <th class="border-primary border-darken-1">Cargo Type</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
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
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            scrollX: true,
            "autoWidth": false,
            paging: false,
            ajax: '{{ route('admin.cargo.draft.list') }}',
            rowId: 'id',
            columns: [
                {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                {data: 'id', name:'draft_cargos.id', class: 'align-middle id'},
                {data: 'origin_id', name:'draft_cargos.orgin_id', class: 'align-middle origin_id'},
                {data: 'shipments_count', name:'draft_cargos.shipments_count', class: 'align-middle shipments_count'},
                {data: 'cargo_type', name:'draft_cargos.cargo_type', class: 'align-middle cargo_type'},
                {data: 'hub_id', name:'draft_cargos.destination_id', class: 'align-middle hub_id'},
                {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
            ],
            rowCallback: function(row, data, index) {
                var info = table.page.info();

                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function() {
                this.api().table().columns.adjust();
            }
        });
    });
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

                    $.each(data, function(index, tracking_number) {
                        tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                    });

                    $('#shipments_count .modal-body').html(tracking_numbers);

                    $('#shipments_count').modal('show');
                }
            });
    });
    </script>

@endsection