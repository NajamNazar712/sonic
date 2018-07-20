
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Packaging Material Stock
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="container justify-content-center pb-2 text-center">
                    <div class="row">
                        <div class="col-3"><h4>Small Flyers: <u id="sm_flyers_title">{{$packaging->small_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Medium Flyers: <u id="md_flyers_title">{{$packaging->medium_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Large Flyers: <u id="lg_flyers_title">{{$packaging->large_flyers}}</u></h4></div>
                        <div class="col-3"><h4>Boxes: <u id="box_title">{{$packaging->boxes}}</u></h4></div>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Reference No.</th>
                        <th class="border-primary border-darken-1">Entry Type</th>
                        <th class="border-primary border-darken-1">Entered Date/Time</th>
                        <th class="border-primary border-darken-1">Entered By</th>
                        <th class="border-primary border-darken-1">Small Flyers</th>
                        <th class="border-primary border-darken-1">Medium Flyers</th>
                        <th class="border-primary border-darken-1">Large Flyers</th>
                        <th class="border-primary border-darken-1">Boxes</th>
                        <th class="border-primary border-darken-1">Hub</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{--Add Stock Modal--}}
    <div class="modal fade text-left" id="AddStockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddStockModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_stock_form" action="{{route('admin.packaging.add.submit')}}" method="post">
                        @method('POST')
                        @csrf
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-4 form-group">
                                <input type="text" name="invoice_number" id="add_stock_invoice" class="form-control" placeholder="Invoice Number *" data-rule-required="true" data-msg-required="This field is required">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <input type="text" name="add_stock_smflyer" id="add_stock_smflyer" class="form-control numeric flyer" placeholder="Small Flyers">
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" name="add_stock_mdflyer" id="add_stock_mdflyer" class="form-control numeric flyer" placeholder="Medium Flyers">
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" name="add_stock_lgflyer" id="add_stock_lgflyer" class="form-control numeric flyer" placeholder="Large Flyers">
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" name="add_stock_boxes" id="add_stock_boxes" class="form-control numeric flyer" placeholder="Boxes">
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="AddNewStock" type="submit" class="btn btn-primary btn-block">Add Stock</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Add Stock Modal--}}
    {{--Send Stock Modal--}}
    <div class="modal fade text-left" id="SendStockModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SendStockModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Send Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="send_stock_form" action="{{route('admin.packaging.send.submit')}}" method="post">
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-4 form-group">
                                    <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-4 form-group">
                                    <input type="text" name="invoice_number" id="send_stock_invoice" class="form-control" placeholder="Cargo Number *" data-rule-required="true" data-msg-required="This field is required">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_smflyer" id="send_stock_smflyer" class="form-control small_flyer flyer" placeholder="Small Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_mdflyer" id="send_stock_mdflyer" class="form-control medium_flyer flyer" placeholder="Medium Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_lgflyer" id="send_stock_lgflyer" class="form-control large_flyer flyer" placeholder="Large Flyers">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="text" name="send_stock_boxes" id="send_stock_boxes" class="form-control box_flyer flyer" placeholder="Boxes">
                                </div>

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="SendNewStock" type="submit" class="btn btn-primary btn-block">Send Stock</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--Send Stock Modal--}}

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
    {{--<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>--}}
    {{--<script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>--}}



    <script type="text/javascript">
        $(document).ready(function () {
            var small_flyer = parseInt($('#sm_flyers_title').text());
            var medium_flyer = parseInt($('#md_flyers_title').text());
            var large_flyer = parseInt($('#lg_flyers_title').text());
            var box_flyer = parseInt($('#box_title').text());
            $('.small_flyer').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': small_flyer
            });
            $('.medium_flyer').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': medium_flyer
            });
            $('.large_flyer').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': large_flyer
            });
            $('.box_flyer').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': box_flyer
            });
            $('.numeric').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'min': 1,
                'max': 10000
            });

            var table = $('#datatable').DataTable({
                // "scrollX": true,
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: 'Add Stock',
                        className: 'btn btn-primary add_stock',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#AddStockModal').modal('show');

                        }
                    },
                    {
                        text:'Send Stock',
                        className: 'btn btn-primary send_stock',
                        enabled:true,
                        action: function(e, dt, node, config){
                            $('#SendStockModal').modal('show');

                        }
                    }
                ],
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
                ajax: '{{ route('admin.packaging.list') }}',
                rowId: 'psh_id',
                order: [[3, 'asc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'reference_number', name: 'packaging_stock_histories.reference_number', class: 'align-middle reference_number'},
                    {data: 'entry_type', name: 'packaging_stock_histories.entry_type', class: 'align-middle entry_type'},
                    {data: 'created_at', name: 'packaging_stock_histories.created_at', class: 'align-middle created_at'},
                    {data: 'admin', name: 'ad.name', class: 'align-middle admin'},
                    {data: 'small_flyers', name: 'packaging_stock_histories.small_flyers', class: 'align-middle small_flyers'},
                    {data: 'medium_flyers', name: 'packaging_stock_histories.medium_flyers', class: 'align-middle medium_flyers'},
                    {data: 'large_flyers', name: 'packaging_stock_histories.large_flyers', class: 'align-middle large_flyers'},
                    {data: 'boxes', name: 'packaging_stock_histories.boxes', class: 'align-middle boxes'},
                    {data: 'hub', name: 'cities.name', class: 'align-middle hub'}

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


                        if ($(header).is('.serial_number')) {
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

            $('body').on('change','#add_stock_form input',function() {
                $(this).val($(this).val().trim());
            });
            $('body').on('change','#send_stock_form input',function() {
                $(this).val($(this).val().trim());
            });
            $('#AddStockModal').on('hidden.bs.modal',function () {
                $('#add_stock_form')[0].reset();
            });
            $('#SendStockModal').on('shown.bs.modal',function () {
                if(!$('#city_select').hasClass('select2-hidden-accessible')){
                    $('#city_select').select2({
                        placeholder:'Send To',
                        dropdownParent:$('#send_stock_form')
                    });
                }

                $.ajax({
                    url: '{!! route('admin.packaging.fetch.cities') !!}',
                    method: 'GET'
                }).done(function (data) {
                    if(data.status === 1){
                        var newOption = new Option('', '', false, false);
                        $('#city_select').append(newOption).trigger('select');
                        $.each(data.cities,function(key,value){
                            var newOption = new Option(value.name, value.id, false, false);
                            $('#city_select').append(newOption).trigger('select');
                        });
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });
            });
            $('#SendStockModal').on('hidden.bs.modal',function () {
                $('#send_stock_form')[0].reset();
                $('#city_select').empty().trigger('change');
                $('#city_select').val('').trigger('change');
            });
            $( "#add_stock_form" ).validate({
                rules: {
                    add_stock_sm_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    add_stock_md_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    add_stock_lg_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    add_stock_boxes: {
                        require_from_group: [1, ".flyer"]
                    }
                },
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    form.submit();

                }


            });

            $( "#send_stock_form" ).validate({
                rules: {
                    send_stock_sm_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_md_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_lg_flyers: {
                        require_from_group: [1, ".flyer"]
                    },
                    send_stock_boxes: {
                        require_from_group: [1, ".flyer"]
                    }
                },
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {

                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    form.submit();

                }


            });

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
            {{--$('body').on('click','.resolve',function () {--}}
                {{--var disputeId = parseInt($(this).parents('tr').attr('id'));--}}
                {{--$('#ResolveModal').modal('show');--}}
                {{--$('#disputeId').val(disputeId);--}}
            {{--});--}}
            {{--$('body').on('click','.dispute-resolve',function () {--}}
                {{--var resolve_id = $('#disputeId').val();--}}
                {{--// console.log(resolve_id);--}}
                {{--if(resolve_id !== '') {--}}
                    {{--$.ajax({--}}
                        {{--url: '{!! route('admin.dispute.resolve') !!}',--}}
                        {{--method: 'POST',--}}
                        {{--data: {--}}
                            {{--'id': resolve_id,--}}
                            {{--'_token': '{{ csrf_token() }}'--}}
                        {{--}--}}
                    {{--}).done(function (data) {--}}
                        {{--$('#ResolveModal').modal('hide');--}}
                        {{--if(data.status === 1){--}}
                            {{--toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
                            {{--table.ajax.reload();--}}
                        {{--}else{--}}
                            {{--toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

                        {{--}--}}
                    {{--});--}}
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