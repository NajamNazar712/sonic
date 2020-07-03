@extends('admin.layout.master')

@section('title', 'Escalation Level(s)')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Escalation Level(s)
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.escalation.levels.store') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                                <div id="levels">
                                    @if(count($levels) > 0)
                                        @foreach($levels as $index => $level)
                                            <div class="row justify-content-center">
                                                <div class="form-group col-6">
                                                    <input type="text" class="form-control" name="level[{{$index}}]" id="level_{{$index}}" value="{{$level->name}}" placeholder="Escalation Title*" data-rule-required="true" data-msg-required="Escalation title is required">
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row justify-content-center">
                                            <div class="form-group col-6">
                                                <input type="text" class="form-control" name="level[0]" id="level_0" placeholder="Escalation Title*" data-rule-required="true" data-msg-required="Escalation title is required">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-1 form-group">
                                        <button type="button" class="btn btn-outline-amber" id="level_add_button">Add Level</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-primary col">Update</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var count = @json(count($levels));
            $('#level_add_button').on('click', function () {
                count = count + 1;
                var html = '<div class="row justify-content-center">' +
                                '<div class="form-group col-6">' +
                                    '<input type="text" class="form-control" name="level['+ count +']" id="level_'+ count +'" placeholder="Escalation Title*" data-rule-required="true" data-msg-required="Escalation title is required">' +
                                '</div>' +
                            '</div>';

                $('#levels').append(html);
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    var content_html = 'Select Yes to update Escalation Level!<br><span class="danger">Note: Once level is updated it can\'t be removed</span>'
                    content = document.createElement('div');
                    content.innerHTML = content_html;
                    swal({
                        title: 'Are You Sure?',
                        content: content,
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {
                            swal({
                                title: 'Please Wait!',
                                text: 'In-Process Escalation is being Added!',
                                icon: 'info',
                                buttons: false,
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection