@extends('admin.layout.master')

@section('title', 'Supply Chain Shipment On Hold')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Supply Chain Shipment On Hold
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="form-group">
                                    <input type="text" name="tracking_numbers" class="form-control tracking_number" placeholder="Tracking Number*" data-rule-required="true" data-msg-required="Tracking Number is required">
                                </div>

                                <div class="form-group ml-1">
                                    <button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
                                </div>
                            </form>


                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Tracking No.</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Consignee Name</th>
                                    <th class="border-primary border-darken-1">Phone</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Collection Amount</th>
                                    <th class="border-primary border-darken-1">Service Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1"></th>
                                </tr>
                                </thead>
                            </table>

                            @if (session('role_id') == 1 || in_array(135, session('permissions')))
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <form id="change_weight_form" class="form mb-1 justify-content-center mt-2 d-none" method="POST" action="{{ route('admin.finance.change_shipment_weight.store') }}" novalidate="novalidate">
                                            {{ csrf_field() }}

                                            <input type="hidden" name="shipment_id" class="shipment_id">

                                            <div class="form-group text-center">
                                                <button type="submit" name="on_hold" class="btn btn-primary change" value="on_hold">On-Hold</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        table.table.table-sm td {
            border: 1px solid #626E82 !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#search_form input.tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            var table = $('#datatable').DataTable({
                dom: 'ltipr',
                scrollX: true,
                paging:false,
                autoWidth: false,
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number'},
                    {name: 'tracking_number', class: 'align-middle tracking_number', orderable: false},
                    {name: 'destination', class: 'align-middle destination', orderable: false},
                    {name: 'consignee_name', class: 'align-middle consignee_name', orderable: false},
                    {name: 'phone', class: 'align-middle phone', orderable: false},
                    {name: 'address', class: 'align-middle address', orderable: false},
                    {name: 'amount', class: 'align-middle amount', orderable: false},
                    {name: 'service_type', class: 'align-middle service_type', orderable: false},
                    {name: 'status', class: 'align-middle status', orderable: false},
                    {name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    $(form).find('button.search').prop('disabled', true);

                    $('#shipment').html('');

                    @if (session('role_id') == 1 || in_array(135, session('permissions')))
                        $('#change_weight_form').addClass('d-none');

                        $('#change_weight_form input.tracking_number').val('');
                    @endif

                    var tracking_number = $(form).find('input.tracking_number').val();

                    form.reset();

                    $.ajax({
                        url: '{!! route('admin.cargo.supply_chain.shipment_on_hold.shipment_details') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'tracking_number': tracking_number
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {

                                var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-danger deliverynoterow"><i class="la la-close"></i></a>';
                                var row = table.row.add([rowNo,data.tracking_number,data.destination,data.consignee_name,data.phone,data.address,data.amount,data.service_type,data.shipment_status,remove]).node().id = data.shId;
                                table.draw(false);

                                $(form).find('button.search').prop('disabled', false);

                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                            }
                            else {
                                $(form).find('button.search').prop('disabled', false);

                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        });

                    return false;
                }
            });

            @if (session('role_id') == 1 || in_array(135, session('permissions')))
                $('#change_weight_form').validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    }
                });
            @endif
        });
    </script>
@endsection