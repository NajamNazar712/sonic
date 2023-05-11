@extends('admin.layout.master')

@section('title', 'Delivery Note Shipments')

@section('content')
<h1 class="mb-1">
    Delivery Note Shipments
</h1>

<div class="card">
    <div class="card-content" aria-expanded="true">
        <div class="card-body">
            @include('admin.inc.messages')


            <div class="row mb-2 justify-content-center">

                    <div class="col-4 form-group">
                        <select name="scan_delivery_note[]" id="scan_delivery_note" class="form-control select2" multiple="multiple" data-msg-required="Atleast One Delivery Note ID Is Required" data-rule-required="true" required="required">
                            @foreach($delivery_notes_id as $delivery_note_id)
                            <option value="{{$delivery_note_id->id}}">{{$delivery_note_id->id}}</option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div class="col-2 mb-3">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                

            </div>


            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                <thead>
                <tr role="row" class="bg-primary white">
                    <th class="border-primary border-darken-1">S No.</th>
                    <th class="border-primary border-darken-1">Delivery Note No.</th>
                    <th class="border-primary border-darken-1">Rider ID</th>
                    <th class="border-primary border-darken-1">Rider Name</th>
                    <th class="border-primary border-darken-1">Tracking Number</th>
                    <th class="border-primary border-darken-1">Created At</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
<style>
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
<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


<script>
    $(document).ready(function() {
        $('body').on('click', '.printdeliverynote', function () {
            var deliverynote = $(this).attr('data-id');
            print(deliverynote);
        });
        function print(id) {
            $.ajax({
                url: '{!! route('admin.delivery.receive.print') !!}',
                method: 'POST',
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                }
            })
                .done(function (data) {
                    var tab = window.open('', '_blank');

                    if (!tab) {
                        swal({
                            title: 'Popup Blocker Enabled!',
                            text: 'Please add this site to your exception list.',
                            icon: 'error',
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                    } else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
        }
        jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.delivery_shipments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Delivery Note No.');
                            head.push('Rider ID');
                            head.push('Rider');
                            head.push('Tracking Number');
                            head.push('Created At');

                          
                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.excel_delivery_note);                              
                                row.push(values.riderID);
                                row.push(values.rider);
                                row.push(values.excel_tracking_number);
                                row.push(values.created_at);
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
                scrollX: false, scrollY: '500px',
                
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Deliveries Note Shipment',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },
                    'reset'
                ],
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.delivery_shipments.list') }}',
                    data: function (d) {
                        
                        d.delivery_note_number = $('#scan_delivery_note').val();
                    }
                },
                rowId: 'delivery_notes.id',
                order: [[1, 'desc']],
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
                    {data: 'delivery_note', name: 'delivery_notes.id', class: 'align-middle delivery_note'},
                    {data: 'riderID', name: 'riders.id', class: 'align-middle riderID'},
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'created_at', name: 'delivery_notes.created_at', class: 'align-middle created_at'},

                  
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },

                // drawCallback: function (settings) {
                //     var api = new $.fn.dataTable.Api(settings);
                //     var data = api.rows({page: 'current'}).data();

                //     if ($('#scan_delivery_note').val() != '') {
                //         if (data.length > 0) {
                //             scan_sound(1);
                //         } else {
                //             scan_sound(2);
                //         }
                //     }
                // },
              
            });


          
            $('#search_filter_btn').on('click',function () {
                let delivery_note = $('#scan_delivery_note').val()
                if(delivery_note.length == 0 ){
                    var error = "Please add one delivery note at least";

                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return false;
                }
                table.draw();
            });     


            $('#scan_delivery_note').select2({
            placeholder: 'Select Note ID'
            , width: '100%'
            , allowClear: true
        })
            
       

    })

</script>
@endsection
