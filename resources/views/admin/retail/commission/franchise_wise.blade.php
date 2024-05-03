@extends('admin.layout.master')

@section('title', 'Franchise Commission')

@section('content')
    <h1 class="mb-1">Franchise Commission</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div id="search_form" class="row p-1 mb-2">
                        <div class="col">
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

                        <div class="col">
                            <select name="" id="" class="form-control select2">
                                @foreach ($franchises as $franchise)
                                    <option value="{{ $franchise->id }}">{{ $franchise->category_id }}</option>
                                @endforeach
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
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Total Charges without GST</th>
                                        <th class="border-primary border-darken-1">GST amount</th>
                                        <th class="border-primary border-darken-1">Total amount</th>
                                        <th class="border-primary border-darken-1">Weight Charges</th>
                                        <th class="border-primary border-darken-1">Fuel Surcharge</th>
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
        $(document).ready(function() {
            $('#search_filter_btn').on('click', function() {
                var selectedMonth = $('#month').val();
                $.ajax({
                    url: "{{ route('admin.retail.franchise.commission.list') }}",
                    method: 'GET',
                    data: { month: selectedMonth },
                    success: function(response) {
                        console.log(response.data);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>
    

@endsection