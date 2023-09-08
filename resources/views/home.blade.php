@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'id' => 'map-image-block',
                'addClass' => 'black',
                'head' => trans('content.home_head1'),
                'content' => [trans('content.home_text_block1'), trans('content.home_text_block2')],
                'addLink' => '#',
                'addLinkText' => trans('content.more_about_gifts'),
                'link' => '#'
            ])
        </div>
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'half',
                'head' => trans('content.home_head2'),
                'content' => trans('content.home_text_block3'),
                'link' => '#'
            ])
            @include('blocks.rounded_home_block',[
                'addClass' => 'half',
                'head' => trans('content.home_head3'),
                'content' => trans('content.home_text_block4'),
                'link' => '#'
            ])

{{--            <div class="rounded-block orange home h50">--}}
{{--                <h1>{!! trans('content.home_head2') !!}</h1>--}}
{{--                <p>{{ trans('content.home_text_block3') }}</p>--}}
{{--                <div class="bottom-block d-flex justify-content-end">--}}
{{--                    @include('blocks.rounded_link_block', ['link' => '#'])--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="rounded-block black home h50">--}}
{{--                <h1>{!! trans('content.home_head3') !!}</h1>--}}
{{--                <p>{{ trans('content.home_text_block4') }}</p>--}}
{{--                <div class="bottom-block d-flex justify-content-end">--}}
{{--                    @include('blocks.rounded_link_block', ['link' => '#'])--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
@endsection
