@if(count($errors) > 0)
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

{{-- Include Toastr --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
@endsection

<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Toastr Notifications
        @if(session('finga_error'))
            toastr.error("{{ session('finga_error') }}", 'Error!', {
                positionClass: 'toast-top-center',
                containerId: 'toast-top-center'
            });
        @endif

    });
</script>