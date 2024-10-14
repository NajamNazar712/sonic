@extends('admin.layout.master')

@section('title', 'Book Excel City(s)')


@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <h1 class="mb-1">Errors In Book Excel City(s)</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            {!! Form::open(['method' => 'POST', 'route' => 'cod.shipment.book.excel_store']) !!}
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tbl">
                                    <thead>
                                    <tr>
                                        <th>City Name</th>
                                        <th>Is City</th>
                                        <th>Is Hub</th>
                                        <th>Select Hub</th>
                                        <th>Select Zone</th>
                                        <th>Add Attempt TAT</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Hub Latitude</th>
                                        <th>Hub Longitude</th>
                                        <th>Pickup</th>
                                        <th>Pickup Cut Off</th>

                                        <!-- Regular -->
                                        <th>Regular Rush</th>
                                        <th>Regular Saver Plus</th>
                                        <th>Regular Swift</th>
                                        <th>Regular Same-day</th>

                                        <!-- Replacement -->
                                        <th>Replacement Rush</th>
                                        <th>Replacement Saver Plus</th>
                                        <th>Replacement Swift</th>
                                        <th>Replacement Same-day</th>

                                        <!-- Try & Buy -->
                                        <th>Try & Buy Rush</th>
                                        <th>Try & Buy Saver Plus</th>
                                        <th>Try & Buy Swift</th>
                                        <th>Try & Buy Same-day</th>

                                        <!-- Reverse Pickup -->
                                        <th>Reverse Pickup Rush</th>
                                        <th>Reverse Pickup Saver Plus</th>
                                        <th>Reverse Pickup Swift</th>
                                        <th>Reverse Pickup Same-day</th>

                                        <!-- FTL -->
                                        <th>FTL Rush</th>
                                        <th>FTL Saver Plus</th>
                                        <th>FTL Swift</th>
                                        <th>FTL Same-day</th>

                                        <!-- Walk-In -->
                                        <th>Walk-In Rush</th>
                                        <th>Walk-In Saver Plus</th>
                                        <th>Walk-In Swift</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $index => $ro)
                                        <tr>
                                            <td>
                                                {!! Form::text($index . "[city_name]", $ro['city_name'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["city_name"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["city_name"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["city_name"]))
                                                    <span class="text-danger">{{ $errors[$index]["city_name"] }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                {!! Form::text($index . "[is_city]", $ro['is_city'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["is_hub"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["is_city"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["is_city"]))
                                                    <span class="text-danger">{{ $errors[$index]["is_city"] }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                {!! Form::text($index . "[is_hub]", $ro['is_hub'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["is_hub"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["is_hub"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["is_hub"]))
                                                    <span class="text-danger">{{ $errors[$index]["is_hub"] }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                {!! Form::select($index . "[select_hub]", $hubs, $ro['select_hub'] ?? '', [
                                                    'class' => 'form-control select_hub select2' . (isset($errors[$index]["select_hub"]) ? ' is-invalid' : ''),
                                                    'style' => 'width:80px','placeholder' => '',
                                                    'disabled' => $ro['is_hub'] == 1 ? 'disabled' : null // Disable if is_hub is 1
                                                ]) !!}

                                                @if ($ro['is_hub'] == 1 && isset($errors[$index]["select_hub"]))
                                                    {{-- No error message shown if is_hub is 1 since select is disabled --}}
                                                @elseif (isset($errors[$index]["select_hub"]))
                                                    <span class="text-danger">{{ $errors[$index]["select_hub"] }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                {!! Form::select($index . "[select_zone]", $zones, $ro['select_zone'] ?? '', [
                                                    'class' => 'form-control select_zone' . (isset($errors[$index]["select_zone"]) ? ' is-invalid' : ''),
                                                    'style' => 'width:80px','placeholder' => '',
                                                    'disabled' => $ro['is_city'] == 1 ? 'disabled' : null
                                                ]) !!}

                                                @if ($ro['is_city'] == 1 && isset($errors[$index]["select_zone"]))
                                                    {{-- No error message shown if is_city is 1 since select is disabled --}}
                                                @elseif (isset($errors[$index]["select_zone"]))
                                                    <span class="text-danger">{{ $errors[$index]["select_zone"] }}</span>
                                                @endif
                                            </td>


                                            <td>
                                                {!! Form::text($index . "[add_attempt_tat]", $ro['add_attempt_tat'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["add_attempt_tat"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["add_attempt_tat"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["add_attempt_tat"]))
                                                    <span class="text-danger">{{ $errors[$index]["add_attempt_tat"] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! Form::text($index . "[latitude]", $ro['latitude'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["latitude"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["latitude"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["latitude"]))
                                                    <span class="text-danger">{{ $errors[$index]["latitude"] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! Form::text($index . "[longitude]", $ro['longitude'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["longitude"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["longitude"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["longitude"]))
                                                    <span class="text-danger">{{ $errors[$index]["longitude"] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! Form::text($index . "[hub_latitude]", $ro['hub_latitude'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["hub_latitude"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["hub_latitude"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["hub_latitude"]))
                                                    <span class="text-danger">{{ $errors[$index]["hub_latitude"] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! Form::text($index . "[hub_longitude]", $ro['hub_longitude'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["hub_longitude"]) ? ' is-invalid' : ''),
                                                    'readonly' => !isset($errors[$index]["hub_longitude"]) ? 'readonly' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["hub_longitude"]))
                                                    <span class="text-danger">{{ $errors[$index]["hub_longitude"] }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                {!! Form::checkbox($index . "[pickup]", 1, isset($ro['pickup']) && $ro['pickup'] == 1, [
                                                    'class' => (isset($errors[$index]["pickup"]) ? ' is-invalid' : ''),
                                                    'disabled' => !isset($errors[$index]["pickup"]) ? 'disabled' : null
                                                ]) !!}
                                                @if (isset($errors[$index]["pickup"]))
                                                    <span class="text-danger">{{ $errors[$index]["pickup"] }}</span>
                                                @endif
                                            </td>


                                            <td>
                                                {!! Form::text($index . "[pick_up_cut_off]", $ro['pick_up_cut_off'] ?? '', [
                                                    'class' => 'form-control' . (isset($errors[$index]["pick_up_cut_off"]) ? ' is-invalid' : ''),
                                                    'readonly' => isset($ro['pickup']) && $ro['pickup'] == 1 ? null : 'readonly' // Enable if pickup is 1
                                                ]) !!}

                                                @if (isset($errors[$index]["pick_up_cut_off"]))
                                                    <span class="text-danger">{{ $errors[$index]["pick_up_cut_off"] }}</span>
                                                @endif
                                            </td>



                                            <!-- Regular -->
                                            <td>
                                                {!! Form::checkbox($index . "[regular_rush]", 1, isset($ro['regular_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["regular_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[regular_saver_plus]", 1, isset($ro['regular_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["regular_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[regular_swift]", 1, isset($ro['regular_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["regular_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[regular_same_day]", 1, isset($ro['regular_same_day']) ? true : false, [
                                                    'class' => (isset($errors[$index]["regular_same_day"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                            <!-- Replacement -->
                                            <td>
                                                {!! Form::checkbox($index . "[replacement_rush]", 1, isset($ro['replacement_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["replacement_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[replacement_saver_plus]", 1, isset($ro['replacement_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["replacement_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[replacement_swift]", 1, isset($ro['replacement_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["replacement_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[replacement_same_day]", 1, isset($ro['replacement_same_day']) ? true : false, [
                                                    'class' => (isset($errors[$index]["replacement_same_day"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                            <!-- Try & Buy -->
                                            <td>
                                                {!! Form::checkbox($index . "[try_and_buy_rush]", 1, isset($ro['try_and_buy_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["try_and_buy_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[try_and_buy_saver_plus]", 1, isset($ro['try_and_buy_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["try_and_buy_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[try_and_buy_swift]", 1, isset($ro['try_and_buy_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["try_and_buy_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[try_and_buy_same_day]", 1, isset($ro['try_and_buy_same_day']) ? true : false, [
                                                    'class' => (isset($errors[$index]["try_and_buy_same_day"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                            <!-- Reverse Pickup -->
                                            <td>
                                                {!! Form::checkbox($index . "[reverse_pickup_rush]", 1, isset($ro['reverse_pickup_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["reverse_pickup_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[reverse_pickup_saver_plus]", 1, isset($ro['reverse_pickup_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["reverse_pickup_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[reverse_pickup_swift]", 1, isset($ro['reverse_pickup_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["reverse_pickup_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[reverse_pickup_same_day]", 1, isset($ro['reverse_pickup_same_day']) ? true : false, [
                                                    'class' => (isset($errors[$index]["reverse_pickup_same_day"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                            <!-- FTL -->
                                            <td>
                                                {!! Form::checkbox($index . "[ftl_rush]", 1, isset($ro['ftl_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["ftl_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[ftl_saver_plus]", 1, isset($ro['ftl_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["ftl_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[ftl_swift]", 1, isset($ro['ftl_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["ftl_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[ftl_same_day]", 1, isset($ro['ftl_same_day']) ? true : false, [
                                                    'class' => (isset($errors[$index]["ftl_same_day"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                            <!-- Walk-In -->
                                            <td>
                                                {!! Form::checkbox($index . "[walkin_rush]", 1, isset($ro['walkin_rush']) ? true : false, [
                                                    'class' => (isset($errors[$index]["walkin_rush"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[walkin_saver_plus]", 1, isset($ro['walkin_saver_plus']) ? true : false, [
                                                    'class' => (isset($errors[$index]["walkin_saver_plus"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>
                                            <td>
                                                {!! Form::checkbox($index . "[walkin_swift]", 1, isset($ro['walkin_swift']) ? true : false, [
                                                    'class' => (isset($errors[$index]["walkin_swift"]) ? ' is-invalid' : ''),
                                                ]) !!}
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    @section('css')
        <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    @endsection

    @section('js')
        <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

        <script>
            $(document).ready(function() {
                $('.select_hub').select2({
                    width: '100%',
                    placeholder: 'Select Hub'
                });
                $('.select_zone').select2({
                    width: '100%',
                    placeholder: 'Select Zone'
                });

                $('.form-control').css('width', '100px');

                var rowCount = $("#tbl td").closest("tr").length;
                if(rowCount == 1){
                    $('.cancel_shipment').addClass('d-none');
                }
                else{
                    $('#tbl .cancel_shipment').on('click', function(e){
                        $(this).closest('tr').remove();
                        rowCount = $("#tbl td").closest("tr").length;
                        if(rowCount == 1){
                            $('.cancel_shipment').addClass('d-none');
                        }
                    });
                }
            });

        </script>
@endsection
