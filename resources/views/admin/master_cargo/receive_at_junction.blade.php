@extends('admin.layout.master')

@section('title', 'Master Cargo Receive at Junction')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Master Cargo Receive at Junction
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div id="camera_scan" class="d-none">
                                <div id="camera_view" class="camera_view"></div>
                            </div>

                            <form id="add_bag_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="bag_number" class="form-control bag_number" placeholder="Bag Number*" data-rule-required="true" data-msg-required="Bag Number is required">

                                    <div class="d-inline-block ml-1">
                                        <a href="#" id="camera_scan_initiate" tabindex="-1">
                                            <i class="ft-camera h1"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="add" class="btn btn-primary" value="Add">Add</button>
                                </div>
                            </form>

                            <div id="information" class="information text-center">
                                Master Cargo No #{{ str_pad($cargo_id, 6, '0', STR_PAD_LEFT) }} | Scanned: <span class="scanned">0</span>/<span class="total">{{ $total }}</span>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                </tr>
                                </thead>
                            </table>

                            <form id="receive_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.master_cargo.in_transit.receive_at_link.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="cargo_consignment_id" class="cargo_consignment_id" value="{{ $cargo_id }}">
                                <input type="hidden" name="junction" class="junction" value="{{ $junction }}">

                                <input type="hidden" name="short_received" class="short_received">

                                <input type="hidden" name="bag_ids" class="bag_ids">

                                <div class="form-group ml-1">
                                    <button type="submit" name="receive" class="btn btn-primary receive" value="Confirm" disabled="disabled">Receive at Junction</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/quagga/quagga.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/custom.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            @if(session('errors'))
            scan_sound(2);
                    @endif
            var cargo_consignment_id = {{ $cargo_id }};

            var bag_ids = [];

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth: false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'bag_number', class: 'align-middle bag_number', orderable: false, searchable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false, searchable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false, searchable: false},
                    {name: 'actual_weight', class: 'align-middle actual_weight', orderable: false, searchable: false},
                    {name: 'shipping_mode', class: 'align-middle shipping_mode', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    // var info = table.page.info();
                    //
                    // $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#add_bag_form input.bag_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#add_bag_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $('#add_bag_form button.add').prop('disabled', true);

                    var bag_number = $(form).find('input.bag_number').val();

                    form.reset();

                    if (table.columns('.bag_number').data().eq(0).indexOf(parseInt(bag_number)) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.master_cargo.receive.bag_details') !!}',
                            method: 'POST',
                            data: {
                                'bag_number': bag_number,
                                'cargo_consignment_id': cargo_consignment_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 0) {
                                    id = data.details.id;

                                    var index = $.inArray(id, bag_ids);

                                    if (index === -1) {
                                        var rowNo = table.rows().count();

                                        table.row.add([rowNo + 1, data.details.bag_number, data.details.origin, data.details.destination, data.details.actual_weight, data.details.shipping_mode]).node().id = data.details.id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        bag_ids.push(data.details.id);

                                        $('#information .scanned').html(bag_ids.length);

                                        $('#add_bag_form button.add').prop('disabled', false);

                                        $('#receive_form .receive').prop('disabled', false);
                                        UnblockPagePermanently();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                }
                                else {
                                    UnblockPagePermanently();
                                    $('#add_bag_form button.add').prop('disabled', false);
                                    scan_sound(2);
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                    }
                    else {
                        UnblockPagePermanently();
                        $('#add_bag_form button.add').prop('disabled', false);
                        scan_sound(2);
                        toastr.error('Bag has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }

                    return false;
                }
            });

            $('#receive_form').bind('submit', function(e) {
                e.preventDefault();

                var form = this;

                $('#receive_form input.bag_ids').val(bag_ids);

                blockPagePermanently();
                $.ajax({
                    url: '{!! route('admin.master_cargo.receive.short_received') !!}',
                    method: 'POST',
                    data: {
                        'cargo_consignment_id': cargo_consignment_id,
                        'bag_ids': bag_ids,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if (data.status == 0) {
                            UnblockPagePermanently();
                            if (data.short_received) {
                                var html = 'There are shipments that are short received from Master Cargo No#' + cargo_consignment_id + ':<br/>';

                                $.each(data.short_received, function(index, bag_number) {
                                    html += bag_number + '<br/>';
                                });

                                html += 'Are you sure, you want to confirm this Master Cargo received at Junction?';

                                $('#receive_form input.short_received').val(1);
                            }
                            else {
                                var html = 'Are you sure, you want to confirm Master Cargo No#' + cargo_consignment_id + ' as received at Junction?';

                                $('#receive_form input.short_received').val(0);
                            }

                            content = document.createElement('div');
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
                                    form.submit();
                                }
                            });
                        }
                        else {
                            UnblockPagePermanently();
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            });

            $('#camera_scan_initiate').bind('click', function() {
                if ($('#camera_scan').hasClass('d-none')) {
                    $('#camera_scan').removeClass('d-none');

                    camera_scanning_start('#camera_view');
                }
                else {
                    $('#camera_scan').addClass('d-none');

                    camera_scanning_stop();
                }
            });
        });

        function camera_scan_detected(bag_number) {
            $('#add_bag_form input.bag_number').val(bag_number);

            $('#add_bag_form').trigger('submit');
        }
    </script>
@endsection