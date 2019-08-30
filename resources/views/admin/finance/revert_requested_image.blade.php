
@php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
@endphp

<div>
    <img class="" src="{{asset($url)}}" alt="" title="" style="max-width: 100%"/>
</div>