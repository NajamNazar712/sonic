
@extends('admin.layout.master')
@section('title','Verify Deliveries')

@section('content')
    <h1 class="mb-1">
        Verify Deliveries(Delivery Note: {{str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT)}})
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="status_update_form" action="{{route('admin.delivery.receive.verify.status.submit')}}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{$delivery_note_id}}" id="delivery_note" name="delivery_note_id">
                    <input type="hidden" value="{{$shipments_count}}" id="shipments_count" name="shipments_count">
                    <input type="hidden" name="shipment_ids" id="shipment_ids">
                    <input type="hidden" name="submit_button_id" id="submit_button_id">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Shipment ID</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Consignee</th>
                            <th class="border-primary border-darken-1">Consignee Phone</th>
                            <th class="border-primary border-darken-1">Collection Amount</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Fake Status</th>
                            <th class="border-primary border-darken-1">Arrival Date</th>
                            <th class="border-primary border-darken-1">Call Verification</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center preventsubmit">
                        @if($delivery_note_status == 0)
                        <div class="col-2">
                            <button id="statusUpdateSubmit" rel="update" type="submit" class="btn btn-primary btn-block">Update Status</button>
                        </div>
                        <div class="col-2">
                            <button id="statusVerifySubmit" rel="verify" type="submit" class="btn btn-primary btn-block">Verify Status</button>
                        </div>
                        @endif
                        @if($delivery_note_status == 1)
                            <div class="col-2">
                                <button id="printDNCC" type="button" class="btn btn-warning btn-block">Print DNCC</button>
                            </div>
                        @endif
                            <div class="col-2">
                                <button id="bloc" type="button" class="btn btn-warning btn-block">Block</button>
                            </div>
                    </div>
                </form>
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
        table.dataTable tbody tr td.call_verification {
           text-align:center;
        }
        table.dataTable tbody tr td.fake_status {
           text-align:center;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        table.dataTable tbody tr td.status,
        table.dataTable tbody tr td.reason,
        table.dataTable tbody tr td.remarks {
            min-width: 110px !important;
            max-width: 150px !important;
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
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/js/scripts/extensions/block-ui.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            var shipment_status = [];
            var shipment_reason = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                processing: true,
                serverSide: false,
                ajax: '{{ route('admin.delivery.receive.verify.status.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                order: [[1, 'asc']],
                ordering: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'shipment_id_padded',name: 'shipments.id', class: 'align-middle shipment_id'},
                    {data:'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data:'consignee_name',name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data:'consignee_phone',name: 'shipments.consignee_phone', class: 'align-middle consignee_phone'},
                    {data:'amount',name: 'shipments.amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange',orderable: false, searchable: false},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect',orderable: false, searchable: false},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks',orderable: false, searchable: false},
                    {data:'fake_status',name: 'fake_status', class: 'align-middle fake_status',orderable: false, searchable: false},
                    {data:'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data:'call_verification',name: 'call_verification', class: 'align-middle call_verification',orderable: false, searchable: false},
                    {data:'address',name: 'shipments.consignee_address', class: 'align-middle address'},
                    {data:'destination',name: 'oc.name', class: 'align-middle destination'},
                    {data:'shipper',name: 'users.name', class: 'align-middle shipper'},
                    {data:'service_type',name: 'bt.booking_type', class: 'align-middle service_type'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    $(".reasonDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").prepend('<option value="" ></option>').select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var api = new $.fn.dataTable.Api( settings );
                    var data = api.rows( {page:'current'} ).data();
                    $.each(data,function (key,value) {

                        if(shipment_status.length !== 0){
                            $('select[name="status_drop['+value.shId+']"]').val(shipment_status[value.shId]).trigger('change');
                        }else{
                            $('select[name="status_drop['+value.shId+']"]').val(value.current_status_id).trigger('change');
                        }
                        if(shipment_reason.length !== 0){
                            $('select[name="reason_drop['+value.shId+']"]').val(shipment_reason[value.shId]).trigger('change');
                        }else{
                            var reasonId = $('select[name="reason_drop['+value.shId+']"]').attr('reasonId');
                            $('select[name="reason_drop['+value.shId+']"]').val(reasonId).trigger('change');

                        }
                    });
                },
                initComplete: function() {

                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action') || $(header).is('.call_verification') || $(header).is('.fake_status')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change keypress', function() {
                                if ($(header).is('.amount')){
                                    var value = $(this).val().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                                    column.search(value, false, false, true).draw();
                                }
                                else {
                                    column.search($(this).val(), false, false, true).draw();
                                }
                            }).wrap(td).after(icon);
                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                var rowid = parseInt($(this).parents('tr').attr('id'));

                $('#statusUpdateSubmit').removeAttr('disabled');
                $('#statusVerifySubmit').removeAttr('disabled');

                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                shipment_status[rowid] = status;
                var reason = statusSelection.closest('td').next('td').find('.reasonDrop');

                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if(data.status == 0){
                        reason.empty().trigger('change');
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                        });
                        reason.val('').trigger('change');
                    }else{
                        reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $('body').on('click','.clear',function () {
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                $('.remarks input').val('');
                // $('.reasonDrop').val('').trigger("change");
            });

            $('#status_update_form').on('keypress',function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });
            var shipments = [];
            var status_array = [];
            $('#status_update_form').bind('submit', function(event) {

                    var verify_form = this;
                    event.preventDefault();
                    var btn = $(document.activeElement).attr('id');
                    $('#submit_button_id').val(btn);
                    $.each($('#datatable tr td.statusOnChange select'), function (key, value) {
                        $(this).find(':selected').removeAttr('disabled');
                        var pre_status = $(this).attr('status');
                        var selected = $(this).find(':selected').val();
                        var tracking = $(this).parents('tr').find('td.tracking_number').text();
                        if (pre_status !== selected) {
                            var newStatus = $(this).find(':selected').text();
                            var oldStatus = $(this).find('option[value="' + pre_status + '"]').text();
                            status_array[key] = {'tracking': tracking, 'old': oldStatus, 'new': newStatus};
                        }


                    });

                    if (status_array.length > 0) {
                        var content_dispute = '';
                        content_dispute += 'These shipments are found different in statuses.' + "<br>";
                        $.each(status_array, function (key, value) {
                            if (value !== undefined) {
                                content_dispute += value.tracking + ' (' + value.old + ')' + ' (' + value.new + ')' + "<br>";
                            }
                        });
                        content = document.createElement('div');
                        content.innerHTML = content_dispute;
                        swal({
                            title: 'Dispute Different Statuses!',
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
                        }).then(function (confirm) {
                            if (confirm) {

                                var shipment = $('#shipment_ids');
                                var id = '';
                                var count = table.data().count();
                                for (var i = 0; i < count; i++) {
                                    id = table.row(i).id();
                                    shipments.push(id);
                                }
                                shipment.val(shipments);
                                $('#statusVerifySubmit').prop('disabled', true);
                                $('#statusUpdateSubmit').prop('disabled', true);
                                blockPagePermanently();
                                verify_form.submit();
                            }
                        });
                    } else {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to update/verify the status of shipments!',
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
                                var shipment = $('#shipment_ids');
                                var id = '';
                                var count = table.data().count();
                                for (var i = 0; i < count; i++) {
                                    id = table.row(i).id();
                                    shipments.push(id);
                                }
                                shipment.val(shipments);
                                $('#statusVerifySubmit').prop('disabled', true);
                                $('#statusUpdateSubmit').prop('disabled', true);
                                blockPagePermanently();
                                verify_form.submit();
                            }
                        });


                    }


            });

            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
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

            $('#printDNCC').on('click',function () {
                var note_id = $('#delivery_note').val();
                print(note_id);
            });


        });
    </script>
@endsection