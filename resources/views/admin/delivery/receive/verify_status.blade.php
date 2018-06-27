
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Verify Deliveries(Delivery Note: {{$delivery_note_id}})
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
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Tracking No.</th>
                            <th class="border-primary border-darken-1">Consignee</th>
                            <th class="border-primary border-darken-1">COD Amount</th>
                            <th class="border-primary border-darken-1">Status</th>
                            <th class="border-primary border-darken-1">Reason</th>
                            <th class="border-primary border-darken-1">Remarks</th>
                            <th class="border-primary border-darken-1">Address</th>
                            <th class="border-primary border-darken-1">Destination</th>
                            <th class="border-primary border-darken-1">Shipper</th>
                            <th class="border-primary border-darken-1">Service Type</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="row justify-content-center">
                        <div class="col-2">
                            <button id="statusSubmit" type="submit" class="btn btn-primary btn-block">Verify Status</button>
                        </div>
                        @if($delivery_note_status == 1)
                            <div class="col-2">
                                <button id="printDNCC" type="button" class="btn btn-warning btn-block">Print DNCC</button>
                            </div>
                        @endif
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
            border-color: #666EE8;
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

    <script type="text/javascript">
        $(document).ready(function () {
            var selected_rows = [];
            var note_id = $('#delivery_note').val();
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                fixedHeader: {
                    header: true,
                    headerOffset: $('.header-navbar').height()
                },
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                stateSave: true,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.delivery.receive.verify.status.list',['id'=>$delivery_note_id]) }}',
                rowId: 'shId',
                order: [[2, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data:'tracking_number',name: 'tracking_number', class: 'align-middle tracking_number'},
                    {data:'consignee_name',name: 'consignee_name', class: 'align-middle consignee_name'},
                    {data:'amount',name: 'amount', class: 'align-middle amount'},
                    {data:'status',name: 'status', class: 'align-middle status statusOnChange'},
                    {data:'reason',name: 'reason', class: 'align-middle reason reasonSelect'},
                    {data:'remarks',name: 'remarks', class: 'align-middle remarks'},
                    {data:'address',name: 'address', class: 'align-middle address'},
                    {data:'destination',name: 'destination', class: 'align-middle destination'},
                    {data:'shipper',name: 'shipper', class: 'align-middle shipper'},
                    {data:'service_type',name: 'service_type', class: 'align-middle service_type'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    $(".reasonDrop").select2({
                        placeholder: "Select a Reason",
                        width:'100%'
                    });
                    $(".statusDrop").select2({
                        placeholder: "Select a Status",
                        width:'100%'
                    });
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.status') || $(header).is('.reason') || $(header).is('.remarks') || $(header).is('.action')) {
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
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button(0).enable();
                    // table.button(1).enable();
                }
                else {
                    table.button(0).disable();
                    // table.button(1).disable();
                }
            });

            $('body').on('select2:select','.statusOnChange .statusDrop',function (e) {
                $('#statusSubmit').removeAttr('disabled');
                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
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
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            reason.append(newOption).trigger('change');
                        });
                    }else{
                        $('.reasonDrop').empty();
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });
            $('body').on('click','.clear',function () {
                // console.log();
                var status = $(this).parents().closest('tr').find('.statusDrop');
                var reason = $(this).parents().closest('tr').find('.reasonDrop');
                status.val('').trigger("change");
                reason.val('').trigger("change");
                $('.remarks input').val('');
                // $('.reasonDrop').val('').trigger("change");
            });
            var shipments = [];
            $('#status_update_form').bind('submit', function(event) {
                var shipment = $('#shipment_ids');
                event.preventDefault();
                var id = '';
                var count = table.data().count();
                for(var i = 0;i<count;i++){
                    id = table.row( i ).id();
                    shipments.push(id);
                }
                shipment.val(shipments);
                $('#statusSubmit').prop('disabled',true);
                this.submit();
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
            //on page load ajax
            // var trybuy_ids = [];
            // var shipment_id_list = [];
            // var shipments_count = $('#shipments_count').val();
            {{--function checkShipmentStatuses(){--}}
                {{--var delivery_note = $('#delivery_note').val();--}}
                {{--$.ajax({--}}
                    {{--url: '{!! route('admin.delivery.receive.shipmentstatuscheck') !!}',--}}
                    {{--method: 'POST',--}}
                    {{--data: {--}}
                        {{--'delivery_note_id': delivery_note,--}}
                        {{--'_token': '{{ csrf_token() }}'--}}
                    {{--}--}}
                {{--}).done(function (data) {--}}

                    {{--shipments_count = shipments_count-1;--}}
                    {{--if(shipments_count>0) {--}}
                        {{--if (data.status == 1) {--}}

                            {{--toastr.success(data.success, 'Success!', {--}}
                                {{--positionClass: 'toast-bottom-center',--}}
                                {{--containerId: 'toast-bottom-center'--}}
                            {{--});--}}
                            {{--checkShipmentStatuses();--}}
                            {{--console.log(shipments_count);--}}

                        {{--} else if (data.status == 2) {--}}

                            {{--$('#ReplacementModal').modal('show');--}}

                            {{--var repl = $('#replacementtable').DataTable({--}}
                                {{--dom: 'ltipr',--}}
                                {{--fixedHeader: {--}}
                                    {{--header: true,--}}
                                    {{--headerOffset: $('.header-navbar').height()--}}
                                {{--},--}}
                                {{--lengthMenu: [[25, 50, 100], [25, 50, 100]],--}}
                                {{--pageLength: 25,--}}
                                {{--stateSave: true,--}}
                                {{--pagingType: 'full_numbers',--}}
                                {{--columns: [--}}
                                    {{--{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},--}}
                                    {{--{name: 'tracking_number', class: 'align-middle tracking_number'},--}}
                                    {{--{name: 'service_type', class: 'align-middle service_type'},--}}
                                    {{--{name: 'weight', class: 'align-middle weight'},--}}

                                {{--],--}}
                                {{--rowCallback: function(row, data, index) {--}}
                                    {{--var info = repl.page.info();--}}

                                    {{--$('td:eq(0)', row).html(index + 1 + info.page * info.length);--}}
                                    {{--if ($.inArray(data.id, selected_rows) !== -1) {--}}
                                        {{--repl.row(row).select();--}}
                                    {{--}--}}
                                {{--},--}}
                                {{--initComplete: function() {--}}

                                    {{--var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());--}}

                                    {{--var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';--}}
                                    {{--var input = '<input type="text" class="form-control form-control-sm input-sm primary">';--}}
                                    {{--var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';--}}

                                    {{--this.api().columns().every(function(column_id) {--}}
                                        {{--var column = this;--}}
                                        {{--var header = column.header();--}}

                                        {{--if ($(header).is('.serial_number') || $(header).is('.weight')) {--}}
                                            {{--$(td).appendTo($(search));--}}
                                        {{--}--}}
                                        {{--else {--}}
                                            {{--var current = $(input).appendTo($(search)).on('change', function() {--}}
                                                {{--column.search($(this).val(), false, false, true).draw();--}}
                                            {{--}).wrap(td).after(icon);--}}

                                            {{--if (column.search()) {--}}
                                                {{--current.val(column.search());--}}
                                            {{--}--}}
                                        {{--}--}}
                                    {{--});--}}
                                {{--}--}}
                            {{--});--}}

                            {{--$.ajax({--}}
                                {{--url:'{!! route('admin.delivery.receive.replacements') !!}',--}}
                                {{--type:'POST',--}}
                                {{--dataType:'json',--}}
                                {{--data: {--}}
                                    {{--'replacements':data.replacement,--}}
                                    {{--'_token': '{{ csrf_token() }}'--}}
                                {{--}--}}
                            {{--}).done(function (data) {--}}
                                {{--if(data.status == 0){--}}
                                    {{--var rowNo = repl.rows().count();--}}
                                    {{--$.each(data.data,function (key,value) {--}}
                                        {{--var inp = "<input class='form-control' name='weight["+value.id+"]' placeholder='Enter Weight'>";--}}
                                        {{--// console.log(value.tracking_number)--}}
                                        {{--repl.row.add([rowNo+1,value.tracking_number,value.booking_type_id,inp]).node().id = value.id;--}}
                                        {{--repl.draw(false);--}}
                                        {{--shipment_id_list.push(value.id);--}}
                                    {{--});--}}

                                {{--}else{--}}
                                    {{--//toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                                {{--}--}}
                            {{--});--}}

                        {{--} else if (data.status == 3) {--}}
                            {{--toastr.success(data.success, 'Success!', {--}}
                                {{--positionClass: 'toast-bottom-center',--}}
                                {{--containerId: 'toast-bottom-center'--}}
                            {{--});--}}

                            {{--$('#TryBuyModal').modal('show');--}}
                            {{--// checkShipmentStatuses();--}}


                            {{--trybuy = $('#trybuytable').DataTable({--}}
                                {{--dom: 'ltipr',--}}
                                {{--fixedHeader: {--}}
                                    {{--header: true,--}}
                                    {{--headerOffset: $('.header-navbar').height()--}}
                                {{--},--}}
                                {{--lengthMenu: [[25, 50, 100], [25, 50, 100]],--}}
                                {{--pageLength: 25,--}}
                                {{--stateSave: true,--}}
                                {{--pagingType: 'full_numbers',--}}

                                {{--columns: [--}}
                                    {{--{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},--}}
                                    {{--{name: 'product_type', class: 'align-middle product_type'},--}}
                                    {{--{name: 'product_description', class: 'align-middle product_description'},--}}
                                    {{--{name: 'item_price', class: 'align-middle item_price'},--}}
                                    {{--{name: 'receiving', class: 'align-middle receiving'},--}}

                                {{--],--}}
                                {{--rowCallback: function(row, data, index) {--}}
                                    {{--var info = trybuy.page.info();--}}

                                    {{--$('td:eq(0)', row).html(index + 1 + info.page * info.length);--}}
                                    {{--if ($.inArray(data.id, selected_rows) !== -1) {--}}
                                        {{--trybuy.row(row).select();--}}
                                    {{--}--}}
                                {{--}--}}
                            {{--});--}}
                            {{--$.ajax({--}}
                                {{--url:'{!! route('admin.delivery.receive.trybuys') !!}',--}}
                                {{--type:'POST',--}}
                                {{--dataType:'json',--}}
                                {{--data: {--}}
                                    {{--'trybuy':data.try,--}}
                                    {{--'_token': '{{ csrf_token() }}'--}}
                                {{--}--}}
                            {{--}).done(function (data) {--}}

                                {{--if(data.status == 0){--}}
                                    {{--var rowNo = trybuy.rows().count();--}}
                                    {{--$.each(data.data,function (key,value) {--}}
                                        {{--trybuy_ids.push(value.pid);--}}
                                        {{--var inp = "<input type='checkbox' checked class='form-control bought' name='bought["+value.pid+"]'>";--}}
                                        {{--// console.log(value.tracking_number)--}}
                                        {{--trybuy.row.add([rowNo+1,value.type,value.description,value.price,inp]).node().id = value.pid;--}}
                                        {{--trybuy.draw(false);--}}
                                        {{--// shipment_id_list.push(value.id);--}}
                                        {{--$('#cod').text(data.total_cod);--}}
                                    {{--});--}}


                                {{--}else{--}}
                                    {{--//toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                                {{--}--}}
                            {{--});--}}


                        {{--} else if (data.status == 0) {--}}
                            {{--console.log(data.error);--}}
                            {{--console.log(shipments_count);--}}

                        {{--}--}}
                    {{--}--}}
                {{--});--}}
            {{--}--}}
            {{--checkShipmentStatuses();--}}



        });
    </script>
@endsection