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
                                    <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-2">
                            <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard overflow-auto">
                            <table class="table table-stripped table-bordered datatable" id="datatable">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">Franchise Name</th>
                                        <th class="border-primary border-darken-1">Franchise Code</th>
                                        <th class="border-primary border-darken-1">Month</th>
                                        <th class="border-primary border-darken-1">Product</th>
                                        <th class="border-primary border-darken-1">Number of shipments</th>
                                        <th class="border-primary border-darken-1">Total Charges</th>
                                        <th class="border-primary border-darken-1">Product %</th>
                                        <th class="border-primary border-darken-1">Commission</th>
                                        <th class="border-primary border-darken-1">GST %</th>
                                        <th class="border-primary border-darken-1">GST Amount</th>
                                        <th class="border-primary border-darken-1">Total Commission</th>
                                        <th class="border-primary border-darken-1">Withholding %</th>
                                        <th class="border-primary border-darken-1">Franchise Withholding amount</th>
                                        <th class="border-primary border-darken-1">Charges minus withholding</th>
                                        <th class="border-primary border-darken-1">Deduction %</th>
                                        <th class="border-primary border-darken-1">Franchise Deduction amount</th>
                                        <th class="border-primary border-darken-1">Net Commission</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script>

        // franchise
        $('#franchise').select2({
                placeholder:'Select Franchise',
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
                cache: false,
                success: function(response) {
                    if (!response.shipments || response.shipments.length === 0) {
                        if (dataTable !== null) {
                            dataTable.clear().draw();
                        }
                        return;
                    }
                    var combinedData = [];
                    response.shipments.forEach(function(item) {
                        const dateString = item.shipment_month;
                        const date = new Date(dateString);
                        const monthNumber = date.toLocaleString('en-US', { month: '2-digit' });
                        const monthName = new Date(Date.UTC(1970, monthNumber - 1, 1)).toLocaleString('en-US', { month: 'long' });

                        var rowData = {
                            name: item.name,
                            code: item.code,
                            shipment_month: monthName,
                            shipping_mode_name: item.shipping_mode_name,
                            shipmentCounts: item.shipmentCounts,
                            total_charges_without_gst: item.total_charges_without_gst,
                            product_percentage: item.product_percentage,
                            commission: item.commission,
                            franchise_gst: item.franchise_gst,
                            gst: item.gst,
                            charges_with_gst: item.charges_with_gst,
                            franchise_withholding_amount: item.franchise_withholding_amount,
                            withholding: item.withholding,
                            charges_without_withholding: item.charges_without_withholding,
                            franchise_deduction_percentage: item.franchise_deduction_percentage,
                            deduction: item.deduction,
                            net_commission: item.net_commission,
                        };
                        combinedData.push(rowData);
                    });
                    if (dataTable !== null) {
                        dataTable.clear().rows.add(combinedData).draw();
                    } else {
                        dataTable = $('#datatable').DataTable({
                            data: combinedData,
                            searching: false,
                            columns: [
                                { data: 'name' },
                                { data: 'code' },
                                { data: 'shipment_month' },
                                { data: 'shipping_mode_name' },
                                { data: 'shipmentCounts' },
                                { data: 'total_charges_without_gst' },
                                { data: 'product_percentage' },
                                { data: 'commission' },
                                { data: 'franchise_gst' },
                                { data: 'gst' },
                                { data: 'charges_with_gst' },
                                { data: 'franchise_withholding_amount' },
                                { data: 'withholding' },
                                { data: 'charges_without_withholding' },
                                { data: 'franchise_deduction_percentage' },
                                { data: 'deduction' },
                                { data: 'net_commission' },
                            ],
                            scrollX: true,
                            scrollY: true,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    </script>

@endsection