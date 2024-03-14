@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="rounded-block half">
                <h1 class="mb-5">О нас</h1>
                <p>Cras quis congue purus, quis fringilla lorem. Suspendisse lacinia mattis ante, nec mollis magna tincidunt et. Fusce blandit dui metus, vel lacinia enim faucibus id. Ut eget eleifend massa. Nulla efficitur ornare nibh vitae auctor. Morbi ligula diam, elementum ut lorem non, sollicitudin ornare est. Praesent at erat placerat tellus ultrices condimentum. Vivamus mollis feugiat lorem, gravida gravida sem luctus rhoncus.</p>
            </div>
            <div class="rounded-block half">
                <div class="w-100 image">
                    <img src="{{ asset('images/help.png') }}" />
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="rounded-block tall">
                <h1 class="mb-5">Обратная связь</h1>
                <p class="mb-0">Nunc et dui non elit vestibulum convallis sit amet sit amet nibh. Phasellus maximus viverra efficitur. Donec consequat congue ornare. Praesent id nulla nec lectus fermentum ultricies sed at mi.</p>
                <feedback-component feedback_url="{{ route('feedback') }}"></feedback-component>
            </div>
        </div>
    </div>
@endsection
