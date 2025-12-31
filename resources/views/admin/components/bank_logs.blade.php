<h1>Bank Logs of {{$user->name}}</h1>

<style>
    .banklog-table { table-layout: fixed; width: 100%; }
    .banklog-table th { width: 220px; }
    .json-box{
        margin: 0;
        max-width: 100%;
        max-height: 140px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-word;
        overflow-wrap: anywhere;
        background: #f8f9fa;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 12px;
    }
</style>

@php $printed = 0; @endphp

@foreach ($banks as $bank)
    @php
        $myLogs = $logs->where('record_id', $bank->id);
    @endphp

    {{-- skip banks with no logs (ONLY here) --}}
    @if($myLogs->isEmpty())
        @continue
    @endif

    @php $printed++; @endphp

    <table class="table table-sm table-bordered mb-2 banklog-table">
        <tbody>
            <tr>
                <th class="border-primary border-darken-1 align-middle text-center">Bank Record ID</th>
                <td class="align-middle text-center">{{ $bank->id }}</td>
            </tr>

            @foreach($myLogs as $lg)
                @php
                    $oldDecoded = json_decode($lg->old_data, true);
                    $newDecoded = json_decode($lg->new_data, true);

                    $oldPretty = $oldDecoded ? json_encode($oldDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $lg->old_data;
                    $newPretty = $newDecoded ? json_encode($newDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $lg->new_data;
                @endphp

                <tr>
                    <th class="border-primary border-darken-1 align-middle text-center">Date</th>
                    <td class="align-middle  text-center">{{ $lg->created_at }}</td>
                </tr>
                <tr>
                    <th class="border-primary border-darken-1 align-middle text-center">Old Data</th>
                    <td class="align-middle"><pre class="json-box">{{ $oldPretty }}</pre></td>
                </tr>
                <tr>
                    <th class="border-primary border-darken-1 align-middle text-center">New Data</th>
                    <td class="align-middle"><pre class="json-box">{{ $newPretty }}</pre></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

@if($printed === 0)
    <div class="alert alert-info">No logs found</div>
@endif
