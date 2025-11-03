@extends('admin.layout.master')
@section('title', 'View Documents')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">

            <h1 class="mb-2">Documents for Request # {{ $request->id }}</h1>
            @include('admin.inc.messages')

            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <table class="table table-bordered text-center align-middle mb-0">
                                <!-- <thead>
                                    <tr>
                                        <th width="50%">Document Type</th>
                                        <th width="50%">Action</th>
                                    </tr>
                                </thead> -->
                                <tbody>
                                    @foreach ([
                                        'document1' => 'Document 1',
                                        'document2' => 'Document 2',
                                        'document3' => 'Document 3',
                                        'document4' => 'Document 4'
                                    ] as $field => $label)
                                        <tr>
                                            <td><h6><b>{{ $label }}</b></h6></td> 
                                            <td>
                                                @if($request->$field)
                                                    <a href="{{ route('admin.finance.prf.document.open', ['id' => $request->id, 'doc' => $field]) }}"
                                                    target="_blank" class="btn btn-primary btn-sm">View</a>
                                                @else
                                                    <span>–</span>
                                                @endif
                                            </td>
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
</div>
@endsection
