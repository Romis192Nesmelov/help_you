@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'id' => 'map-image-block',
                'addClass' => 'tall black',
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
        </div>
    </div>
@endsection
