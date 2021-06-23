@extends('admin.layout.master')

@section('title', 'Search Consignee')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Search Consignee
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="search_form" class="mb-1 justify-content-center" novalidate="novalidate">
                                <div class="row justify-content-center">
                                    <div class="form-group col-3">
                                        <input type="text" name="phone" class="form-control phone" placeholder="Phone Number*" data-rule-required="true" data-msg-required="Phone Number is required">
                                    </div>

                                    <div class="form-group ml-1">
                                        <button type="submit" name="search" class="btn btn-primary search" value="Search">Search</button>
                                    </div>
                                </div>
                            </form>
                            <div class="row justify-content-center d-none" id="information_div">
                                <div class="col-6 border border-primary">
                                    <div class="consignee_information_div mt-2" id="consignee_information_div"></div>

                                    <form id="label_update_form" class="mb-1 mt-2" method="POST" action="{{ route('admin.settings.blacklist.search.update') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <input type="hidden" id="consignee_information_id" name="consignee_information_id">
                                        <div class="row justify-content-center">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <select name="label_select" id="label_select" class="form-control" data-msg-required="This field is required">
                                                        @foreach($blacklists as $blacklist)
                                                            <option value="{{ $blacklist->id }}">{{ $blacklist->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="form-group">
                                                <button type="submit" name="action" class="btn btn-success" id="exclude_btn" value="exclude">Exclude</button>
                                            </div>
                                            <div class="form-group ml-1">
                                                <button type="submit" name="action" class="btn btn-primary" id="label_btn" value="label">Label</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('#label_select').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Labeling*'
            });
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button.search').prop('disabled', true);

                    $('#consignee_information_div').html('');


                    var phone = $(form).find('input.phone').val();

                    form.reset();

                    $.ajax({
                        url: '{!! route('admin.settings.blacklist.search.consignee') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'phone': phone
                        }
                    })
                        .done(function(data) {
                            if (data.status == 0) {
                                details = data.details;
                                $('#consignee_information_id').val(details.consignee.id);
                                var html = '<div class="row mb-1">';

                                html += '<div class="col-4">Consignee Name :</div><div class="col-8">'+ details.consignee.name +'</div>';
                                html += '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">'+ details.consignee.phone +'</div>';
                                var consignee_phone = '';
                                if(details.consignee.phone2 != null){
                                    consignee_phone = details.consignee.phone2;
                                }
                                html += '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">'+ consignee_phone +'</div>';
                                html += '<div class="col-4">Consignee Address :</div><div class="col-8">'+ details.consignee.address +'</div>';
                                html += '<div class="col-4">Consignee City :</div><div class="col-8">'+ details.consignee.city +'</div>';

                                html += '</div>';
                                if ('blacklist' in details) {
                                    html += '<div class="row p-1" style="background-color: '+ details.blacklist.color +'; color:white;">';
                                    html += '<div class="col-12">';
                                    html += '<table class="table table-sm table-bordered mb-0">';
                                    html += '<tbody>';
                                    html += '<tr>';
                                    html += '<td><strong>Total Shipments</strong></td>';
                                    html += '<td><strong>Delivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Undelivered</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '<td><strong>Return Confirmed</strong></td>';
                                    html += '<td><strong>Ratio</strong></td>';
                                    html += '</tr>';
                                    html += '<tr>';
                                    html += '<td>' + details.blacklist.total_shipments + '</td>';
                                    html += '<td>' + details.blacklist.delivered + '</td>';
                                    html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.undelivered + '</td>';
                                    html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';
                                    html += '<td>' + details.blacklist.return + '</td>';
                                    html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';
                                    html += '</tr>';
                                    html += '</tbody>';
                                    html += '</table>';
                                    html += '</div></div>';
                                }

                                $('#consignee_information_div').html(html);


                                $('#information_div').removeClass('d-none');

                                $('#label_select').val('').trigger('change');


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

            $('#label_btn').on('click', function() {
                $('#label_update_form').validate({
                    rules: {
                        label_select: {
                            required: true,
                        }
                    },
                    ignore:[],
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parents('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Category is being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });
            });
            $('#exclude_btn').on('click', function() {
                $('#label_update_form').validate({
                    ignore: ":not(:visible),:disabled",
                    errorClass: 'danger',
                    successClass: 'success',
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parents('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Category is being added!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });
            });



        });
    </script>
@endsection