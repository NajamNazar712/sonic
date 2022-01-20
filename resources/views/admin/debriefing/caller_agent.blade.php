@extends('admin.layout.master')

@section('title', 'Caller Agent Screen')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Caller Agent Screen
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            @if($data)
                            {{--<div class="row justify-content-center">

                                <div class="col-6 text-center border tracking_box ">
                                    --}}{{-- <fieldset class="position-relative has-icon-left"> --}}{{--
                                        <u><a href='{{route('admin.tracking.index')}}?tracking_number={{$shipment->tracking_number}}' class='tracking' target='_blank'>{{$shipment->tracking_number}}</a></u>
                                        --}}{{-- <input type="text" class="form-control" placeholder="Tracking Number" value="{{$shipment->tracking_number}}" readonly style="text-align: center;"> --}}{{--
                                    --}}{{-- </fieldset> --}}{{--
                                </div>
                            </div>--}}

                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="card bg-gradient-directional-total-calls pull-up">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-flag text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-center">
                                                        <h3 class="text-white">Total Calls : {{$total_calls}}</h3>
                                                      {{--  <span>Total Call(s)</span>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-gradient-directional-completed-calls pull-up">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-check text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-center">
                                                        <h3 class="text-white">Completed Call(s) : {{$completed_calls}}</h3>
                                                       {{-- <span>Completed Call(s)</span>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-gradient-directional-pending-calls pull-up">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-close text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-center">
                                                        <h3 class="text-white">Pending Call(s) : {{$pending_calls}}</h3>
                                                      {{--  <span>Pending Call(s)</span>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-primary">
                                <div class="align-items-center bg-primary">
                                    <div class="d-flex flex-wrap ml-1 mr-1 font-medium-3 white">
                                        <div class="col-4">
                                            <a href='{{route('admin.tracking.index')}}?tracking_number={{$shipment->tracking_number}}' class="tracking font-medium-2 text-white" target='_blank'>{{$shipment->tracking_number}}</a>
                                        </div>
                                        <div class="col-4 text-center">
                                                Rider Name : <span class="font-medium-2">{{$delivery_note->rider->name}}</span>
                                        </div>
                                        <div class="col-4 text-right">
                                            Rider Phone Number : <span class="font-medium-2">{{$delivery_note->rider->phone}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-1">
                                    <div class="row justify-content-between">
                                        <div class="col-12 mb-1">
                                            <h4><u>Shipper Information</u></h4>
                                            <div class="border table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Name :</strong></td>
                                                            <td>{{$shipment->user->brand_name ?? $shipment->user->name}}</td>
                                                            <td><strong>Amount :</strong></td>
                                                            <td>{{$shipment->amount}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Address :</strong></td>
                                                            <td>{{$shipment->user->address}}</td>
                                                            <td><strong>Type :</strong></td>
                                                            <td>{{$shipment->shipping_mode->mode}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Phone :</strong></td>
                                                            <td>{{$shipment->user->phone}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <h4><u>Consignee Information</u></h4>
                                            <div class="border table-responsive" style="border:3px solid black !important;">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Name :</strong></td>
                                                            <td><strong>{{$shipment->consignee_name}}</strong></td>
                                                            <td><strong>Phone :</strong></td>
                                                            <td><strong>{{$shipment->consignee_phone_number_1}}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Address :</strong></td>
                                                            <td><strong>{{$shipment->consignee_address}}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <h4><u>Order Information</u></h4>
                                            <div class="border table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                       
                                                        @foreach ($shipment->items as $item)
                                                            <tr>
                                                                <td><strong>Product Type :</strong></td>
                                                                <td>{{$item->product->product_name}}</td>
                                                                <td><strong>Description :</strong></td>
                                                                <td>{{$item->description}}</td>
                                                                <td><strong>Quantity :</strong></td>
                                                                <td>{{$item->quantity}}</td>
                                                                <td><strong>Order ID :</strong></td>
                                                                <td>{{(($shipment->order_id) ? $shipment->order_id : '-')}}</td>
                                                            </tr>
                                                        @endforeach

                                                        @if($shipment->height != null)
                                                            <tr>
                                                                <td><strong>Weight </strong><small>(Volumetric)</small></td>
                                                                <td>{{$shipment->weight}}kg</td>
                                                                <td><strong>Service Type</strong></td>
                                                                <td>{{$shipment->booking_type->booking_type}}</td>
                                                                <td><strong>Collection Amount</strong></td>
                                                                <td>{{number_format($shipment->amount)}}</td>
                                                                <td><strong>Piece(s)</strong></td>
                                                                <td>{{$shipment->pieces}}</td>
                                                            </tr>
                                                            
                                                        @else
                                                        <tr>
                                                            <td><strong>Weight </strong><small>(Dense)</small></td>
                                                            <td>{{$shipment->weight}}kg</td>
                                                            <td><strong>Service Type</strong></td>
                                                            <td>{{$shipment->booking_type->booking_type}}</td>
                                                            <td><strong>Collection Amount</strong></td>
                                                            <td>{{number_format($shipment->amount)}}</td>
                                                            <td><strong>Piece(s)</strong></td>
                                                            <td>{{$shipment->pieces}}</td>
                                                        </tr>
                                                        @endif

                                                        <tr>
                                                            @if ($shipment->length != null)
                                                            <td><strong>Length</strong></td>
                                                            <td>{{$item->length}}cm</td>
                                                            @endif
                                                            <td><strong>Shipping Mode</strong></td>
                                                            <td>{{$shipment->shipping_mode->mode}}</td>
                                                            <td><strong>Instructions</strong></td>
                                                            <td>{{(($shipment->special_instructions) ? $shipment->special_instructions : '-')}}</td>
                                                        </tr>
                                                        <tr>
                                                            @if ($shipment->breadth != null)
                                                            <td><strong>Breadth</strong></td>
                                                            <td>{{$shipment->breadth}}cm</td>
                                                            @endif
                                                            <td><strong>Business Category</strong></td>
                                                            <td>{{$shipment->business_category->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            @if ($shipment->height != null)
                                                            <td><strong>Height</strong></td>
                                                            <td>{{$item->height}}cm</td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <h4><u>Other Information</u></h4>
                                            <div class="border table-responsive spacing">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Attempt Count :</strong></td>
                                                            <td>{{$reattempt_count->reattempts}}</td>
                                                            <td><strong>Rider Status :</strong></td>
                                                            @if($rider_status != NULL)
                                                            <td>{{$rider_status->shipment_status_shipper->name}}</td>
                                                            @else
                                                                <td></td>
                                                            @endif
                                                            <td><strong>Rider Status Reason :</strong></td>
                                                            @if($rider_status != NULL)
                                                            <td>{{($rider_status->status_reason_id) ? $rider_status->shipment_status_reason->name : '-'}}</td>
                                                            @else
                                                                <td></td>
                                                            @endif
                                                            <td><strong>OTP Entered : </strong></td>
                                                            @if($rider_delivery != NULL)
                                                                @if($rider_delivery->otp_entered != NULL)
                                                                    @if($rider_delivery->otp_entered == 1)
                                                                        <td>'Yes'</td>
                                                                    @else
                                                                        <td>'No'</td>
                                                                    @endif
                                                                @else
                                                                    <td>'-'</td>
                                                                @endif
                                                            @else
                                                                <td>'-'</td>
                                                            @endif

                                                            <td><strong>Remarks :</strong></td>
                                                            @if($rider_status != NULL)
                                                            <td>{{($rider_status->remarks) ? $rider_status->remarks : '-'}}</td>
                                                            @else
                                                                <td></td>
                                                            @endif
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <form action="{{route('admin.debriefing.caller_agent.next')}}" id="next_form" method="post" novalidate="novalidate">
                                                @csrf
                                                <input type="hidden" name="call_id" id="call_id" value="{{$call->id}}">
                                                <div class="row justify-content-center">
                                                    <div class="col-2">
                                                        <fieldset class="form-group">
                                                            <input type="checkbox" name="fake_status" id="fake_status">
                                                            <label for="fake_status">Fake Status</label>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-3">
                                                        <fieldset class="form-group">
                                                            <select name="status" id="status" class="form-control select2" data-rule-required="true" data-msg-required="Status is Required">
                                                                @foreach($statuses as $status)
                                                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-3">
                                                        <fieldset class="form-group">
                                                            <select name="reason" id="reasons" class="form-control select2">
                                                                <option value=""></option>
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-3">
                                                        <fieldset class="form-group">
                                                            <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col justify-content-start">
                                                        @if($rider_delivery != NULL)
                                                            @if($shipment->amount == 0 && $rider_delivery->delivered_status == 1)
                                                                <a href="{{asset(Storage::url($rider_delivery->cnic_image))}}" target="_blank"><button type="button" class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-image"></i> View CNIC</button></a>
                                                            @elseif($rider_delivery->delivered_status == 0)
                                                                <a href="{{asset(Storage::url($rider_delivery->picture_path))}}" target="_blank"><button type="button" class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-image"></i> View Image</button></a>
                                                                @if($rider_delivery->audio_path != Null)
                                                                    <a href="{{asset(Storage::url($rider_delivery->audio_path))}}" target="_blank"><button type="button" class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-file-sound-o"></i> Audio</button></a>
                                                                @endif
                                                                <a href="{{'http://www.google.com/maps/place/'. $rider_delivery->actual_location_latitude . ',' . $rider_delivery->actual_location_longitude}}" target="_blank"><button type="button" class="mr-1 mb-1 btn btn-primary btn-min-width"><i class="la la-map-marker align-middle"></i>Location</button></a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="justify-content-end">
                                                        @if($call->skip == 0)
                                                            <button type="button" id="skip_btn" class="mr-1 mb-1 btn btn-danger btn-min-width"> Skip </button>
                                                        @endif
                                                        <button type="submit" value="next" class="mr-1 mb-1 btn btn-success btn-min-width"> Next </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                                <div class="row justify-content-center">
                                    <h3>No Calls Assigned</h3>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">

    <style>
        .tracking_box{
            padding: 10px 2px;
            border-radius: 5px;
        }
        .bg-gradient-directional-total-calls {
            background-image: linear-gradient(45deg, #074077, #2FBEF5);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-completed-calls {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-pending-calls {
            background-image: linear-gradient(45deg, #FF0C0C, #FF9191);
            background-repeat: repeat-x;
        }
        .spacing{
            padding-top: 20px;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection


@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="https://kit.fontawesome.com/e7bc565afe.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#status').prepend('<option value="" selected></option>').select2({
                placeholder: 'Select Status',
                width:'100%',
                allowClear: true
            });

            $('#reasons').prepend('<option value="" selected></option>').select2({
                placeholder: 'Select Reason',
                width:'100%',
                allowClear: true
            });

            $('body').on('select2:select','#status',function (e) {

                var statusSelection = $(this).find(':selected');
                var status = statusSelection.val();
                var all_reason = $('#reasons');
                $.ajax({
                    url:'{!! route('admin.delivery.receive.reason_all') !!}',
                    type:'POST',
                    dataType:'json',
                    data: {
                        'status':status,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function (data) {
                    if(data.status == 0){
                        all_reason.empty().trigger('change');
                        $.each(data.reasons,function (key,value) {
                            var newOption = new Option(value.name, value.id, false, false);
                            all_reason.append(newOption).trigger('change');
                            if(all_reason != 14){
                                all_reason.attr('data-rule-required', 'true');
                                all_reason.attr('data-msg-required', 'Reason is required');
                            }
                        });
                        all_reason.val('').trigger('change');
                    }else{
                        all_reason.empty().trigger('change');
                        toastr.success(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                    }
                });
            });

            $('#next_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
            @if($data == true && $call->skip == 0)
            $("#skip_btn").on('click',function () {
                var id = $("#call_id").val();
                $.ajax({
                    url: '{!! route('admin.debriefing.caller_agent.skip') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        if(data.status == 1)
                        {
                            location.reload();
                        }
                    });
            });
            @endif

        });

    </script>
@endsection