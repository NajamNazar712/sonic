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
                                                <select name="rider" id="rider" class="form-control select2" data-rule-required="true" data-msg-required="Rider is required">
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                    @endforeach
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
                                                <button type="submit" name="upload" class="btn btn-primary">Bulk Deliver</button>
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
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
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
                        text: 'Your shipment(s) are being delivered!',
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