
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Same-Day Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Consignee Address</th>
                        <th class="border-primary border-darken-1">Product Type</th>
                        <th class="border-primary border-darken-1">Same-day Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Booked Date</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Dispatched Time</th>
                        <th class="border-primary border-darken-1">Delivered Time</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">TAT</th>
                        <th class="border-primary border-darken-1">Time Remining</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
{{--    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">--}}
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    {{--    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/ui/perfect-scrollbar.min.css')}}">--}}



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
{{--    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>--}}
{{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
{{--    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>--}}
    {{--<script src="{{asset('app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>--}}


    <script type="text/javascript">
        $(document).ready(function () {
            {{--$('#city_select').select2({--}}
                {{--placeholder:'Select a city',--}}
                {{--dropdownParent:$('#dispute_form')--}}
            {{--});--}}
            {{--$('#dispute_type_select').select2({--}}
                {{--placeholder:'Select a Dispute type',--}}
                {{--dropdownParent:$('#dispute_form')--}}
            {{--});--}}
            {{--var select = $('#tracking_number').selectize({--}}
                {{--placeholder: 'Tracking Number(s)*',--}}
                {{--delimiter: ',',--}}
                {{--createOnBlur: true,--}}
                {{--persist: false,--}}
                {{--plugins: ['remove_button'],--}}
                {{--onDropdownOpen: function(dropdown) {--}}
                    {{--dropdown.remove();--}}
                {{--},--}}
                {{--onType: function(str) {--}}
                    {{--var regex = /^[0-9,]+$/;--}}

                    {{--if (!regex.test(str)) {--}}
                        {{--select[0].selectize.setTextboxValue('');--}}
                    {{--}--}}
                {{--},--}}
                {{--create: function(input) {--}}
                    {{--if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {--}}
                        {{--return {--}}
                            {{--value: input,--}}
                            {{--text: input--}}
                        {{--}--}}
                    {{--}--}}
                    {{--else {--}}
                        {{--return false;--}}
                    {{--}--}}
                {{--}--}}
            {{--});--}}

            var table = $('#datatable').DataTable({
                "scrollX": true,
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
                ajax: '{{ route('admin.sameday.list') }}',
                rowId: 'shId',
                order: [[1, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone'},
                    {data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'product_name', name: 'p.product_name', class: 'align-middle product_name'},
                    {data: 'timing', name: 'sms.timing', class: 'align-middle timing'},
                    {data: 'current_status', name: 'sst.name', class: 'align-middle current_status'},
                    {data: 'booked_date', name: 'shipments.created_at', class: 'align-middle booked_date'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'dispatched_time', name: 'dispatched.created_at', class: 'align-middle dispatched_time'},
                    {data: 'delivered_status', name: 'delivered_status', class: 'align-middle delivered_status'},
                    {data: 'updated_by', name: 'updater.name', class: 'align-middle updated_by'},
                    {data: 'tat', name: 'tat', class: 'align-middle tat'},
                    {data: 'remaining_time', name: 'remaining_time', class: 'align-middle remaining_time'},
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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number')) {
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
            {{--// $('.dispute_modal').on('click',function () {--}}
            {{--//--}}
            {{--// });--}}

            {{--var max_char = 250;--}}
            {{--$('#description').keypress(function (e) {--}}
                {{--// var comment = $(this).val();--}}
                {{--// console.log(comment)--}}
                {{--if ($(this).val().length == max_char) {--}}
                    {{--e.preventDefault();--}}
                {{--} else if ($(this).val().length > max_char) {--}}
                    {{--// Maximum exceeded--}}
                    {{--this.value = this.value.substring(0, max_char);--}}
                {{--}--}}
            {{--});--}}
            {{--$('body').on('change','#update_dispute_form input',function() {--}}
                {{--$(this).val($(this).val().trim());--}}
            {{--});--}}
            {{--$('body').on('change','#dispute_form textarea',function() {--}}
                {{--$(this).val($(this).val().trim());--}}
            {{--});--}}
            {{--$('#DisputeModal').on('hidden.bs.modal',function (e) {--}}
                {{--$('#dispute_form')[0].reset();--}}
                {{--$('#DisputeCreate').removeAttr('disabled');--}}
                {{--select[0].selectize.clear();--}}
                {{--$('#city_select').val('').trigger('change');--}}
                {{--$('#dispute_type_select').val('').trigger('change');--}}
            {{--});--}}
            {{--$('#DisputeUpdateModal').on('hidden.bs.modal',function (e) {--}}
                {{--table.ajax.reload();--}}
            {{--});--}}
            {{--$('#dispute_form').on('submit',function (e) {--}}
                {{--e.preventDefault();--}}
            {{--});--}}
            {{--$( "#dispute_form" ).validate({--}}
                {{--ignore: [],--}}
                {{--errorClass:"danger",--}}
                {{--errorPlacement: function(error, element) {--}}
                    {{--error.addClass('w-100').appendTo(element.parents('.form-group'));--}}
                {{--},--}}
                {{--submitHandler: function(form) {--}}

                    {{--$(form).find('button[type=submit]').attr('disabled', 'disabled');--}}

                    {{--var city_select = $('#city_select').val();--}}
                    {{--var dispute_type_select = $('#dispute_type_select').val();--}}
                    {{--var tracking_number = $('#tracking_number').val();--}}
                    {{--var description = $('#description').val();--}}
                    {{--$.ajax({--}}
                        {{--url: '{!! route('admin.dispute.create') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                            {{--'_token': '{{ csrf_token() }}',--}}
                            {{--'city_select': city_select,--}}
                            {{--'dispute_type_select':dispute_type_select,--}}
                            {{--'tracking_number':tracking_number,--}}
                            {{--'description':description--}}
                        {{--}--}}
                    {{--}).done(function(data){--}}
                        {{--$('#DisputeModal').modal('hide');--}}
                        {{--if (data.invalid !== undefined) {--}}

                            {{--var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');--}}

                            {{--toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                        {{--}--}}

                        {{--if(data.success != undefined){--}}
                            {{--table.ajax.reload();--}}
                            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}--}}
                    {{--});--}}

                {{--}--}}


            {{--});--}}
            {{--$('body').on('click','.shipment_count',function () {--}}
                {{--var dispute_id = parseInt($(this).parents('tr').attr('id'));--}}
                {{--if(dispute_id != ''){--}}
                    {{--$.ajax({--}}
                        {{--url: '{!! route('admin.dispute.get.shipments') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                            {{--'id': dispute_id,--}}
                            {{--'_token': '{{ csrf_token() }}'--}}
                        {{--}--}}
                    {{--}).done(function (data) {--}}
                        {{--if(data.status == 1){--}}
                            {{--// console.log(data.shipments);--}}
                            {{--var shipment = '';--}}
                            {{--var i = 1;--}}
                            {{--$.each(data.shipments,function (key,value) {--}}
                                {{--shipment += "<span class='mb-1 block'><b>"+i+':'+"</b>&emsp;<u>"+value.tracking_number+"</u></span>";--}}
                                {{--i++;--}}
                            {{--});--}}
                            {{--$('#ShipmentsModal').modal('show');--}}

                            {{--$('.modal-body.dispute_shipments').html(shipment);--}}
                            {{--// var shipment = "<p></p>";--}}
                        {{--}else{--}}
                            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}--}}
                    {{--})--}}
                {{--}--}}
            {{--});--}}


            {{--$('body').on('click','.update',function(){--}}
                {{--var disputeId = parseInt($(this).parents('tr').attr('id'));--}}
                {{--// console.log(disputeId)--}}
                {{--if(disputeId !== ''){--}}
                    {{--$.ajax({--}}
                        {{--url: '{!! route('admin.dispute.update') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                            {{--'id': disputeId,--}}
                            {{--'_token': '{{ csrf_token() }}'--}}
                        {{--}--}}
                    {{--}).done(function (data) {--}}

                        {{--if(data.status === 1){--}}
                            {{--$('.update_dispute_body').html(data.view);--}}
                            {{--$('#DisputeUpdateModal').modal('show');--}}

                        {{--}else{--}}
                            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}--}}
                    {{--});--}}
                {{--}--}}
            {{--});--}}

        });

    </script>
@endsection