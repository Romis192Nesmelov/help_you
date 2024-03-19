@extends('layouts.main')

@section('content')
    <div class="row">
        @foreach ($partners as $partner)
            <div class="rounded-block logo">
                <img src="{{ asset($partner->logo) }}" />
{{--                <a href="{{ route('partners',['id' => $partner->id]) }}"><img src="{{ asset($partner->logo) }}" /></a>--}}
            </div>
        @endforeach
    </div>
@endsection
