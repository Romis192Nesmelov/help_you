@extends('layouts.main')

@section('content')
    <form class="row" method="post" enctype="multipart/form-data" action="{{ route('edit_account') }}" accept-charset="utf-8">
        @csrf
        <div class="col-12 col-lg-4">
            <div class="rounded-block tall">
                <div id="avatar-block">
                    <div class="d-flex flex-column align-items-center">
                        <div class="avatar @error('avatar') error @enderror" style="background-image: url({{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg') }} );">
                            <input type="file" name="avatar">
                        </div>
                        @error('avatar')
                            @include('blocks.error_block', ['name' => 'avatar'])
                        @enderror
                    </div>
                    <div class="id">ID {{ auth()->user()->id }}</div>
                </div>
                <ul class="menu">
                    @foreach ($account_menu as $itemMenu)
                        <li><a href="{{ route($itemMenu['href']) }}"><i class="{{ $itemMenu['icon'] }}"></i>{{ $itemMenu['name'] }}</a></li>
                    @endforeach
                </ul>
                <div class="bottom-block">
                    <div class="d-flex align-items-center justify-content-left">
                        <a href="{{ route('logout') }}"><i class="icon-exit3"></i>{{ trans('auth.logout') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="rounded-block tall row">
                <div class="col-12 col-lg-6">
                    @foreach (['name','family','born','email','phone'] as $accountTtem)
                        @include('blocks.input_block',[
                            'name' => $accountTtem,
                            'placeholder' => trans('auth.'.$accountTtem),
                            'label' => trans('auth.'.$accountTtem),
                            'ajax' => false,
                            'value' => auth()->user()->$accountTtem
                        ])
                    @endforeach
                </div>
                <div class="col-12 col-lg-6">
                    @include('blocks.textarea_block',[
                        'name' => 'info_about',
                        'label' => trans('auth.info_about'),
                        'ajax' => false,
                        'value' => auth()->user()->info_about
                    ])
                    <div class="w-100 text-end mt-4">
                        @include('blocks.button_block',[
                            'primary' => true,
                            'buttonType' => 'submit',
                            'type' => 'submit',
                            'buttonText' => trans('content.save')
                        ])
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
