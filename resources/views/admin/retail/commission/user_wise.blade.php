@extends('admin.layout.master')

@section('title', 'User Commission')

@section('content')
    <h1 class="mb-1">User Commission</h1>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div id="search_form" class="row p-1">
                        <div class="col-4">
                            <select name="month" id="month" class="form-control select2">
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>

                        <div class="col-4">
                            <select name="franchise" id="franchise" class="select2 form-control">
                                <option value="" class="text-secondary">Select Franchise</option>
                                @foreach ($franchises as $franchise)
                                <option value="{{ $franchise->id }}">
                                    {{ $franchise->name }} {{ $franchise->trax_id ? ' - (' . $franchise->trax_id . ')' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-2">
                            <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard overflow-auto">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="width: 100%;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S.no</th>
                                        <th class="border-primary border-darken-1">Trax User Name</th>
                                        <th class="border-primary border-darken-1">Franchise Code</th>
                                        <th class="border-primary border-darken-1">CNIC</th>
                                        <th class="border-primary border-darken-1">Phone Number</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Month</th>
                                        <th class="border-primary border-darken-1">Product</th>
                                        <th class="border-primary border-darken-1">Number of shipments</th>
                                        <th class="border-primary border-darken-1">Total Charges</th>
                                        <th class="border-primary border-darken-1">Weight Charges</th>
                                        <th class="border-primary border-darken-1">GST Amount</th>
                                        <th class="border-primary border-darken-1">Product %</th>
                                        <th class="border-primary border-darken-1">Commission</th>
                                        <th class="border-primary border-darken-1">Payment Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="paymentConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentConfirmationModalLabel">Confirm Payment</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p id="franchiseName"></p>
                                    <p id="franchisePaymentDetails"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="confirmPaymentBtn">Confirm Payment</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $('#franchise').select2({
                placeholder:'Select Trax User',
                width:'100%',
                allowClear:true
            }).bind('select2:select', function () {

                if($(this).val().length != 0){
                    $('#search_form').find('button[type=button]').prop('disabled', false);
                }
            });

        var dataTable = null;
        $('#search_filter_btn').on('click', function() {
            var selectedMonth = $('#month').val();
            var franchise = $('#franchise').val();
            $.ajax({
                url: "{{ route('admin.retail.franchise.user_commission.list') }}",
                method: 'GET',
                data: { 
                    month: selectedMonth,
                    franchise: franchise
                },
                success: function(response) {

                    if (response.data === 0) {
                        if (dataTable !== null) {
                            dataTable.clear().draw();
                        }
                        return;
                    }
                    if (dataTable !== null) {
                        dataTable.clear().rows.add(response.data).draw();
                    } else {
                        dataTable = $('#datatable').DataTable({
                            dom: '<"d-inline-block"l><"pull-right"B>tipr',
                            buttons: [
                                @if ($allowed_users->contains('id', auth()->user()->id) || auth()->user()->role_id = 1)
                                    {
                                        text: 'Mark as paid',
                                        className: 'select_to_pay disabled',
                                        action: function () {
                                            var selectedFranchiseNames = [];
                                            $('#datatable > tbody > .selected').each(function(index){
                                                var franchiseCode = $(this).find('td:eq(1)').text().trim();
                                                if (franchiseCode) {
                                                    selectedFranchiseNames.push(franchiseCode);
                                                }
                                            });
                                            var franchiseCodes = selectedFranchiseNames.join(', ');

                                            if (franchiseCodes.length > 0) {
                                                $.ajax({
                                                    url: '{{ route('admin.retail.users.show_retail_commission') }}',
                                                    method: 'POST',
                                                    data: {
                                                        id: franchiseCodes,
                                                        _token: '{{ csrf_token() }}'
                                                    }
                                                }).then(function (response) {
                                                    var modalBody = $('#paymentConfirmationModal .modal-body');
                                                    var paymentTable = '<table class="table"><thead><tr><th>Trax Center Code</th><th>Retail User Name</th><th>Is Paid</th></tr></thead><tbody>';
                                                    $.each(response.data, function (index, item) {
                                                        var isPaidText = item.is_paid == 1 ? '<span style="color: green;">Paid</span>' : '<span style="color: red;">Not Paid</span>';
                                                        paymentTable += '<tr><td>' + item.franchise_code + '</td><td>' + item.trax_center_name + '</td><td>' + isPaidText + '</td></tr>';
                                                    });
                                                    paymentTable += '</tbody></table>';
                                                    modalBody.html(paymentTable);
                                                    $('#paymentConfirmationModal').modal('show');
                                                    $('#confirmPaymentBtn').off('click').on('click', function () {
                                                        $.ajax({
                                                            url: '{{ route('admin.retail.users.retail_commission_payment') }}',
                                                            method: 'POST',
                                                            data: {
                                                                id: franchiseCodes,
                                                                _token: '{{ csrf_token() }}'
                                                            }
                                                        }).then(function (paymentResponse) {
                                                            toastr.success('Payment has been made successfully.', '', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });

                                                            var table = $('#datatable').DataTable();
                                                            var selectedIds = franchiseCodes.split(',').map(id => id.trim());
                                                            selectedIds.forEach(id => {
                                                                var row = table.row(function (idx, data, node) {
                                                                    return data.id == id;
                                                                });

                                                                if (row && row.data()) {
                                                                    row.data().is_paid = 1;
                                                                    row.invalidate().draw(false);
                                                                }
                                                            });
                                                        }).catch(function (paymentError) {
                                                            if (paymentError.responseJSON && paymentError.responseJSON.error) {
                                                                if (paymentError.responseJSON.status === 1) {
                                                                    toastr.error('Selected data does not belong to the same user.', '', {
                                                                        positionClass: 'toast-top-center',
                                                                        containerId: 'toast-top-center'
                                                                    });
                                                                } else if (paymentError.responseJSON.status === 2) {
                                                                    toastr.error('Payment for ' + paymentError.responseJSON.error_data + ' franchise has already been made.', '', {
                                                                        positionClass: 'toast-top-center',
                                                                        containerId: 'toast-top-center'
                                                                    });
                                                                } else {
                                                                    toastr.error(paymentError.responseJSON.error, '', {
                                                                        positionClass: 'toast-top-center',
                                                                        containerId: 'toast-top-center'
                                                                    });
                                                                }
                                                            } else {
                                                                toastr.error('An unexpected error occurred during payment.', '', {
                                                                    positionClass: 'toast-top-center',
                                                                    containerId: 'toast-top-center'
                                                                });
                                                            }
                                                        });

                                                        $('#paymentConfirmationModal').modal('hide');
                                                    });
                                                }).catch(function (error) {
                                                    if (error.responseJSON && error.responseJSON.error) {
                                                        if (error.responseJSON.status === 1) {
                                                            toastr.error('Selected IDs do not belong to the same User.', '', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        } else {
                                                            toastr.error(error.responseJSON.error, '', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                    } else {
                                                        toastr.error('An unexpected error occurred.', '', {
                                                            positionClass: 'toast-top-center',
                                                            containerId: 'toast-top-center'
                                                        });
                                                    }
                                                });
                                            }
                                        }
                                    },
                                @endif
                                {
                                    extend: 'selectAll',
                                    text: 'Select All',
                                    className: 'select_all',
                                    action : function(e) {
                                        e.preventDefault();
                                        dataTable.rows().nodes().each(function(index) {
                                            var row = dataTable.row(index);
                                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                row.select();
                                                id = parseInt(row.id());
                                            }
                                        });
                                    }
                                },

                                {
                                    text: 'Select None',
                                    className: 'btn btn-secondary unselect_all disabled',
                                    action : function(e) {
                                        e.preventDefault();
                                        dataTable.rows().nodes().each(function(index) {
                                            var row = dataTable.row(index);
                                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                row.deselect();
                                            }
                                        });
                                    }
                                },

                                {
                                    text: 'Print Invoice',
                                    className: 'btn btn-primary print_invoice',
                                    action: function () {
                                        var selectedFranchiseNames = [];
                                        $('#datatable > tbody > .selected').each(function(index){
                                            var franchiseCode = $(this).find('td:eq(1)').text().trim();
                                            if (franchiseCode) {
                                                selectedFranchiseNames.push(franchiseCode);
                                            }
                                        });
                                        var franchiseCodes = selectedFranchiseNames.join(', ');
                                        if (franchiseCodes.length > 0) {
                                            $.ajax({
                                                url: '{{ route('admin.retail.users.user_commission_invoice_print') }}',
                                                method: 'POST',
                                                data: {
                                                    trax_users: franchiseCodes,
                                                    _token: '{{ csrf_token() }}'
                                                }
                                            }).then(function(response) {
                                                var newTab = window.open('', '_blank');
                                                newTab.document.write(response);
                                                newTab.document.close();
                                                newTab.onload = function() {
                                                    newTab.print();
                                                };
                                            });
                                        }
                                    }
                                }
                            ],

                            select: {
                                info: false,
                                style: 'multi',
                                selector: 'td.select-checkbox',
                                className: 'selected bg-primary bg-lighten-5 primary'
                            },

                            data: response.data,
                            searching: false,
                            columns: [
                                {
                                    data: '',
                                    defaultContent: '',
                                    orderable: false,
                                    searchable: false,
                                    class: 'text-center align-middle select select-checkbox',
                                    render: function (data, type, row) {
                                        return '<input type="checkbox" class="select-checkbox d-none" />';
                                    }
                                },
                                { data: 'id' },
                                { data: 'trax_center_name' },
                                { data: 'franchise_code' },
                                { data: 'trax_center_cnic' },
                                { data: 'trax_center_phone' },
                                { data: 'franchise_address' },
                                { data: 'month' },
                                { data: 'retail_shipping_mode_name' },
                                { data: 'number_of_shipments' },
                                { data: 'total_charges' },
                                { data: 'weight_charges' },
                                { data: 'franchise_gst_amount' },
                                { data: 'commission' },
                                { data: 'net_commission' },
                                {
                                    data: 'is_paid',
                                    render: function (data, type, row) {
                                        if (data == 1) {
                                            return '<span style="color: green;">Paid</span>';
                                        } else {
                                            return '<span style="color: red;">Not Paid</span>';
                                        }
                                    }
                                }
                            ],
                            scrollX: true,
                            scrollY: true,
                        });
                        dataTable.on('select.dt deselect.dt', function() {
                            var $unselectButton = $('.unselect_all');
                            if (dataTable.rows('.selected').count() > 0) {
                                $unselectButton.removeClass('disabled');
                            } else {
                                $unselectButton.addClass('disabled');
                            }
                        });
                        dataTable.on('select.dt deselect.dt', function() {
                            var $unselectButton = $('.select_to_pay');
                            if (dataTable.rows('.selected').count() > 0) {
                                $unselectButton.removeClass('disabled');
                            } else {
                                $unselectButton.addClass('disabled');
                            }
                        });
                    }
                }
            });
        });
    </script>

@endsection