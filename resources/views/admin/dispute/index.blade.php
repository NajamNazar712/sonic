
@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
        Disputes
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Dispute No.</th>
                        <th class="border-primary border-darken-1">Dispute Date/Time</th>
                        <th class="border-primary border-darken-1">Description</th>
                        <th class="border-primary border-darken-1">Originated At</th>
                        <th class="border-primary border-darken-1">Dispute Type</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">Launched By</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!--Dispute Modal -->
    <div class="modal fade text-left" id="DisputeModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DisputeModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Launch Dispute</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="dispute_form" action="" method="post">

                        <div class="row mb-2">
                            <div class="col-12 form-group">
                                <select name="city_select" id="city_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    <option></option>
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <select name="dispute_type_select" id="dispute_type_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    <option></option>
                                @foreach($dispute_types as $dispute)
                                        <option value="{{$dispute->id}}">{{$dispute->type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <input name="tracking_number" id="tracking_number" class="tracking_number" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">

                            </div>
                        </div>
                        <div class="row mb-2 justify-content-center">
                            <div class="col-12 form-group">
                                <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter Description" data-rule-required="true" data-msg-required="This field is required"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="DisputeCreate" type="submit" class="btn btn-primary btn-block">Launch Dispute</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Dispute Modal -->
    {{--shipments modal--}}
    <div class="modal fade text-left" id="ShipmentsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShipmentsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Dispute Shipments List</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center dispute_shipments">

                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{--shipments modal--}}
    <!--Dispute Update Modal -->
    <div class="modal fade text-left" id="DisputeUpdateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DisputeUpdateModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Dispute</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center update_dispute_body">

                </div>
            </div>
        </div>
    </div>
    <!--Dispute Update Modal -->
    {{--resolve modal--}}
    <div class="modal fade text-left" id="ResolveModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ResolveModal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Resolve Dispute</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                        <h2>Are You Sure?</h2>
                </div>
                <div class="modal-footer text-center">
                <input type="hidden" id="disputeId">
                    <div class="row">

                            <button type="button" class="btn btn-danger btn-min-width mr-1 mb-1 dispute-resolve">Yes</button>


                            <button type="button" class="btn btn-primary btn-min-width mr-1 mb-1" data-dismiss="modal">No</button>

                    </div>

                </div>

            </div>
        </div>
    </div>

    {{--resolve modal--}}

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
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
        .dispute_comments_section{
            max-height: 200px;
            overflow-y:scroll;
            overflow-x:hidden;
            /*overflow:hidden;*/
            /*position: absolute;*/
            padding: 10px;
        }

        p.comment{
            text-align: left;
            -ms-word-wrap: break-word;
            word-wrap: break-word;
        }
        .description-div .border{
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .description-div{
            text-align: left;
        }
        .description-div p{
            padding:10px;
            word-wrap: break-word;
        }
        .comment-post{
            padding-top: 10px;
        }
        .comment-date{
            float:right;
            font-size: 13px;
            border-bottom: 1px solid #606060;
        }
        .selectize-control {
            width: 100%;
        }

        .selectize-control .selectize-input {
            vertical-align: middle;
        }

        .selectize-control .selectize-input .item {
            word-break: break-all;
        }
        .comment-row{
            padding: 10px;
            border: 1px #cccccc;
            border-radius:4px;
            background: #64a0d20d;
            margin-bottom: 5px;
        }
        td.align-middle.description {
            word-break: break-word;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    {{--<script src="{{asset('app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>--}}


    <script type="text/javascript">
        $(document).ready(function () {
        $('#city_select').select2({
            placeholder:'Select a city',
            dropdownParent:$('#dispute_form')
        });
        $('#dispute_type_select').select2({
            placeholder:'Select a Dispute type',
            dropdownParent:$('#dispute_form')
        });
            var select = $('#tracking_number').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 12 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                }
            });

        var table = $('#datatable').DataTable({
            @if (session('role_id') == 1 || in_array(2, session('permissions')))
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    text: 'Launch Dispute',
                    className: 'btn btn-primary dispute_modal',
                    enabled: true,
                    action: function (e, dt, node, config) {
                        $('#DisputeModal').modal('show');
                    }
                }],
            @else
                dom: 'ltipr',
            @endif
            lengthMenu: [[25, 50, 100], [25, 50, 100]],
            pageLength: 25,
            stateSave: true,
            pagingType: 'full_numbers',
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.dispute.list') }}',
            rowId: 'dispute_id',
            order: [[1, 'asc']],
            columns: [
                {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                {data: 'dispute_id', name: 'disputes.id', class: 'align-middle dispute_id'},
                {data: 'created_at', name: 'disputes.created_at', class: 'align-middle created_at'},
                {data: 'description', name: 'disputes.description', class: 'align-middle description'},
                {data: 'originated_at', name: 'cities.name', class: 'align-middle originated_at'},
                {data: 'dispute_type', name: 'dt.type', class: 'align-middle dispute_type'},
                {data: 'no_of_shipments', name: 'disputes.shipments_count', class: 'align-middle mode'},
                {data: 'launched_by', name: 'launched_by', class: 'align-middle launched_by',orderable: false, searchable: false},
                {data: 'updated_by', name: 'au.name', class: 'align-middle updated_by'},
                {data: 'status', name: 'status', class: 'align-middle status'},
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


                    if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.launched_by')) {
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
        // $('.dispute_modal').on('click',function () {
        //
        // });

        var max_char = 190;
        $('#description').on('keypress copy paste',function (e) {
            // var comment = $(this).val();
            // console.log(comment)
            if ($(this).val().length == max_char) {
                e.preventDefault();
            } else if ($(this).val().length > max_char) {
                // Maximum exceeded
                this.value = this.value.substring(0, max_char);
            }
        });
        $('body').on('change','#update_dispute_form input',function() {
            $(this).val($(this).val().trim());
        });
        $('body').on('change','#dispute_form textarea',function() {
            $(this).val($(this).val().trim());
        });
        $('#DisputeModal').on('hidden.bs.modal',function (e) {
            $('#dispute_form')[0].reset();
            $('#DisputeCreate').removeAttr('disabled');
            select[0].selectize.clear();
            $('#city_select').val('').trigger('change');
            $('#dispute_type_select').val('').trigger('change');
        });
        $('#DisputeUpdateModal').on('hidden.bs.modal',function (e) {
            table.draw('false');
        });
        $('#dispute_form').on('submit',function (e) {
            e.preventDefault();
        });
        $( "#dispute_form" ).validate({
            ignore: [],
            errorClass:"danger",
            errorPlacement: function(error, element) {
                error.addClass('w-100').appendTo(element.parents('.form-group'));
            },
            submitHandler: function(form) {

                $(form).find('button[type=submit]').attr('disabled', 'disabled');

                var city_select = $('#city_select').val();
                var dispute_type_select = $('#dispute_type_select').val();
                var tracking_number = $('#tracking_number').val();
                var description = $('#description').val();
                $.ajax({
                    url: '{!! route('admin.dispute.create') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'city_select': city_select,
                        'dispute_type_select':dispute_type_select,
                        'tracking_number':tracking_number,
                        'description':description
                    }
                }).done(function(data){
                    $('#DisputeModal').modal('hide');
                    if (data.invalid !== undefined) {

                        var message = 'Invalid Tracking Number(s): ' + data.invalid.join(', ');

                        toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }

                    if(data.success != undefined){
                        table.draw('false');
                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });

                }


        });
        $('body').on('click','.shipment_count',function () {
            var dispute_id = parseInt($(this).parents('tr').attr('id'));
            if(dispute_id != ''){
                $.ajax({
                    url: '{!! route('admin.dispute.get.shipments') !!}',
                    method: 'POST',
                    data: {
                        'id': dispute_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 1){
                        // console.log(data.shipments);
                        var shipment = '';
                        var i = 1;
                        $.each(data.shipments,function (key,value) {
                            shipment += "<span class='mb-1 block'><b>"+i+':'+"</b>&emsp;<u>"+value.tracking_number+"</u></span>";
                            i++;
                        });
                        $('#ShipmentsModal').modal('show');

                        $('.modal-body.dispute_shipments').html(shipment);
                        // var shipment = "<p></p>";
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                })
            }
        });
        $('body').on('click','.resolve',function () {
            var disputeId = parseInt($(this).parents('tr').attr('id'));
            if(disputeId){
                swal({
                    title: 'Are You Sure?',
                    text: 'Select Yes to resolve this dispute!',
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
                        $.ajax({
                            url: '{!! route('admin.dispute.resolve') !!}',
                            method: 'POST',
                            data: {
                                'id': disputeId,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            // $('#ResolveModal').modal('hide');
                            if(data.status === 1){
                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                table.draw('false');
                            }else{
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                            }
                        });
                    }
                });
            }
           // $('#ResolveModal').modal('show');
           // $('#disputeId').val(disputeId);
        });
        // $('body').on('click','.dispute-resolve',function () {
        //     var resolve_id = $('#disputeId').val();
        //     // console.log(resolve_id);
        //     if(resolve_id !== '') {
        //
        //     }
        // });
        $('body').on('click','.update',function(){
            var disputeId = parseInt($(this).parents('tr').attr('id'));
            // console.log(disputeId)
            if(disputeId !== ''){
                $.ajax({
                    url: '{!! route('admin.dispute.update') !!}',
                    method: 'POST',
                    data: {
                        'id': disputeId,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {

                    if(data.status === 1){
                        $('.update_dispute_body').html(data.view);
                        $('#DisputeUpdateModal').modal('show');

                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                    }
                });
            }
        });

    });

    </script>
@endsection