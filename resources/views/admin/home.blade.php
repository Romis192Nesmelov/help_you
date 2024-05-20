@extends('layouts.admin')

@section('content')
    @foreach($menu as $item)
        @if (!$loop->first && !$item['hidden'])
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="panel panel-flat">
                    <div class="panel-body text-center">
                        <div class="icon-object"><i class="{{ $item['icon'] }}"></i></div>
                        <h3 class="text-semibold">{{ trans('admin_menu.'.$item['key']) }}</h3>
                        <p class="mb-15 h-50">{{ trans('admin_menu.'.$item['key'].'_description') }}</p>
                        <a href="{{ route('admin.'.$item['key']) }}">
                            @include('admin.blocks.button_block', [
                                'primary' => true,
                                'buttonType' => 'button',
                                'buttonText' => trans('admin.goto'),
                                'icon' => 'icon-circle-right2'
                            ])
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
