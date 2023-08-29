@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col col-md-6 col-sm-12">
            <div id="map-image-block" class="rounded-block black home h100">
                <h1>{!! trans('content.home_head1') !!}</h1>
                <div class="bottom-block">
                    <p>{{ trans('content.home_text_block1') }}</p>
                    <p>{{ trans('content.home_text_block2') }}</p>
                    <div class="d-flex justify-content-between">
                        <a href="#">{{ trans('content.more_about_gifts') }}</a>
                        @include('blocks.rounded_link_block', ['link' => route('map')])
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-md-6 col-sm-12">
            <div class="rounded-block orange home h50">
                <h1>{!! trans('content.home_head2') !!}</h1>
                <p>{{ trans('content.home_text_block3') }}</p>
                <div class="bottom-block d-flex justify-content-end">
                    @include('blocks.rounded_link_block', ['link' => '#'])
                </div>
            </div>
            <div class="rounded-block black home h50">
                <h1>{!! trans('content.home_head3') !!}</h1>
                <p>{{ trans('content.home_text_block4') }}</p>
                <div class="bottom-block d-flex justify-content-end">
                    @include('blocks.rounded_link_block', ['link' => '#'])
                </div>
            </div>
        </div>
    </div>
@endsection
