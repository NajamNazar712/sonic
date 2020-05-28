@extends('admin.layout.master')

@section('title', 'Pending Pickups')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pending Pickups
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <div class="row justify-content-end">
                                <div class="col-5">
                                    <div class="card border border-lighten-5">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title info">Legend</h4>
                                                <table class="table mb-0">
                                                    <tbody>
                                                    @foreach($legends as $legend)
                                                        @if($legend->id == 7)
                                                            <tr>
                                                                <td><button type="button" class="btn btn-sm round btn-min-width text-white" style="background-color: {{$legend->color}}" disabled>{{$cut_off_time}}</button></td>
                                                                <td>{{ $legend->name }}</td>
                                                            </tr>
                                                            @else
                                                            <tr>
                                                                <td><button type="button" class="btn btn-sm round btn-min-width p-1" style="background-color: {{$legend->color}}" disabled> </button></td>
                                                                <td class="align-middle">{{ $legend->name }}</td>
                                                            </tr>
                                                            @endif

                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1"></th>
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Requested Date</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Booked</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Rider Picked</th>
                                    <th class="border-primary border-darken-1">Shipment(s) Received</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Contact No(s).</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Trax Reason</th>
                                    <th class="border-primary border-darken-1">Trax Remark(s)</th>
                                    <th class="border-primary border-darken-1">Shipper Remark(s)</th>
                                    <th class="border-primary border-darken-1">Rider Status</th>
                                    <th class="border-primary border-darken-1">Attempt Date/Time</th>
                                    <th class="border-primary border-darken-1">Attempt(s)</th>
                                    <th class="border-primary border-darken-1">Last Rider</th>
                                    <th class="border-primary border-darken-1">Current Rider</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="assign_to_rider" role="dialog" aria-labelledby="assign_to_rider_title" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <form class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="modal-header">
                                    <h4 class="modal-title" id="assign_to_rider_title">Assign to Rider</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group m-0">
                                        <select name="rider" class="select2 rider" data-rule-required="true" data-msg-required="Rider is required">
                                            @foreach($riders as $rider)
                                                <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-auto">Assign</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--Shipments popup -->
                <div class="modal fade" id="bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="bookings_modal_title">Booking Shipment(s)</h4>

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
                <!--Shipments popup -->
                <!--Shipments popup pending booking-->
                <div class="modal fade" id="pending_bookings_modal" data-backdrop="static" role="dialog" aria-labelledby="pending_bookings_modal" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="pending_bookings_modal_title">Pending Booking Shipment(s)</h4>

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
                <!--Shipments popup -->

            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>

    </script>
@endsection