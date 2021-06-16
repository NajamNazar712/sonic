@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp
@extends('admin.layout.master')
@section('title','Documents')

@section('content')
    <h1 class="mb-1">
        Documents - ERF {{$id}}
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                    <div class="row justify-content-center mt-4">
                        <div class="col-4">
                            <h3 class="text-center mb-4"><strong>Employee Requisition Documents</strong></h3>
                            <table class="table table-sm table-bordered text-center">
                                <thead>
                                <tr>
                                    <td><strong>Admin</strong></td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($documents as $document)
                                    <tr style="height: 50px">
                                        <td class="align-middle"><strong>{{$document->admin->name}}</strong></td>
                                        <td class="align-middle"><a class="white" href="{{Storage::url('employee_requisition/' . $id . '/'. $document->file)}}" target="_blank"><button type="button" class="btn btn-primary btn-sm">View</button></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection