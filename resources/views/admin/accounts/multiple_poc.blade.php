@extends('admin.layout.master')

@section('title', 'Add Contacts')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Add Contacts for {{$shipper->name}}
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                                <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.accounts.add_contacts.store') }}" novalidate="novalidate">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="shipper_id" value="{{$shipper->id}}">
                                    <div class="col contact_rows">
                                        <div class="row justify-content-center mb-1">
                                            <div class="col form-group">
                                                <input type="text" name="" class="form-control poc" placeholder="Poc*" data-rule-required="true" data-msg-required="POC is required" value="{{ $sale_person->name }}" disabled>
                                            </div>
                                            <div class="col form-group">
                                                <input type="text" name="" class="form-control designation" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required" value="{{ $sale_person->role->name }}" disabled>
                                            </div>
                                            <div class="col form-group">
                                                <input type="text" name="" class="form-control phone" placeholder="Phone*" data-rule-required="true" data-msg-required="Phone is required" value="{{ $sale_person->phone_number }}" disabled>
                                            </div>
                                            <div class="col-1">
                                            </div>
                                        </div>
                                        @if($contacts != null)
                                            @foreach($contacts as $index => $contact)
                                                <div class="row justify-content-center contact_row mb-1" id="contact_row{{$index}}">
                                                    <div class="col form-group">
                                                        <input type="text" name="poc[{{$index}}]" class="form-control poc" placeholder="Poc*" data-rule-required="true" data-msg-required="POC is required" value="{{ $contact->poc }}">
                                                    </div>
                                                    <div class="col form-group">
                                                        <input type="text" name="designation[{{$index}}]" class="form-control designation" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required" value="{{ $contact->designation }}">
                                                    </div>
                                                    <div class="col form-group">
                                                        <input type="text" name="phone[{{$index}}]" class="form-control phone" placeholder="Phone*" data-rule-required="true" data-msg-required="Phone is required" value="{{ $contact->phone_number }}">
                                                    </div>
                                                    <div class="col-1">
                                                        <span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 remove_contact_row"><i class="ft-x"></i></span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-outline-success mr-1" title="Add more slabs" id="add_contact_row"><i class="la la-plus"></i></button>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.phone').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            var row_count = $('.contact_row').length + 1;
            $('body').on('click','#add_contact_row',function () {

                let htmdiv1 = '<div class="row justify-content-center contact_row mb-1" id="contact_row' + row_count + '">' +
                    '<div class="col form-group">' +
                    '<input type="text" name="poc[' + row_count + ']" class="form-control poc" placeholder="Poc*" data-rule-required="true" data-msg-required="POC is required" value="">' +
                '</div>' +
                '<div class="col form-group">' +
                    '<input type="text" name="designation[' + row_count + ']" class="form-control designation" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required" value="">' +
                '</div>' +
                '<div class="col form-group">' +
                    '<input type="text" name="phone[' + row_count + ']" class="form-control phone" placeholder="Phone*" data-rule-required="true" data-msg-required="Phone is required" value="">' +
                '</div>' +
                '<div class="col-1">' +
                    '<span  class="btn btn-danger rounded btn-sm-width mr-1 mb-1 remove_contact_row"><i class="ft-x"></i></span>'
                '</div>' +
                '</div>';
                $('.contact_rows').append(htmdiv1);

                $('.phone').inputmask({
                    'mask': '9999-9999999',
                    'clearIncomplete': true
                });

                $("#contact_row"+ row_count +" .validated").each(function(){
                    $( this ).rules( "add", {
                        required: true,
                    });

                });
                row_count++;
            });
            $('body').on('click','.remove_contact_row',function () {
                $(this).parent().parent().remove();
            });

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                }
            });
        });
    </script>
@endsection