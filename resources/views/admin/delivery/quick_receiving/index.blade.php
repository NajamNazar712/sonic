
@extends('admin.layout.master')
@section('title','Quick Receiving')

@section('content')
    <h1 class="mb-1">
        Quick Receiving
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" autofocus autocomplete="off" class="form-control" placeholder="Scan Delivery Note Number" name="delivery_note" id="scan_delivery_note">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                            <span class="text-danger font-small-3 myError" id="delivery_note_error"></span>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" readonly autocomplete="off" class="form-control" placeholder="Scan Tracking Number" id="scan_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                            <span class="text-danger font-small-3 myError" id="scan_tracking_error"></span>
                        </fieldset>
                    </div>


                </div>
                <div class="row mb-2 justify-content-center">
                    <div class="col-3 text-center">
                        <span>Delivery Note #</span>
                        <span id="delivery_note_label"></span>
                    </div>

                    <div class="col-3 text-center">
                        <span>Scanned :</span>
                        <span id="remaining_scanned">0</span> / <b id="total_to_scan">0</b>
                    </div>

                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                    </tr>
                    </thead>
                </table>

                <div class="row mb-2 justify-content-center">
                    <form id="submit_form" action="{{route('admin.delivery.quick_receiving.submit')}}" method="post">
                        @csrf
                        <input type="hidden" name="delivery_note" id="submit_delivery_note_id" value="">
{{--                        <input type="hidden" name="tracking_numbers" id="submit_tracking_numbers" value="">--}}
                        <button type="button" id="submit_button" class="btn btn-primary">Receive</button>
                    </form>
                </div>
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

        table.dataTable tbody tr.statusUpdated {
            background-color:yellow;
            color: #000;
        }
        table.dataTable tbody tr.statusDelivered {
            background-color:springgreen;
            color: #000;
        }
        table.dataTable tbody tr.statusReturn {
            background-color: #ef5753;
            color: #000;
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
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var tracking_numbers = [];
            var all_tracking_numbers = []
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                paging:false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false, searchable: false},
                    {name: 'status', class: 'align-middle status', orderable: false, searchable: false},
                    {name: 'reason', class: 'align-middle reason', orderable: false, searchable: false},
                    {name: 'remarks', class: 'align-middle remarks', orderable: false, searchable: false},
                    {name: 'status_date', class: 'align-middle status_date', orderable: false, searchable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false, searchable: false},
                    {name: 'destination', class: 'align-middle destination', sortable: false, orderable: false, searchable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false, searchable: false},
                    {name: 'shipper_name', class: 'align-middle shipper_name', sortable: false, orderable: false, searchable: false},
                ],
                initComplete: function() {

                }
            });

            $('#scan_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).on('change', function() {
                blockPagePermanently();
                var tracking_number = this.value;
                var delivery_note_id = $("#scan_delivery_note").val();
                $.ajax({
                    url: '{!! route('admin.delivery.quick_receiving.track_tracking_number') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': delivery_note_id,
                        'tracking_number': tracking_number,
                    }
                }).done(function(data){
                    UnblockPagePermanently();
                    $("#scan_tracking").val('');
                    if(data.status == 0){
                        if(tracking_numbers.indexOf(data.details.tracking_number) != -1)
                        {
                            toastr.error("Tracking Number Already Scanned", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            return;
                        }
                        tracking_numbers.push(data.details.tracking_number);
                        scan_sound(1);
                        $("#remaining_scanned").html(parseInt($("#remaining_scanned").html()) + 1);
                        var rowNo = table.rows().count();
                        var tracking_number_column = `<input type="hidden" form="submit_form" name="tracking_number[]" value="${data.details.tracking_number}">${data.details.tracking_number}`
                        var rowNode = table.row.add([rowNo + 1, tracking_number_column , data.details.status,data.details.reason,data.details.remarks,data.details.status_date, data.details.origin, data.details.destination, data.details.amount, data.details.shipper_name]).node().id = data.details.row_id;
                        table.draw(false);
                        $('#datatable tr').last().addClass(data.details.class);
                        table.order([0, 'asc']).draw();
                    }else{
                        scan_sound(2);
                        $("#scan_tracking").focus();
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            });

            $('#scan_delivery_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).on('change', function(e) {
                blockPagePermanently();
                var delivery_note_id = this.value;
                $.ajax({
                    url: '{!! route('admin.delivery.quick_receiving.track_delivery_note') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': delivery_note_id
                    }
                }).done(function(data){
                    UnblockPagePermanently();
                    if(data.status == 0){
                        scan_sound(1);
                        $("#delivery_note_label").html(data.delivery_note_number);
                        $("#total_to_scan").html(data.total_shipments);
                        $.each(data.tracking_numbers,function (i,v) {
                           all_tracking_numbers.push(v);
                        });
                        $("#scan_delivery_note").attr("readonly",true);
                        $("#scan_tracking").attr("readonly",false);
                        $("#scan_tracking").focus();
                    }else{
                        scan_sound(2);
                        $("#delivery_note_label").html("");
                        $("#total_to_scan").html("0");
                        $("#scan_delivery_note").attr("readonly",false);
                        $("#scan_delivery_note").focus();
                        $("#scan_tracking").attr("readonly",true);
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                });
            });

            $("#submit_button").on('click', function () {
                var delivery_note = $("#scan_delivery_note").val();
                if(delivery_note == "" || !$("#scan_delivery_note").attr('readonly') || $("#delivery_note_label").html("") == "")
                {
                    $("#delivery_note_error").html("Delivery Note Number Can\'t Be Empty or Invalid");
                    return;
                }
                if(all_tracking_numbers.length != tracking_numbers.length)
                    {
                        var html = "There are Shipments that are not scanned from delivery note number# "+delivery_note;

                        var dtrows = $('#datatable').DataTable().rows().count();

                        if(dtrows == 0)
                        {
                            toastr.error("Please Scan at least 1(one) tracking number", 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            return;
                        }
                        
                        jQuery.grep(all_tracking_numbers, function(el) {
                            if (jQuery.inArray(el, tracking_numbers) == -1) html += "</br>"+el;
                        });

                        html += "</br>Are you sure, You want to scan incomplete delivery note?";

                        var content = document.createElement('div');
                        content.innerHTML = html;
                        swal({
                            content: content,
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
                        }).then(function(confirm) {
                            if (confirm) {
                                $('#submit_delivery_note_id').val(delivery_note);
                                $('#submit_form').submit();
                            }
                        });
                    }
                    else {
                        $('#submit_delivery_note_id').val(delivery_note);
                        $('#submit_form').submit();
                    }
            });

        });
    </script>
@endsection