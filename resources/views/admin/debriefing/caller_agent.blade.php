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
                            <div class="row mb-2 justify-content-center">

                                <div class="col-6">
                                    <fieldset class="position-relative has-icon-left">
                                        <input type="text" class="form-control" placeholder="Tracking Number" value="{{$shipment->tracking_number}}" readonly style="text-align: center;">
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row mb-2 justify-content-center">
                                <div class="col-4">
                                    <div class="card bg-gradient-directional-total-calls pull-up">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i class="icon-flag text-white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">{{$total_calls}}</h3>
                                                        <span>Total Call(s)</span>
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
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">{{$completed_calls}}</h3>
                                                        <span>Completed Call(s)</span>
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
                                                    <div class="media-body text-white text-right">
                                                        <h3 class="text-white">{{$pending_calls}}</h3>
                                                        <span>Pending Call(s)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 border-primary">
                                <div class="align-items-center bg-primary">
                                    <div class="d-flex flex-wrap ml-1 mr-1 font-medium-3 white">
                                        <div>
                                                Rider Name : <span class="font-medium-2">{{$delivery_note->rider->name}}</span>
                                        </div>
                                        <div class="ml-auto mr-0 mr-sm-1">
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
                                            <div class="border table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Name :</strong></td>
                                                            <td>{{$shipment->consignee_name}}</td>
                                                            <td><strong>Phone :</strong></td>
                                                            <td>{{$shipment->consignee_phone_number_1}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Address :</strong></td>
                                                            <td>{{$shipment->consignee_address}}</td>
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
                                                    <div class="col-4">
                                                        <fieldset class="form-group">
                                                            <select name="status" id="status" class="form-control select2" data-rule-required="true" data-msg-required="Status is Required">
                                                                @foreach($statuses as $status)
                                                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-4">
                                                        <fieldset class="form-group">
                                                            <select name="reason" id="reasons" class="form-control select2">
                                                                <option value=""></option>
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-4">
                                                        <fieldset class="form-group">
                                                            <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                                <div class="row justify-content-end">
                                                    @if($call->skip == 0)
                                                        <button type="button" id="skip_btn" class="mr-1 mb-1 btn btn-danger btn-min-width"> Skip </button>
                                                    @endif
                                                    <button type="submit" value="next" class="mr-1 mb-1 btn btn-success btn-min-width"> Next </button>
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
            @if($call->skip == 0)
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