@extends('layouts.main')

@section('content')
    @csrf
    <x-modal id="point-modal" close-white>
        <div class="image"><img height="200" src="{{ asset('storage/images/placeholder.jpg') }}" /></div>
        <h3></h3>
        <p></p>
    </x-modal>
    <div class="row">
        <div class="col col-lg-3 col-md-4 col-sm-12">
            <div id="filters-block" class="rounded-block white h100">
                <div class="title">{{ trans('content.filter') }}</div>
            </div>
        </div>
        <div class="col col-lg-9 col-md-8 col-sm-12">
            <div id="map" class="rounded-block white h100"></div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('js/map.js') }}"></script>
@endsection
