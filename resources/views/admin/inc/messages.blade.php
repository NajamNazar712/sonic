@php
    $attachmentErrorFields = [
        'cnic_1', 'cnic_2', 'cnic_3', 'cnic_4',
        'cv_1', 'cv_2', 'cv_3', 'cv_4',
        'photo_1', 'photo_2', 'photo_3', 'photo_4',
        'cheque_1', 'cheque_2', 'cheque_3', 'cheque_4',
        'last_pay_slip_1', 'last_pay_slip_2', 'last_pay_slip_3', 'last_pay_slip_4',
    ];

    $hasAttachmentErrors = false;

    foreach ($attachmentErrorFields as $field) {
        if ($errors->has($field)) {
            $hasAttachmentErrors = true;
            break;
        }
    }
@endphp

@if(!$hasAttachmentErrors)
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                {{$error}}
            </div>
        @endforeach
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{session('success')}}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{session('error')}}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-warning">
            {{session('info')}}
        </div>
    @endif
@endif

{{--@if(count($errors) > 0)--}}
{{--    @foreach($errors->all() as $error)--}}
{{--        <div class="alert alert-danger">--}}
{{--            {{$error}}--}}
{{--        </div>--}}
{{--    @endforeach--}}
{{--@endif--}}

{{--@if(session('success'))--}}
{{--    <div class="alert alert-success">--}}
{{--        {{session('success')}}--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if(session('error'))--}}
{{--    <div class="alert alert-danger">--}}
{{--        {{session('error')}}--}}
{{--    </div>--}}
{{--@endif--}}
{{--@if(session('info'))--}}
{{--    <div class="alert alert-warning">--}}
{{--        {{session('info')}}--}}
{{--    </div>--}}
{{--@endif--}}