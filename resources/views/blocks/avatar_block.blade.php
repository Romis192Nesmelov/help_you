<div id="avatar-block" {{ isset($addClass) && $addClass ? 'class='.$addClass : '' }}>
    <div class="w-100">
        <div class="d-flex align-items-center justify-content-start">
            <div class="avatar cir @error('avatar') error @enderror" style="{!! avatarProps(auth()->user()->avatar, auth()->user()->avatar_props, 0.35) !!}">
                @if ($hasChangeAvatar)
                    <img src="{{ asset('images/input_image_hover.svg') }}" />
                    <input type="file" name="avatar">
                @endif
            </div>
            <div class="user-name">{!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}</div>
        </div>
        @include('blocks.error_block', ['name' => 'avatar'])
    </div>
</div>
