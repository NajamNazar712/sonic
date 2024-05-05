@extends('admin.layout.master')

@section('title', 'Franchise Commission')

@section('content')
    <h1 class="mb-1">Franchise Commission</h1>

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
                        <div class="col-2">
                            <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">Franchise Name</th>
                                        <th class="border-primary border-darken-1">Total Charges Without GST</th>
                                        <th class="border-primary border-darken-1">Product %</th>
                                        <th class="border-primary border-darken-1">Commission</th>
                                        <th class="border-primary border-darken-1">Franchise GST %</th>
                                        <th class="border-primary border-darken-1">Franchise GST Amount</th>
                                        <th class="border-primary border-darken-1">Franchise Withholding %</th>
                                        <th class="border-primary border-darken-1">Franchise Withholding amount</th>
                                        <th class="border-primary border-darken-1">Charges without withholding</th>
                                        <th class="border-primary border-darken-1">Franchise Deduction %</th>
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
        var dataTable = null;
        $('#search_filter_btn').on('click', function() {
            var selectedMonth = $('#month').val();
            $.ajax({
                url: "{{ route('admin.retail.franchise.commission.list') }}",
                method: 'GET',
                data: { month: selectedMonth },
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
                        var rowData = {
                            name: item.name,
                            product_percentage: item.product_percentage,
                            total_charges_without_gst: item.total_charges_without_gst,
                            commission_percentage: item.commission_percentage,
                            franchise_gst: item.franchise_gst,
                            charegs_with_gst: item.charegs_with_gst,
                            franchise_withholding: item.franchise_withholding,
                            withholding: item.withholding,
                            charges_without_withholding: item.charges_without_withholding,
                            franchise_deduction: item.franchise_deduction,
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
                            columns: [
                                { data: 'name' },
                                { data: 'total_charges_without_gst' },
                                { data: 'product_percentage' },
                                { data: 'commission_percentage' },
                                { data: 'franchise_gst' },
                                { data: 'charegs_with_gst' },
                                { data: 'franchise_withholding' },
                                { data: 'withholding' },
                                { data: 'charges_without_withholding' },
                                { data: 'franchise_deduction' },
                                { data: 'deduction' },
                                { data: 'net_commission' },
                            ]
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