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
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Draft Cargo ID.</th>
                                    <th class="border-primary border-darken-1">Orgin ID.</th>
                                    <th class="border-primary border-darken-1">Shipments</th>
                                    <th class="border-primary border-darken-1">Cargo Type</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                </tr>
                                </thead>
                            </table>
                            <div class="text-center">
                            <button type="submit" class="btn btn-primary center" id="draft_cargo" data-toggle="modal" data-target="#draft_cargo_data" disabled="disabled">Confirm</button>
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
            columns: [
                {
                    name: 'serial_number',
                    orderable: false,
                    searchable: false,
                    class: 'align-middle serial_number',
                    targets: 1
                },
                {name: 'id', name:'draft_cargos.id', class: 'align-middle tracking_number'},
                {name: 'orgin_id', name:'draft_cargos.orgin_id', class: 'align-middle order_id'},
                {name: 'shipments_count', name:'draft_cargos.shipments_count', class: 'align-middle service_type'},
                {name: 'cargo_type', name:'draft_cargos.cargo_type', class: 'align-middle service_type'},
                {name: 'hub_id', name:'draft_cargos.destination_id', class: 'align-middle amount'}
            ],
            rowCallback: function (row, data, index) {
                var info = table.page.info();

                $('td:eq(0)', row).html(index + 1 + info.page * info.length);
            },
            initComplete: function () {
                this.api().table().columns.adjust();
            }
        });
    });
    </script>

@endsection