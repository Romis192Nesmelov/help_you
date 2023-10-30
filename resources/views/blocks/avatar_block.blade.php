<div id="avatar-block" {{ isset($addClass) && $addClass ? 'class='.$addClass : '' }}>
    <table>
        <tr>
            <td class="cell-avatar">
                <div class="avatar cir @error('avatar') error @enderror" style="background-image: url({{ asset(auth()->user()->avatar ? auth()->user()->avatar : 'images/def_avatar.svg') }} );">
                    <img src="{{ asset('images/input_image_hover.svg') }}" />
                    <input type="file" name="avatar">
                </div>
            </td>
            <td>
                <div class="user-name">{!! trans('content.welcome', ['user' => auth()->user()->name ? auth()->user()->name.' '.auth()->user()->family : auth()->user()->phone ]) !!}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                @include('blocks.error_block', ['name' => 'avatar'])
            </td>
        </tr>
    </table>
</div>
