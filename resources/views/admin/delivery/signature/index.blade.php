@extends('admin.layout.master')

@section('title', 'Delivery Consignee Signature')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <h1 class="mb-1">
                    Delivery Consignee Signature
                </h1>
            </div>
            <div class="content-body">
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                        @include('admin.inc.messages')
                            <div class="row justify-content-center mb-1">
                                <div class="col-3 text-center">
                                    <form id="tracking_number_search_form"
                                          class="form" novalidate="novalidate">
                                        <div class="form-group">
                                            <input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-3 text-center">
                                    <form id="delivery_note_id_form"
                                          class="form" novalidate="novalidate">
                                        <div class="form-group">
                                            <input type="text" name="delivery_note_ids" class="delivery_note_ids" placeholder="Delivery Note ID(s)*" data-tags-input-name="delivery_note_ids" data-rule-required="true" data-msg-required="Delivery Note ID(s) is required">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col text-center mb-1">
                                <button type="button" id="search_filter_btn"
                                        class="btn btn-outline-primary"><i
                                            class="la la-search"></i> Search
                                </button>
                            </div>
                            <div class="col">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Area</th>
                                        <th class="border-primary border-darken-1">Delivery Note ID</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Signature</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="picture_modal" data-backdrop="static" role="dialog" aria-labelledby="picture_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="picture_modal_title">Picture</h4>

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {


            $('#tracking_number_search_form #tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });

            $('#delivery_note_id_form #delivery_note_ids').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });



            table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                lengthMenu: [[10, 50, 100, 500, 1000, -1], [10, 50, 100, 500, 1000, 'All']],
                pageLength: 10,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.signature.list') }}',
                    //url: tableUrl,
                    data: function (d) {
                        d.tracking_numbers = $('#tracking_number_search_form .tracking_numbers').val();
                        d.search_delivery_note_ids = $('#delivery_note_id_form .delivery_note_ids').val();
                    }
                },
                rowId: 'delivery_note_id',
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'area' ,name: 'ca.name', class: 'align-middle text-center area'},
                    { data:'delivery_note' ,name: 'rider_deliveries.delivery_note_id', class: 'align-middle text-center delivery_note'},
                    {data: 'tracking_number', name: 's.tracking_number', class: 'align-middle tracking_number text-center'},
                    {data: 'picture_path', name: 'rider_deliveries.picture_path', class: 'align-middle picture_path text-center', orderable: false, searchable: false},
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

                        if ($(header).is('.serial_number') || $(header).is('.picture_path')) {
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

                    this.api().table().columns.adjust();
                }
            });

            var select = $('#tracking_number_search_form .tracking_numbers').selectize({
                placeholder: 'Tracking Number(s)',
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
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });

            var payment_select = $('#delivery_note_id_form .delivery_note_ids').selectize({
                placeholder: 'Delivery Note ID(s)',
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
                        payment_select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });


            $('#search_filter_btn').on('click',function () {
                table.draw();
            });



            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click','tr td.picture_path button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#picture_modal .modal-body').html(image);

                $('#picture_modal').modal('show');
            });

            $('#datatable tbody').on('click','tr td.signature_via_app button',function () {
                var link = $(this).attr('data-link');

                var image = '<img src="' + link + '" style="width: 100%; max-width: 200px;" />';

                $('#signature_modal .modal-body').html(image);

                $('#signature_modal').modal('show');
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

            $('body').on('click','.printdeliverynote',function () {
                var deliverynote = $(this).parents('tr').attr('id');
                // console.log(deliverynote);
                print(deliverynote);
            });
            function printDNCC(id) {
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

            $('body').on('click','.printDNCC',function () {
                var note_id = $(this).parents('tr').attr('id');
                printDNCC(note_id);
            });
        });
    </script>
@endsection