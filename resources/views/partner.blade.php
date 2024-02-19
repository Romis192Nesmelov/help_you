@extends('layouts.main')

@section('content')
    <div class="row border-top border-2 pt-3">
        <div class="col-12 col-md-6 col-lg-2 d-flex align-items-center">
            <img src="{{ asset($partner->logo) }}" />
        </div>
        <div class="col-12 col-md-6 col-lg-10">
            <div class="rounded-block partner">
                <h2>{{ trans('content.info_about_company') }}</h2>
                <p>{{ $partner->about }}</p>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {!! $partner->info !!}
    </div>
@endsection
