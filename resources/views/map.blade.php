@extends('layouts.main')

@section('content')
    @csrf

    <div class="row">
        <div class="col col-lg-3 col-md-4 col-sm-12">
            <div id="filters-block" class="rounded-block white h100">
                <div class="title">{{ trans('content.filter') }}</div>
                <form class="form-horizontal">
                    @include('blocks.select_block',[
                        'name' => 'city',
                        'label' => trans('content.city'),
                        'values' => $cities,
                        'option' => 'name',
                        'selected' => 1
                    ])
                    @include('blocks.select_block',[
                        'name' => 'type',
                        'label' => trans('content.request_type'),
                        'values' => [
                            trans('content.type',['type' => 1]),
                            trans('content.type',['type' => 2]),
                            trans('content.type',['type' => 3]),
                            trans('content.type',['type' => 4]),
                            trans('content.type',['type' => 5]),
                            trans('content.type',['type' => 6]),
                        ],
                        'selected' => 0
                    ])
                    @include('blocks.select_block',[
                        'name' => 'type',
                        'label' => trans('content.number_of_performers'),
                        'values' => [1,2,3,4,5,6,7,8,9,10],
                        'selected' => 0
                    ])
                    @include('blocks.button_block',[
                        'addClass' => 'w-100 mt-3',
                        'id' => null,
                        'primary' => false,
                        'icon' => 'icon-search4',
                        'buttonText' => trans('content.search')
                    ])
                </form>
            </div>
        </div>
        <div class="col col-lg-9 col-md-8 col-sm-12">
            <div id="map" class="rounded-block white h100"></div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('js/map.js') }}"></script>
@endsection
