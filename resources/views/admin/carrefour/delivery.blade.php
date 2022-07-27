@extends('admin.layout.master')

@section('title', 'Carrefour Bulk Delivery')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Carrefour Bulk Delivery
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="delivery_form" class="form-horizontal" method="POST" action="{{ route('admin.carrefour.delivery.submit') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col ml-auto">
                                            <div class="form-group text-right">
                                                <a href="{{ asset('file/Carrefour Bulk Delivery.xlsx') }}?v=21_10_2020" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2 justify-content-center">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2 justify-content-center">
                                        <div class="col-3">
                                            <fieldset class="form-group">
                                                <select name="operation_rider_id" id="operation_rider_id" class="form-control select2" data-rule-required="true" data-msg-required="Category is required">
                                                    @foreach($operation_rider_category as $category)
                                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-3">
                                            <fieldset class="form-group">
                                                <select name="rider" id="rider" class="form-control select2" data-rule-required="true" data-msg-required="Rider is required">
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-3">
                                            <fieldset class="form-group">
                                                <select name="route" id="route" class="form-control select2"  data-rule-required="true" data-msg-required="Route is required">
                                                    @foreach($routes as $route)
                                                        <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <button type="submit" name="upload" class="btn btn-primary">Submit &amp; Print</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
                    @if(session('print'))
            var pid = '{{ session('print') }}';
            print(pid);
            @endif
            $('#rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider*',
            });

            $('#operation_rider_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Category*',
            }).bind('select2:select', function () {
                if(this.value){
                    $.ajax({
                        url: '{!! route('admin.delivery.note.operation_riders') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'operation_rider_type': this.value,
                        }
                    }).done(function(data){

                        if (data.status == 1) {
                            var html = "";
                            $.each(data.riders, function(key,value) {
                                html += `<option value="${value.id}">${value.name}</option>`;
                            });
                            $('#rider').html(html);
                            $('#rider').val('').trigger('change');
                        }
                        else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
            });
            $('#rider').on('change',function () {
                var route = $(this).find(":selected").data("id");
                var rider_id = $(this).val();
                if(rider_id != null){
                    $.ajax({
                        url: '{!! route('admin.delivery.note.rider_dncc_status') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'rider_id': rider_id,
                        }
                    }).done(function(data){
                        if (data.status == 1) {
                            ccd_rider = parseInt(data.ccd_rider);
                            $('#route').val(route).trigger('change');
                            $("#deliveryNoteSubmitBtn").attr('disabled',false);
                        }
                        else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $("#deliveryNoteSubmitBtn").attr('disabled',true);
                        }
                    });
                }
                else{
                    $('#route').val(route).trigger('change');
                }

            });
            $.validator.addMethod('maxsize', function(value, element, params) {
                if ($(element).attr('type') === 'file') {
                    if (element.files && element.files.length) {
                        console.log(element.files);
                        for (var c = 0; c < element.files.length; c++) {
                            if (element.files[c].size > params) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }, $.validator.format("File Size must not exceed {0} bytes."));

            $('#delivery_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Creating Delivery Note!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });
    </script>
@endsection