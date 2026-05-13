@extends('app.layouts.shell')

@section('content')
    @if (! empty($apiError))
        <div class="app-alert app-alert--error" role="alert">{{ $apiError }}</div>
    @endif

    @include('app.shell.partials.'.$page)
@endsection
