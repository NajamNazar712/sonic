@extends('admin.layout.master')

@section('title', 'Request History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Request History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="row p-1 mb-2 w-50">
                                    <div class=" col-9">
                                        <fieldset class="form-group">
                                            <input type="text" name="fuel_request_id" class="form-control w-100 fuel_request_id" placeholder="Fuel Request Id*" data-tags-input-name="fuel_request_id" data-rule-required="true" data-msg-required="Fuel Request Id is required">
                                        </fieldset>
                                    </div>
                                    <div class="col-2 form-group ml-1">
                                        <button type="submit" class="btn btn-primary" value="See History">See History</button>
                                    </div>
                                </div>
                            </form>

                            <div class="history" id="history">
                            </div>

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

    <style>
        .selectize-control {
            width: 300px !important;
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

            function track(fuel_request_id) {
                    $.ajax({
                    url: '{!! route('admin.user_management.fuel_management.history.index') !!}',
                    method: 'GET',
                    data: {
                        'fuel_request_id': fuel_request_id,
                    }
                    })
                    .done(function (data) {

                        $('#history').html('');

                        if (data.status == 1) {
                            html = '';
                            html += `<div class="mt-4 border-primary">
                                <div class="d-flex flex-wrap align-items-center bg-primary">
                                    <div class="mb-0 ml-1 mr-1 font-medium-3 white">${data.request.fuel_request_id}</div>
                                </div>
                                <div class="p-1">
                                    <div class="row justify-content-between">
                                        <div class="col-12">
                                            <h4><u>Request Information</u></h4>
                                            <div class="border table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Card Number</strong></td><td>${data.request.card_number}</td>
                                                            <td><strong>Card Holder Name</strong></td><td>`
                            if (data.request.card_holder_type_id == 1) {
                                html += data.request.staff_name
                            } else if (data.request.card_holder_type_id == 2) {
                                html += data.request.rider_name
                            } else if (data.request.card_holder_type_id == 3) {
                                html += data.request.fleet_name
                            }
                            html += `</td>
                                                            <td><strong>Card Holder Type</strong></td><td>${data.request.card_holder_type}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Fuel Type</strong></td><td>${data.request.fuel_type}</td>
                                                            <td><strong>Fuel Deduction Type</strong></td><td>${data.request.fuel_deduction_type}</td>
                                                            <td><strong>Amount</strong></td><td>${data.request.amount}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Request Type</strong></td><td>${data.request.card_request_type}</td>
                                                            <td><strong>Requested By</strong></td><td>${data.request.requested_by}</td>
                                                            <td><strong>Approved By</strong></td><td>${data.request.approved_by}</td>
                                                        </tr>
                                                     </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <h4><u>Request History</u></h4>
                                            <div class="border table-responsive">
                                                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                                    <table class="table table-sm table-borderless request_history no-footer" id="datatable_history" role="grid">
                                                        <thead>
                                                            <tr role="row">
                                                                <th class="align-middle date_time"><strong>Date / Time</strong></th>
                                                                <th class="align-middle action" aria-sort="descending"><strong>Action Performed</strong></th>
                                                                <th class="align-middle approved_by"><strong>Performed By</strong></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>`;

                            $.each(data.logs, function (i, v) {
                                html += `
                                        <tr>
                                            <td>${v['created_at']}</td>
                                            <td>${v['action']}</td>
                                            <td>${v['approved_by']}</td>
                                        </tr>
                                 `;
                            });

                            html += `</tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                            $('#history').html(html);


                            $('#history table#datatable_history').DataTable({
                                dom: 't',
                                paging: false,
                                order: [[0, 'desc']],
                                columns: [
                                    {name: 'date_time', class: 'align-middle date_time'},
                                    {name: 'action_performed', class: 'align-middle action_performed'},
                                    {name: 'performed_by', class: 'align-middle performed_by'},
                                ]
                            });
                        }
                    });

            }

            @if (app('request')->has('fuel_request_id'))
                track("{{ app('request')->input('fuel_request_id') }}");
            @endif

            $('#track_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function (form) {
                    track($(form).find('.fuel_request_id').val());
                    return false;
                }
            });






        });


    </script>
@endsection