@extends('admin.layout.master')

@section('title', 'Update Area(s)  of Hub')

@section('content')
    <h1>Update Area(s)  of Hub</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')
                               
                                <form id="search_form"  action="{{route('admin.human_resource.employee_areas.assign')}}" method="post" class="form row mb-1" novalidate="novalidate"  enctype="multipart/form-data">
                                     {{ csrf_field() }}

                                    <div class="form-group col-3">
                                        <select name="hub_id" class="form-control select2 hub_id" data-rule-required="true" data-msg-required="Hub is required">
                                            @foreach($hubs as $hub)
                                                <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-3">
                                        <input type="file" name="upload_excel" class="form-control upload_excel" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                    </div>

                                    <div class="form-group col-2">
                                        <button type="submit" class="btn btn-primary upload" value="Upload">Upload</button>
                                    </div>
                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/Employee Assign Hubs.xlsx') }}?v=14_09_2022" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </form>

                                 <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">Hub</th>
                                        <th class="border-primary border-darken-1">Area</th>
                                        <th class="border-primary border-darken-1">Area ID</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($areas as $area)
                                            <tr>
                                                <td>{{$area->hubs->name}}</td>
                                                <td>{{$area->name}}</td>
                                                <td>{{$area->id}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>
                    </div>        
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

    <style>

        .legends{
            cursor:pointer;
        }
        
        .is_line_manager{
            background-color: yellow;
        }
    </style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_form .hub_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Hub*'
            });

            $("#search_form").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Area(s) are being Assigned!',
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