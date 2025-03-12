@extends('admin.layout.master')

@section('title', 'Zone/Margin Column Mapping')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Zone/Margin Column Mapping
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            
                            <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.retail.international.zonal_margin_column.submit') }}" novalidate="novalidate">
                                {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                        <label class="mr-2 font-medium-2"><b>International Rate Margin Column(s) </b></label>
                                        <div class="col-12 form-group">
                                            <select name="margin_column[]" id="included_margin_column_container" class="form-control select2" multiple="multiple">
                                                <option value="select_all">Select All</option> <!-- Added Select All Option -->
                                                @foreach($marginColumn as $key => $column)
                                                    <option value="{{$column}}" @if(in_array($column, old('margin_columns', explode(',',$selectedMarginColumns) ?? []))) selected @endif >{{$column}}</option>
                                                @endforeach
                                            </select>
                                        </div>               
                                </div>
                                <div class="col-md-6">
                                    <label class="mr-2 font-medium-2"><b>International Rate Zone Column(s) </b></label>
                                        <div class="col-12 form-group">
                                            <select name="zone_column[]" id="included_zone_column_container" class="form-control select2" multiple="multiple">
                                                @foreach($zoneColumn as $key => $column)
                                                    <option value="{{$column}}"  @if(in_array($column, old('margin_columns', explode(',',$selectedZoneColumn) ?? []))) selected @endif >{{$column}}</option>
                                                @endforeach
                                            </select>
                                        </div>               
                                </div>
                                <div class="col-md-12 form-group mt-5">
                                        <button type="submit" class="col-md-4 btn btn-primary">Save</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <style>
        .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #64a0d2 !important;
            border-color: #5587b4 !important;
            color: #FFFFFF;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            setTimeout(function(){
                $("#all_shipper_toggle").trigger('change');
                }, 100);

            $('#included_margin_column_container').select2({
                placeholder:'Select Margins Column',
                width:'100%',
                allowClear:true
            });
            $('#included_zone_column_container').select2({
                placeholder:'Select Zone Column',
                width:'100%',
                allowClear:true
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
