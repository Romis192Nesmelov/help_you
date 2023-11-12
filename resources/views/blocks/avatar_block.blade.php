@php
    $avatarStyles = 'background-image:url('.(asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg')).');';
    if (auth()->user()->avatar_props) {
        foreach (auth()->user()->avatar_props as $prop => $value) {
            $avatarStyles .= $prop.':'.$value.';';
        }
    }
@endphp

<div id="avatar-block" {{ isset($addClass) && $addClass ? 'class='.$addClass : '' }}>
    <div class="w-100">
        <div class="d-flex align-items-center justify-content-start">
            <div class="avatar cir @error('avatar') error @enderror" style="{!! $avatarStyles !!}">
                <img src="{{ asset('images/input_image_hover.svg') }}" />
                <input type="file" name="avatar">
            </div>
            <div class="user-name">{!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}</div>
        </div>
        @include('blocks.error_block', ['name' => 'avatar'])
    </div>
</div>
