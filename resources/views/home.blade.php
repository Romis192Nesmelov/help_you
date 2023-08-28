@extends('layouts.main')

@section('content')
    @csrf
    <x-modal id="point-modal" close-white>
        <div class="image"><img height="200" src="{{ asset('storage/images/placeholder.jpg') }}" /></div>
        <h3></h3>
        <p></p>
    </x-modal>
    <section>
        <div class="container">
            <div class="row d-table-lg d-table-md">
                <div class="d-table-cell-lg d-table-cell-md col-lg-3 col-md-4 col-sm-12">
                    <div id="filters-block">
                        <div class="title">{{ trans('content.filter') }}</div>
                    </div>
                </div>
                <div class="d-table-cell-lg d-table-cell-md d-table-sm col-lg-9 col-md-8 col-sm-12">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
