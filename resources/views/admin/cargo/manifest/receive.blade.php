@extends('admin.layout.master')

@section('title', 'Quick Receive Bag(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Quick Receive Bag(s)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            @if(session('success_html'))
                                <div class="alert alert-success">
                                    {!! session('success_html') !!}
                                </div>
                            @endif
                            @if(session('misroute_html'))
                                <div class="alert alert-info">
                                    {!! session('misroute_html') !!}
                                </div>
                            @endif
                            @if(session('bag_short_received_error'))
                                <div class="alert alert-danger">
                                    {!! session('bag_short_received_error') !!}
                                </div>
                            @endif
                            @if(session('bag_not_exists_in_mapping_error'))
                                <div class="alert alert-danger">
                                    {!! session('bag_not_exists_in_mapping_error') !!}
                                </div>
                            @endif
                            @if(session('bag_not_exists_in_manifest_error'))
                                <div class="alert alert-danger">
                                    {!! session('bag_not_exists_in_manifest_error') !!}
                                </div>
                            @endif
                            @if(session('bag_not_exist_error'))
                                <div class="alert alert-danger">
                                    {!! session('bag_not_exist_error') !!}
                                </div>
                            @endif
                            @if(session('went_wrong_html'))
                                <div class="alert alert-danger">
                                    {!! session('went_wrong_html') !!}
                                </div>
                            @endif
                            <form id="add_bag_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div id="camera_scan" class="d-none">
                                    <div id="camera_view" class="camera_view"></div>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="bag_number" class="form-control bag_number" placeholder="Enter Bag Number*" data-rule-required="true" data-msg-required="Bag Number is required">

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
                                Bags Scanned <span class="scanned">0</span>/{{$total}} | Extra Scanned: <span class="extra_scanned">0</span>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag Number</th>
                                    <th class="border-primary border-darken-1">Manifest ID</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Last Junction</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            <form id="receive_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.cargo_manifest.receive.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <input type="hidden" name="bag_ids" class="bag_ids">

                                <div class="form-group ml-1">
                                    <button type="submit" name="receive" class="btn btn-primary receive" value="Confirm" disabled="disabled">Receive</button>
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
        let misroute_id = 9;
        $(document).ready(function() {
            @if(session('errors'))
            scan_sound(2);
            @endif

            var bag_ids = [];

            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                autoWidth: false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'bag_number', class: 'align-middle bag_number', orderable: false, searchable: false},
                    {name: 'manifest_id', class: 'align-middle manifest_id', orderable: false, searchable: false},
                    {name: 'origin', class: 'align-middle origin', orderable: false, searchable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false, searchable: false},
                    {name: 'last_junction', class: 'align-middle last_junction', orderable: false, searchable: false},
                    {name: 'actual_weight', class: 'align-middle actual_weight', orderable: false, searchable: false},
                    {name: 'shipping_mode', class: 'align-middle shipping_mode', orderable: false, searchable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false},
                    {name: 'misroute', class: 'misroute', orderable: false,searchable: false, visible: false}
                ],
                rowCallback: function(row, data, index) {

                    if(data[misroute_id] == 1)
                    {
                        $(row).addClass('alert-danger');
                    }
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
                    if (table.columns('.bag_number').data().eq(0).indexOf(bag_number) === -1) {
                        blockPagePermanently();
                        $.ajax({
                            url: '{!! route('admin.cargo_manifest.receive.bag_details') !!}',
                            method: 'POST',
                            data: {
                                'bag_number': bag_number,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                            .done(function(data) {
                                if (data.status == 0) {
                                    id = data.details.bag_id;

                                    var index = $.inArray(id, bag_ids);

                                    if (index === -1) {
                                        $action = "<button class='btn btn-danger btn-icon btn-sm remove_bag'><i class='la la-close'></i></button>";
                                        var rowNo = table.rows().count();
                                        table.row.add([rowNo + 1,data.details.bag_number,data.details.manifest_id,data.details.origin,data.details.destination,data.details.last_junction,data.details.actual_weight,data.details.shipping_mode,$action,data.details.misroute]).node().id = data.details.bag_id;
                                        table.draw(false);
                                        table.order([0, 'desc']).draw();
                                        scan_sound(1);
                                        bag_ids.push(data.details.bag_id);
                                        if(data.details.misroute == 0)
                                        {
                                            $('#information .scanned').html(parseInt($('#information .scanned').html()) + 1);

                                        }
                                        else{
                                            $('#information .extra_scanned').html(parseInt($('#information .extra_scanned').html()) + 1);
                                        }


                                        $('#add_bag_form button.add').prop('disabled', false);

                                        $('#receive_form .receive').prop('disabled', false);
                                        UnblockPagePermanently();
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    else {
                                        UnblockPagePermanently();
                                        $('#add_bag_form button.add').prop('disabled', false);
                                        scan_sound(2);
                                        toastr.error('Bag has been added already', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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

            $('#datatable tbody').on('click','tr td .remove_bag',function () {
                let id = parseInt($(this).parents('tr').attr('id'));
                index = bag_ids.indexOf(id);
                if (index > -1) {
                    bag_ids.splice(index, 1);
                }
                let misroute = table.row( $(this).parents('tr') ).data()[misroute_id];
                console.log(misroute);
                if(misroute == 1)
                {
                    $('#information .extra_scanned').html(parseInt($('#information .extra_scanned').html()) - 1);
                }
                else{
                    $('#information .scanned').html(parseInt($('#information .scanned').html()) - 1);
                }
                table.row( $(this).parents('tr') ).remove();
                table.draw(0);
                if(bag_ids.length < 1)
                {
                    $('#receive_form .receive').prop('disabled', true);
                }
            });

            $('#receive_form').bind('submit', function(e) {
                e.preventDefault();

                var form = this;

                $('#receive_form input.bag_ids').val(bag_ids);
                var html = 'Are you sure, you want to receive Bag(s)?';

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