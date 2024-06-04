@extends('admin.layout.master')

@section('title', 'KAM Bulk Tagging & De-Tagging')

@section('content')
    <h1>KAM Bulk Tagging & De-Tagging</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
    
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row upload_shippers_form_div px-1">
                                <form id="upload_shippers_form" class="form-horizontal w-100 p-2" method="POST" action="#" novalidate="novalidate" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="file" name="shippers" class="w-100 border-primary rounded" style="padding: 6px" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="adjustment_type" id="adjustment_types" data-rule-required="true" data-msg-required="Adjustment Type is required">
                                                    <option disabled selected>Select Rate Type</option>
                                                    {{-- @foreach ($baseRateTypes as $baseRateType)
                                                    <option value="{{$baseRateType->id}}">{{$baseRateType->name}}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary mb-2">Upload</button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-end">
                                            <div class="form-group text-right">
                                                <a href="{{ asset('file/Base Rate Revisions Template.xlsx') }}?v=09_06_2021" class="btn btn-primary btn-block"><i class="la la-download"></i> Download Template</a>
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
    </section>
 


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')


@endsection

