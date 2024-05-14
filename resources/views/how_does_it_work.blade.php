@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'tall black how-does-it-work',
                'image' => 'images/edit_order/step1.png',
                'head' => trans('content.how_does_it_work_head1'),
                'link' => route('how_does_it_work',['slug' => 'who-needs-help'])
            ])
        </div>
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'tall black how-does-it-work',
                'image' => 'images/edit_order/step2.png',
                'head' => trans('content.how_does_it_work_head2'),
                'link' => route('how_does_it_work',['slug' => 'who-wants-to-help'])
            ])
        </div>
    </div>
@endsection
