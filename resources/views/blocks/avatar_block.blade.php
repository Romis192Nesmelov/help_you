<div id="avatar-block" {{ isset($addClass) && $addClass ? 'class='.$addClass : '' }}>
    <table>
        <tr>
            <td style="width: 70px;">
                @if ($accountMode)
                    <div class="avatar cir @error('avatar') error @enderror" style="background-image: url({{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg') }} );">
                        <img src="{{ asset('images/avatar_hover.svg') }}" />
                        <input type="file" name="avatar">
                    </div>
                @else
                    <div class="avatar cir" style="background-image: url({{ asset($avatar ?? 'images/def_avatar.svg') }} );"></div>
                @endif
            </td>
            <td>
                <div class="user-name">
                    @if ($accountMode)
                        {!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}
                    @else
                        {{ $userName ?? '' }}
                    @endif
                </div>
                @if (isset($born))
                    <div class="born">{!! trans('content.born_date') !!}</div>
                @endif
            </td>
            @if (!$accountMode)
                <td>
                    @include('blocks.button_block',[
                        'id' => 'subscribe-button',
                        'addClass' => 'mt-0',
                        'icon' => 'icon-bell-check',
                        'primary' => true,
                        'buttonText' => trans('content.subscribe')
                    ])
                </td>
            @endif
        </tr>
        @if ($accountMode)
            <tr>
                <td colspan="2">
                    @include('blocks.error_block', ['name' => 'avatar'])
                </td>
            </tr>
        @endif
    </table>
</div>
