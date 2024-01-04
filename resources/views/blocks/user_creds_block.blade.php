<div class="w-100 d-flex mb-3 align-items-center">
    <div class="d-flex align-items-center justify-content-center">
        <div class="avatar cir" style="{!! avatarProps($user->avatar, $user->avatar_props, 0.35) !!}"></div>
        <div class="ms-3">
            <div class="fs-lg-6 fs-sm-7">@include('blocks.user_name_block', ['user' => $user])</div>
            <div class="fs-lg-6 fs-sm-7">{{ getUserAge($user) }}</div>
            @if (isset($rating) && $rating !== null)
                <div class="mt-2">
                    @include('blocks.rating_line_block',['rating' => $rating])
                </div>
            @endif
        </div>
    </div>
</div>
