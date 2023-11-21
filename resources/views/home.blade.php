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
                'link' => route('how_does_it_work')
            ])
        </div>
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'half',
                'head' => trans('content.home_head2'),
                'content' => trans('content.home_text_block3'),
                'link' => route('order.orders')
            ])
            @include('blocks.rounded_home_block',[
                'addClass' => 'half',
                'head' => trans('content.home_head3'),
                'content' => trans('content.home_text_block4'),
                'link' => route('order.new_order')
            ])
        </div>
    </div>
    @if (!auth()->check())
        <script>
            let getPrevUrl = "{{ route('prev_url') }}";
            const generateCodeUrl = "{{ route('auth.generate_code') }}",
                passwordsMismatch = "{{ trans('auth.password_mismatch') }}",
                passwordCannotBeLess = "{{ trans('auth.password_cannot_be_less', ['length' => 6]) }}",
                youMustConsent = "{{ trans('auth.you_must_consent_to_the_processing_of_personal_data') }}",
        </script>
    @endif
@endsection
