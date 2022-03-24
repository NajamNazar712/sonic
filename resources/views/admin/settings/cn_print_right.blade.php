@extends('admin.layout.master')

{{--@section('title', 'Re-Attempt Percentage')--}}

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">

            <div class="content-body">
                <h1 class="mb-1">
                    CN Print Rights
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-5 col-sm-4 col-md-3 col-lg-2">
                                    <form id="track_form" action="{{route('admin.settings.cn_print_right.store')}}" method="post" class="justify-content-center m-2" novalidate="novalidate">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Rule Ids" name="role_ids"
                                                   id="role_ids"  data-tags-input-name="role_ids">
                                        </div>
                                        <button type="submit" id="search_filter_btn"
                                                class="btn btn-outline-primary btn-min-width search"><i
                                                    class="la la-search"></i> Insert
                                        </button>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script>

        $(document).ready(function () {

            //form submission
            $("#track_form").submit(function(e) {
                e.preventDefault(); // prevent actual form submit
                var form = $(this);
                var url = form.attr('action'); //get submit url [replace url here if desired]
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes form input
                }).done(function (data) {
                    // console.log(data);
                        if(data.status == 2)
                        {
                            toastr.success(data.success, 'Success!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
            });
            //form submission end
            var select = $('#track_form #role_ids').selectize({
                placeholder: 'Enter Role ID(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                create: function (input) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(input)) {
                        return false;
                    }
                    return {
                        value: input,
                        text: input
                    }
                }
            });
        });



    </script>
@endsection